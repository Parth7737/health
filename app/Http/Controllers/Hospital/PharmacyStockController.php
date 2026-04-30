<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\PharmacyStockBatch;
use App\Services\PharmacyInventoryService;
use Illuminate\Http\Request;
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
        $data = PharmacyStockBatch::query()->with('medicine:id,name')->latest('id');

        return DataTables::of($data)
            ->addColumn('medicine_name', fn ($row) => $row->medicine?->name ?? '-')
            ->editColumn('expiry_date', fn ($row) => $row->expiry_date ? $row->expiry_date->format('d-m-Y') : '-')
            ->addColumn('actions', function ($row) {
                if (!auth()->user()->can('edit-pharmacy-bad-stock')) {
                    return '-';
                }

                return view('hospital.pharmacy.stock.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
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
