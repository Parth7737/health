<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\HeaderFooter;
use App\Models\Hospital;
use App\Models\IpdPrescription;
use App\Models\Medicine;
use App\Models\OpdPrescription;
use App\Models\Patient;
use App\Models\PharmacySaleBill;
use App\Models\PharmacyStockBatch;
use App\Services\PharmacyInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PharmacySaleController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pharmacy-sale', ['only' => ['store', 'showform']]);
        $this->routes = [
            'store' => route('hospital.pharmacy.sale.store'),
            'loadtable' => route('hospital.pharmacy.sale-load'),
            'showform' => route('hospital.pharmacy.sale.showform'),
            'loadPrescriptionItems' => route('hospital.pharmacy.sale.load-prescription-items'),
            'medicineBatches' => route('hospital.pharmacy.sale.medicine-batches'),
            'print' => route('hospital.pharmacy.sale.print', ['bill' => '__ID__']),
        ];
    }

    public function index()
    {
        $today = now()->toDateString();

        $todaySales = PharmacySaleBill::query()->whereDate('bill_date', $today);
        $todaySalesCount = (clone $todaySales)->count();
        $todayNetTotal = (float) (clone $todaySales)->sum('net_total');
        $todayDue = (float) (clone $todaySales)->sum('due_amount');
        $todayPrescriptionSales = (clone $todaySales)->where('is_from_prescription', true)->count();
        $todayWalkInSales = (clone $todaySales)->whereNull('patient_id')->count();
        $pendingDues = (float) PharmacySaleBill::query()->where('due_amount', '>', 0)->sum('due_amount');

        $quickOpdPrescriptions = OpdPrescription::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($query) use ($today) {
                $query->whereNull('valid_till')->orWhere('valid_till', '>=', $today);
            })
            ->doesntHave('saleBill')
            ->with('patient:id,name,patient_id')
            ->latest('id')
            ->limit(150)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'patient_id' => $p->patient_id,
                'label' => $this->formatPrescriptionLabel('opd', $p->id, $p->prescription_no, $p->patient?->name, $p->patient?->patient_id, $p->valid_till),
            ]);

        $quickIpdPrescriptions = IpdPrescription::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($query) use ($today) {
                $query->whereNull('valid_till')->orWhere('valid_till', '>=', $today);
            })
            ->doesntHave('saleBill')
            ->with('patient:id,name,patient_id')
            ->latest('id')
            ->limit(150)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'patient_id' => $p->patient_id,
                'label' => $this->formatPrescriptionLabel('ipd', $p->id, $p->prescription_no, $p->patient?->name, $p->patient?->patient_id, $p->valid_till),
            ]);

        return view('hospital.pharmacy.sale.index', [
            'pathurl' => 'pharmacy-sale',
            'routes' => $this->routes,
            'saleStats' => [
                'today_sales_count' => $todaySalesCount,
                'today_net_total' => round($todayNetTotal, 2),
                'today_due_total' => round($todayDue, 2),
                'today_prescription_sales' => $todayPrescriptionSales,
                'today_walk_in_sales' => $todayWalkInSales,
                'pending_due_total' => round($pendingDues, 2),
            ],
            'quickOpdPrescriptions' => $quickOpdPrescriptions,
            'quickIpdPrescriptions' => $quickIpdPrescriptions,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PharmacySaleBill::query()->with('patient')->latest('id');

        return DataTables::of($data)
            ->addColumn('patient_name', fn ($row) => $row->patient?->name ?? '-')
            ->editColumn('bill_date', fn ($row) => optional($row->bill_date)->format('d-m-Y'))
            ->addColumn('actions', function ($row) {
                return view('hospital.pharmacy.sale.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $today = now()->toDateString();

        $patients = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->select('id', 'name', 'patient_id')
            ->orderBy('name')
            ->get();

        $medicines = Medicine::query()->select('id', 'name', 'unit')->orderBy('name')->get();

        $opdPrescriptions = OpdPrescription::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($query) use ($today) {
                $query->whereNull('valid_till')->orWhere('valid_till', '>=', $today);
            })
            ->doesntHave('saleBill')
            ->with('patient:id,name')
            ->latest('id')
            ->limit(100)
            ->get();

        $ipdPrescriptions = IpdPrescription::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($query) use ($today) {
                $query->whereNull('valid_till')->orWhere('valid_till', '>=', $today);
            })
            ->doesntHave('saleBill')
            ->with('patient:id,name')
            ->latest('id')
            ->limit(100)
            ->get();

        $initialPrescriptionType = $request->input('prescription_type');
        $initialPrescriptionId = $request->input('prescription_id');
        $initialPatientId = null;

        if ($initialPrescriptionType === 'opd' && !empty($initialPrescriptionId)) {
            $rx = $opdPrescriptions->firstWhere('id', (int) $initialPrescriptionId)
                ?: OpdPrescription::query()->where('hospital_id', $this->hospital_id)->find((int) $initialPrescriptionId);
            $initialPatientId = $rx?->patient_id;
        }

        if ($initialPrescriptionType === 'ipd' && !empty($initialPrescriptionId)) {
            $rx = $ipdPrescriptions->firstWhere('id', (int) $initialPrescriptionId)
                ?: IpdPrescription::query()->where('hospital_id', $this->hospital_id)->find((int) $initialPrescriptionId);
            $initialPatientId = $rx?->patient_id;
        }

        return view('hospital.pharmacy.sale.form', compact(
            'patients',
            'medicines',
            'opdPrescriptions',
            'ipdPrescriptions',
            'initialPrescriptionType',
            'initialPrescriptionId',
            'initialPatientId'
        ));
    }

    public function loadPrescriptionItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prescription_type' => 'required|in:opd,ipd',
            'prescription_id' => 'required|integer',
            'patient_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $type = $request->prescription_type;
        $id = (int) $request->prescription_id;

        if ($this->isPrescriptionAlreadyBilled($type, $id)) {
            return response()->json([
                'status' => false,
                'message' => 'This prescription is already billed in pharmacy sale.',
            ], 422);
        }

        if ($type === 'opd') {
            $prescription = OpdPrescription::query()
                ->where('hospital_id', $this->hospital_id)
                ->where(function ($query) {
                    $query->whereNull('valid_till')->orWhere('valid_till', '>=', now()->toDateString());
                })
                ->with('items.medicine:id,name')
                ->findOrFail($id);
        } else {
            $prescription = IpdPrescription::query()
                ->where('hospital_id', $this->hospital_id)
                ->where(function ($query) {
                    $query->whereNull('valid_till')->orWhere('valid_till', '>=', now()->toDateString());
                })
                ->with('items.medicine:id,name')
                ->findOrFail($id);
        }

        if ($request->filled('patient_id') && (int) $request->patient_id !== (int) $prescription->patient_id) {
            return response()->json([
                'status' => false,
                'message' => 'Selected prescription does not belong to selected patient.',
            ], 422);
        }

        $items = $prescription->items
            ->filter(fn ($item) => !empty($item->medicine_id))
            ->map(function ($item) {
                return [
                    'medicine_id' => (int) $item->medicine_id,
                    'medicine_name' => $item->medicine?->name,
                    'quantity' => max(1, (int) ($item->no_of_day ?? 1)),
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'items' => $items,
            'patient_id' => $prescription->patient_id,
        ]);
    }

    private function formatPrescriptionLabel(string $type, int $id, ?string $prescriptionNo, ?string $patientName, ?string $patientCode, $validTill): string
    {
        $prefix = strtoupper($type) === 'IPD' ? 'IPD-RX' : 'OPD-RX';
        $rxNo = $prescriptionNo ?: ($prefix . '-' . now()->format('ym') . '-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT));
        $patient = $patientName ?: 'Unknown';
        $uhid = $patientCode ?: '-';
        $valid = $validTill ? 'Valid: ' . optional($validTill)->format('d-m-Y') : 'Valid: NA';

        return $rxNo . ' | #' . $id . ' - ' . $patient . ' (' . $uhid . ') | ' . $valid;
    }

    public function medicineBatches(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|integer|exists:medicines,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $medicineId = (int) $request->medicine_id;

        $batches = PharmacyStockBatch::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('medicine_id', $medicineId)
            ->where('status', 'active')
            ->where('available_qty', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->with('purchaseItem:id,tax_percent')
            ->get(['id', 'purchase_item_id', 'batch_no', 'expiry_date', 'available_qty', 'unit_sale_price', 'unit_mrp']);

        return response()->json([
            'status' => true,
            'batches' => $batches->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'expiry_date' => optional($batch->expiry_date)->format('m/y'),
                    'available_qty' => (float) $batch->available_qty,
                    'unit_sale_price' => (float) $batch->unit_sale_price,
                    'unit_mrp' => (float) $batch->unit_mrp,
                    'tax_percent' => (float) ($batch->purchaseItem?->tax_percent ?? 0),
                ];
            })->values(),
        ]);
    }

    public function store(Request $request, PharmacyInventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'bill_date' => 'required|date',
            'patient_id' => 'nullable|exists:patients,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'prescription_type' => 'nullable|in:opd,ipd',
            'prescription_id' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.stock_batch_id' => 'nullable|exists:pharmacy_stock_batches,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.unit_mrp' => 'nullable|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.is_substituted' => 'nullable|boolean',
            'items.*.substitution_note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $subtotal = 0.0;
        $itemDiscountTotal = 0.0;
        $taxTotal = 0.0;

        foreach ((array) $request->items as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $discountPercent = (float) ($item['discount_percent'] ?? 0);
            $taxPercent = (float) ($item['tax_percent'] ?? 0);

            $lineSubtotal = $qty * $unitPrice;
            $lineDiscount = $lineSubtotal * $discountPercent / 100;
            $taxable = max(0, $lineSubtotal - $lineDiscount);
            $lineTax = $taxable * $taxPercent / 100;

            $subtotal += $lineSubtotal;
            $itemDiscountTotal += $lineDiscount;
            $taxTotal += $lineTax;
        }

        $headerDiscount = (float) ($request->discount_amount ?? 0);
        $beforeHeaderDiscount = max(0, $subtotal - $itemDiscountTotal + $taxTotal);
        $netTotal = max(0, $beforeHeaderDiscount - $headerDiscount);
        $paidAmount = (float) ($request->paid_amount ?? 0);

        if ($paidAmount > $netTotal + 0.0001) {
            return response()->json([
                'errors' => [[
                    'code' => 'paid_amount',
                    'message' => 'Paid amount cannot be greater than net total.',
                ]],
            ], 422);
        }

        $prescriptionType = $request->prescription_type;
        $prescriptionId = $request->prescription_id;

        if (!empty($prescriptionType) && !empty($prescriptionId) && $this->isPrescriptionAlreadyBilled($prescriptionType, (int) $prescriptionId)) {
            return response()->json([
                'errors' => [[
                    'code' => 'prescription_id',
                    'message' => 'This prescription is already billed in pharmacy sale.',
                ]],
            ], 422);
        }

        $payload = [
            'hospital_id' => $this->hospital_id,
            'patient_id' => $request->patient_id,
            'bill_date' => $request->bill_date,
            'discount_amount' => $request->discount_amount,
            'paid_amount' => $request->paid_amount,
            'notes' => $request->notes,
            'items' => $request->items,
            'is_from_prescription' => !empty($prescriptionType) && !empty($prescriptionId),
        ];

        if ($prescriptionType === 'opd' && $prescriptionId) {
            $payload['opd_prescription_id'] = (int) $prescriptionId;
            $payload['source_type'] = OpdPrescription::class;
            $payload['source_id'] = (int) $prescriptionId;
        }

        if ($prescriptionType === 'ipd' && $prescriptionId) {
            $payload['ipd_prescription_id'] = (int) $prescriptionId;
            $payload['source_type'] = IpdPrescription::class;
            $payload['source_id'] = (int) $prescriptionId;
        }

        try {
            $bill = $inventoryService->createSaleBill($payload);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Sale bill created successfully.',
            'bill_no' => $bill->bill_no,
        ]);
    }

    public function printBill(PharmacySaleBill $bill)
    {
        if ($bill->hospital_id !== $this->hospital_id) {
            abort(403);
        }

        $bill->load(['items.medicine', 'patient']);
        $hospital = Hospital::query()->find($this->hospital_id);
        $printTemplate = HeaderFooter::query()->where('type', 'pharmacy_bill')->first();

        return view('hospital.pharmacy.sale.print', compact('bill', 'hospital', 'printTemplate'));
    }

    private function isPrescriptionAlreadyBilled(string $type, int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        $query = PharmacySaleBill::query();
        if (strtolower($type) === 'opd') {
            $query->where('opd_prescription_id', $id);
        } elseif (strtolower($type) === 'ipd') {
            $query->where('ipd_prescription_id', $id);
        } else {
            return false;
        }

        return $query->exists();
    }
}
