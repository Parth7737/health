<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PharmacyDashboardController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->routes = [
            'dispenseQueueLoad' => route('hospital.pharmacy.dispense-queue-load'),
            'stockLoad' => route('hospital.pharmacy.stock-load'),
            'stockExport' => route('hospital.pharmacy.stock-export'),
            'showBadStockForm' => route('hospital.pharmacy.stock.show-bad-stock-form'),
            'adjustBadStock' => route('hospital.pharmacy.stock.adjust-bad-stock'),
            'expiryLoad' => route('hospital.pharmacy.expiry-load'),
            'expiryProcess' => route('hospital.pharmacy.expiry.process'),
            'purchaseLoad' => route('hospital.pharmacy.purchase-load'),
            'purchasePrint' => route('hospital.pharmacy.purchase.print', ['bill' => '__ID__']),
        ];
    }

    public function index()
    {
        $medicineCategories = MedicineCategory::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('hospital.pharmacy.dashboard', [
            'pathurl' => 'pharmacy-dashboard',
            'routes'  => $this->routes,
            'medicineCategories' => $medicineCategories,
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
            ->selectRaw("CASE WHEN LOWER(COALESCE(op.visit_type, '')) = 'emergency' THEN 'urgent' ELSE 'normal' END as priority")
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
            ->selectRaw("'normal' as priority")
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


}

