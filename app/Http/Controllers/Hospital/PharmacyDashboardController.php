<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\IpdPrescription;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\OpdPrescription;
use App\Models\Patient;
use App\Models\PharmacySaleBill;
use App\Models\PharmacySupplier;
use App\Models\PharmacyStockBatch;
use App\Services\PharmacyInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Throwable;

class PharmacyDashboardController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->routes = [
            'dashboardCounts' => route('hospital.pharmacy.dashboard-counts'),
            'dispenseQueueLoad' => route('hospital.pharmacy.dispense-queue-load'),
            'dispensePreview' => route('hospital.pharmacy.dispense.prescription-preview'),
            'dispenseMedicineSearch' => route('hospital.pharmacy.dispense.medicine-search'),
            'dispenseStore' => route('hospital.pharmacy.dispense.store'),
            'statOrdersLoad' => route('hospital.pharmacy.stat-orders-load'),
            'stockLoad' => route('hospital.pharmacy.stock-load'),
            'stockExport' => route('hospital.pharmacy.stock-export'),
            'showBadStockForm' => route('hospital.pharmacy.stock.show-bad-stock-form'),
            'adjustBadStock' => route('hospital.pharmacy.stock.adjust-bad-stock'),
            'quarantineStockLoad' => route('hospital.pharmacy.quarantine-stock-load'),
            'quarantineStockExport' => route('hospital.pharmacy.quarantine-stock-export'),
            'showBadQuarantineStockForm' => route('hospital.pharmacy.quarantine-stock.show-bad-stock-form'),
            'adjustBadQuarantineStock' => route('hospital.pharmacy.quarantine-stock.adjust-bad-stock'),
            'expiryLoad' => route('hospital.pharmacy.expiry-load'),
            'expiryProcess' => route('hospital.pharmacy.expiry.process'),
            'expiryQuarantine' => route('hospital.pharmacy.expiry.quarantine', ['batch' => '__ID__']),
            'purchaseLoad' => route('hospital.pharmacy.purchase-load'),
            'purchaseStore' => route('hospital.pharmacy.purchase.store'),
            'purchaseShowform' => route('hospital.pharmacy.purchase.showform'),
            'purchaseApprove' => route('hospital.pharmacy.purchase.approve', ['bill' => '__ID__']),
            'purchaseReject' => route('hospital.pharmacy.purchase.reject', ['bill' => '__ID__']),
            'purchasePrint' => route('hospital.pharmacy.purchase.print', ['bill' => '__ID__']),
            'grnStore' => route('hospital.pharmacy.grn.store'),
            'grnLoad' => route('hospital.pharmacy.grn-load'),
            'grnApprovedPOs' => route('hospital.pharmacy.grn.approved-pos'),
            'grnView' => route('hospital.pharmacy.grn.view', ['grn' => '__ID__']),
            'grnPrint' => route('hospital.pharmacy.grn.print', ['grn' => '__ID__']),
        ];
    }

    public function index()
    {
        $medicineCategories = MedicineCategory::query()->select('id', 'name')->orderBy('name')->get();
        $medicines = Medicine::query()->select('id', 'name', 'unit')->orderBy('name')->get();
        $suppliers = PharmacySupplier::query()->select('id', 'name', 'phone')->orderBy('name')->get();

        return view('hospital.pharmacy.dashboard', [
            'pathurl' => 'pharmacy-dashboard',
            'routes'  => $this->routes,
            'medicineCategories' => $medicineCategories,
            'medicines' => $medicines,
            'suppliers' => $suppliers,
        ]);
    }

    public function counts()
    {
        $today = now()->toDateString();

        $pendingOpdCount = $this->pendingOpdPrescriptionQuery($today)->count('rx.id');
        $pendingIpdCount = $this->pendingIpdPrescriptionQuery($today)->count('rx.id');
        $statOrdersCount = $this->pendingIpdPrescriptionQuery($today)
            ->where('rx.dispense_type', 'Emergency')
            ->count('rx.id');

        $expiryAlertsCount = PharmacyStockBatch::query()
            ->whereNotNull('expiry_date')
            ->where('available_qty', '>', 0)
            ->where(function ($q) {
                $q->whereDate('expiry_date', '<', now()->toDateString())
                    ->orWhereBetween('expiry_date', [now()->toDateString(), now()->addDays(90)->toDateString()]);
            })
            ->count();

        $lowStockSubQuery = DB::table('medicines as m')
            ->leftJoin('pharmacy_stock_batches as psb', function ($join) {
                $join->on('psb.medicine_id', '=', 'm.id')
                    ->where('psb.hospital_id', '=', $this->hospital_id)
                    ->where('psb.status', '=', 'active')
                    ->where('psb.available_qty', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('psb.expiry_date')
                            ->orWhereDate('psb.expiry_date', '>=', now()->toDateString());
                    });
            })
            ->where('m.hospital_id', $this->hospital_id)
            ->where('m.reorder_level', '>', 0)
            ->select('m.id')
            ->groupBy('m.id', 'm.reorder_level')
            ->havingRaw('COALESCE(SUM(psb.available_qty), 0) <= m.reorder_level');

        $lowStockItemsCount = DB::query()
            ->fromSub($lowStockSubQuery, 'low_stock_items')
            ->count();

        $drugItemsCount = PharmacyStockBatch::query()
            ->where('status', 'active')
            ->where('available_qty', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now()->toDateString());
            })
            ->distinct('medicine_id')
            ->count('medicine_id');

        return response()->json([
            'queue_pending' => (int) ($pendingOpdCount + $pendingIpdCount),
            'stat_orders' => (int) $statOrdersCount,
            'expiry_alerts' => (int) $expiryAlertsCount,
            'low_stock_items' => (int) $lowStockItemsCount,
            'drug_items' => (int) $drugItemsCount,
        ]);
    }

    public function loadDispenseQueue(Request $request)
    {
        $queueType = strtolower((string) $request->input('queue_type', 'all'));
        $today = now()->toDateString();

        $opdBase = DB::table('opd_prescriptions as rx')
            ->leftJoin('patients as p', 'p.id', '=', 'rx.patient_id')
            ->leftJoin('opd_patients as op', 'op.id', '=', 'rx.opd_patient_id')
            ->leftJoin('staff as d', 'd.id', '=', 'rx.doctor_id')
            ->leftJoin('opd_prescription_items as ri', 'ri.opd_prescription_id', '=', 'rx.id')
            ->leftJoin('medicines as m', 'm.id', '=', 'ri.medicine_id')
            ->leftJoin('pharmacy_sale_bills as sb', 'sb.opd_prescription_id', '=', 'rx.id')
            ->where('rx.hospital_id', $this->hospital_id)
            ->whereNull('sb.id')
            ->where(function ($q) use ($today) {
                $q->whereNull('rx.valid_till')->orWhereDate('rx.valid_till', '>=', $today);
            });

        if ($queueType === 'emergency') {
            $opdBase->whereRaw("LOWER(COALESCE(op.visit_type, '')) = 'emergency'");
        }

        $opdQuery = $opdBase
            ->selectRaw("CONCAT('opd-', rx.id) as row_id")
            ->selectRaw("'opd' as source_type")
            ->selectRaw('rx.id as source_id')
            ->selectRaw("COALESCE(rx.prescription_no, CONCAT('OPD-RX-', DATE_FORMAT(rx.created_at, '%y%m'), '-', LPAD(rx.id, 5, '0'))) as rx_no")
            ->selectRaw("COALESCE(p.name, '-') as patient_name")
            ->selectRaw("'OPD' as patient_type")
            ->selectRaw("COALESCE(op.visit_type, 'OPD') as ward_type")
            ->selectRaw("TRIM(CONCAT(COALESCE(d.first_name, ''), ' ', COALESCE(d.last_name, ''))) as doctor_name")
            ->selectRaw("COALESCE(GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ', '), '-') as drugs")
            ->selectRaw("COALESCE(rx.dispense_type, 'Normal') as priority")
            ->selectRaw("DATE_FORMAT(rx.created_at, '%H:%i') as queue_time")
            ->selectRaw("'pending' as status")
            ->selectRaw('rx.created_at as created_at')
            ->groupBy('rx.id', 'rx.prescription_no', 'rx.created_at', 'p.name', 'op.visit_type', 'd.first_name', 'd.last_name');

        $ipdQuery = DB::table('ipd_prescriptions as rx')
            ->leftJoin('patients as p', 'p.id', '=', 'rx.patient_id')
            ->leftJoin('staff as d', 'd.id', '=', 'rx.doctor_id')
            ->leftJoin('ipd_prescription_items as ri', 'ri.ipd_prescription_id', '=', 'rx.id')
            ->leftJoin('medicines as m', 'm.id', '=', 'ri.medicine_id')
            ->leftJoin('pharmacy_sale_bills as sb', 'sb.ipd_prescription_id', '=', 'rx.id')
            ->where('rx.hospital_id', $this->hospital_id)
            ->whereNull('sb.id')
            ->where(function ($q) use ($today) {
                $q->whereNull('rx.valid_till')->orWhereDate('rx.valid_till', '>=', $today);
            })
            ->selectRaw("CONCAT('ipd-', rx.id) as row_id")
            ->selectRaw("'ipd' as source_type")
            ->selectRaw('rx.id as source_id')
            ->selectRaw("COALESCE(rx.prescription_no, CONCAT('IPD-RX-', DATE_FORMAT(rx.created_at, '%y%m'), '-', LPAD(rx.id, 5, '0'))) as rx_no")
            ->selectRaw("COALESCE(p.name, '-') as patient_name")
            ->selectRaw("'IPD' as patient_type")
            ->selectRaw("'IPD' as ward_type")
            ->selectRaw("TRIM(CONCAT(COALESCE(d.first_name, ''), ' ', COALESCE(d.last_name, ''))) as doctor_name")
            ->selectRaw("COALESCE(GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ', '), '-') as drugs")
            ->selectRaw("COALESCE(rx.dispense_type, 'Normal') as priority")
            ->selectRaw("DATE_FORMAT(rx.created_at, '%H:%i') as queue_time")
            ->selectRaw("'pending' as status")
            ->selectRaw('rx.created_at as created_at')
            ->groupBy('rx.id', 'rx.prescription_no', 'rx.created_at', 'p.name', 'd.first_name', 'd.last_name');

        if ($queueType === 'opd') {
            $finalQuery = DB::query()->fromSub($opdQuery, 'dispense_queue');
        } elseif ($queueType === 'ipd') {
            $finalQuery = DB::query()->fromSub($ipdQuery, 'dispense_queue');
        } elseif ($queueType === 'emergency') {
            $finalQuery = DB::query()->fromSub($opdQuery, 'dispense_queue');
        } else {
            $finalQuery = DB::query()->fromSub($opdQuery->unionAll($ipdQuery), 'dispense_queue');
        }

        $finalQuery->orderByDesc('created_at');

        return DataTables::of($finalQuery)->make(true);
    }

    public function loadStatOrders(Request $request)
    {
        $today = now()->toDateString();

        $orders = DB::table('ipd_prescriptions as rx')
            ->leftJoin('patients as p', 'p.id', '=', 'rx.patient_id')
            ->leftJoin('staff as d', 'd.id', '=', 'rx.doctor_id')
            ->leftJoin('bed_allocations as ba', 'ba.id', '=', 'rx.bed_allocation_id')
            ->leftJoin('beds as b', 'b.id', '=', 'ba.bed_id')
            ->leftJoin('rooms as r', 'r.id', '=', 'b.room_id')
            ->leftJoin('wards as w', 'w.id', '=', 'r.ward_id')
            ->leftJoin('pharmacy_sale_bills as sb', 'sb.ipd_prescription_id', '=', 'rx.id')
            ->leftJoin('ipd_prescription_items as ri', 'ri.ipd_prescription_id', '=', 'rx.id')
            ->leftJoin('medicines as m', 'm.id', '=', 'ri.medicine_id')
            ->where('rx.hospital_id', $this->hospital_id)
            ->whereNull('sb.id')
            ->where('rx.dispense_type', 'Emergency')
            ->where(function ($q) use ($today) {
                $q->whereNull('rx.valid_till')->orWhereDate('rx.valid_till', '>=', $today);
            })
            ->selectRaw("rx.id as prescription_id")
            ->selectRaw("COALESCE(rx.prescription_no, CONCAT('IPD-RX-', DATE_FORMAT(rx.created_at, '%y%m'), '-', LPAD(rx.id, 5, '0'))) as rx_no")
            ->selectRaw("COALESCE(p.name, '-') as patient_name")
            ->selectRaw("COALESCE(w.ward_name, '') as ward_name")
            ->selectRaw("COALESCE(b.bed_number, '') as bed_number")
            ->selectRaw("TRIM(CONCAT(COALESCE(d.first_name, ''), ' ', COALESCE(d.last_name, ''))) as doctor_name")
            ->selectRaw("COALESCE(GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ', '), '-') as drugs")
            ->selectRaw("DATE_FORMAT(rx.created_at, '%H:%i') as queue_time")
            ->selectRaw('rx.created_at as created_at')
            ->groupBy('rx.id', 'rx.prescription_no', 'rx.created_at', 'p.name', 'w.ward_name', 'b.bed_number', 'd.first_name', 'd.last_name')
            ->orderByDesc('rx.created_at')
            ->get();

        $payload = $orders->map(function ($row) {
            $elapsedMinutes = Carbon::parse($row->created_at)->diffInMinutes(now());
            $locationParts = array_filter([
                trim($row->ward_name),
                $row->bed_number ? 'Bed ' . $row->bed_number : null,
            ]);

            return [
                'rx' => $row->rx_no,
                'patient' => trim($row->patient_name . ($locationParts ? ' - ' . implode(' ', $locationParts) : '')),
                'drug' => $row->drugs,
                'time' => $row->queue_time,
                'elapsed' => intval($elapsedMinutes) . ' min',
                'doctor' => $row->doctor_name ?: '-',
            ];
        });

        return response()->json($payload);
    }

    public function prescriptionPreview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prescription_type' => 'required|in:opd,ipd',
            'prescription_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $type = strtolower((string) $request->input('prescription_type'));
        $id = (int) $request->input('prescription_id');

        if ($this->isPrescriptionAlreadyBilled($type, $id)) {
            return response()->json([
                'status' => false,
                'message' => 'This prescription is already billed in pharmacy sale.',
            ], 422);
        }

        $with = [
            'patient:id,patient_id,mrn,name,gender,age_years,age_months,blood_group,known_allergies',
            'doctor:id,first_name,last_name',
            'items.medicine:id,name,unit',
            'items.dosage:id,dosage',
            'items.frequency:id,frequency',
            'items.route:id,route',
            'items.instruction:id,instruction',
        ];

        $prescription = $type === 'opd'
            ? OpdPrescription::query()->where('hospital_id', $this->hospital_id)->where(function ($query) {
                $query->whereNull('valid_till')->orWhereDate('valid_till', '>=', now()->toDateString());
            })->with($with)->findOrFail($id)
            : IpdPrescription::query()->where('hospital_id', $this->hospital_id)->where(function ($query) {
                $query->whereNull('valid_till')->orWhereDate('valid_till', '>=', now()->toDateString());
            })->with($with)->findOrFail($id);

        $items = $prescription->items
            ->filter(fn ($item) => !empty($item->medicine_id))
            ->values()
            ->map(function ($item) {
                $batches = $this->availableBatchesForMedicine((int) $item->medicine_id);
                $availableQty = (float) $batches->sum('available_qty');
                $prescribedQty = max(1, (float) ($item->no_of_day ?? 1));
                $firstBatch = $batches->first();

                return [
                    'medicine_id' => (int) $item->medicine_id,
                    'medicine_name' => $item->medicine?->name ?? '-',
                    'unit' => $item->medicine?->unit,
                    'dosage' => $item->dosage?->dosage ?? '-',
                    'frequency' => $item->frequency?->frequency ?? '-',
                    'route' => $item->route?->route ?? '-',
                    'instruction' => $item->instruction?->instruction ?? '-',
                    'days' => (int) ($item->no_of_day ?? 1),
                    'prescribed_qty' => $prescribedQty,
                    'available_qty' => $availableQty,
                    'dispense_qty' => min($prescribedQty, $availableQty),
                    'stock_status' => $availableQty <= 0 ? 'out' : ($availableQty < $prescribedQty ? 'partial' : 'available'),
                    'batch_id' => $firstBatch?->id,
                    'unit_price' => (float) ($firstBatch?->unit_sale_price ?? 0),
                    'unit_mrp' => (float) ($firstBatch?->unit_mrp ?? 0),
                    'tax_percent' => (float) ($firstBatch?->purchaseItem?->tax_percent ?? 0),
                    'batches' => $batches->map(fn ($batch) => [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => optional($batch->expiry_date)->format('m/y'),
                        'available_qty' => (float) $batch->available_qty,
                        'unit_sale_price' => (float) $batch->unit_sale_price,
                        'unit_mrp' => (float) $batch->unit_mrp,
                        'tax_percent' => (float) ($batch->purchaseItem?->tax_percent ?? 0),
                    ])->values(),
                ];
            });

        $patient = $prescription->patient;
        $doctorName = trim(($prescription->doctor?->first_name ?? '') . ' ' . ($prescription->doctor?->last_name ?? ''));
        $rxNo = $prescription->prescription_no ?: (strtoupper($type) . '-RX-' . optional($prescription->created_at)->format('ym') . '-' . str_pad((string) $prescription->id, 5, '0', STR_PAD_LEFT));

        return response()->json([
            'status' => true,
            'prescription' => [
                'type' => $type,
                'id' => $prescription->id,
                'rx_no' => $rxNo,
                'valid_till' => optional($prescription->valid_till)->format('d-m-Y'),
                'doctor_name' => $doctorName ?: '-',
                'created_at' => optional($prescription->created_at)->format('d-m-Y H:i'),
            ],
            'patient' => [
                'id' => $patient?->id,
                'name' => $patient?->name ?? '-',
                'mrn' => $patient?->mrn ?: ($patient?->patient_id ?? '-'),
                'gender' => $patient?->gender ?? '-',
                'age' => trim(($patient?->age_years ? $patient->age_years . 'Y' : '') . ($patient?->age_months ? ' ' . $patient->age_months . 'M' : '')) ?: '-',
                'blood_group' => $patient?->blood_group ?? '-',
                'known_allergies' => $patient?->known_allergies,
            ],
            'items' => $items,
        ]);
    }

    public function medicineSearch(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        $medicines = Medicine::query()
            ->select('id', 'name', 'unit')
            ->when($term !== '', fn ($q) => $q->where('name', 'like', '%' . $term . '%'))
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->map(function ($medicine) {
                $batches = $this->availableBatchesForMedicine((int) $medicine->id);
                $firstBatch = $batches->first();

                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'unit' => $medicine->unit,
                    'available_qty' => (float) $batches->sum('available_qty'),
                    'batch_id' => $firstBatch?->id,
                    'unit_price' => (float) ($firstBatch?->unit_sale_price ?? 0),
                    'unit_mrp' => (float) ($firstBatch?->unit_mrp ?? 0),
                    'tax_percent' => (float) ($firstBatch?->purchaseItem?->tax_percent ?? 0),
                    'batches' => $batches->map(fn ($batch) => [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => optional($batch->expiry_date)->format('m/y'),
                        'available_qty' => (float) $batch->available_qty,
                        'unit_sale_price' => (float) $batch->unit_sale_price,
                        'unit_mrp' => (float) $batch->unit_mrp,
                        'tax_percent' => (float) ($batch->purchaseItem?->tax_percent ?? 0),
                    ])->values(),
                ];
            });

        return response()->json(['status' => true, 'items' => $medicines]);
    }

    public function storeDispense(Request $request, PharmacyInventoryService $inventoryService)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'nullable|exists:patients,id',
            'prescription_type' => 'nullable|in:opd,ipd',
            'prescription_id' => 'nullable|integer',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
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

        $prescriptionType = $request->input('prescription_type');
        $prescriptionId = (int) $request->input('prescription_id');

        if ($prescriptionType && $prescriptionId && $this->isPrescriptionAlreadyBilled($prescriptionType, $prescriptionId)) {
            return response()->json([
                'errors' => [[
                    'code' => 'prescription_id',
                    'message' => 'This prescription is already billed in pharmacy sale.',
                ]],
            ], 422);
        }

        $sourcePrescription = null;
        if ($prescriptionType === 'opd' && $prescriptionId) {
            $sourcePrescription = OpdPrescription::query()
                ->where('hospital_id', $this->hospital_id)
                ->where(function ($query) {
                    $query->whereNull('valid_till')->orWhereDate('valid_till', '>=', now()->toDateString());
                })
                ->findOrFail($prescriptionId);
        }

        if ($prescriptionType === 'ipd' && $prescriptionId) {
            $sourcePrescription = IpdPrescription::query()
                ->where('hospital_id', $this->hospital_id)
                ->where(function ($query) {
                    $query->whereNull('valid_till')->orWhereDate('valid_till', '>=', now()->toDateString());
                })
                ->findOrFail($prescriptionId);
        }

        $payload = [
            'hospital_id' => $this->hospital_id,
            'patient_id' => $sourcePrescription?->patient_id ?: ($request->input('patient_id') ?: null),
            'bill_date' => now()->toDateString(),
            'discount_amount' => (float) $request->input('discount_amount', 0),
            'paid_amount' => (float) $request->input('paid_amount', 0),
            'notes' => $request->input('notes'),
            'items' => collect($request->input('items', []))->filter(fn ($item) => (float) ($item['quantity'] ?? 0) > 0)->values()->all(),
            'is_from_prescription' => !empty($prescriptionType) && !empty($prescriptionId),
        ];

        if (empty($payload['items'])) {
            return response()->json(['status' => false, 'message' => 'Please enter at least one dispense quantity.'], 422);
        }

        $payable = collect($payload['items'])->reduce(function ($total, $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $discountPercent = (float) ($item['discount_percent'] ?? 0);
            $taxPercent = (float) ($item['tax_percent'] ?? 0);
            $lineSubtotal = $qty * $price;
            $lineDiscount = $lineSubtotal * $discountPercent / 100;
            $taxable = max(0, $lineSubtotal - $lineDiscount);

            return $total + $taxable + ($taxable * $taxPercent / 100);
        }, 0.0);
        $payable = max(0, $payable - (float) $payload['discount_amount']);

        if ((float) $payload['paid_amount'] > $payable + 0.0001) {
            return response()->json([
                'errors' => [[
                    'code' => 'paid_amount',
                    'message' => 'Paid amount cannot be greater than net payable.',
                ]],
            ], 422);
        }

        if ($prescriptionType === 'opd' && $prescriptionId) {
            $payload['opd_prescription_id'] = $prescriptionId;
            $payload['source_type'] = OpdPrescription::class;
            $payload['source_id'] = $prescriptionId;
        }

        if ($prescriptionType === 'ipd' && $prescriptionId) {
            $payload['ipd_prescription_id'] = $prescriptionId;
            $payload['source_type'] = IpdPrescription::class;
            $payload['source_id'] = $prescriptionId;
        }

        try {
            $bill = $inventoryService->createSaleBill($payload);
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Medicine dispensed and bill created successfully.',
            'bill_id' => $bill->id,
            'bill_no' => $bill->bill_no,
            'print_url' => route('hospital.pharmacy.sale.print', ['bill' => $bill->id]),
        ]);
    }

    private function pendingOpdPrescriptionQuery(string $today)
    {
        return DB::table('opd_prescriptions as rx')
            ->leftJoin('pharmacy_sale_bills as sb', 'sb.opd_prescription_id', '=', 'rx.id')
            ->where('rx.hospital_id', $this->hospital_id)
            ->whereNull('sb.id')
            ->where(function ($q) use ($today) {
                $q->whereNull('rx.valid_till')->orWhereDate('rx.valid_till', '>=', $today);
            });
    }

    private function pendingIpdPrescriptionQuery(string $today)
    {
        return DB::table('ipd_prescriptions as rx')
            ->leftJoin('pharmacy_sale_bills as sb', 'sb.ipd_prescription_id', '=', 'rx.id')
            ->where('rx.hospital_id', $this->hospital_id)
            ->whereNull('sb.id')
            ->where(function ($q) use ($today) {
                $q->whereNull('rx.valid_till')->orWhereDate('rx.valid_till', '>=', $today);
            });
    }

    private function availableBatchesForMedicine(int $medicineId)
    {
        return PharmacyStockBatch::query()
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
