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
        $data = PharmacyStockBatch::query()
            ->with('medicine:id,name')
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date')
            ->orderBy('id');

        return DataTables::of($data)
            ->addColumn('medicine_name', fn ($row) => $row->medicine?->name ?? '-')
            ->addColumn('expiry_status', function ($row) {
                if (!$row->expiry_date) {
                    return 'N/A';
                }

                if ($row->expiry_date->isPast()) {
                    return '<span class="badge badge-danger">Expired</span>';
                }

                $days = now()->diffInDays($row->expiry_date, false);
                if ($days <= 30) {
                    return '<span class="badge badge-warning">Near Expiry (' . $days . ' days)</span>';
                }

                return '<span class="badge badge-success">Safe</span>';
            })
            ->editColumn('expiry_date', fn ($row) => $row->expiry_date ? $row->expiry_date->format('d-m-Y') : '-')
            ->rawColumns(['expiry_status'])
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
