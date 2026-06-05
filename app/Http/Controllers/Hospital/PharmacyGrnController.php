<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\PharmacyGrn;
use App\Models\PharmacyPurchaseBill;
use App\Services\PharmacyInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PharmacyGrnController extends BaseHospitalController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pharmacy-purchase', ['only' => ['store']]);
    }

    /**
     * DataTable source for GRN log.
     */
    public function loaddata(Request $request)
    {
        $data = PharmacyGrn::with(['supplier:id,name', 'receivedByUser:id,name', 'purchaseBill:id,bill_no'])
            ->withCount('items')
            ->latest('id');

        return DataTables::of($data)
            ->addColumn('supplier_name', fn ($row) => $row->supplier?->name ?? '—')
            ->addColumn('po_no', fn ($row) => $row->purchaseBill?->bill_no ?? '—')
            ->addColumn('received_by_name', fn ($row) => $row->receivedByUser?->name ?? '—')
            ->editColumn('received_at', fn ($row) => optional($row->received_at)->format('d-m-Y H:i'))
            ->editColumn('total_amount', fn ($row) => $row->total_amount)
            ->make(true);
    }

    public function view(PharmacyGrn $grn)
    {
        if ((int) $grn->hospital_id !== (int) $this->hospital_id) {
            abort(403);
        }

        $grn->load([
            'supplier',
            'purchaseBill:id,bill_no,bill_date',
            'receivedByUser:id,name',
            'items.medicine:id,name,medicine_unit_id',
        ]);

        return view('hospital.pharmacy.grn.view', compact('grn'));
    }

    public function print(PharmacyGrn $grn)
    {
        if ((int) $grn->hospital_id !== (int) $this->hospital_id) {
            abort(403);
        }

        $grn->load([
            'supplier',
            'purchaseBill:id,bill_no,bill_date',
            'receivedByUser:id,name',
            'items.medicine:id,name,medicine_unit_id',
        ]);

        $hospital = \App\Models\Hospital::query()->find($this->hospital_id);
        $printTemplate = \App\Models\HeaderFooter::query()->where('type', 'pharmacy_bill')->first();

        return view('hospital.pharmacy.grn.print', compact('grn', 'hospital', 'printTemplate'));
    }

    /**
     * Return approved POs with their items for the GRN modal dropdown.
     */
    public function approvedPOs(Request $request)
    {
        $pos = PharmacyPurchaseBill::with(['items.medicine:id,name,medicine_unit_id,vat,default_pack_size', 'supplier:id,name,state_id'])
            ->whereIn('status', ['approved', 'partially_received'])
            ->latest('id')
            ->get()
            ->map(function ($po) {
                return [
                    'id'       => $po->id,
                    'bill_no'  => $po->bill_no,
                    'supplier' => $po->supplier?->name ?? '—',
                    'supplier_state_id' => $po->supplier?->state_id,
                    'date'     => optional($po->bill_date)->format('d-m-Y'),
                    'items'    => $po->items->map(function ($item) {
                        $packSize = max(1, (int) ($item->pack_size_qty ?: ($item->medicine?->default_pack_size ?? 1)));
                        $remaining = max(0, (float) $item->total_quantity - (float) $item->quantity_received);
                        return [
                            'purchase_item_id'    => $item->id,
                            'medicine_id'         => $item->medicine_id,
                            'medicine_name'       => $item->medicine?->name ?? '—',
                            'ordered_qty'         => (float) $item->total_quantity,
                            'ordered_pack_qty'    => (float) ($item->pack_qty ?: ((float) $item->total_quantity / $packSize)),
                            'already_received'    => (float) $item->quantity_received,
                            'remaining_qty'       => $remaining,
                            'remaining_pack_qty'  => $remaining / $packSize,
                            'unit_purchase_price' => (float) $item->unit_purchase_price,
                            'pack_purchase_price' => (float) ($item->pack_purchase_price ?: ((float) $item->unit_purchase_price * $packSize)),
                            'vat'                 => (float) ($item->medicine?->vat ?? 0),
                            'default_pack_size'   => $packSize,
                        ];
                    })->filter(fn ($i) => $i['remaining_qty'] > 0)->values(),
                ];
            })
            ->filter(fn ($po) => $po['items']->isNotEmpty())
            ->values();

        return response()->json($pos);
    }

    /**
     * Store a new GRN against an approved PO.
     */
    public function store(Request $request, PharmacyInventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'purchase_bill_id'            => 'required|exists:pharmacy_purchase_bills,id',
            'invoice_no'                  => 'required|string|max:255',
            'invoice_date'                => 'required|date',
            'vehicle_no'                  => 'nullable|string|max:100',
            'temperature_status'          => 'nullable|string|max:100',
            'notes'                       => 'nullable|string',
            'gst_type'                    => 'required|in:local,interstate',
            'items'                       => 'required|array|min:1',
            'items.*.purchase_item_id'    => 'required|integer',
            'items.*.batch_no'            => 'required|string|max:100',
            'items.*.expiry_date'         => 'nullable|date',
            'items.*.pack_size'           => 'required|integer|min:1',
            'items.*.quantity_received'   => 'required|numeric|min:0.0001',
            'items.*.quantity_free'       => 'nullable|numeric|min:0',
            'items.*.quantity_rejected'   => 'nullable|numeric|min:0',
            'items.*.unit_purchase_price' => 'required|numeric|min:1',
            'items.*.unit_sale_price'     => 'nullable|numeric|min:1',
            'items.*.unit_mrp'            => 'nullable|numeric|min:1',
            'items.*.tax_percent'         => 'nullable|numeric|min:0|max:100',
            'items.*.rejection_reason'    => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $grn = $inventoryService->createGRN([
                'hospital_id'      => $this->hospital_id,
                'purchase_bill_id' => $request->purchase_bill_id,
                'invoice_no'       => $request->invoice_no,
                'invoice_date'     => $request->invoice_date,
                'vehicle_no'       => $request->vehicle_no,
                'temperature_status' => $request->temperature_status,
                'notes'            => $request->notes,
                'items'            => $request->items,
                'gst_type'         => $request->gst_type,
            ]);
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'GRN created successfully. Stock inwarded for accepted quantities.',
            'grn_no'  => $grn->grn_no,
        ]);
    }
}
