<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\PharmacyStockBatch;
use App\Services\PharmacyInventoryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PharmacyExpiryController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->routes = [
            'loadtable' => route('hospital.pharmacy.expiry-load'),
            'process' => route('hospital.pharmacy.expiry.process'),
        ];
    }

    public function index()
    {
        return view('hospital.pharmacy.expiry.index', [
            'pathurl' => 'pharmacy-expiry',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $search       = is_array($request->input('search')) ? ($request->input('search.value') ?? '') : (string) $request->input('search', '');
        $expiryFilter = (string) $request->input('expiry_filter', 'all_alerts');

        $data = PharmacyStockBatch::query()
            ->with('medicine:id,name')
            ->whereNotNull('expiry_date')
            ->where('available_qty', '>', 0);

        // Default: show only alerts (expired or expiring within 90 days)
        switch ($expiryFilter) {
            case 'expired':
                $data->whereDate('expiry_date', '<', now()->toDateString());
                break;
            case 'exp_30':
                $data->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()]);
                break;
            case 'exp_90':
                $data->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(90)->toDateString()]);
                break;
            default: // all_alerts — expired or expiring within 90 days
                $data->where(function ($q) {
                    $q->whereDate('expiry_date', '<', now()->toDateString())
                      ->orWhereBetween('expiry_date', [now()->toDateString(), now()->addDays(90)->toDateString()]);
                });
                break;
        }

        if ($search !== '') {
            $data->where(function ($q) use ($search) {
                $q->where('batch_no', 'like', '%' . $search . '%')
                  ->orWhereHas('medicine', fn ($mq) => $mq->where('name', 'like', '%' . $search . '%'));
            });
        }

        $data->orderBy('expiry_date')->orderBy('pharmacy_stock_batches.id');

        return DataTables::of($data)
            ->addColumn('medicine_name', fn ($row) => $row->medicine?->name ?? '-')
            ->addColumn('days_left', function ($row) {
                if (!$row->expiry_date) {
                    return null;
                }
                return (int) now()->startOfDay()->diffInDays($row->expiry_date->startOfDay(), false);
            })
            ->addColumn('expiry_status_label', function ($row) {
                if (!$row->expiry_date) {
                    return 'N/A';
                }
                $days = (int) now()->startOfDay()->diffInDays($row->expiry_date->startOfDay(), false);
                if ($days < 0) {
                    return 'Expired';
                }
                if ($days <= 30) {
                    return 'Near Expiry';
                }
                return 'Watch';
            })
            ->editColumn('expiry_date', fn ($row) => $row->expiry_date ? $row->expiry_date->format('d-m-Y') : '-')
            ->rawColumns([])
            ->make(true);
    }

    public function processExpired(Request $request, PharmacyInventoryService $inventoryService)
    {
        $affected = $inventoryService->markExpiredBatches($this->hospital_id);

        return response()->json([
            'status' => true,
            'message' => $affected . ' expired batch processed successfully.',
        ]);
    }
}
