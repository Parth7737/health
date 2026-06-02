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

class PharmacyStockController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->routes = [
            'loadtable' => route('hospital.pharmacy.stock-load'),
            'showBadStockForm' => route('hospital.pharmacy.stock.show-bad-stock-form'),
            'adjustBadStock' => route('hospital.pharmacy.stock.adjust-bad-stock'),
        ];
    }

    public function index()
    {
        return view('hospital.pharmacy.stock.index', [
            'pathurl' => 'pharmacy-stock',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = $this->stockQuery($request);

        return DataTables::of($data)
            ->addColumn('medicine_name', fn ($row) => $row->medicine?->name ?? '-')
            ->addColumn('category_name', fn ($row) => $row->medicine?->category?->name ?? '-')
            ->addColumn('form_name', fn ($row) => $row->medicine?->medicine_unit_id ? optional($row->medicine->unit)->name : '-')
            ->addColumn('min_level', fn ($row) => $row->medicine?->min_level ?? 0)
            ->addColumn('reorder_level', fn ($row) => $row->medicine?->reorder_level ?? 0)
            ->addColumn('medicine_available_qty', fn ($row) => (float) ($row->medicine_available_qty ?? 0))
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
        $filename = 'pharmacy-stock-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Medicine',
                'Category',
                'Form',
                'Batch',
                'Expiry Date',
                'Stock',
                'Min Level',
                'MRP',
                'Sale Price',
                'Status',
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
                    $row->medicine?->medicine_unit_id ? optional($row->medicine->unit)->name : '-',
                    $row->batch_no ?? '-',
                    $expiry,
                    (string) $row->available_qty,
                    (string) ($row->medicine?->min_level ?? 0),
                    (string) $row->unit_mrp,
                    (string) $row->unit_sale_price,
                    (string) $row->status,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function stockQuery(Request $request)
    {
        $usableStock = $this->usableStockByMedicineSubQuery();

        $query = PharmacyStockBatch::query()
            ->select('pharmacy_stock_batches.*')
            ->selectRaw('COALESCE(usable_stock.available_qty, 0) as medicine_available_qty')
            ->leftJoinSub($usableStock, 'usable_stock', function ($join) {
                $join->on('usable_stock.medicine_id', '=', 'pharmacy_stock_batches.medicine_id');
            })
            ->with('medicine:id,name,medicine_unit_id,min_level,reorder_level,medicine_category_id')
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
        if ($stockFilter === 'in_stock') {
            $query->whereRaw('COALESCE(usable_stock.available_qty, 0) > 0');
        } elseif ($stockFilter === 'out_of_stock') {
            $query->whereRaw('COALESCE(usable_stock.available_qty, 0) <= 0');
        } elseif ($stockFilter === 'low_stock') {
            $query->whereRaw('COALESCE(usable_stock.available_qty, 0) > 0')
                ->whereHas('medicine', function ($q) {
                    $q->where('medicines.reorder_level', '>', 0);
                })
                ->whereRaw('COALESCE(usable_stock.available_qty, 0) <= (select m.reorder_level from medicines as m where m.id = pharmacy_stock_batches.medicine_id limit 1)');
        } elseif ($stockFilter === 'expired') {
            $query->whereDate('expiry_date', '<', now()->toDateString());
            $query->whereRaw('COALESCE(usable_stock.available_qty, 0) <= 0');
        } elseif ($stockFilter === 'all' && $expiryFilter === 'all') {
            $query->where(function ($q) {
                $q->whereRaw('COALESCE(usable_stock.available_qty, 0) <= 0')
                    ->orWhere(function ($activeBatch) {
                        $activeBatch->where('pharmacy_stock_batches.available_qty', '>', 0)
                            ->where('pharmacy_stock_batches.status', 'active')
                            ->where(function ($expiry) {
                                $expiry->whereNull('pharmacy_stock_batches.expiry_date')
                                    ->orWhereDate('pharmacy_stock_batches.expiry_date', '>=', now()->toDateString());
                            });
                    });
            });
        }

        if ($expiryFilter === 'exp_30') {
            $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()]);
        } elseif ($expiryFilter === 'exp_90') {
            $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(90)->toDateString()]);
        } elseif ($expiryFilter === 'expired') {
            $query->whereDate('expiry_date', '<', now()->toDateString());
            $query->whereRaw('COALESCE(usable_stock.available_qty, 0) <= 0');
        }

        return $query;
    }

    private function usableStockByMedicineSubQuery()
    {
        return DB::table('pharmacy_stock_batches')
            ->select('medicine_id')
            ->selectRaw('SUM(available_qty) as available_qty')
            ->where('hospital_id', $this->hospital_id)
            ->where('status', 'active')
            ->where('available_qty', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now()->toDateString());
            })
            ->groupBy('medicine_id');
    }

    public function showBadStockForm(Request $request)
    {
        $id = (int) $request->id;
        $batch = PharmacyStockBatch::query()->with('medicine:id,name')->findOrFail($id);

        return view('hospital.pharmacy.stock.bad-stock-form', compact('batch'));
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
            (string) ($request->reason ?: 'damaged')
        );

        return response()->json([
            'status' => true,
            'message' => 'Bad stock adjusted successfully.',
        ]);
    }
}
