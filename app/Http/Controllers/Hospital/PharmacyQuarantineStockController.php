<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\PharmacyStockBatch;
use App\Services\PharmacyInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PharmacyQuarantineStockController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->routes = [
            'loadtable' => route('hospital.pharmacy.quarantine-stock-load'),
            'showQuarantineBadStockForm' => route('hospital.pharmacy.quarantine-stock.show-bad-stock-form'),
            'adjustQuarantineBadStock' => route('hospital.pharmacy.quarantine-stock.adjust-bad-stock'),
        ];
    }

    public function loaddata(Request $request)
    {
        $data = $this->stockQuery($request);

        return DataTables::of($data)
            ->addColumn('medicine_name', fn ($row) => $row->medicine?->name ?? '-')
            ->addColumn('category_name', fn ($row) => $row->medicine?->category?->name ?? '-')
            ->addColumn('form_name', fn ($row) => $row->medicine?->unit ?? '-')
            ->addColumn('min_level', fn ($row) => $row->medicine?->min_level ?? 0)
            ->addColumn('reorder_level', fn ($row) => $row->medicine?->reorder_level ?? 0)
            ->addColumn('expiry_iso', function ($row) {
                if (!$row->expiry_date) {
                    return null;
                }

                try {
                    return Carbon::parse($row->expiry_date)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return null;
                }
            })
            ->editColumn('expiry_date', function ($row) {
                if (!$row->expiry_date) {
                    return '-';
                }

                try {
                    return Carbon::parse($row->expiry_date)->format('d-m-Y');
                } catch (\Throwable $e) {
                    return (string) $row->expiry_date;
                }
            })
            ->addColumn('actions', function ($row) {
                if (!auth()->user()->can('edit-pharmacy-bad-stock')) {
                    return '-';
                }

                return view('hospital.pharmacy.stock.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->stockQuery($request)->get();
        $filename = 'pharmacy-quarantine-stock-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Medicine',
                'Category',
                'Form',
                'Batch',
                'Expiry Date',
                'Quarantine Stock',
                'Min Level',
                'MRP',
            ]);

            foreach ($rows as $row) {
                $expiry = '-';
                if ($row->expiry_date) {
                    try {
                        $expiry = Carbon::parse($row->expiry_date)->format('d-m-Y');
                    } catch (\Throwable $e) {
                        $expiry = (string) $row->expiry_date;
                    }
                }

                fputcsv($out, [
                    $row->medicine?->name ?? '-',
                    $row->medicine?->category?->name ?? '-',
                    $row->medicine?->unit ?? '-',
                    $row->batch_no ?? '-',
                    $expiry,
                    (string) $row->reserved_qty,
                    (string) ($row->medicine?->min_level ?? 0),
                    (string) $row->unit_mrp,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function stockQuery(Request $request)
    {

        $query = PharmacyStockBatch::query()
            ->select('pharmacy_stock_batches.*')
            ->where('pharmacy_stock_batches.status', 'quarantined')
            ->with('medicine:id,name,unit,min_level,reorder_level,medicine_category_id')
            ->with('medicine.category:id,name')
            ->latest('pharmacy_stock_batches.id');

        $categoryId = (int) $request->input('category_id', 0);
        if ($categoryId > 0) {
            $query->whereHas('medicine', function ($q) use ($categoryId) {
                $q->where('medicine_category_id', $categoryId);
            });
        }

        $searchInput = $request->input('search', '');
        if (is_array($searchInput)) {
            $searchInput = $searchInput['value'] ?? '';
        }
        $search = trim((string) $searchInput);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('batch_no', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('medicine', function ($mq) use ($search) {
                        $mq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('medicine.category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $stockFilter = (string) $request->input('stock_filter', 'all');
        $expiryFilter = (string) $request->input('expiry_filter', 'all');

        if ($expiryFilter === 'exp_30') {
            $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()]);
        } elseif ($expiryFilter === 'exp_90') {
            $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(90)->toDateString()]);
        } elseif ($expiryFilter === 'expired') {
            $query->whereDate('expiry_date', '<', now()->toDateString());
            $query->whereRaw('COALESCE(usable_stock.reserved_qty, 0) <= 0');
        }

        return $query;
    }

    public function showBadStockForm(Request $request)
    {
        $id = (int) $request->id;
        $batch = PharmacyStockBatch::query()->with('medicine:id,name')->findOrFail($id);

        return view('hospital.pharmacy.quarantine-stock.bad-stock-form', compact('batch'));
    }

    public function adjustBadStock(Request $request, PharmacyInventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'stock_batch_id' => 'required|exists:pharmacy_stock_batches,id',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $inventoryService->adjustBadStock(
            (int) $request->stock_batch_id,
            (float) $request->quantity,
            (string) ($request->reason ?: 'damaged'),
            (string) ('quarantine')
        );

        return response()->json([
            'status' => true,
            'message' => 'Bad stock adjusted successfully.',
        ]);
    }
}
