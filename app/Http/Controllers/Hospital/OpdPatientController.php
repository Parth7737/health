<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\OpdPatient;
use App\Models\Patient;
use App\Models\DoctorOpdCharge;
use App\Models\TpaOpdCharge;
use App\Models\HrDepartment;
use App\Models\Habit;
use App\Models\Disease;
use App\Models\DiseaseType;
use App\Models\Allergy;
use App\Models\AllergyReaction;
use App\Models\DiagnosticOrderItem;
use App\Models\OpdPrescription;
use App\Models\PatientCharge;
use App\Models\PatientPayment;
use App\Models\PatientPaymentAllocation;
use App\Models\PatientTimeline;
use App\Models\HeaderFooter;
use App\Models\Staff;
use App\Models\Hospital;
use App\Models\BusinessSetting;
use App\Models\BedAllocation;
use App\Services\ChargeLedgerService;
use App\Services\OpdTokenNoService;
use App\Services\PatientTimelineService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\Facades\DNS1DFacade;
use Yajra\DataTables\Facades\DataTables;

class OpdPatientController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-opd-patient', ['only' => ['store']]);
        $this->middleware('permission:edit-opd-patient', ['only' => ['update', 'updateStatus']]);
        $this->middleware('permission:delete-opd-patient', ['only' => ['destroy']]);
        $this->routes = [   
            'destroy-post' => route('hospital.opd-patient.destroy-post', ['opd_patient' => '__OPD_PATIENT__']),
            'store'   => route('hospital.opd-patient.store'),   
            'loadtable'   => route('hospital.opd-patient.opd-patient-load'),
            'showform'   => route('hospital.opd-patient.showform'),
            'update-status'   => route('hospital.opd-patient.update-status', ['__OPD_PATIENT__']),
            'health-card'   => route('hospital.opd-patient.health-card', ['__PATIENT__']),
            'visit-summary'   => route('hospital.opd-patient.visit-summary.print', ['opdPatient' => '__OPD_PATIENT__']),
            'load-units'   => route('hospital.load-units'),
            'load-tpas'   => route('hospital.load-tpas'),
            'load-doctors'   => route('hospital.load-doctors'),
            'load-doctor-slots'   => route('hospital.load-doctor-slots'),
            'load-symptoms'   => route('hospital.load-symptoms'),
            'load-diseases-by-types'   => route('hospital.load-diseases-by-types'),
            'get-opd-charge'   => route('hospital.get-opd-charge'),
            'search-patients'   => route('hospital.search-patients'),
            'update-vitals-social' => route('hospital.opd-patient.vitals-social.update', ['opdPatient' => '__OPD_PATIENT__']),
            'sticker'            => route('hospital.opd-patient.sticker', ['__OPD_PATIENT__']),
            'queue-status'       => route('hospital.opd-patient.queue-status'),
            'queue-call-next'    => route('hospital.opd-patient.queue-call-next'),
            'queue-skip'         => route('hospital.opd-patient.queue-skip', ['__OPD_PATIENT__']),
            'queue-undo-skip'    => route('hospital.opd-patient.queue-undo-skip', ['__OPD_PATIENT__']),
            'doctor-queue'       => route('hospital.opd-patient.doctor-queue'),
            'token-display'      => route('hospital.opd-patient.token-display'),
        ];
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.opd-patient.index', ['pathurl' => 'opd-patient', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $latestVisitSub = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->selectRaw('MAX(id) as id')
            ->groupBy('patient_id');

        $visitCountSub = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->selectRaw('patient_id, COUNT(*) as visit_count')
            ->groupBy('patient_id');

        $activeIpdSub = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereNull('discharge_date')
            ->selectRaw('patient_id, MAX(id) as allocation_id')
            ->groupBy('patient_id');

        $data = OpdPatient::query()
            ->joinSub($latestVisitSub, 'latest_visits', function ($join) {
                $join->on('latest_visits.id', '=', 'opd_patients.id');
            })
            ->leftJoin('patients', 'patients.id', '=', 'opd_patients.patient_id')
            ->leftJoin('staff', 'staff.id', '=', 'opd_patients.doctor_id')
            ->leftJoinSub($visitCountSub, 'visit_counts', function ($join) {
                $join->on('visit_counts.patient_id', '=', 'opd_patients.patient_id');
            })
            ->leftJoinSub($activeIpdSub, 'active_ipd', function ($join) {
                $join->on('active_ipd.patient_id', '=', 'opd_patients.patient_id');
            })
            ->where('opd_patients.hospital_id', $this->hospital_id)
            ->when(auth()->user()->hasRole('Doctor'), function ($q) {
                $doctorStaffId = Staff::where('user_id', auth()->id())->value('id');
                $q->where('opd_patients.doctor_id', $doctorStaffId ?? 0);
            })
            ->select(
                'opd_patients.*',
                'patients.name as patient_name',
                'patients.patient_id as patient_code',
                'patients.phone as patient_phone',
                DB::raw("TRIM(CONCAT(COALESCE(staff.first_name, ''), ' ', COALESCE(staff.last_name, ''))) as consultant_name"),
                DB::raw('COALESCE(visit_counts.visit_count, 0) as number_of_visits'),
                DB::raw('CASE WHEN active_ipd.allocation_id IS NULL THEN 0 ELSE 1 END as has_active_ipd')
            )
            ->orderByRaw("CASE WHEN COALESCE(opd_patients.status, 'waiting') = 'completed' THEN 1 ELSE 0 END ASC")
            ->orderByRaw('(opd_patients.token_no IS NULL) ASC, opd_patients.token_no ASC')
            ->orderBy('opd_patients.case_no', 'asc');

        return DataTables::of($data)
            ->filterColumn('token_no', function ($query, $keyword) {
                $query->where('opd_patients.token_no', 'like', "%{$keyword}%");
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('patients.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('patient_id', function ($query, $keyword) {
                $query->where('patients.patient_id', 'like', "%{$keyword}%");
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $query->where('patients.phone', 'like', "%{$keyword}%");
            })
            ->filterColumn('consultant', function ($query, $keyword) {
                $query->whereRaw("TRIM(CONCAT(COALESCE(staff.first_name, ''), ' ', COALESCE(staff.last_name, ''))) LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn('last_visit', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(opd_patients.appointment_date, '%d-%m-%Y %H:%i') LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('opd_patients.status', 'like', "%{$keyword}%");
            })
            ->addColumn('name', function ($row) {
                return $row->patient_name ?? '-';
            })
            ->addColumn('token_no', function ($row) {
                return $row->token_no ? OpdTokenNoService::formatShort($row->token_no) : '-';
            })
            ->addColumn('patient_id', function ($row) {
                return $row->patient_code ?? '-';
            })
            ->addColumn('phone', function ($row) {
                return $row->patient_phone ?? '-';
            })
            ->addColumn('consultant', function ($row) {
                return $row->consultant_name ?: '-';
            })
            ->addColumn('last_visit', function ($row) {
                return $row->appointment_date ? Carbon::parse($row->appointment_date)->format('d-m-Y H:i') : '-';
            })
            ->editColumn('name', function ($row) {
                return '<a href="' . route('hospital.opd-patient.visits', $row->patient_id) . '" data-bs-toggle="tooltip" title="View Visits">' .
                    ($row->patient_name ?? '-') .
                    '</a>';
            })
            ->addColumn('status', function ($row) {
                $status = $row->status ?: 'waiting';

                if ($status === 'in_room') {
                    return '<span class="badge bg-warning text-dark">In-Room</span>';
                }

                if ($status === 'completed') {
                    return '<span class="badge bg-success">Completed</span>';
                }

                return '<span class="badge bg-secondary">Waiting</span>';
            })
            ->addColumn('number_of_visits', function ($row) {
                return (int) ($row->number_of_visits ?? 0);
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.opd-patient.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['name','status', 'actions'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        $request->merge([
            'dietary_id' => collect($request->input('dietary_id', []))->filter(fn ($value) => $value !== null && $value !== '')->values()->all(),
            'allergy_id' => collect($request->input('allergy_id', []))->filter(fn ($value) => $value !== null && $value !== '')->values()->all(),
            'habit_id' => collect($request->input('habit_id', []))->filter(fn ($value) => $value !== null && $value !== '')->values()->all(),
            'disease_type_id' => collect($request->input('disease_type_id', []))->filter(fn ($value) => $value !== null && $value !== '')->values()->all(),
            'disease_id' => collect($request->input('disease_id', []))->filter(fn ($value) => $value !== null && $value !== '')->values()->all(),
            'symptoms_type' => collect($request->input('symptoms_type', []))->filter(fn ($value) => $value !== null && $value !== '')->values()->all(),
            'symptoms' => collect($request->input('symptoms', []))->filter(fn ($value) => $value !== null && $value !== '')->values()->all(),
        ]);

        $validator = Validator::make($request->all(), [
            'selected_patient_id' => 'nullable|integer|exists:patients,id',
            'country_code' => 'required_without:selected_patient_id|string|max:10',
            'phone' => 'required|digits_between:7,15',
            'name' => 'required_without:selected_patient_id|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date_format:d-m-Y',
            'age_years' => 'required|integer|min:0|max:150',
            'age_months' => 'nullable|integer|min:0|max:11',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Not Specified',
            'aadhar_card_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'patient_category_id' => 'nullable|exists:patient_categories,id',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'religion_id' => 'nullable|exists:religions,id',
            'dietary_id' => 'nullable|array',
            'dietary_id.*' => 'nullable|integer|exists:dietaries,id',
            'allergy_id' => 'nullable|array',
            'allergy_id.*' => 'nullable|integer|exists:allergies,id',
            'habit_id' => 'nullable|array',
            'habit_id.*' => 'nullable|integer|exists:habits,id',
            'disease_type_id' => 'nullable|array',
            'disease_type_id.*' => 'nullable|integer|exists:disease_types,id',
            'disease_id' => 'nullable|array',
            'disease_id.*' => 'nullable|integer|exists:diseases,id',
            'address' => 'nullable|string',
            'known_allergies' => 'nullable|string',
            'is_staff' => 'nullable|in:Yes,No',
            'appointment_date' => 'required|date_format:d-m-Y H:i',
            'casualty' => 'nullable|in:Yes,No',
            'mlc_patient' => 'nullable|in:Yes,No',
            'tpa_id' => 'nullable|exists:tpas,id',
            'tpa_reference_no' => 'nullable|string|max:255',
            'hr_department_id' => 'required|exists:hr_departments,id',
            'doctor_id' => 'required|exists:staff,id',
            'slot' => 'nullable|string|max:50',
            'applied_charge' => 'required|numeric|min:0',
            'standard_charge' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|in:Cash,Card,Online',
            'live_consultation' => 'nullable|in:Yes,No',
            'symptoms_type' => 'nullable|array',
            'symptoms_type.*' => 'nullable|integer|exists:symptoms_types,id',
            'symptoms' => 'nullable|array',
            'symptoms.*' => 'nullable|integer|exists:symptoms,id',
            'symptoms_description' => 'nullable|string',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'bp' => 'nullable|string|max:50',
            'pluse' => 'nullable|string|max:50',
            'temperature' => 'nullable|string|max:50',
            'respiration' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        if (!empty($request->selected_patient_id)) {
            $validSelectedPatient = Patient::where('id', $request->selected_patient_id)
                ->where('hospital_id', $this->hospital_id)
                ->exists();

            if (!$validSelectedPatient) {
                return response()->json([
                    'errors' => [
                        ['code' => 'phone', 'message' => 'Selected patient is invalid for this hospital.']
                    ]
                ], 422);
            }

            $phoneOwner = Patient::where('phone', $request->phone)->first();
            if ($phoneOwner) {
                if ((int) $phoneOwner->hospital_id !== (int) $this->hospital_id) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'phone', 'message' => 'This phone number is already linked with another hospital.']
                        ]
                    ], 422);
                }

                if ((int) $phoneOwner->id !== (int) $request->selected_patient_id) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'phone', 'message' => 'This phone number is already used by another patient.']
                        ]
                    ], 422);
                }
            }
        }

        if (empty($request->selected_patient_id)) {
            $alreadyInOtherHospital = Patient::where('phone', $request->phone)
                ->where('hospital_id', '!=', $this->hospital_id)
                ->exists();

            if ($alreadyInOtherHospital) {
                return response()->json([
                    'errors' => [
                        ['code' => 'phone', 'message' => 'This phone number is already linked with another hospital.']
                    ]
                ], 422);
            }
        }

        try {
            $result = DB::transaction(function () use ($request, $chargeLedger, $timelineService) {
                $appointmentDate = Carbon::createFromFormat('d-m-Y H:i', $request->appointment_date);
                $patient = null;

                if (!empty($request->selected_patient_id)) {
                    $patient = Patient::where('id', $request->selected_patient_id)
                        ->where('hospital_id', $this->hospital_id)
                        ->first();

                    if ($patient) {
                        if ($request->hasFile('image')) {
                            if ($patient->image) {
                                Storage::disk('public')->delete($patient->image);
                            }
                            $patient->image = $request->file('image')->store('patients', 'public');
                        }

                        $patient->name = $request->name;
                        $patient->guardian_name = $request->guardian_name;
                        $patient->date_of_birth = $request->date_of_birth
                            ? Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d')
                            : null;
                        $patient->age_years = $request->age_years;
                        $patient->age_months = $request->age_months ?? 0;
                        $patient->country_code = $request->country_code;
                        $patient->phone = $request->phone;
                        $patient->email = $request->email;
                        $patient->gender = $request->gender;
                        $patient->patient_category_id = $request->patient_category_id;
                        $patient->nationality_id = $request->nationality_id;
                        $patient->religion_id = $request->religion_id;
                        $patient->dietary_id = count($request->dietary_id) ? $request->dietary_id : null;
                        $patient->allergy_id = count($request->allergy_id) ? $request->allergy_id : null;
                        $patient->habit_id = count($request->habit_id) ? $request->habit_id : null;
                        $patient->disease_type_id = count($request->disease_type_id) ? $request->disease_type_id : null;
                        $patient->disease_id = count($request->disease_id) ? $request->disease_id : null;
                        $patient->blood_group = $request->blood_group;
                        $patient->marital_status = $request->marital_status;
                        $patient->address = $request->address;
                        $patient->known_allergies = $request->known_allergies;
                        $patient->aadhar_no = $request->aadhar_card_no;
                        $patient->is_staff = $request->is_staff ?? 'No';
                        $patient->save();
                    }
                } else {
                    $patient = Patient::where('hospital_id', $this->hospital_id)
                        ->where('phone', $request->phone)
                        ->first();

                    if (!$patient) {
                        $imagePath = null;
                        if ($request->hasFile('image')) {
                            $imagePath = $request->file('image')->store('patients', 'public');
                        }

                        $patient = Patient::create([
                            'hospital_id' => $this->hospital_id,
                            'patient_id' => $this->generateHospitalWisePatientId(),
                            'name' => $request->name,
                            'guardian_name' => $request->guardian_name,
                            'date_of_birth' => $request->date_of_birth
                                ? Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d')
                                : null,
                            'age_years' => $request->age_years,
                            'age_months' => $request->age_months ?? 0,
                            'country_code' => $request->country_code,
                            'phone' => $request->phone,
                            'email' => $request->email,
                            'image' => $imagePath,
                            'gender' => $request->gender,
                            'patient_category_id' => $request->patient_category_id,
                            'nationality_id' => $request->nationality_id,
                            'religion_id' => $request->religion_id,
                            'dietary_id' => count($request->dietary_id) ? $request->dietary_id : null,
                            'allergy_id' => count($request->allergy_id) ? $request->allergy_id : null,
                            'habit_id' => count($request->habit_id) ? $request->habit_id : null,
                            'disease_type_id' => count($request->disease_type_id) ? $request->disease_type_id : null,
                            'disease_id' => count($request->disease_id) ? $request->disease_id : null,
                            'blood_group' => $request->blood_group,
                            'marital_status' => $request->marital_status,
                            'address' => $request->address,
                            'known_allergies' => $request->known_allergies,
                            'aadhar_no' => $request->aadhar_card_no,
                            'is_staff' => $request->is_staff ?? 'No',
                        ]);
                    }
                }

                $chargeSnapshot = $this->resolveOpdChargeDetails(
                    (int) $request->hr_department_id,
                    $request->doctor_id ? (int) $request->doctor_id : null,
                    $request->tpa_id ? (int) $request->tpa_id : null,
                    $patient?->id,
                    $request->phone,
                    $appointmentDate,
                );

                // Duplicate OPD check: agar aaj ka active (waiting/in_room) OPD already hai to block karo
                $existingActiveOPD = OpdPatient::where('hospital_id', $this->hospital_id)
                    ->where('patient_id', $patient->id)
                    ->whereIn('status', ['waiting', 'in_room'])
                    ->first();

                if ($existingActiveOPD) {
                    throw new \Exception('DUPLICATE_OPD:' . $existingActiveOPD->case_no . ':' . OpdTokenNoService::formatShort($existingActiveOPD->token_no));
                }

                $opdPatient = OpdPatient::create([
                    'hospital_id' => $this->hospital_id,
                    'patient_id' => $patient->id,
                    'doctor_id' => $request->doctor_id,
                    'hr_department_id' => $request->hr_department_id,
                    'appointment_date' => $appointmentDate->format('Y-m-d H:i:s'),
                    'case_no' => $this->generateDailyCaseNo($appointmentDate),
                    'token_no' => $this->generateDailyTokenNo($appointmentDate, $request->slot),
                    'casualty' => $request->casualty ?? 'No',
                    'visit_type' => ($request->casualty ?? 'No') === 'Yes' ? 'Emergency' : 'OPD',
                    'mlc_patient' => $request->mlc_patient ?? 'No',
                    'tpa_id' => $request->tpa_id,
                    'tpa_reference_no' => $request->tpa_reference_no,
                    'symptoms_type_id' => count($request->symptoms_type) ? $request->symptoms_type : null,
                    'symptoms' => count($request->symptoms) ? $request->symptoms : null,
                    'symptoms_description' => $request->symptoms_description,
                    'height' => $request->height,
                    'weight' => $request->weight,
                    'bp' => $request->bp,
                    'pluse' => $request->pluse,
                    'temperature' => $request->temperature,
                    'respiration' => $request->respiration,
                    'slot' => $request->slot,
                    'standard_charge' => $chargeSnapshot['standard_charge'] ?? ($request->standard_charge ?? 0),
                    'applied_charge' => $request->applied_charge,
                    'consultation_case_type' => $chargeSnapshot['consultation_case_type'] ?? null,
                    'consultation_case_label' => $chargeSnapshot['consultation_case_label'] ?? null,
                    'consultation_charge_source' => $chargeSnapshot['consultation_charge_source'] ?? null,
                    'consultation_reference_opd_patient_id' => $chargeSnapshot['reference_visit_id'] ?? null,
                    'consultation_valid_until' => $chargeSnapshot['consultation_valid_until'] ?? null,
                    'payment_mode' => $request->payment_mode,
                    'live_consultation' => $request->live_consultation ?? 'No',
                    'status' => 'waiting',
                ]);

                if ((float) $opdPatient->applied_charge > 0) {
                    // Lookup ChargeMaster linked to the doctor's OPD charge (no auto-create)
                    $chargeMasterId = null;
                    if ($request->doctor_id) {
                        $doctorOpdCharge = DoctorOpdCharge::where('doctor_id', $request->doctor_id)->first();
                        $chargeMasterId  = $doctorOpdCharge?->chargeMaster?->id;
                    }

                    $charge = $chargeLedger->upsertCharge([
                        'hospital_id'       => $this->hospital_id,
                        'patient_id'        => $patient->id,
                        'visitable_type'    => OpdPatient::class,
                        'visitable_id'      => $opdPatient->id,
                        'source_type'       => OpdPatient::class,
                        'source_id'         => $opdPatient->id,
                        'module'            => 'opd',
                        'particular'        => 'OPD Consultation - ' . $opdPatient->case_no,
                        'charge_master_id'  => $chargeMasterId,
                        'charge_category'   => 'opd_consultation',
                        'calculation_type'  => 'fixed',
                        'billing_frequency' => 'one_time',
                        'quantity'          => 1,
                        'unit_rate'         => $opdPatient->applied_charge,
                        'net_amount'        => $opdPatient->applied_charge,
                        'payer_type'        => $opdPatient->tpa_id ? 'tpa' : 'self',
                        'tpa_id'            => $opdPatient->tpa_id,
                        'charged_at'        => $opdPatient->appointment_date,
                    ]);

                    // Auto-pay when payment mode is collected at registration
                    if (filled($opdPatient->payment_mode)) {
                        $chargeLedger->collectPayment($patient, [
                            'amount'       => (float) $opdPatient->applied_charge,
                            'payment_mode' => $opdPatient->payment_mode,
                        ], [$charge->id]);
                    }
                }

                $timelineService->logForOpdVisit($opdPatient, [
                    'event_key' => 'opd.visit.created',
                    'title' => 'OPD Visit Registered',
                    'description' => 'Case ' . $opdPatient->case_no . ' with token ' . OpdTokenNoService::formatForDisplay($opdPatient->token_no) . ' has been created.',
                    'meta' => [
                        'case_no' => $opdPatient->case_no,
                        'token_no' => $opdPatient->token_no,
                        'doctor_id' => $opdPatient->doctor_id,
                        'department_id' => $opdPatient->hr_department_id,
                        'status' => $opdPatient->status,
                    ],
                    'logged_at' => $opdPatient->appointment_date,
                ]);

                return [
                    'opdPatient' => $opdPatient,
                    'patient' => $patient,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'OPD Patient created successfully.',
                'id' => $result['opdPatient']->id,
                'patient_id' => $result['patient']->id,
                'patient_code' => $result['patient']->patient_id,
            ]);
        } catch (\Throwable $exception) {
            if (str_starts_with($exception->getMessage(), 'DUPLICATE_OPD:')) {
                $parts = explode(':', $exception->getMessage());
                $existingCase = $parts[1] ?? '-';
                $existingToken = $parts[2] ?? '-';

                return response()->json([
                    'errors' => [[ 'code'=>"patient_active_opd", 'message'=>"The patient already has an active OPD (Case: {$existingCase}, Token: {$existingToken}). Please complete that visit first." ]],
                ], 422);
            }

            return response()->json([
                'status' => false,
                'message' => 'Unable to save OPD patient right now. Please try again.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if (!empty($request->patient_id)) {
            $data = OpdPatient::query()
                ->with('patient')
                ->where('hospital_id', $this->hospital_id)
                ->where('patient_id', $request->patient_id)
                ->latest('appointment_date')
                ->latest('id')
                ->first();
        }

        return view('hospital.opd-patient.form', compact('data', 'id'));
    }

    public function updateStatus(Request $request, OpdPatient $opdPatient, PatientTimelineService $timelineService)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized OPD patient record.',
            ], 403);
        }

        $currentStatus = $opdPatient->status ?: 'waiting';
        $nextStatus = match ($currentStatus) {
            'waiting' => 'in_room',
            'in_room' => 'completed',
            default => 'completed',
        };

        if ($currentStatus === 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'This OPD visit is already completed.',
            ], 422);
        }

        $opdPatient->update([
            'status' => $nextStatus,
        ]);

        $statusLabels = [
            'waiting' => 'Waiting',
            'in_room' => 'In-Room',
            'completed' => 'Completed',
        ];

        $timelineService->logForOpdVisit($opdPatient->fresh(), [
            'event_key' => 'opd.visit.status_changed',
            'title' => 'OPD Visit Status Updated',
            'description' => 'Status changed from ' . ($statusLabels[$currentStatus] ?? ucfirst($currentStatus)) . ' to ' . ($statusLabels[$nextStatus] ?? ucfirst($nextStatus)) . '.',
            'meta' => [
                'from_status' => $currentStatus,
                'to_status' => $nextStatus,
            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OPD status updated successfully.',
            'current_status' => $nextStatus,
        ]);
    }
    public function visits(Patient $patient, ChargeLedgerService $chargeLedger)
    {
        if ($patient->hospital_id !== $this->hospital_id) {
            abort(403, 'Unauthorized patient record.');
        }

        $visits = OpdPatient::select(
                'id',
                'hospital_id',
                'patient_id',
                'case_no',
                'doctor_id',
                'hr_department_id',
                'appointment_date',
                'tpa_id',
                'tpa_reference_no as reference',
                'standard_charge',
                'applied_charge',
                'payment_mode',
                'symptoms',
                'height',
                'weight',
                'bp',
                'pluse',
                'temperature',
                'respiration',
                'systolic_bp',
                'diastolic_bp',
                'diabetes',
                'bmi',
                'body_area',
                'social_known_allergies',
                'social_allergic_reactions',
                'occupation',
                'social_marital_status',
                'place_of_birth',
                'current_location',
                'years_in_current_location',
                'social_habits',
                'family_history'
            )
            ->where('patient_id', $patient->id)
            ->with([
                'consultant:id,first_name,last_name',
                'prescription:id,opd_patient_id',
            ])
            ->orderByDesc('appointment_date')
            ->orderByDesc('id')
            ->get();

        $visits->each(function (OpdPatient $visit) use ($patient, $chargeLedger) {
            if ((float) $visit->applied_charge <= 0) {
                return;
            }

            $chargeLedger->upsertCharge([
                'hospital_id' => $this->hospital_id,
                'patient_id' => $patient->id,
                'visitable_type' => OpdPatient::class,
                'visitable_id' => $visit->id,
                'source_type' => OpdPatient::class,
                'source_id' => $visit->id,
                'module' => 'opd',
                'particular' => 'OPD Consultation - ' . $visit->case_no,
                'charge_code' => $visit->doctor_id ? 'OPD-CONSULT-' . $visit->doctor_id : 'OPD-DEPT-' . $visit->hr_department_id,
                'charge_category' => 'opd_consultation',
                'calculation_type' => 'fixed',
                'billing_frequency' => 'one_time',
                'quantity' => 1,
                'unit_rate' => $visit->applied_charge,
                'net_amount' => $visit->applied_charge,
                'payer_type' => $visit->tpa_id ? 'tpa' : 'self',
                'tpa_id' => $visit->tpa_id,
                'charged_at' => $visit->appointment_date,
            ]);
        });

        $prescriptionVisits = OpdPrescription::query()
            ->where('patient_id', $patient->id)
            ->with([
                'opdPatient:id,case_no,appointment_date,doctor_id,tpa_reference_no',
                'doctor:id,first_name,last_name',
            ])
            ->latest('id')
            ->get();

        $diagnosticItems = DiagnosticOrderItem::query()
            ->whereHas('order', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with(['order'])
            ->latest('id')
            ->get();

        $pathologyVisits = $diagnosticItems->where('department', 'pathology')->values();
        $radiologyVisits = $diagnosticItems->where('department', 'radiology')->values();
        $visitsById = $visits->keyBy('id');

        $patientCharges = PatientCharge::query()
            ->where('patient_id', $patient->id)
            ->orderByDesc('id')
            ->get();

        $patientPayments = PatientPayment::query()
            ->where('patient_id', $patient->id)
            ->orderByDesc('id')
            ->get();

        $totalCharges = (float) $patientCharges->sum('amount');
        $totalDiscount = (float) $patientCharges->sum('discount_amount');
        $totalTax = (float) $patientCharges->sum('tax_amount');
        $totalPaid = (float) $patientCharges->sum('paid_amount');
        $totalDue = (float) $patientCharges->sum(function (PatientCharge $charge) {
            return max(0, (float) $charge->amount - (float) $charge->paid_amount);
        });
        $totalPayments = (float) $patientPayments->sum('amount');
        $totalAllocated = (float) PatientPaymentAllocation::query()
            ->whereHas('charge', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->sum('amount');
        $advanceCredit = max(0, $totalPayments - $totalAllocated);

        $vitalsVisits = $visits->filter(function ($visit) {
            return filled($visit->systolic_bp)
                || filled($visit->diastolic_bp)
                || filled($visit->respiration)
                || filled($visit->temperature)
                || filled($visit->pluse)
                || filled($visit->diabetes)
                || filled($visit->height)
                || filled($visit->weight)
                || filled($visit->bmi);
        })->values();

        $habitOptions = Habit::query()->select('id', 'name')->orderBy('name')->get();
        $diseaseOptions = Disease::query()->select('id', 'name')->orderBy('name')->get();
        $allergyOptions = Allergy::query()->select('id', 'name')->orderBy('name')->get();
        $allergyReactionOptions = AllergyReaction::query()
            ->select('id', 'allergy_id', 'name')
            ->with('allergy:id,name')
            ->orderBy('name')
            ->get();
        $relationOptions = ['Father', 'Mother', 'Brother', 'Sister', 'Spouse', 'Son', 'Daughter', 'Other'];

        $diagnosisRoutes = [
            'loadtable' => route('hospital.opd-patient.diagnosis.load', ['patient' => '__PATIENT__']),
            'showform' => route('hospital.opd-patient.diagnosis.showform', ['patient' => '__PATIENT__']),
            'store' => route('hospital.opd-patient.diagnosis.store', ['patient' => '__PATIENT__']),
            'destroy' => route('hospital.opd-patient.diagnosis.destroy', ['patient' => '__PATIENT__', 'diagnosis' => '__DIAGNOSIS__']),
            'showPaymentForm' => route('hospital.opd-patient.charges.show-payment-form', ['patient' => '__PATIENT__']),
            'collectPayment' => route('hospital.opd-patient.charges.collect-payment', ['patient' => '__PATIENT__']),
            'printFinalBill' => route('hospital.opd-patient.charges.final-bill.print', ['patient' => '__PATIENT__']),
        ];

        $timelineEntries = PatientTimeline::query()
            ->where('patient_id', $patient->id)
            ->orderByDesc('logged_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get();
            
        return view('hospital.opd-patient.visits', [
            'patient' => $patient,
            'visits' => $visits,
            'vitalsVisits' => $vitalsVisits,
            'habitOptions' => $habitOptions,
            'diseaseOptions' => $diseaseOptions,
            'allergyOptions' => $allergyOptions,
            'allergyReactionOptions' => $allergyReactionOptions,
            'relationOptions' => $relationOptions,
            'prescriptionVisits' => $prescriptionVisits,
            'pathologyVisits' => $pathologyVisits,
            'radiologyVisits' => $radiologyVisits,
            'visitsById' => $visitsById,
            'patientCharges' => $patientCharges,
            'patientPayments' => $patientPayments,
            'totalCharges' => $totalCharges,
            'totalDiscount' => $totalDiscount,
            'totalTax' => $totalTax,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
            'advanceCredit' => $advanceCredit,
            'timelineEntries' => $timelineEntries,
            'routes' => $diagnosisRoutes,
            'pathurl' => 'diagnosis',
        ]);
    }

    public function updateVitalsSocial(Request $request, OpdPatient $opdPatient, PatientTimelineService $timelineService)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized OPD patient record.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'respiration' => 'nullable|numeric|min:0|max:999',
            'diabetes' => 'nullable|numeric|min:0|max:999',
            'pluse' => 'nullable|numeric|min:0|max:999',
            'systolic_bp' => 'nullable|numeric|min:0|max:400',
            'diastolic_bp' => 'nullable|numeric|min:0|max:400',
            'temperature' => 'nullable|numeric|min:0|max:200',
            'height' => 'nullable|numeric|min:0|max:20',
            'weight' => 'nullable|numeric|min:0|max:1000',
            'spo2' => 'nullable|numeric|min:0|max:100',
            'bmi' => 'nullable|numeric|min:0|max:200',
            'subjective_notes' => 'nullable|string|max:5000',
            'objective_notes' => 'nullable|string|max:5000',
            'assessment_notes' => 'nullable|string|max:5000',
            'plan_notes' => 'nullable|string|max:5000',
            'patient_instructions' => 'nullable|string|max:5000',
            'follow_up_date' => 'nullable|date',
            'disposition' => 'nullable|string|max:100',
            'body_area' => 'nullable|string|max:100',
            'known_allergies' => 'nullable|array',
            'known_allergies.*' => 'nullable|integer|exists:allergies,id',
            'allergic_reactions' => 'nullable|array',
            'allergic_reactions.*' => 'nullable|integer|exists:allergy_reactions,id',
            'occupation' => 'nullable|string|max:255',
            'social_marital_status' => 'nullable|string|max:100',
            'place_of_birth' => 'nullable|string|max:255',
            'current_location' => 'nullable|string|max:255',
            'years_in_current_location' => 'nullable|string|max:100',
            'habit_name' => 'nullable|array',
            'habit_name.*' => 'nullable|string|max:255',
            'habit_status' => 'nullable|array',
            'habit_status.*' => 'nullable|string|max:100',
            'family_disease' => 'nullable|array',
            'family_disease.*' => 'nullable|string|max:255',
            'family_relation' => 'nullable|array',
            'family_relation.*' => 'nullable|string|max:100',
            'family_age' => 'nullable|array',
            'family_age.*' => 'nullable|string|max:20',
            'family_comments' => 'nullable|array',
            'family_comments.*' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $habitNames = $request->input('habit_name', []);
        $habitStatuses = $request->input('habit_status', []);
        $habits = [];
        $maxHabits = max(count($habitNames), count($habitStatuses));
        for ($i = 0; $i < $maxHabits; $i++) {
            $name = trim((string) ($habitNames[$i] ?? ''));
            $status = trim((string) ($habitStatuses[$i] ?? ''));
            if ($name === '' && $status === '') {
                continue;
            }

            $habits[] = [
                'name' => $name,
                'status' => $status,
            ];
        }

        $familyDiseases = $request->input('family_disease', []);
        $familyRelations = $request->input('family_relation', []);
        $familyAges = $request->input('family_age', []);
        $familyComments = $request->input('family_comments', []);
        $familyHistory = [];
        $maxFamily = max(count($familyDiseases), count($familyRelations), count($familyAges), count($familyComments));
        for ($i = 0; $i < $maxFamily; $i++) {
            $disease = trim((string) ($familyDiseases[$i] ?? ''));
            $relation = trim((string) ($familyRelations[$i] ?? ''));
            $age = trim((string) ($familyAges[$i] ?? ''));
            $comments = trim((string) ($familyComments[$i] ?? ''));
            if ($disease === '' && $relation === '' && $age === '' && $comments === '') {
                continue;
            }

            $familyHistory[] = [
                'disease' => $disease,
                'relation' => $relation,
                'age' => $age,
                'comments' => $comments,
            ];
        }

        $systolicBp = $request->filled('systolic_bp') ? (string) $request->input('systolic_bp') : null;
        $diastolicBp = $request->filled('diastolic_bp') ? (string) $request->input('diastolic_bp') : null;
        $bp = null;
        if ($systolicBp !== null || $diastolicBp !== null) {
            $bp = trim(($systolicBp ?? '') . '/' . ($diastolicBp ?? ''), '/');
        }

        $opdPatient->update([
            'respiration' => $request->filled('respiration') ? (string) $request->input('respiration') : null,
            'diabetes' => $request->filled('diabetes') ? (string) $request->input('diabetes') : null,
            'pluse' => $request->filled('pluse') ? (string) $request->input('pluse') : null,
            'systolic_bp' => $systolicBp,
            'diastolic_bp' => $diastolicBp,
            'bp' => $bp,
            'temperature' => $request->filled('temperature') ? (string) $request->input('temperature') : null,
            'height' => $request->filled('height') ? (string) $request->input('height') : null,
            'weight' => $request->filled('weight') ? (string) $request->input('weight') : null,
            'spo2' => $request->filled('spo2') ? (string) $request->input('spo2') : null,
            'bmi' => $request->filled('bmi') ? (string) $request->input('bmi') : null,
            'subjective_notes' => $request->input('subjective_notes'),
            'objective_notes' => $request->input('objective_notes'),
            'assessment_notes' => $request->input('assessment_notes'),
            'plan_notes' => $request->input('plan_notes'),
            'patient_instructions' => $request->input('patient_instructions'),
            'follow_up_date' => $request->input('follow_up_date'),
            'disposition' => $request->input('disposition'),
            'body_area' => $request->input('body_area'),
            'social_known_allergies' => collect($request->input('known_allergies', []))
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->map(fn ($value) => (int) $value)
                ->values()
                ->all(),
            'social_allergic_reactions' => collect($request->input('allergic_reactions', []))
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->map(fn ($value) => (int) $value)
                ->values()
                ->all(),
            'occupation' => $request->input('occupation'),
            'social_marital_status' => $request->input('social_marital_status'),
            'place_of_birth' => $request->input('place_of_birth'),
            'current_location' => $request->input('current_location'),
            'years_in_current_location' => $request->input('years_in_current_location'),
            'social_habits' => $habits,
            'family_history' => $familyHistory,
        ]);

        // $timelineService->logForOpdVisit($opdPatient->fresh(), [
        //     'event_key' => 'opd.visit.vitals_updated',
        //     'title' => 'Vitals & Social History Updated',
        //     'description' => 'Vitals and social history details were updated for this visit.',
        //     'meta' => [
        //         'has_bp' => filled($bp),
        //         'has_bmi' => $request->filled('bmi'),
        //         'known_allergies_count' => count($request->input('known_allergies', [])),
        //         'family_history_count' => count($familyHistory),
        //     ],
        // ]);

        return response()->json([
            'status' => true,
            'message' => 'Vitals and social history saved successfully.',
            'data' => $opdPatient->fresh(),
        ]);
    }
    public function destroy(OpdPatient $opd_patient)
    {
        $opd_patient->delete();

        return response()->json(['status' => true, 'message' => 'OPD Patient Deleted Successfully.']);
    }
    public function getOpdCharge(Request $request)
    {
        $appointmentDate = $request->filled('appointment_date')
            ? Carbon::createFromFormat('d-m-Y H:i', $request->appointment_date)
            : now();

        $chargeSnapshot = $this->resolveOpdChargeDetails(
            (int) $request->hr_department_id,
            $request->doctor_id ? (int) $request->doctor_id : null,
            $request->tpa_id ? (int) $request->tpa_id : null,
            $request->selected_patient_id ? (int) $request->selected_patient_id : null,
            $request->phone,
            $appointmentDate,
        );

        return response()->json([
            'status' => true,
            'charge' => $chargeSnapshot['charge'],
            'standard_charge' => $chargeSnapshot['standard_charge'] ?? null,
            'apply_charge_type' => $chargeSnapshot['apply_charge_type'],
            'consultation_case_type' => $chargeSnapshot['consultation_case_type'],
            'consultation_case_label' => $chargeSnapshot['consultation_case_label'],
            'consultation_charge_source' => $chargeSnapshot['consultation_charge_source'],
            'reference_visit_id' => $chargeSnapshot['reference_visit_id'],
            'consultation_valid_until' => optional($chargeSnapshot['consultation_valid_until'])->format('d-m-Y'),
        ]);    
    }

    public function loadDiseasesByTypes(Request $request)
    {
        $types = collect($request->input('types', []))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->values()
            ->all();

        if (empty($types)) {
            return response()->json([]);
        }

        $diseaseTypeIds = DiseaseType::query()
            ->whereIn('id', $types)
            ->pluck('id')
            ->all();

        $diseases = Disease::query()
            ->whereIn('disease_type_id', $diseaseTypeIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($diseases);
    }

    public function searchPatients(Request $request)
    {
        $query = trim((string) $request->get('query', ''));
        $searchBy = (string) $request->get('search_by', 'phone');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($subQuery) use ($query) {
                $subQuery->where('phone', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('patient_id', 'like', "%{$query}%");
            })
            ->when($searchBy === 'health_id', function ($patientQuery) use ($query) {
                $patientQuery->orderByRaw('CASE WHEN patient_id = ? THEN 0 ELSE 1 END', [$query]);
            })
            ->orderBy('name')
            ->limit(15)
            ->get([
                'id',
                'patient_id',
                'name',
                'guardian_name',
                'date_of_birth',
                'age_years',
                'age_months',
                'country_code',
                'phone',
                'email',
                'gender',
                'nationality_id',
                'patient_category_id',
                'religion_id',
                'dietary_id',
                'allergy_id',
                'habit_id',
                'disease_type_id',
                'disease_id',
                'blood_group',
                'marital_status',
                'address',
                'known_allergies',
                'aadhar_no',
                'is_staff',
            ])
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'patient_code' => $patient->patient_id,
                    'name' => $patient->name,
                    'phone' => $patient->phone,
                    'country_code' => $patient->country_code,
                    'guardian_name' => $patient->guardian_name,
                    'date_of_birth' => $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->format('d-m-Y') : null,
                    'age_years' => $patient->age_years,
                    'age_months' => $patient->age_months,
                    'email' => $patient->email,
                    'gender' => $patient->gender,
                    'nationality_id' => $patient->nationality_id,
                    'patient_category_id' => $patient->patient_category_id,
                    'religion_id' => $patient->religion_id,
                    'dietary_id' => $patient->dietary_id,
                    'allergy_id' => $patient->allergy_id,
                    'habit_id' => $patient->habit_id,
                    'disease_type_id' => $patient->disease_type_id,
                    'disease_id' => $patient->disease_id,
                    'blood_group' => $patient->blood_group,
                    'marital_status' => $patient->marital_status,
                    'address' => $patient->address,
                    'known_allergies' => $patient->known_allergies,
                    'aadhar_no' => $patient->aadhar_no,
                    'is_staff' => $patient->is_staff,
                ];
            })
            ->values();

        return response()->json($patients);
    }

    public function healthCard(Patient $patient)
    {
        if ((int) $patient->hospital_id !== (int) $this->hospital_id) {
            abort(403, 'Unauthorized patient record.');
        }

        $hospital = auth()->user()?->hospital;

        $hospitalName = $hospital?->name ?: config('app.name', 'Hospital');
        $hospitalLogo = $hospital?->image
            ? asset('public/storage/' . $hospital->image)
            : asset('images/logo.png');

        $hospitalAddressLine1 = trim((string) ($hospital?->address ?? ''));

        $hospitalAddressLine2Parts = array_filter([
            $hospital?->landmark,
            $hospital?->city,
            $hospital?->pincode,
        ], fn ($value) => !empty($value));

        $hospitalAddressLine2 = implode(', ', $hospitalAddressLine2Parts);

        if ($hospitalAddressLine1 === '' && $hospitalAddressLine2 === '') {
            $hospitalAddressLine1 = $hospitalName;
        }

        $barcodePng = DNS1DFacade::getBarcodePNG((string) $patient->patient_id, 'C128');

        return view('hospital.health-card', [
            'patient' => $patient,
            'hospitalName' => $hospitalName,
            'hospitalLogo' => $hospitalLogo,
            'hospitalAddressLine1' => $hospitalAddressLine1,
            'hospitalAddressLine2' => $hospitalAddressLine2,
            'barcodePng' => $barcodePng,
        ]);
    }

    public function viewVisitSummary(OpdPatient $opdPatient)
    {
        if ((int) $opdPatient->hospital_id !== (int) $this->hospital_id) {
            abort(403, 'Unauthorized OPD patient record.');
        }

        $visit = OpdPatient::query()
            ->where('id', $opdPatient->id)
            ->with([
                'patient:id,name,patient_id,guardian_name,gender,age_years,age_months,phone,email,address,date_of_birth,marital_status,known_allergies,disease_type_id,disease_id,allergy_id',
                'consultant:id,first_name,last_name',
            ])
            ->firstOrFail();

        $tpaName = null;
        if ($visit->tpa_id) {
            $tpaName = DB::table('tpas')->where('id', $visit->tpa_id)->value('name');
        }

        $patientDiseaseTypeNames = DiseaseType::query()
            ->whereIn('id', collect((array) ($visit->patient?->disease_type_id ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $patientDiseaseNames = Disease::query()
            ->whereIn('id', collect((array) ($visit->patient?->disease_id ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $patientAllergyNames = Allergy::query()
            ->whereIn('id', collect((array) ($visit->patient?->allergy_id ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $socialKnownAllergyNames = Allergy::query()
            ->whereIn('id', collect((array) ($visit->social_known_allergies ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $socialAllergicReactionNames = AllergyReaction::query()
            ->whereIn('id', collect((array) ($visit->social_allergic_reactions ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        return view('hospital.opd-patient.visit-summary.view', [
            'visit' => $visit,
            'patient' => $visit->patient,
            'tpaName' => $tpaName,
            'patientDiseaseTypeNames' => $patientDiseaseTypeNames,
            'patientDiseaseNames' => $patientDiseaseNames,
            'patientAllergyNames' => $patientAllergyNames,
            'socialKnownAllergyNames' => $socialKnownAllergyNames,
            'socialAllergicReactionNames' => $socialAllergicReactionNames,
            'socialHabits' => is_array($visit->social_habits) ? $visit->social_habits : [],
            'familyHistory' => is_array($visit->family_history) ? $visit->family_history : [],
        ]);
    }

    public function doctorCareUnified(OpdPatient $opdPatient)
    {
        if ((int) $opdPatient->hospital_id !== (int) $this->hospital_id) {
            abort(403, 'Unauthorized OPD patient record.');
        }

        $visit = OpdPatient::query()
            ->where('id', $opdPatient->id)
            ->with([
                'patient:id,name,patient_id,guardian_name,gender,age_years,age_months,phone',
                'consultant:id,first_name,last_name',
                'department:id,name',
            ])
            ->firstOrFail();

        return view('hospital.opd-patient.doctor-care.unified', [
            'visit' => $visit,
            'patient' => $visit->patient,
            'canPathology' => auth()->user()->can('create-pathology-order'),
            'canRadiology' => auth()->user()->can('create-radiology-order'),
        ]);
    }

    public function printVisitSummary(OpdPatient $opdPatient)
    {
        if ((int) $opdPatient->hospital_id !== (int) $this->hospital_id) {
            abort(403, 'Unauthorized OPD patient record.');
        }

        $visit = OpdPatient::query()
            ->where('id', $opdPatient->id)
            ->with([
                'patient:id,name,patient_id,guardian_name,gender,age_years,age_months,phone,email,address,date_of_birth,marital_status,known_allergies,disease_type_id,disease_id,allergy_id',
                'consultant:id,first_name,last_name',
            ])
            ->firstOrFail();

        $hospital = auth()->user()?->hospital;

        $tpaName = null;
        if ($visit->tpa_id) {
            $tpaName = DB::table('tpas')->where('id', $visit->tpa_id)->value('name');
        }

        $patientDiseaseTypeNames = DiseaseType::query()
            ->whereIn('id', collect((array) ($visit->patient?->disease_type_id ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $patientDiseaseNames = Disease::query()
            ->whereIn('id', collect((array) ($visit->patient?->disease_id ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $patientAllergyNames = Allergy::query()
            ->whereIn('id', collect((array) ($visit->patient?->allergy_id ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $socialKnownAllergyNames = Allergy::query()
            ->whereIn('id', collect((array) ($visit->social_known_allergies ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $socialAllergicReactionNames = AllergyReaction::query()
            ->whereIn('id', collect((array) ($visit->social_allergic_reactions ?? []))->filter()->values()->all())
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $visitCharge = PatientCharge::query()
            ->where('visitable_type', OpdPatient::class)
            ->where('visitable_id', $visit->id)
            ->where('source_type', OpdPatient::class)
            ->where('source_id', $visit->id)
            ->where('module', 'opd')
            ->where('charge_category', 'opd_consultation')
            ->latest('id')
            ->first();

        $doctorChargeRule = $visit->doctor_id
            ? DoctorOpdCharge::query()->where('doctor_id', $visit->doctor_id)->first()
            : null;

        return view('hospital.opd-patient.visit-summary.print', [
            'visit' => $visit,
            'patient' => $visit->patient,
            'hospital' => $hospital,
            'tpaName' => $tpaName,
            'patientDiseaseTypeNames' => $patientDiseaseTypeNames,
            'patientDiseaseNames' => $patientDiseaseNames,
            'patientAllergyNames' => $patientAllergyNames,
            'socialKnownAllergyNames' => $socialKnownAllergyNames,
            'socialAllergicReactionNames' => $socialAllergicReactionNames,
            'socialHabits' => is_array($visit->social_habits) ? $visit->social_habits : [],
            'familyHistory' => is_array($visit->family_history) ? $visit->family_history : [],
            'visitCharge' => $visitCharge,
            'doctorChargeRule' => $doctorChargeRule,
        ]);
    }

    private function resolveOpdChargeDetails(
        int $departmentId,
        ?int $doctorId,
        ?int $tpaId,
        ?int $patientId,
        ?string $phone,
        ?Carbon $appointmentDate = null
    ): array {
        $appointmentDate = $appointmentDate ?: now();
        $charge = null;
        $standardCharge = null;
        $applyChargeType = '';
        $consultationCaseType = 'department_case';
        $consultationCaseLabel = 'Department Charge';
        $consultationChargeSource = 'Department OPD Charge';
        $referenceVisitId = null;
        $consultationValidUntil = null;

        $department = HrDepartment::query()->where('id', $departmentId)->first();
        if ($department) {
            $charge = (float) $department->charge;
            $standardCharge = (float) $department->charge;
            $applyChargeType = 'department';
        }

        if ($doctorId) {
            $doctorCharge = DoctorOpdCharge::query()
                ->with('chargeMaster.tpaRates')
                ->where('doctor_id', $doctorId)
                ->first();

            if ($doctorCharge) {
                $chargeMaster = $doctorCharge->chargeMaster;
                $newCaseCharge = $chargeMaster ? (float) $chargeMaster->standard_rate : (float) $doctorCharge->charge;

                $charge = $newCaseCharge;
                $standardCharge = $newCaseCharge;
                $applyChargeType = 'doctor';
                $consultationCaseType = 'new_case';
                $consultationCaseLabel = 'New Case';
                $consultationChargeSource = 'Doctor Consultation Charge';

                $resolvedPatientId = $patientId ?: $this->resolvePatientIdByPhone($phone);
                $latestDoctorVisit = $resolvedPatientId
                    ? OpdPatient::query()
                        ->where('hospital_id', $this->hospital_id)
                        ->where('patient_id', $resolvedPatientId)
                        ->where('doctor_id', $doctorId)
                        ->where('appointment_date', '<', $appointmentDate->format('Y-m-d H:i:s'))
                        ->latest('appointment_date')
                        ->latest('id')
                        ->first()
                    : null;

                if (
                    $latestDoctorVisit &&
                    $doctorCharge->follow_up_charge !== null &&
                    !empty($doctorCharge->follow_up_validity_months)
                ) {
                    $validUntil = Carbon::parse($latestDoctorVisit->appointment_date)
                        ->addMonthsNoOverflow((int) $doctorCharge->follow_up_validity_months)
                        ->endOfDay();

                    if ($appointmentDate->lte($validUntil)) {
                        $charge = (float) $doctorCharge->follow_up_charge;
                        $standardCharge = (float) $doctorCharge->follow_up_charge;
                        $consultationCaseType = 'follow_up';
                        $consultationCaseLabel = 'Old Case / Follow-up';
                        $consultationChargeSource = 'Doctor Follow-up Charge';
                        $referenceVisitId = $latestDoctorVisit->id;
                        $consultationValidUntil = $validUntil;
                    }
                }

                if ($tpaId) {
                    if ($chargeMaster) {
                        $tpaRate = collect($chargeMaster->tpaRates)->firstWhere('tpa_id', (int) $tpaId);
                        if ($tpaRate) {
                            $charge = (float) $tpaRate->rate;
                            $applyChargeType = 'tpa';
                            $consultationChargeSource = 'TPA OPD Charge';
                        }
                    } else {
                        $legacyTpaCharge = TpaOpdCharge::query()
                            ->where('doctor_opd_charge_id', $doctorCharge->id)
                            ->where('tpa_id', $tpaId)
                            ->first();

                        if ($legacyTpaCharge) {
                            $charge = (float) $legacyTpaCharge->charge;
                            $applyChargeType = 'tpa';
                            $consultationChargeSource = 'TPA OPD Charge';
                        }
                    }
                }
            }
        }

        return [
            'charge' => $charge,
            'standard_charge' => $standardCharge,
            'apply_charge_type' => $applyChargeType,
            'consultation_case_type' => $consultationCaseType,
            'consultation_case_label' => $consultationCaseLabel,
            'consultation_charge_source' => $consultationChargeSource,
            'reference_visit_id' => $referenceVisitId,
            'consultation_valid_until' => $consultationValidUntil,
        ];
    }

    private function resolvePatientIdByPhone(?string $phone): ?int
    {
        $phone = trim((string) $phone);
        if ($phone === '') {
            return null;
        }

        return Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('phone', $phone)
            ->value('id');
    }

    private function generateHospitalWisePatientId(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'P' . str_pad((string) $this->hospital_id, 4, '0', STR_PAD_LEFT) . $datePart;

        $lastPatient = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastPatient && str_starts_with($lastPatient->patient_id, $prefix)) {
            $nextNumber = ((int) substr($lastPatient->patient_id, -4)) + 1;
        }

        do {
            $patientCode = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (Patient::where('patient_id', $patientCode)->exists());

        return $patientCode;
    }

    private function generateDailyCaseNo(Carbon $appointmentDate): string
    {
        $datePart = $appointmentDate->format('Ymd');
        $prefix = 'OPD' . $datePart;

        $lastCase = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereDate('appointment_date', $appointmentDate->toDateString())
            ->where('case_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastCase && str_starts_with($lastCase->case_no, $prefix)) {
            $nextNumber = ((int) substr($lastCase->case_no, -4)) + 1;
        }

        do {
            $caseNo = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (OpdPatient::where('case_no', $caseNo)->exists());

        return $caseNo;
    }

    private function generateDailyTokenNo(Carbon $appointmentDate, ?string $slot = null): string
    {
        return app(OpdTokenNoService::class)->nextSequentialToken(
            $this->hospital_id,
            $appointmentDate,
            $slot
        );
    }

    public function printSticker(Request $request, OpdPatient $opdPatient)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            abort(403, 'Unauthorized OPD record.');
        }

        $copies = max(1, min((int) $request->get('copies', 1), 10));
        $patient = $opdPatient->patient;
        $doctor = Staff::find($opdPatient->doctor_id);
        $hospital = Hospital::find($this->hospital_id);
        $labelWidthMm = (float) (BusinessSetting::where('key', "hospital_{$this->hospital_id}_sticker_width_mm")->value('value') ?? 90);
        $labelHeightMm = (float) (BusinessSetting::where('key', "hospital_{$this->hospital_id}_sticker_height_mm")->value('value') ?? 45);

        $labelWidthMm = max(20, min($labelWidthMm, 150));
        $labelHeightMm = max(15, min($labelHeightMm, 120));

        return view('hospital.opd-patient.sticker', compact('opdPatient', 'patient', 'doctor', 'hospital', 'copies', 'labelWidthMm', 'labelHeightMm'));
    }

    public function printFileSticker(Request $request, OpdPatient $opdPatient)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            abort(403, 'Unauthorized OPD record.');
        }

        $copies = max(1, min((int) $request->get('copies', 1), 10));
        $patient  = $opdPatient->patient;
        $doctor   = Staff::find($opdPatient->doctor_id);
        $hospital = Hospital::find($this->hospital_id);

        return view('hospital.opd-patient.file-sticker', compact('opdPatient', 'patient', 'doctor', 'hospital', 'copies'));
    }

    public function doctorQueue()
    {
        if (auth()->user()->hasRole('Doctor')) {
            return redirect()->route('hospital.doctor-dashboard');
        }

        return view('hospital.opd-patient.doctor-queue');
    }

    public function doctorQueueList()
    {
        return view('hospital.opd-patient.doctor-queue-list', [
            'pathurl' => 'opd-queue-list',
            'routes' => [
                'loadtable' => route('hospital.opd-patient.doctor-queue-list-load'),
            ],
        ]);
    }

    public function doctorQueueListLoad(Request $request)
    {
        $data = OpdPatient::query()
            ->leftJoin('patients', 'patients.id', '=', 'opd_patients.patient_id')
            ->leftJoin('staff', 'staff.id', '=', 'opd_patients.doctor_id')
            ->leftJoin('hr_departments', 'hr_departments.id', '=', 'opd_patients.hr_department_id')
            ->where('opd_patients.hospital_id', $this->hospital_id)
            ->where('opd_patients.status', 'waiting')
            ->when(auth()->user()->hasRole('Doctor'), function ($q) {
                $doctorStaffId = Staff::where('user_id', auth()->id())->value('id');
                $q->where('opd_patients.doctor_id', $doctorStaffId ?? 0);
            })
            ->select(
                'opd_patients.id',
                'opd_patients.token_no',
                'opd_patients.patient_id',
                'opd_patients.case_no',
                'opd_patients.symptoms',
                'opd_patients.absent_flag',
                'opd_patients.appointment_date',
                'patients.name as patient_name',
                'patients.age_years as patient_age_years',
                'patients.gender as patient_gender',
                DB::raw("TRIM(CONCAT(COALESCE(staff.first_name, ''), ' ', COALESCE(staff.last_name, ''))) as consultant_name"),
                'hr_departments.name as department_name'
            )
            ->orderBy('opd_patients.absent_flag')
            ->orderBy('opd_patients.token_no');

        return DataTables::of($data)
            ->addColumn('token', function ($row) {
                return OpdTokenNoService::formatShort($row->token_no);
            })
            ->addColumn('patient', function ($row) {
                $name = e($row->patient_name ?: '-');
                $caseNo = e($row->case_no ?: '-');
                return "<div class='fw-semibold'>{$name}</div><div class='text-muted small'>{$caseNo}</div>";
            })
            ->addColumn('age_gender', function ($row) {
                $age = $row->patient_age_years !== null ? (int) $row->patient_age_years . 'Y' : '-';
                $gender = trim((string) $row->patient_gender);
                return $gender !== '' ? ($age . ' / ' . $gender) : $age;
            })
            ->addColumn('complaint', function ($row) {
                $text = trim(strip_tags((string) $row->symptoms));
                if ($text === '') {
                    return 'General consultation';
                }

                return e(\Illuminate\Support\Str::limit($text, 80));
            })
            ->addColumn('wait_time', function ($row) {
                if (!$row->appointment_date) {
                    return '-';
                }

                $minutes = Carbon::parse($row->appointment_date)->diffInMinutes(now());
                return $minutes . ' min';
            })
            ->addColumn('priority', function ($row) {
                if ($row->absent_flag) {
                    return '<span class="badge bg-secondary">Pending</span>';
                }

                $minutes = $row->appointment_date ? Carbon::parse($row->appointment_date)->diffInMinutes(now()) : 0;
                if ($minutes >= 15) {
                    return '<span class="badge bg-danger">Urgent</span>';
                }

                return '<span class="badge bg-success">Routine</span>';
            })
            ->addColumn('doctor', function ($row) {
                $doctor = trim((string) $row->consultant_name);
                $dept = trim((string) $row->department_name);

                if ($dept !== '') {
                    return e(($doctor !== '' ? $doctor : '-') . ' / ' . $dept);
                }

                return e($doctor !== '' ? $doctor : '-');
            })
            ->addColumn('actions', function ($row) {
                $emrUrl = route('hospital.opd-patient.visits', $row->patient_id) . '#consolidated';
                return '<a href="' . e($emrUrl) . '" class="btn btn-primary btn-sm">Consult</a>';
            })
            ->rawColumns(['patient', 'priority', 'actions'])
            ->make(true);
    }

    public function tokenDisplay()
    {
        $hospitalName = Hospital::where('id', $this->hospital_id)->value('name');
        return view('hospital.opd-patient.token-display', compact('hospitalName'));
    }

    public function queueStatus()
    {
        $baseQuery = OpdPatient::query()
            ->with(['patient:id,name,age_years,age_months,gender', 'consultant:id,first_name,last_name', 'department:id,name'])
            ->where('hospital_id', $this->hospital_id);

        if (auth()->user()->hasRole('Doctor')) {
            $doctorStaffId = Staff::where('user_id', auth()->id())->value('id');
            $baseQuery->where('doctor_id', $doctorStaffId ?? 0);
        }

        $inRoomList = (clone $baseQuery)
            ->where('status', 'in_room')
            ->orderBy('doctor_id')
            ->orderBy('token_no')
            ->get();

        $waiting = (clone $baseQuery)
            ->where('status', 'waiting')
            ->orderBy('absent_flag')
            ->orderByRaw('(opd_patients.token_no IS NULL) ASC, opd_patients.token_no ASC')
            ->get();

        $completedToday = (clone $baseQuery)
            ->where('status', 'completed')
            ->whereDate('appointment_date', Carbon::today()->toDateString())
            ->orderByDesc('updated_at')
            ->get();

        $formatAgeGender = function (OpdPatient $row) {
            $ageGender = trim((string) ($row->patient?->age_years ?? ''));
            if ($ageGender !== '') {
                $ageGender .= 'Y';
            }
            if (!empty($row->patient?->gender)) {
                $ageGender = trim($ageGender . ' / ' . strtoupper($row->patient->gender), ' /');
            }

            return $ageGender !== '' ? $ageGender : '-';
        };

        $formatComplaint = function (OpdPatient $row) {
            $text = trim(strip_tags((string) ($row->symptoms_name ?: '')));
            if ($text !== '') {
                return $text;
            }

            $rawSymptoms = collect($row->symptoms ?? [])->filter()->implode(', ');
            $rawSymptoms = trim(strip_tags($rawSymptoms));

            return $rawSymptoms !== '' ? $rawSymptoms : 'General consultation';
        };

        $formatBp = function (OpdPatient $row) {
            if (!empty($row->bp)) {
                return $row->bp;
            }

            if (!empty($row->systolic_bp) || !empty($row->diastolic_bp)) {
                return trim(($row->systolic_bp ?: '-') . '/' . ($row->diastolic_bp ?: '-'), '/');
            }

            return null;
        };

        return response()->json([
            'status' => true,
            'current' => $inRoomList->first() ? [
                'id' => $inRoomList->first()->id,
                'patient_id' => $inRoomList->first()->patient_id,
                'doctor_id' => $inRoomList->first()->doctor_id,
                'token' => OpdTokenNoService::formatShort($inRoomList->first()->token_no),
                'name' => $inRoomList->first()->patient?->name ?? '-',
                'doctor' => trim(($inRoomList->first()->consultant?->first_name ?? '') . ' ' . ($inRoomList->first()->consultant?->last_name ?? '')) ?: '-',
                'dept' => $inRoomList->first()->department?->name ?? '-',
                'case_no' => $inRoomList->first()->case_no,
            ] : null,
            'current_list' => $inRoomList->map(function ($row) use ($formatAgeGender, $formatComplaint, $formatBp) {
                return [
                    'id' => $row->id,
                    'patient_id' => $row->patient_id,
                    'doctor_id' => $row->doctor_id,
                    'token' => OpdTokenNoService::formatShort($row->token_no),
                    'name' => $row->patient?->name ?? '-',
                    'doctor' => trim(($row->consultant?->first_name ?? '') . ' ' . ($row->consultant?->last_name ?? '')) ?: '-',
                    'dept' => $row->department?->name ?? '-',
                    'case_no' => $row->case_no,
                    'age_gender' => $formatAgeGender($row),
                    'complaint' => $formatComplaint($row),
                    'wait_time' => $row->appointment_date ? Carbon::parse($row->appointment_date)->diffForHumans(now(), ['parts' => 2, 'short' => true]) : null,
                    'wait_minutes' => $row->appointment_date ? Carbon::parse($row->appointment_date)->diffInMinutes(now()) : null,
                    'bp' => $formatBp($row),
                    'pulse' => $row->pluse,
                    'temperature' => $row->temperature,
                    'respiration' => $row->respiration,
                    'weight' => $row->weight,
                ];
            })->values(),
            'queue' => $waiting->map(function ($row) use ($formatAgeGender, $formatComplaint) {
                $doctor = trim(($row->consultant?->first_name ?? '') . ' ' . ($row->consultant?->last_name ?? '')) ?: '-';
                return [
                    'id' => $row->id,
                    'patient_id' => $row->patient_id,
                    'doctor_id' => $row->doctor_id,
                    'status' => $row->status,
                    'token' => OpdTokenNoService::formatShort($row->token_no),
                    'name' => $row->patient?->name ?? '-',
                    'doctor' => $doctor,
                    'dept' => $row->department?->name ?? '-',
                    'case_no' => $row->case_no,
                    'age_gender' => $formatAgeGender($row),
                    'complaint' => $formatComplaint($row),
                    'wait_time' => $row->appointment_date ? Carbon::parse($row->appointment_date)->diffForHumans(now(), ['parts' => 2, 'short' => true]) : '-',
                    'wait_minutes' => $row->appointment_date ? Carbon::parse($row->appointment_date)->diffInMinutes(now()) : 0,
                    'absent' => (bool) $row->absent_flag,
                ];
            })->values(),
            'completed' => $completedToday->map(function ($row) use ($formatAgeGender, $formatComplaint) {
                $doctor = trim(($row->consultant?->first_name ?? '') . ' ' . ($row->consultant?->last_name ?? '')) ?: '-';
                return [
                    'id' => $row->id,
                    'patient_id' => $row->patient_id,
                    'doctor_id' => $row->doctor_id,
                    'token' => OpdTokenNoService::formatShort($row->token_no),
                    'name' => $row->patient?->name ?? '-',
                    'doctor' => $doctor,
                    'dept' => $row->department?->name ?? '-',
                    'case_no' => $row->case_no,
                    'age_gender' => $formatAgeGender($row),
                    'complaint' => $formatComplaint($row),
                    'completed_at' => $row->updated_at ? Carbon::parse($row->updated_at)->format('h:i A') : '-',
                ];
            })->values(),
        ]);
    }

    public function callNextToken(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $doctorScopeId = null;

        $baseQuery = OpdPatient::query()
            ->where('hospital_id', $this->hospital_id);

        if (auth()->user()->hasRole('Doctor')) {
            $doctorStaffId = Staff::where('user_id', auth()->id())->value('id');
            $doctorScopeId = $doctorStaffId ?? 0;
        } elseif (!empty($request->doctor_id)) {
            $doctorScopeId = (int) $request->doctor_id;
        }

        if ($doctorScopeId) {
            $baseQuery->where('doctor_id', $doctorScopeId);
        }

        $inRoom = (clone $baseQuery)->where('status', 'in_room')->first();
        if ($inRoom) {
            $inRoom->update(['status' => 'completed']);
        }

        $nextWaiting = (clone $baseQuery)
            ->where('status', 'waiting')
            ->where('absent_flag', false)
            ->orderBy('token_no')
            ->first();

        if (!$nextWaiting) {
            return response()->json([
                'status' => true,
                'message' => 'Queue complete. No more waiting patients.',
            ]);
        }

        $nextWaiting->update([
            'status' => 'in_room',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Next patient moved to in-room.',
        ]);
    }

    public function skipWaitingPatient(OpdPatient $opdPatient)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized OPD record.'], 403);
        }

        if ($opdPatient->status !== 'waiting') {
            return response()->json(['status' => false, 'message' => 'Only waiting patients can be marked not present.'], 422);
        }

        if (auth()->user()->hasRole('Doctor')) {
            $doctorStaffId = Staff::where('user_id', auth()->id())->value('id');
            if (!$doctorStaffId || (int) $opdPatient->doctor_id !== (int) $doctorStaffId) {
                return response()->json(['status' => false, 'message' => 'You can manage only your queue.'], 403);
            }
        }

        $opdPatient->update(['absent_flag' => true]);

        return response()->json(['status' => true, 'message' => 'Patient marked as not present.']);
    }

    public function undoSkipWaitingPatient(OpdPatient $opdPatient)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized OPD record.'], 403);
        }

        if (! $opdPatient->absent_flag) {
            return response()->json(['status' => false, 'message' => 'Patient is already marked present.'], 422);
        }

        if ($opdPatient->status !== 'waiting') {
            return response()->json(['status' => false, 'message' => 'Present/absent can only be updated for waiting visits.'], 422);
        }

        if (auth()->user()->hasRole('Doctor')) {
            $doctorStaffId = Staff::where('user_id', auth()->id())->value('id');
            if (!$doctorStaffId || (int) $opdPatient->doctor_id !== (int) $doctorStaffId) {
                return response()->json(['status' => false, 'message' => 'You can manage only your queue.'], 403);
            }
        }

        $opdPatient->update(['absent_flag' => false]);

        return response()->json(['status' => true, 'message' => 'Patient marked as present.']);
    }
}

