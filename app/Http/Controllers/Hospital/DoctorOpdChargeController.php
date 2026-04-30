<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\ChargeMaster;
use App\Models\DoctorOpdCharge;
use App\Models\Staff;
use App\Models\Tpa;
use App\Models\TpaOpdCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DoctorOpdChargeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-doctor-opd-charges', ['only' => ['store']]);
        $this->middleware('permission:edit-doctor-opd-charges', ['only' => ['update']]);
        $this->middleware('permission:delete-doctor-opd-charges', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.charges.doctor-opd-charges.destroy', ['doctor_opd_charge' => '__DOCTOR_OPD_CHARGE__']),
            'store' => route('hospital.charges.doctor-opd-charges.store'),
            'loadtable' => route('hospital.charges.doctor-opd-charges.load'),
            'showform' => route('hospital.charges.doctor-opd-charges.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.charges.doctor-opd-charges.index', [
            'pathurl' => 'doctor-opd-charge',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = DoctorOpdCharge::query()
            ->with(['doctor:id,first_name,last_name'])
            ->withCount('tpaOpdCharges')
            ->where('hospital_id', $this->hospital_id);

        return DataTables::of($data)
            ->addColumn('doctor_name', function ($row) {
                return $row->doctor ? $row->doctor->full_name : 'N/A';
            })
            ->addColumn('new_case_charge', function ($row) {
                return number_format((float) $row->charge, 2);
            })
            ->addColumn('follow_up_charge_display', function ($row) {
                return $row->follow_up_charge !== null ? number_format((float) $row->follow_up_charge, 2) : '-';
            })
            ->addColumn('emergency_charge_display', function ($row) {
                return $row->emergency_charge !== null ? number_format((float) $row->emergency_charge, 2) : '-';
            })
            ->addColumn('follow_up_window_display', function ($row) {
                return $row->follow_up_validity_months ? $row->follow_up_validity_months . ' month(s)' : '-';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.charges.doctor-opd-charges.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        $tpaCharges = collect();

        $doctors = Staff::doctor()
            ->active()
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        $tpas = Tpa::orderBy('name')->get(['id', 'name']);

        if ($id) {
            $data = DoctorOpdCharge::where('id', $id)
                ->where('hospital_id', $this->hospital_id)
                ->first();

            if ($data) {
                $tpaCharges = TpaOpdCharge::where('hospital_id', $this->hospital_id)
                    ->where('doctor_opd_charge_id', $data->id)
                    ->pluck('charge', 'tpa_id');
            }
        }

        return view('hospital.charges.doctor-opd-charges.form', compact('data', 'id', 'doctors', 'tpas', 'tpaCharges'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:staff,id',
            'charge' => 'required|numeric|min:0',
            'follow_up_charge' => 'nullable|numeric|min:0',
            'follow_up_validity_months' => 'nullable|integer|min:1|max:24',
            'emergency_charge' => 'nullable|numeric|min:0',
            'tpa_charges' => 'nullable|array',
            'tpa_charges.*.tpa_id' => 'required|exists:tpas,id',
            'tpa_charges.*.charge' => 'nullable|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('follow_up_charge') && !$request->filled('follow_up_validity_months')) {
                $validator->errors()->add('follow_up_validity_months', 'Follow-up validity months is required when follow-up charge is set.');
            }

            if ($request->doctor_id) {
                $exists = DoctorOpdCharge::where('doctor_id', $request->doctor_id)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('doctor_id', 'Doctor OPD charge for this doctor already exists.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        DB::transaction(function () use ($request) {
            $doctorCharge = DoctorOpdCharge::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id' => $this->hospital_id,
                    'doctor_id' => $request->doctor_id,
                    'charge' => $request->charge,
                    'follow_up_charge' => $request->filled('follow_up_charge') ? $request->follow_up_charge : null,
                    'follow_up_validity_months' => $request->filled('follow_up_validity_months') ? $request->follow_up_validity_months : null,
                    'emergency_charge' => $request->filled('emergency_charge') ? $request->emergency_charge : null,
                ]
            );

            // Sync linked ChargeMaster whenever DoctorOpdCharge is created/updated
            $doctorCharge->loadMissing('doctor');
            $chargeMaster = ChargeMaster::updateOrCreate(
                [
                    'related_type' => DoctorOpdCharge::class,
                    'related_id'   => $doctorCharge->id,
                ],
                [
                    'hospital_id'      => $this->hospital_id,
                    'code'             => 'OPD-CONSULT-' . $doctorCharge->doctor_id,
                    'name'             => 'OPD Consultation - ' . ($doctorCharge->doctor?->full_name ?? 'Doctor'),
                    'category'         => 'opd_consultation',
                    'calculation_type' => 'fixed',
                    'billing_frequency'=> 'one_time',
                    'standard_rate'    => $doctorCharge->charge,
                    'is_active'        => true,
                ]
            );

            $submittedTpaIds = [];
            $tpaCharges = $request->input('tpa_charges', []);

            foreach ($tpaCharges as $row) {
                $tpaId = $row['tpa_id'] ?? null;
                if (!$tpaId) {
                    continue;
                }

                $submittedTpaIds[] = (int) $tpaId;
                $charge = array_key_exists('charge', $row) ? $row['charge'] : null;

                if ($charge === null || $charge === '') {
                    TpaOpdCharge::where('hospital_id', $this->hospital_id)
                        ->where('doctor_opd_charge_id', $doctorCharge->id)
                        ->where('tpa_id', $tpaId)
                        ->delete();

                    $chargeMaster->tpaRates()->where('tpa_id', $tpaId)->delete();
                    continue;
                }

                TpaOpdCharge::updateOrCreate(
                    [
                        'hospital_id' => $this->hospital_id,
                        'doctor_opd_charge_id' => $doctorCharge->id,
                        'tpa_id' => $tpaId,
                    ],
                    ['charge' => $charge]
                );

                $chargeMaster->tpaRates()->updateOrCreate(
                    [
                        'hospital_id' => $this->hospital_id,
                        'tpa_id' => $tpaId,
                    ],
                    ['rate' => (float) $charge]
                );
            }

            $deleteQuery = TpaOpdCharge::where('hospital_id', $this->hospital_id)
                ->where('doctor_opd_charge_id', $doctorCharge->id);

            $deleteMasterRateQuery = $chargeMaster->tpaRates();

            if (!empty($submittedTpaIds)) {
                $deleteQuery->whereNotIn('tpa_id', $submittedTpaIds);
                $deleteMasterRateQuery->whereNotIn('tpa_id', $submittedTpaIds);
            }

            $deleteQuery->delete();
            $deleteMasterRateQuery->delete();
        });

        $msg = $request->id ? 'Doctor OPD charge updated successfully.' : 'Doctor OPD charge created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(DoctorOpdCharge $doctor_opd_charge)
    {
        if ($doctor_opd_charge->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Remove the associated ChargeMaster entry
        ChargeMaster::where('related_type', DoctorOpdCharge::class)
            ->where('related_id', $doctor_opd_charge->id)
            ->delete();

        $doctor_opd_charge->delete();

        return response()->json(['status' => true, 'message' => 'Doctor OPD charge deleted successfully.']);
    }
}
