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
            'purchaseUpdate' => route('hospital.pharmacy.purchase.update', ['bill' => '__ID__']),
            'purchaseDetails' => route('hospital.pharmacy.purchase.details', ['bill' => '__ID__']),
            'purchaseDelete' => route('hospital.pharmacy.purchase.delete', ['bill' => '__ID__']),
            'grnStore' => route('hospital.pharmacy.grn.store'),
            'grnLoad' => route('hospital.pharmacy.grn-load'),
            'grnApprovedPOs' => route('hospital.pharmacy.grn.approved-pos'),
            'grnView' => route('hospital.pharmacy.grn.view', ['grn' => '__ID__']),
            'grnPrint' => route('hospital.pharmacy.grn.print', ['grn' => '__ID__']),
            'allBillsLoad' => route('hospital.pharmacy.bills-load'),
            'billView' => route('hospital.pharmacy.bill.view', ['bill' => '__ID__']),
            'billPrint' => route('hospital.pharmacy.sale.print', ['bill' => '__ID__']),
            'patientSearch' => route('hospital.pharmacy.dispense.patient-search'),
            'prescriptionSearch' => route('hospital.pharmacy.dispense.prescription-search'),
        ];
    }

    public function index()
    {
        $medicineCategories = MedicineCategory::query()->select('id', 'name')->orderBy('name')->get();
        $medicines = Medicine::query()->select('id', 'name', 'medicine_unit_id')->orderBy('name')->get();
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

        $todaySales = PharmacySaleBill::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('bill_date', $today)
            ->sum('net_total');

        $todayDispensed = PharmacySaleBill::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('bill_date', $today)
            ->count();

        $yesterday = Carbon::yesterday()->toDateString();

        $yesterdaySales = PharmacySaleBill::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('bill_date', $yesterday)
            ->sum('net_total');

        $yesterdayDispensed = PharmacySaleBill::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('bill_date', $yesterday)
            ->count();

        $dispensedChange = 0.0;
        if ($yesterdayDispensed > 0) {
            $dispensedChange = (($todayDispensed - $yesterdayDispensed) / $yesterdayDispensed) * 100;
        } elseif ($todayDispensed > 0) {
            $dispensedChange = 100.0;
        }

        $salesChange = 0.0;
        if ($yesterdaySales > 0) {
            $salesChange = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100;
        } elseif ($todaySales > 0) {
            $salesChange = 100.0;
        }

        return response()->json([
            'queue_pending' => (int) ($pendingOpdCount + $pendingIpdCount),
            'stat_orders' => (int) $statOrdersCount,
            'expiry_alerts' => (int) $expiryAlertsCount,
            'low_stock_items' => (int) $lowStockItemsCount,
            'drug_items' => (int) $drugItemsCount,
            'today_dispensed' => (int) $todayDispensed,
            'today_sales' => (float) $todaySales,
            'dispensed_change' => round($dispensedChange, 1),
            'sales_change' => round($salesChange, 1),
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
                'prescription_id' => $row->prescription_id,
                'source_type' => 'ipd',
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
            // For partial re-dispense, we still load the prescription but mark already dispensed items
            $allowPartialRedispense = true;
        } else {
            $allowPartialRedispense = false;
        }

        $with = [
            'patient:id,patient_id,mrn,name,gender,age_years,age_months,blood_group,known_allergies',
            'doctor:id,first_name,last_name',
            'items.medicine:id,name,medicine_unit_id',
            'items.dosage:id,dosage',
            'items.frequency:id,frequency,no_of_medicine',
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

        $dispensedQtys = $this->getAlreadyDispensedQty($type, $id);

        $items = $prescription->items
            ->filter(fn ($item) => !empty($item->medicine_id))
            ->values()
            ->map(function ($item) use ($dispensedQtys) {
                $batches = $this->availableBatchesForMedicine((int) $item->medicine_id);
                $availableQty = (float) $batches->sum('available_qty');
                $prescribedQty = max(1, (float) ($item->no_of_day ?? 1));
                $frequencyQty = max(1, (float) ($item->frequency?->no_of_medicine ?? 1));
                $totalPrescribed = $prescribedQty * $frequencyQty;
                $alreadyDispensed = (float) ($dispensedQtys[(int) $item->medicine_id] ?? 0);
                $remainingQty = max(0.0, $totalPrescribed - $alreadyDispensed);
                $firstBatch = $batches->first();

                return [
                    'medicine_id' => (int) $item->medicine_id,
                    'medicine_name' => $item->medicine?->name ?? '-',
                    'unit' => $item->medicine?->medicine_unit_id ? optional($item->medicine->unit)->name : '-',
                    'dosage' => $item->dosage?->dosage ?? '-',
                    'frequency' => $item->frequency?->frequency ?? '-',
                    'route' => $item->route?->route ?? '-',
                    'instruction' => $item->instruction?->instruction ?? '-',
                    'days' => (int) ($item->no_of_day ?? 1),
                    'prescribed_qty' => $totalPrescribed,
                    'available_qty' => $availableQty,
                    'dispense_qty' => min($remainingQty, $availableQty),
                    'stock_status' => $availableQty <= 0 ? 'out' : ($availableQty < $remainingQty ? 'partial' : 'available'),
                    'batch_id' => $firstBatch?->id,
                    'unit_price' => (float) ($firstBatch?->unit_sale_price ?? 0),
                    'unit_mrp' => (float) ($firstBatch?->unit_mrp ?? 0),
                    'tax_percent' => (float) ($firstBatch?->tax_percent ?? $firstBatch?->purchaseItem?->tax_percent ?? 0),
                    'sale_tax_type' => $firstBatch?->sale_tax_type ?? 'exclusive',
                    'pack_size' => (int) ($firstBatch?->pack_size ?? 1),
                    'already_dispensed_qty' => $alreadyDispensed,
                    'batches' => $batches->map(fn ($batch) => [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => optional($batch->expiry_date)->format('m/y'),
                        'available_qty' => (float) $batch->available_qty,
                        'unit_sale_price' => (float) $batch->unit_sale_price,
                        'unit_mrp' => (float) $batch->unit_mrp,
                        'tax_percent' => (float) ($batch->tax_percent ?? $batch->purchaseItem?->tax_percent ?? 0),
                        'sale_tax_type' => $batch->sale_tax_type ?? 'exclusive',
                        'pack_size' => (int) ($batch->pack_size ?? 1),
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
            'is_partial_redispense' => $allowPartialRedispense ?? false,
        ]);
    }

    public function medicineSearch(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        $medicines = Medicine::query()
            ->select('id', 'name', 'medicine_unit_id')
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
                    'unit' => $medicine?->unit?->name ?? '-',
                    'available_qty' => (float) $batches->sum('available_qty'),
                    'batch_id' => $firstBatch?->id,
                    'unit_price' => (float) ($firstBatch?->unit_sale_price ?? 0),
                    'unit_mrp' => (float) ($firstBatch?->unit_mrp ?? 0),
                    'tax_percent' => (float) ($firstBatch?->tax_percent ?? $firstBatch?->purchaseItem?->tax_percent ?? 0),
                    'sale_tax_type' => $firstBatch?->sale_tax_type ?? 'exclusive',
                    'pack_size' => (int) ($firstBatch?->pack_size ?? 1),
                    'batches' => $batches->map(fn ($batch) => [
                        'id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => optional($batch->expiry_date)->format('m/y'),
                        'available_qty' => (float) $batch->available_qty,
                        'unit_sale_price' => (float) $batch->unit_sale_price,
                        'unit_mrp' => (float) $batch->unit_mrp,
                        'tax_percent' => (float) ($batch->tax_percent ?? $batch->purchaseItem?->tax_percent ?? 0),
                        'sale_tax_type' => $batch->sale_tax_type ?? 'exclusive',
                        'pack_size' => (int) ($batch->pack_size ?? 1),
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
            // Allow partial re-dispense — don't block, just allow creating another bill for same prescription
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
            ->get([
                'id', 'purchase_item_id', 'batch_no', 'expiry_date', 'available_qty',
                'unit_sale_price', 'unit_mrp', 'pack_size', 'pack_qty', 'pack_mrp',
                'pack_purchase_price', 'pack_sale_price', 'purchase_tax_type', 'sale_tax_type',
                'tax_percent', 'cgst_percent', 'sgst_percent', 'igst_percent', 'gst_type'
            ]);
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

    /**
     * Get already dispensed qty per medicine for a given prescription.
     */
    private function getAlreadyDispensedQty(string $type, int $id): array
    {
        $column = strtolower($type) === 'opd' ? 'opd_prescription_id' : 'ipd_prescription_id';

        return DB::table('pharmacy_sale_bills as sb')
            ->join('pharmacy_sale_items as si', 'si.sale_bill_id', '=', 'sb.id')
            ->where('sb.' . $column, $id)
            ->where('sb.hospital_id', $this->hospital_id)
            ->groupBy('si.medicine_id')
            ->selectRaw('si.medicine_id, SUM(si.quantity) as total_dispensed')
            ->pluck('total_dispensed', 'medicine_id')
            ->toArray();
    }

    /**
     * Load all bills for All Bills tab DataTable.
     */
    public function loadAllBills(Request $request)
    {
        $query = PharmacySaleBill::query()
            ->with('patient:id,name,patient_id')
            ->withCount('items');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('bill_date', [$request->input('start_date'), $request->input('end_date')]);
        }

        $query->latest('id');

        return DataTables::of($query)
            ->filter(function ($q) use ($request) {
                $search = $request->input('search.value');
                if (!empty($search)) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('bill_no', 'like', '%' . $search . '%')
                            ->orWhereHas('patient', function ($pq) use ($search) {
                                $pq->where('name', 'like', '%' . $search . '%')
                                  ->orWhere('patient_id', 'like', '%' . $search . '%');
                            });
                    });
                }
            })
            ->addColumn('patient_name', fn ($row) => $row->patient?->name ?? 'Walk-in')
            ->addColumn('patient_uhid', fn ($row) => $row->patient?->patient_id ?? '-')
            ->editColumn('bill_date', fn ($row) => optional($row->bill_date)->format('d-m-Y'))
            ->addColumn('items_count', fn ($row) => $row->items_count)
            ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
            ->editColumn('discount_amount', fn ($row) => number_format((float) $row->discount_amount, 2))
            ->editColumn('net_total', fn ($row) => number_format((float) $row->net_total, 2))
            ->editColumn('paid_amount', fn ($row) => number_format((float) $row->paid_amount, 2))
            ->editColumn('due_amount', fn ($row) => number_format((float) $row->due_amount, 2))
            ->addColumn('actions', fn ($row) => $row->id)
            ->make(true);
    }

    /**
     * View a specific bill for the view modal.
     */
    public function viewBill(PharmacySaleBill $bill)
    {
        if ($bill->hospital_id !== $this->hospital_id) {
            abort(403);
        }

        $bill->load(['items.medicine:id,name', 'patient:id,name,patient_id,phone,gender,age_years']);

        $items = $bill->items->map(function ($item) {
            $qty = (float) $item->quantity;
            $lineTotal = (float) $item->line_total;
            $discAmt = (float) ($item->discount_amount ?? 0);
            $inclusiveRate = $qty > 0 ? round(($lineTotal + $discAmt) / $qty, 2) : 0.0;

            return [
                'medicine_name' => $item->medicine?->name ?? '-',
                'batch_no' => $item->batch_no ?: '-',
                'expiry_date' => optional($item->expiry_date)->format('m/y'),
                'quantity' => $qty,
                'unit_price' => $inclusiveRate,
                'line_total' => $lineTotal,
            ];
        });

        return response()->json([
            'status' => true,
            'bill' => [
                'id' => $bill->id,
                'bill_no' => $bill->bill_no,
                'bill_date' => optional($bill->bill_date)->format('d-m-Y'),
                'patient_name' => $bill->patient?->name ?? 'Walk-in',
                'patient_uhid' => $bill->patient?->patient_id ?? '-',
                'patient_phone' => $bill->patient?->phone ?? '-',
                'subtotal' => (float) $bill->subtotal,
                'discount_amount' => (float) $bill->discount_amount,
                'net_total' => (float) $bill->net_total,
                'paid_amount' => (float) $bill->paid_amount,
                'due_amount' => (float) $bill->due_amount,
                'notes' => $bill->notes ?: '-',
                'print_url' => route('hospital.pharmacy.sale.print', ['bill' => $bill->id]),
            ],
            'items' => $items,
        ]);
    }

    /**
     * Search patients for walk-in dispense.
     */
    public function patientSearch(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        if (strlen($term) < 2) {
            return response()->json(['items' => []]);
        }

        $patients = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('patient_id', 'like', '%' . $term . '%')
                    ->orWhere('phone', 'like', '%' . $term . '%');
            })
            ->select('id', 'name', 'patient_id', 'phone', 'gender', 'age_years', 'age_months', 'blood_group', 'known_allergies')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'uhid' => $p->patient_id ?? '-',
                    'phone' => $p->phone ?? '-',
                    'gender' => $p->gender ?? '-',
                    'age' => trim(($p->age_years ? $p->age_years . 'Y' : '') . ($p->age_months ? ' ' . $p->age_months . 'M' : '')) ?: '-',
                    'blood_group' => $p->blood_group ?? '-',
                    'known_allergies' => $p->known_allergies,
                ];
            });

        return response()->json(['items' => $patients]);
    }

    /**
     * Search prescriptions for re-dispensing.
     */
    public function prescriptionSearchSuggestions(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        if (strlen($term) < 2) {
            return response()->json(['items' => []]);
        }

        $opdResults = DB::table('opd_prescriptions as rx')
            ->leftJoin('patients as p', 'p.id', '=', 'rx.patient_id')
            ->leftJoin('staff as d', 'd.id', '=', 'rx.doctor_id')
            ->where('rx.hospital_id', $this->hospital_id)
            ->where(function ($q) use ($term) {
                $q->where('rx.prescription_no', 'like', '%' . $term . '%')
                    ->orWhere('p.name', 'like', '%' . $term . '%')
                    ->orWhere('p.patient_id', 'like', '%' . $term . '%');
            })
            ->selectRaw("'opd' as source_type, rx.id as source_id")
            ->selectRaw("COALESCE(rx.prescription_no, CONCAT('OPD-RX-', LPAD(rx.id, 5, '0'))) as rx_no")
            ->selectRaw("COALESCE(p.name, '-') as patient_name")
            ->selectRaw("COALESCE(p.patient_id, '-') as patient_uhid")
            ->selectRaw("TRIM(CONCAT(COALESCE(d.first_name, ''), ' ', COALESCE(d.last_name, ''))) as doctor_name")
            ->selectRaw("DATE_FORMAT(rx.created_at, '%d-%m-%Y') as rx_date")
            ->orderByDesc('rx.created_at')
            ->limit(15)
            ->get();

        $ipdResults = DB::table('ipd_prescriptions as rx')
            ->leftJoin('patients as p', 'p.id', '=', 'rx.patient_id')
            ->leftJoin('staff as d', 'd.id', '=', 'rx.doctor_id')
            ->where('rx.hospital_id', $this->hospital_id)
            ->where(function ($q) use ($term) {
                $q->where('rx.prescription_no', 'like', '%' . $term . '%')
                    ->orWhere('p.name', 'like', '%' . $term . '%')
                    ->orWhere('p.patient_id', 'like', '%' . $term . '%');
            })
            ->selectRaw("'ipd' as source_type, rx.id as source_id")
            ->selectRaw("COALESCE(rx.prescription_no, CONCAT('IPD-RX-', LPAD(rx.id, 5, '0'))) as rx_no")
            ->selectRaw("COALESCE(p.name, '-') as patient_name")
            ->selectRaw("COALESCE(p.patient_id, '-') as patient_uhid")
            ->selectRaw("TRIM(CONCAT(COALESCE(d.first_name, ''), ' ', COALESCE(d.last_name, ''))) as doctor_name")
            ->selectRaw("DATE_FORMAT(rx.created_at, '%d-%m-%Y') as rx_date")
            ->orderByDesc('rx.created_at')
            ->limit(15)
            ->get();

        // Check which have been already billed
        $results = $opdResults->merge($ipdResults)->map(function ($row) {
            $col = $row->source_type === 'opd' ? 'opd_prescription_id' : 'ipd_prescription_id';
            $billExists = PharmacySaleBill::where($col, $row->source_id)->where('hospital_id', $this->hospital_id)->exists();

            return [
                'source_type' => $row->source_type,
                'source_id' => $row->source_id,
                'rx_no' => $row->rx_no,
                'patient_name' => $row->patient_name,
                'patient_uhid' => $row->patient_uhid,
                'doctor_name' => $row->doctor_name,
                'rx_date' => $row->rx_date,
                'has_bill' => $billExists,
                'label' => ($billExists ? '🔄 ' : '') . $row->rx_no . ' — ' . $row->patient_name . ' (' . strtoupper($row->source_type) . ') ' . $row->rx_date,
            ];
        })->values();

        return response()->json(['items' => $results]);
    }
}
