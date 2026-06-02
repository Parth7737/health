<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\HeaderFooter;
use App\Models\Hospital;
use App\Models\Medicine;
use App\Models\PharmacyPurchaseBill;
use App\Models\PharmacySupplier;
use App\Services\PharmacyInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PharmacyPurchaseController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pharmacy-purchase', ['only' => ['store', 'update', 'destroy']]);
        $this->routes = [
            'store'     => route('hospital.pharmacy.purchase.store'),
            'loadtable' => route('hospital.pharmacy.purchase-load'),
            'showform'  => route('hospital.pharmacy.purchase.showform'),
            'update'    => route('hospital.pharmacy.purchase.update', ['bill' => '__ID__']),
            'approve'   => route('hospital.pharmacy.purchase.approve', ['bill' => '__ID__']),
            'reject'    => route('hospital.pharmacy.purchase.reject', ['bill' => '__ID__']),
            'print'     => route('hospital.pharmacy.purchase.print', ['bill' => '__ID__']),
            'details'   => route('hospital.pharmacy.purchase.details', ['bill' => '__ID__']),
            'delete'    => route('hospital.pharmacy.purchase.delete', ['bill' => '__ID__']),
        ];
    }

    public function index()
    {
        return view('hospital.pharmacy.purchase.index', [
            'pathurl' => 'pharmacy-purchase',
            'routes'  => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PharmacyPurchaseBill::with(['supplier', 'createdBy:id,name'])->withCount('items')->latest('id');

        return DataTables::of($data)
            ->editColumn('bill_date', fn ($row) => optional($row->bill_date)->format('d-m-Y'))
            ->editColumn('status', fn ($row) => $row->status ?? 'pending')
            ->addColumn('supplier_name', fn ($row) => $row->supplier?->name ?? $row->supplier_name ?? '—')
            ->addColumn('items_count', fn ($row) => $row->items_count)
            ->addColumn('created_by_name', fn ($row) => $row->createdBy?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('hospital.pharmacy.purchase.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        if ($request->boolean('view')) {
            $bill = PharmacyPurchaseBill::with([
                'supplier',
                'createdBy:id,name',
                'approvedBy:id,name',
                'items.medicine',
            ])->findOrFail($request->id);

            if ($bill->hospital_id !== $this->hospital_id) {
                abort(403);
            }

            return view('hospital.pharmacy.purchase.view', compact('bill'));
        }

        $bill      = null;
        $medicines = Medicine::query()->select('id', 'name', 'unit')->orderBy('name')->get();
        $suppliers = PharmacySupplier::query()->select('id', 'name', 'phone')->orderBy('name')->get();

        if ($request->id) {
            $bill = PharmacyPurchaseBill::with('items.medicine')->findOrFail($request->id);
            if ($bill->hospital_id !== $this->hospital_id) {
                abort(403);
            }
        }

        return view('hospital.pharmacy.purchase.form', compact('bill', 'medicines', 'suppliers'));
    }

    public function store(Request $request, PharmacyInventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'bill_date'                   => 'required|date',
            'supplier_id'                 => 'nullable|exists:pharmacy_suppliers,id',
            'notes'                       => 'nullable|string',
            'items'                       => 'required|array|min:1',
            'items.*.medicine_id'         => 'required|exists:medicines,id',
            'items.*.quantity_purchased'  => 'required|numeric|min:1',
            'items.*.unit_purchase_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $bill = $inventoryService->createPurchaseBill([
                'hospital_id' => $this->hospital_id,
                'bill_date'   => $request->bill_date,
                'supplier_id' => $request->supplier_id,
                'notes'       => $request->notes,
                'items'       => $request->items,
            ]);
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Purchase bill created successfully.',
            'bill_no' => $bill->bill_no,
        ]);
    }

    public function update(Request $request, PharmacyPurchaseBill $bill, PharmacyInventoryService $inventoryService)
    {
        if ($bill->hospital_id !== $this->hospital_id) {
            abort(403);
        }

        if ($bill->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'Only pending purchase orders can be updated.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'bill_date'                   => 'required|date',
            'supplier_id'                 => 'nullable|exists:pharmacy_suppliers,id',
            'notes'                       => 'nullable|string',
            'items'                       => 'required|array|min:1',
            'items.*.medicine_id'         => 'required|exists:medicines,id',
            'items.*.quantity_purchased'  => 'required|numeric|min:1',
            'items.*.unit_purchase_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $bill = $inventoryService->updatePurchaseBill($bill, [
                'bill_date'   => $request->bill_date,
                'supplier_id' => $request->supplier_id,
                'notes'       => $request->notes,
                'items'       => $request->items,
            ]);
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Purchase bill updated successfully.',
            'bill_no' => $bill->bill_no,
        ]);
    }

    public function approve(PharmacyPurchaseBill $bill, PharmacyInventoryService $inventoryService)
    {
        if ($bill->hospital_id !== $this->hospital_id) {
            abort(403);
        }

        try {
            $bill = $inventoryService->approvePurchaseBill($bill);
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Purchase order approved. Stock inward completed.',
            'bill_no' => $bill->bill_no,
        ]);
    }

    public function reject(Request $request, PharmacyPurchaseBill $bill, PharmacyInventoryService $inventoryService)
    {
        if ($bill->hospital_id !== $this->hospital_id) {
            abort(403);
        }

        try {
            $bill = $inventoryService->rejectPurchaseBill($bill, $request->input('reject_reason', ''));
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Purchase order rejected.',
            'bill_no' => $bill->bill_no,
        ]);
    }

    public function printBill(PharmacyPurchaseBill $bill)
    {
        if ($bill->hospital_id !== $this->hospital_id) {
            abort(403);
        }

        $bill->load(['items.medicine', 'supplier']);
        $hospital = Hospital::query()->find($this->hospital_id);
        $printTemplate = HeaderFooter::query()->where('type', 'pharmacy_bill')->first();

        return view('hospital.pharmacy.purchase.print', compact('bill', 'hospital', 'printTemplate'));
    }

    /**
     * Get single purchase bill details as JSON.
     */
    public function getDetails($id)
    {
        $bill = PharmacyPurchaseBill::with('items.medicine')->findOrFail($id);
        if ($bill->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => true,
            'bill' => [
                'id' => $bill->id,
                'bill_no' => $bill->bill_no,
                'bill_date' => optional($bill->bill_date)->format('Y-m-d'),
                'supplier_id' => $bill->supplier_id,
                'notes' => $bill->notes,
                'status' => $bill->status,
            ],
            'items' => $bill->items->map(fn ($item) => [
                'id' => $item->id,
                'medicine_id' => $item->medicine_id,
                'medicine_name' => $item->medicine?->name ?? '—',
                'quantity_purchased' => (float) $item->quantity_purchased,
                'unit_purchase_price' => (float) $item->unit_purchase_price,
            ]),
        ]);
    }

    /**
     * Delete a pending purchase order.
     */
    public function destroy(PharmacyPurchaseBill $bill)
    {
        if ($bill->hospital_id !== $this->hospital_id) {
            abort(403);
        }

        if ($bill->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'Only pending purchase orders can be deleted.'], 422);
        }

        // Delete items and the bill itself
        $bill->items()->delete();
        $bill->delete();

        return response()->json([
            'status' => true,
            'message' => 'Purchase order deleted successfully.',
        ]);
    }
}
