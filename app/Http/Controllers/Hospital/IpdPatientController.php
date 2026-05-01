<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Bed;
use App\Models\BedAllocation;
use App\Models\BedStatus;
use App\Models\ChargeMaster;
use App\Models\Disease;
use App\Models\DiagnosticOrder;
use App\Models\DiagnosticOrderItem;
use App\Models\HeaderFooter;
use App\Models\HrDepartment;
use App\Models\IpdPrescription;
use App\Models\IpdProgressNote;
use App\Models\OpdPatient;
use App\Models\PathologyTest;
use App\Models\Patient;
use App\Models\PatientCharge;
use App\Models\PatientPayment;
use App\Models\PatientPaymentAllocation;
use App\Models\PatientTimeline;
use App\Models\RadiologyTest;
use App\Models\Hospital;
use App\Models\Staff;
use App\Models\Tpa;
use App\Services\BedAllocationService;
use App\Services\ChargeLedgerService;
use App\Services\PatientTimelineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class IpdPatientController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:create-ipd-patient', ['only' => ['store']]);
        $this->middleware('permission:edit-ipd-patient', ['only' => ['transfer', 'discharge']]);

        $this->routes = [
            'loadtable' => route('hospital.ipd-patient.load'),
            'showform' => route('hospital.ipd-patient.showform'),
            'store' => route('hospital.ipd-patient.store'),
            'profile' => route('hospital.ipd-patient.profile', ['allocation' => '__ALLOCATION__']),
            'clinical-update' => route('hospital.ipd-patient.clinical.update', ['allocation' => '__ALLOCATION__']),
            'transfer-form' => route('hospital.ipd-patient.transfer.showform', ['allocation' => '__ALLOCATION__']),
            'transfer' => route('hospital.ipd-patient.transfer', ['allocation' => '__ALLOCATION__']),
            'discharge-form' => route('hospital.ipd-patient.discharge.showform', ['allocation' => '__ALLOCATION__']),
            'discharge' => route('hospital.ipd-patient.discharge', ['allocation' => '__ALLOCATION__']),
            'notes-store' => route('hospital.ipd-patient.notes.store', ['allocation' => '__ALLOCATION__']),
            'diagnostics-showform' => route('hospital.ipd-patient.diagnostics.showform', ['allocation' => '__ALLOCATION__']),
            'diagnostics-store' => route('hospital.ipd-patient.diagnostics.store', ['allocation' => '__ALLOCATION__']),
            'diagnostics-destroy' => route('hospital.ipd-patient.diagnostics.destroy', ['allocation' => '__ALLOCATION__', 'item' => '__ITEM__']),
            'final-bill-print' => route('hospital.ipd-patient.final-bill.print', ['allocation' => '__ALLOCATION__']),
            'discharge-summary-print' => route('hospital.ipd-patient.discharge-summary.print', ['allocation' => '__ALLOCATION__']),
            'search-patients' => route('hospital.search-patients'),
            'load-tpas' => route('hospital.load-tpas'),
            'load-doctors' => route('hospital.load-doctors'),
        ];
    }

    public function index()
    {
        $stats = [
            'active_admissions' => BedAllocation::query()
                ->where('hospital_id', $this->hospital_id)
                ->whereNull('discharge_date')
                ->count(),
            'today_admissions' => BedAllocation::query()
                ->where('hospital_id', $this->hospital_id)
                ->whereDate('admission_date', today())
                ->count(),
            'available_beds' => Bed::query()
                ->where('hospital_id', $this->hospital_id)
                ->where('bed_status_id', BedStatus::AVAILABLE)
                ->count(),
            'occupied_beds' => Bed::query()
                ->where('hospital_id', $this->hospital_id)
                ->where('bed_status_id', BedStatus::OCCUPIED)
                ->count(),
        ];

        return view('hospital.ipd-patient.index', [
            'pathurl' => 'ipd-patient',
            'routes' => $this->routes,
            'stats' => $stats,
        ]);
    }

    public function loaddata(Request $request)
    {
        $outstandingSub = PatientCharge::query()
            ->selectRaw('patient_id, SUM(GREATEST(amount - paid_amount, 0)) as outstanding_amount')
            ->groupBy('patient_id');

        $data = BedAllocation::query()
            ->leftJoin('patients', 'patients.id', '=', 'bed_allocations.patient_id')
            ->leftJoin('beds', 'beds.id', '=', 'bed_allocations.bed_id')
            ->leftJoin('rooms', 'rooms.id', '=', 'beds.room_id')
            ->leftJoin('wards', 'wards.id', '=', 'rooms.ward_id')
            ->leftJoin('bed_types', 'bed_types.id', '=', 'beds.bed_type_id')
            ->leftJoin('staff as consultants', 'consultants.id', '=', 'bed_allocations.consultant_doctor_id')
            ->leftJoin('tpas', 'tpas.id', '=', 'bed_allocations.tpa_id')
            ->leftJoinSub($outstandingSub, 'pending_charges', function ($join) {
                $join->on('pending_charges.patient_id', '=', 'bed_allocations.patient_id');
            })
            ->where('bed_allocations.hospital_id', $this->hospital_id)
            ->select(
                'bed_allocations.*',
                'patients.name as patient_name',
                'patients.patient_id as patient_code',
                'patients.phone as patient_phone',
                'patients.age_years',
                'patients.gender',
                'beds.bed_number',
                'beds.bed_code',
                'rooms.room_number',
                'wards.ward_name',
                'bed_types.type_name as bed_type_name',
                'tpas.name as tpa_name',
                DB::raw('COALESCE(pending_charges.outstanding_amount, 0) as outstanding_amount'),
                DB::raw("TRIM(CONCAT(COALESCE(consultants.first_name, ''), ' ', COALESCE(consultants.last_name, ''))) as consultant_name")
            )
            ->orderByRaw('CASE WHEN bed_allocations.discharge_date IS NULL THEN 0 ELSE 1 END ASC')
            ->orderByDesc('bed_allocations.admission_date');

        return DataTables::of($data)
            ->addColumn('admission_no', function ($row) {
                return $row->admission_no ?: ('IPD-' . str_pad((string) $row->id, 6, '0', STR_PAD_LEFT));
            })
            ->editColumn('patient_name', function ($row) {
                $label = $row->patient_name ?: '-';
                return '<a href="' . route('hospital.ipd-patient.profile', ['allocation' => $row->id]) . '">' . e($label) . '</a>';
            })
            ->addColumn('patient_id', function ($row) {
                return $row->patient_code ?: '-';
            })
            ->addColumn('age_gender', function ($row) {
                return trim(($row->age_years !== null ? $row->age_years . 'y' : 'N/A') . ' / ' . ($row->gender ?: 'N/A'), ' /');
            })
            ->addColumn('consultant', function ($row) {
                return $row->consultant_name ?: '-';
            })
            ->addColumn('bed_identifier', function ($row) {
                return collect([$row->ward_name, $row->room_number, $row->bed_number ? 'Bed ' . $row->bed_number : null])
                    ->filter()
                    ->implode(' | ');
            })
            ->addColumn('los', function ($row) {
                $admission = Carbon::parse($row->admission_date);
                $end = $row->discharge_date ? Carbon::parse($row->discharge_date) : now();
                return max(1, $admission->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1) . ' day(s)';
            })
            ->addColumn('status', function ($row) {
                if ($row->discharge_date) {
                    return '<span class="badge bg-success">Discharged</span>';
                }

                return '<span class="badge bg-warning text-dark">Active</span>';
            })
            ->addColumn('payer', function ($row) {
                return $row->tpa_name ?: 'Self';
            })
            ->addColumn('outstanding', function ($row) {
                return number_format((float) $row->outstanding_amount, 2);
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.ipd-patient.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['patient_name', 'status', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $sourceOpdPatient = null;
        $patient = null;

        if ($request->filled('opd_patient_id')) {
            $sourceOpdPatient = OpdPatient::query()
                ->with(['patient', 'consultant', 'department'])
                ->where('hospital_id', $this->hospital_id)
                ->findOrFail($request->opd_patient_id);

            $patient = $sourceOpdPatient->patient;
        } elseif ($request->filled('patient_id')) {
            $patient = Patient::query()
                ->where('hospital_id', $this->hospital_id)
                ->findOrFail($request->patient_id);
        }

        $availableBeds = $this->availableBedsQuery()->get();
        $departments = HrDepartment::query()->orderBy('name')->get(['id', 'name']);
        $tpas = Tpa::query()->where('hospital_id', $this->hospital_id)->orderBy('name')->get(['id', 'name']);

        return view('hospital.ipd-patient.form', [
            'patient' => $patient,
            'sourceOpdPatient' => $sourceOpdPatient,
            'availableBeds' => $availableBeds,
            'departments' => $departments,
            'tpas' => $tpas,
        ]);
    }

    public function store(
        Request $request,
        BedAllocationService $bedAllocationService,
        ChargeLedgerService $chargeLedger,
        PatientTimelineService $timelineService
    ) {
        $validator = Validator::make($request->all(), [
            'selected_patient_id' => 'nullable|integer|exists:patients,id',
            'opd_patient_id' => 'nullable|integer|exists:opd_patients,id',
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
            'email' => 'nullable|email|max:255',
            'patient_category_id' => 'nullable|exists:patient_categories,id',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'religion_id' => 'nullable|exists:religions,id',
            'address' => 'nullable|string',
            'admission_date' => 'required|date_format:d-m-Y H:i',
            'expected_discharge_date' => 'nullable|date_format:d-m-Y',
            'admission_type' => 'required|in:emergency,planned,observation,icu',
            'doctor_id' => 'required|exists:staff,id',
            'hr_department_id' => 'required|exists:hr_departments,id',
            'bed_id' => 'required|exists:beds,id',
            'tpa_id' => 'nullable|exists:tpas,id',
            'tpa_reference_no' => 'nullable|string|max:255',
            'admission_reason' => 'required|string',
            'provisional_diagnosis' => 'nullable|string',
            'admission_notes' => 'nullable|string',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'bp' => 'nullable|string|max:50',
            'pulse' => 'nullable|string|max:50',
            'temperature' => 'nullable|string|max:50',
            'respiration' => 'nullable|string|max:50',
            'initial_payment_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $selectedPatientId = $request->filled('selected_patient_id') ? (int) $request->selected_patient_id : null;
        if ($selectedPatientId) {
            $patientExists = Patient::query()
                ->where('hospital_id', $this->hospital_id)
                ->where('id', $selectedPatientId)
                ->exists();

            if (!$patientExists) {
                return response()->json([
                    'errors' => [
                        ['code' => 'phone', 'message' => 'Selected patient is invalid for this hospital.'],
                    ],
                ], 422);
            }
        }

        $phoneOwner = Patient::query()->where('phone', $request->phone)->first();
        if ($phoneOwner && (int) $phoneOwner->hospital_id !== (int) $this->hospital_id) {
            return response()->json([
                'errors' => [
                    ['code' => 'phone', 'message' => 'This phone number is already linked with another hospital.'],
                ],
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($request, $bedAllocationService, $chargeLedger, $timelineService) {
                $patient = $this->upsertPatient($request);

                $existingActiveAdmission = BedAllocation::query()
                    ->where('hospital_id', $this->hospital_id)
                    ->where('patient_id', $patient->id)
                    ->whereNull('discharge_date')
                    ->first();

                if ($existingActiveAdmission) {
                    throw new \RuntimeException('Patient already has an active IPD admission.');
                }

                $staffId = Staff::query()->where('user_id', auth()->id())->value('id');
                $admissionDate = Carbon::createFromFormat('d-m-Y H:i', $request->admission_date);
                $expectedDischargeDate = $request->filled('expected_discharge_date')
                    ? Carbon::createFromFormat('d-m-Y', $request->expected_discharge_date)->endOfDay()
                    : null;

                $allocation = $bedAllocationService->allocateBed(
                    $this->hospital_id,
                    $patient->id,
                    (int) $request->bed_id,
                    $staffId,
                    $request->admission_type,
                    $request->admission_notes,
                    [
                        'admission_no' => $this->generateAdmissionNo(),
                        'consultant_doctor_id' => (int) $request->doctor_id,
                        'hr_department_id' => $request->filled('hr_department_id') ? (int) $request->hr_department_id : null,
                        'tpa_id' => $request->filled('tpa_id') ? (int) $request->tpa_id : null,
                        'source_opd_patient_id' => $request->filled('opd_patient_id') ? (int) $request->opd_patient_id : null,
                        'admission_source' => $request->filled('opd_patient_id') ? 'opd' : 'direct',
                        'tpa_reference_no' => $request->tpa_reference_no,
                        'admission_date' => $admissionDate,
                        'expected_discharge_date' => $expectedDischargeDate,
                        'admission_reason' => $request->admission_reason,
                        'provisional_diagnosis' => $request->provisional_diagnosis,
                        'height' => $request->height,
                        'weight' => $request->weight,
                        'bp' => $request->bp,
                        'pulse' => $request->pulse,
                        'temperature' => $request->temperature,
                        'respiration' => $request->respiration,
                    ]
                );

                if ($request->filled('opd_patient_id')) {
                    $sourceOpdPatient = OpdPatient::query()
                        ->where('hospital_id', $this->hospital_id)
                        ->findOrFail($request->opd_patient_id);

                    $allocation->update([
                        'systolic_bp' => $sourceOpdPatient->systolic_bp ?? null,
                        'diastolic_bp' => $sourceOpdPatient->diastolic_bp ?? null,
                        'diabetes' => $sourceOpdPatient->diabetes ?? null,
                        'bmi' => $sourceOpdPatient->bmi ?? null,
                        'family_history' => is_array($sourceOpdPatient->family_history)
                            ? implode(', ', array_filter($sourceOpdPatient->family_history))
                            : ($sourceOpdPatient->family_history ?? null),
                    ]);

                    $sourceOpdPatient->update([
                        'status' => 'completed',
                        'ipd_admitted_at' => now(),
                        'ipd_bed_allocation_id' => $allocation->id,
                    ]);
                }

                $allocation = $this->syncBedCharge($allocation, $chargeLedger);

                if ((float) $request->initial_payment_amount > 0) {
                    $episodeAllocations = $this->resolveEpisodeAllocations($allocation);
                    $episodeChargeIds = $this->episodeChargeQuery($allocation, $episodeAllocations)
                        ->whereColumn('paid_amount', '<', 'amount')
                        ->orderBy('charged_at')
                        ->orderBy('id')
                        ->pluck('id')
                        ->all();

                    if (empty($episodeChargeIds)) {
                        $episodeChargeIds = $allocation->charges()->latest('id')->limit(1)->pluck('id')->all();
                    }

                    $payment = $chargeLedger->collectPayment($patient, [
                        'amount' => (float) $request->initial_payment_amount,
                        'payment_mode' => $request->payment_mode,
                        'notes' => 'Initial IPD admission payment',
                    ], $episodeChargeIds);

                    $timelineService->logForIpdAdmission($allocation, [
                        'event_key' => 'ipd.payment.collected',
                        'title' => 'IPD Payment Collected',
                        'description' => 'Admission payment of ' . number_format((float) $payment->amount, 2) . ' collected.',
                        'meta' => [
                            'payment_id' => $payment->id,
                            'amount' => (float) $payment->amount,
                            'payment_mode' => $payment->payment_mode,
                        ],
                        'logged_at' => $payment->paid_at,
                    ]);
                }

                return $allocation;
            });

            return response()->json([
                'status' => true,
                'message' => 'IPD admission completed successfully.',
                'allocation_id' => $result->id,
                'redirect_url' => route('hospital.ipd-patient.profile', ['allocation' => $result->id]),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage() ?: 'Unable to complete IPD admission.',
            ], 422);
        }
    }

    public function showAddChargeForm(BedAllocation $allocation)
    {
        $allocation = $this->findAllocation($allocation->id);

        $chargeMasters = ChargeMaster::query()
            ->where('is_active', true)
            ->whereRaw("LOWER(REPLACE(category, ' ', '_')) = ?", ['general'])
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'category', 'standard_rate']);

        return view('hospital.ipd-patient.partials.add_charge_form', [
            'allocation' => $allocation,
            'chargeMasters' => $chargeMasters,
            'submitUrl' => route('hospital.ipd-patient.charges.store', ['allocation' => $allocation->id]),
        ]);
    }

    public function storeAdditionalCharge(
        Request $request,
        BedAllocation $allocation,
        ChargeLedgerService $chargeLedger,
        PatientTimelineService $timelineService
    ) {
        $allocation = $this->findAllocation($allocation->id);

        if ($allocation->discharge_date) {
            return response()->json([
                'status' => false,
                'message' => 'Discharged admission cannot be modified.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'charge_master_id' => 'required|integer|exists:charge_masters,id',
            'quantity' => 'required|numeric|min:1|max:9999',
            'unit_rate' => 'required|numeric|min:0',
            'particular' => 'nullable|string|max:255',
            'charged_at' => 'nullable|date_format:d-m-Y H:i',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $chargeMaster = ChargeMaster::query()->find((int) $request->charge_master_id);
        if (!$chargeMaster || strtolower(str_replace(' ', '_', (string) $chargeMaster->category)) !== 'general') {
            return response()->json([
                'errors' => [
                    ['code' => 'charge_master_id', 'message' => 'Please select a valid Other Charge master.'],
                ],
            ], 422);
        }

        $quantity = (float) $request->quantity;
        $unitRate = (float) $request->unit_rate;
        $netAmount = $quantity * $unitRate;
        $chargedAt = $request->filled('charged_at')
            ? Carbon::createFromFormat('d-m-Y H:i', (string) $request->charged_at)
            : now();

        $charge = $chargeLedger->upsertCharge([
            'hospital_id' => $this->hospital_id,
            'patient_id' => $allocation->patient_id,
            'visitable_type' => BedAllocation::class,
            'visitable_id' => $allocation->id,
            'module' => 'ipd',
            'particular' => $request->filled('particular') ? (string) $request->particular : ('OTHER CHARGE - ' . $chargeMaster->name),
            'charge_master_id' => $chargeMaster->id,
            'charge_category' => $chargeMaster->category,
            'calculation_type' => $chargeMaster->calculation_type ?? 'fixed',
            'billing_frequency' => $chargeMaster->billing_frequency ?? 'one_time',
            'quantity' => $quantity,
            'unit_rate' => $unitRate,
            'net_amount' => $netAmount,
            'payer_type' => $allocation->tpa_id ? 'tpa' : 'self',
            'tpa_id' => $allocation->tpa_id,
            'charged_at' => $chargedAt,
        ]);

        $timelineService->logForIpdAdmission($allocation, [
            'event_key' => 'ipd.charge.added',
            'title' => 'Additional Charge Added',
            'description' => ($charge->particular ?: 'Additional charge') . ' of ' . number_format((float) $charge->amount, 2) . ' has been added.',
            'meta' => [
                'charge_id' => $charge->id,
                'charge_master_id' => $chargeMaster->id,
                'amount' => (float) $charge->amount,
            ],
            'logged_at' => $chargedAt,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Additional charge added successfully.',
        ]);
    }

    public function profile(BedAllocation $allocation, ChargeLedgerService $chargeLedger)
    {
        $allocation = $this->findAllocation($allocation->id);
        $allocation = $this->syncBedCharge($allocation, $chargeLedger);

        $episodeAllocations = $this->resolveEpisodeAllocations($allocation);
        $episodeCharges = $this->episodeChargeQuery($allocation, $episodeAllocations)
            ->orderBy('charged_at')
            ->orderBy('id')
            ->get();

        $episodeOutstandingAmount = (float) $episodeCharges->sum(function (PatientCharge $charge) {
            return max(0, (float) $charge->amount - (float) $charge->paid_amount);
        });

        $episodeTotalCharges = (float) $episodeCharges->sum('amount');
        $episodeTotalDiscount = (float) $episodeCharges->sum('discount_amount');
        $episodeTotalTax = (float) $episodeCharges->sum('tax_amount');
        $episodeTotalPaid = (float) $episodeCharges->sum('paid_amount');
        $episodeTotalDue = max(0, $episodeTotalCharges - $episodeTotalPaid);

        $allPatientPayments = PatientPayment::query()
            ->where('patient_id', $allocation->patient_id)
            ->get();

        $allPatientAllocations = PatientPaymentAllocation::query()
            ->whereHas('charge', function ($query) use ($allocation) {
                $query->where('patient_id', $allocation->patient_id);
            })
            ->get();

        $advanceCredit = max(
            0,
            (float) $allPatientPayments->sum('amount') - (float) $allPatientAllocations->sum('amount')
        );

        $pendingChargeIds = $episodeCharges
            ->filter(function (PatientCharge $charge) {
                return (float) $charge->amount > (float) $charge->paid_amount;
            })
            ->pluck('id')
            ->values();

        $pendingEpisodeChargeIds = $pendingChargeIds;

        $allocationCharges = $episodeCharges
            ->where('source_type', BedAllocation::class)
            ->whereIn('source_id', $episodeAllocations->pluck('id')->all())
            ->values();

        $diagnosticItems = DiagnosticOrderItem::query()
            ->with(['order', 'patientCharge'])
            ->whereHas('order', function ($query) use ($episodeAllocations) {
                $query->where('visitable_type', BedAllocation::class)
                    ->whereIn('visitable_id', $episodeAllocations->pluck('id')->all());
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $pathologyOrders = $diagnosticItems->where('department', 'pathology')->values();
        $radiologyOrders = $diagnosticItems->where('department', 'radiology')->values();

        // OPD orders visible on IPD when patient was moved from OPD
        $opdPathologyOrders = collect();
        $opdRadiologyOrders = collect();
        if ($allocation->source_opd_patient_id) {
            $opdDiagnosticItems = DiagnosticOrderItem::query()
                ->with(['order', 'patientCharge'])
                ->whereHas('order', function ($query) use ($allocation) {
                    $query->where('visitable_type', OpdPatient::class)
                        ->where('visitable_id', $allocation->source_opd_patient_id);
                })
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get();
            $opdPathologyOrders = $opdDiagnosticItems->where('department', 'pathology')->values();
            $opdRadiologyOrders = $opdDiagnosticItems->where('department', 'radiology')->values();
        }

        $payments = PatientPayment::query()
            ->where('patient_id', $allocation->patient_id)
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get();

        $timeline = PatientTimeline::query()
            ->where('patient_id', $allocation->patient_id)
            ->where(function ($query) use ($allocation) {
                $query->where(function ($ipd) use ($allocation) {
                    $ipd->where('encounter_type', 'ipd')
                        ->where('encounter_id', $allocation->id);
                })->orWhere('encounter_type', 'general');
            })
            ->orderByDesc('logged_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $history = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $allocation->patient_id)
            ->with(['bed.room.ward', 'consultantDoctor', 'dischargedBy'])
            ->orderByDesc('admission_date')
            ->get();

        $progressNotes = IpdProgressNote::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('bed_allocation_id', $allocation->id)
            ->with('creator:id,name')
            ->orderByDesc('noted_at')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $prescriptions = IpdPrescription::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('bed_allocation_id', $allocation->id)
            ->with([
                'doctor:id,first_name,last_name',
                'items:id,ipd_prescription_id',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('hospital.ipd-patient.show', [
            'pathurl' => 'ipd-patient',
            'routes' => $this->routes,
            'allocation' => $allocation,
            'outstandingAmount' => $episodeOutstandingAmount,
            'episodeTotalCharges' => $episodeTotalCharges,
            'episodeTotalDiscount' => $episodeTotalDiscount,
            'episodeTotalTax' => $episodeTotalTax,
            'episodeTotalPaid' => $episodeTotalPaid,
            'episodeTotalDue' => $episodeTotalDue,
            'advanceCredit' => $advanceCredit,
            'allocationCharges' => $allocationCharges,
            'episodeCharges' => $episodeCharges,
            'episodeAllocations' => $episodeAllocations,
            'pendingChargeIds' => $pendingChargeIds,
            'pendingEpisodeChargeIds' => $pendingEpisodeChargeIds,
            'payments' => $payments,
            'timeline' => $timeline,
            'history' => $history,
            'familyHistoryOptions' => Disease::orderBy('name')->pluck('name')->map(fn ($n) => strtolower(trim($n)))->filter()->values()->toArray(),
            'progressNotes' => $progressNotes,
            'prescriptions' => $prescriptions,
            'pathologyOrders' => $pathologyOrders,
            'radiologyOrders' => $radiologyOrders,
            'opdPathologyOrders' => $opdPathologyOrders,
            'opdRadiologyOrders' => $opdRadiologyOrders,
        ]);
    }

    public function storeNote(Request $request, BedAllocation $allocation, PatientTimelineService $timelineService)
    {
        $allocation = $this->findAllocation($allocation->id);

        $validator = Validator::make($request->all(), [
            'note_type' => 'required|in:doctor,nursing,progress,discharge_plan',
            'note' => 'required|string',
            'noted_at' => 'nullable|date_format:d-m-Y H:i',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $notedAt = $request->filled('noted_at')
            ? Carbon::createFromFormat('d-m-Y H:i', $request->noted_at)
            : now();

        $note = IpdProgressNote::create([
            'hospital_id' => $this->hospital_id,
            'bed_allocation_id' => $allocation->id,
            'patient_id' => $allocation->patient_id,
            'note_type' => $request->note_type,
            'note' => $request->note,
            'noted_at' => $notedAt,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $timelineService->logForIpdAdmission($allocation, [
            'event_key' => 'ipd.note.created',
            'title' => strtoupper($request->note_type) . ' Note Added',
            'description' => $request->note,
            'meta' => [
                'note_id' => $note->id,
                'note_type' => $note->note_type,
            ],
            'logged_at' => $notedAt,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Progress note saved successfully.',
        ]);
    }

    public function updateClinicalSnapshot(Request $request, BedAllocation $allocation, PatientTimelineService $timelineService)
    {
        $allocation = $this->findAllocation($allocation->id);

        if ($allocation->discharge_date) {
            return response()->json([
                'status' => false,
                'message' => 'Discharged admission clinical snapshot cannot be edited.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'bp' => 'nullable|string|max:50',
            'pulse' => 'nullable|string|max:50',
            'temperature' => 'nullable|string|max:50',
            'respiration' => 'nullable|string|max:50',
            'systolic_bp' => 'nullable|string|max:50',
            'diastolic_bp' => 'nullable|string|max:50',
            'spo2' => 'nullable|string|max:50',
            'diabetes' => 'nullable|string|max:50',
            'bmi' => 'nullable|string|max:50',
            'family_history' => 'nullable|array',
            'family_history.*' => 'nullable|string|max:100',
            'family_history_other' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $familyHistoryValues = collect($request->input('family_history', []))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        if ($request->filled('family_history_other')) {
            collect(explode(',', (string) $request->family_history_other))
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->each(function ($item) use ($familyHistoryValues) {
                    if (!$familyHistoryValues->contains($item)) {
                        $familyHistoryValues->push($item);
                    }
                });
        }

        $allocation->update([
            'height' => $request->height,
            'weight' => $request->weight,
            'bp' => $request->bp,
            'pulse' => $request->pulse,
            'temperature' => $request->temperature,
            'respiration' => $request->respiration,
            'systolic_bp' => $request->systolic_bp,
            'diastolic_bp' => $request->diastolic_bp,
            'spo2' => $request->spo2,
            'diabetes' => $request->diabetes,
            'bmi' => $request->bmi,
            'family_history' => $familyHistoryValues->implode(', '),
        ]);

        // $timelineService->logForIpdAdmission($allocation, [
        //     'event_key' => 'ipd.clinical_snapshot.updated',
        //     'title' => 'Clinical Snapshot Updated',
        //     'description' => 'Vitals and family history details were updated.',
        //     'meta' => [
        //         'height' => $allocation->height,
        //         'weight' => $allocation->weight,
        //         'bp' => $allocation->bp,
        //         'pulse' => $allocation->pulse,
        //     ],
        // ]);

        return response()->json([
            'status' => true,
            'message' => 'Clinical snapshot updated successfully.',
        ]);
    }

    public function doctorCareUnified(BedAllocation $allocation)
    {
        $allocation = $this->findAllocation($allocation->id);

        return view('hospital.ipd-patient.doctor-care.unified', [
            'allocation' => $allocation,
            'patient' => $allocation->patient,
            'canPathology' => auth()->user()->can('create-pathology-order'),
            'canRadiology' => auth()->user()->can('create-radiology-order'),
        ]);
    }

    public function showDiagnosticOrderForm(Request $request, BedAllocation $allocation)
    {
        $allocation = $this->findAllocation($allocation->id);

        $orderType = $request->get('order_type');
        if (!in_array($orderType, ['pathology', 'radiology'], true)) {
            abort(422, 'Invalid diagnostic order type.');
        }

        $this->authorizeOrderType($orderType);

        $tests = $orderType === 'pathology'
            ? PathologyTest::with(['category:id,name', 'parameters:id,name', 'chargeMaster.tpaRates'])->orderBy('test_name')->get()
            : RadiologyTest::with(['category:id,name', 'parameters:id,name', 'chargeMaster.tpaRates'])->orderBy('test_name')->get();

        $tests = $tests->map(function ($test) use ($allocation) {
            $test->resolved_charge = $this->resolveTestCharge($test, $allocation->tpa_id ? (int) $allocation->tpa_id : null);
            return $test;
        });

        return view('hospital.ipd-patient.partials.diagnostic_order_form', [
            'allocation' => $allocation,
            'orderType' => $orderType,
            'tests' => $tests,
        ]);
    }

    public function storeDiagnosticOrder(
        Request $request,
        BedAllocation $allocation,
        ChargeLedgerService $chargeLedger,
        PatientTimelineService $timelineService
    ) {
        $allocation = $this->findAllocation($allocation->id);

        if ($allocation->discharge_date) {
            return response()->json(['status' => false, 'message' => 'Discharged admission cannot be modified.'], 422);
        }

        $orderType = $request->order_type;
        if (!in_array($orderType, ['pathology', 'radiology'], true)) {
            return response()->json(['status' => false, 'message' => 'Invalid diagnostic order type.'], 422);
        }

        $this->authorizeOrderType($orderType);

        $priorityValue = $orderType === 'pathology' ? (string) $request->input('priority', 'Routine') : 'Routine';

        $validator = Validator::make($request->all(), [
            'order_type' => 'required|in:pathology,radiology',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'required|integer',
            'priority' => 'required|in:Routine,Urgent,STAT',
            'notes' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request, $orderType) {
            $testIds = collect($request->test_ids ?? [])->filter()->map(fn ($id) => (int) $id)->unique()->values();
            if ($testIds->isEmpty()) {
                $validator->errors()->add('test_ids', 'Please select at least one test.');
                return;
            }

            $query = $orderType === 'pathology' ? PathologyTest::query() : RadiologyTest::query();
            $validCount = $query->whereIn('id', $testIds)->count();
            if ($validCount !== $testIds->count()) {
                $validator->errors()->add('test_ids', 'One or more selected tests are invalid.');
            }

            $missingChargeMasterTests = $query->whereIn('id', $testIds)
                ->whereNull('charge_master_id')
                ->pluck('test_name')
                ->all();

            if (!empty($missingChargeMasterTests)) {
                $validator->errors()->add('test_ids', 'Charge master not mapped for: ' . implode(', ', $missingChargeMasterTests));
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $testIds = collect($request->test_ids)->map(fn ($id) => (int) $id)->unique()->values();
        $tests = $orderType === 'pathology'
            ? PathologyTest::with(['category:id,name', 'parameters.unit:id,name', 'chargeMaster.tpaRates'])->whereIn('id', $testIds)->get()
            : RadiologyTest::with(['category:id,name', 'parameters.unit:id,name', 'chargeMaster.tpaRates'])->whereIn('id', $testIds)->get();

        $order = DB::transaction(function () use ($allocation, $request, $orderType, $tests, $chargeLedger, $priorityValue) {
            $order = DiagnosticOrder::create([
                'hospital_id' => $this->hospital_id,
                'patient_id' => $allocation->patient_id,
                'visitable_type' => BedAllocation::class,
                'visitable_id' => $allocation->id,
                'order_type' => $orderType,
                'order_no' => $this->generateDiagnosticOrderNo($orderType),
                'ordered_by' => auth()->id(),
                'notes' => $request->notes,
                'status' => 'ordered',
            ]);

            foreach ($tests as $test) {
                $resolvedCharge = $this->resolveTestCharge($test, $allocation->tpa_id ? (int) $allocation->tpa_id : null);

                $item = $order->items()->create([
                    'department' => $orderType,
                    'testable_type' => get_class($test),
                    'testable_id' => $test->id,
                    'test_name' => $test->test_name,
                    'test_code' => $test->test_code,
                    'category_name' => optional($test->category)->name,
                    'priority' => $priorityValue,
                    'sample_type' => $test->sample_type ?? null,
                    'method' => $test->method,
                    'expected_report_days' => $test->report_days,
                    'standard_charge' => $resolvedCharge,
                    'status' => 'ordered',
                ]);

                $chargeLedger->upsertCharge([
                    'hospital_id' => $this->hospital_id,
                    'patient_id' => $allocation->patient_id,
                    'visitable_type' => BedAllocation::class,
                    'visitable_id' => $allocation->id,
                    'source_type' => DiagnosticOrderItem::class,
                    'source_id' => $item->id,
                    'module' => $orderType,
                    'particular' => strtoupper($orderType) . ' - ' . $test->test_name,
                    'charge_master_id' => $test->charge_master_id,
                    'charge_category' => $orderType,
                    'calculation_type' => 'fixed',
                    'billing_frequency' => 'one_time',
                    'quantity' => 1,
                    'unit_rate' => $resolvedCharge,
                    'net_amount' => $resolvedCharge,
                    'payer_type' => $allocation->tpa_id ? 'tpa' : 'self',
                    'tpa_id' => $allocation->tpa_id,
                    'charged_at' => now(),
                ]);

                foreach ($test->parameters as $index => $parameter) {
                    $item->parameters()->create([
                        'parameterable_type' => get_class($parameter),
                        'parameterable_id' => $parameter->id,
                        'parameter_name' => $parameter->name,
                        'unit_name' => optional($parameter->unit)->name,
                        'normal_range' => $parameter->range,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $order;
        });

        $timelineService->logForIpdAdmission($allocation, [
            'event_key' => 'ipd.diagnostic_order.created',
            'title' => ucfirst($orderType) . ' Order Placed',
            'description' => ucfirst($orderType) . ' order ' . $order->order_no . ' created with ' . $tests->count() . ' test(s).',
            'meta' => [
                'order_type' => $orderType,
                'order_no' => $order->order_no,
                'priority' => $priorityValue,
                'test_count' => $tests->count(),
            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => ucfirst($orderType) . ' test order created successfully.',
            'order_no' => $order->order_no,
        ]);
    }

    public function destroyDiagnosticOrder(BedAllocation $allocation, DiagnosticOrderItem $item, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        $allocation = $this->findAllocation($allocation->id);

        if ($allocation->discharge_date) {
            return response()->json([
                'status' => false,
                'message' => 'Discharged admission orders cannot be deleted.',
            ], 422);
        }

        if ((int) ($item->order->visitable_id ?? 0) !== (int) $allocation->id || (string) ($item->order->visitable_type ?? '') !== BedAllocation::class) {
            return response()->json(['status' => false, 'message' => 'Diagnostic item does not belong to this IPD admission.'], 422);
        }

        $this->authorizeDeleteOrderType((string) $item->department);

        $statusKey = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
        if (in_array($statusKey, ['in_progress', 'completed'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'In-progress or completed tests cannot be deleted.',
            ], 422);
        }

        $deletedItemName = $item->test_name;
        $deletedDepartment = $item->department;

        $reversalSummary = DB::transaction(function () use ($item, $chargeLedger) {
            $charge = PatientCharge::query()
                ->where('source_type', DiagnosticOrderItem::class)
                ->where('source_id', $item->id)
                ->first();

            $summary = [
                'released_amount' => 0.0,
                'unallocated_credit' => 0.0,
            ];

            if ($charge) {
                $summary = $chargeLedger->removeChargeAndRebalance($charge);
            }

            $order = $item->order;
            $item->parameters()->delete();
            $item->delete();

            if ($order && !$order->items()->exists()) {
                $order->delete();
            }

            return $summary;
        });

        $timelineService->logForIpdAdmission($allocation, [
            'event_key' => 'ipd.diagnostic_order.deleted',
            'title' => ucfirst((string) $deletedDepartment) . ' Test Removed',
            'description' => ($deletedItemName ?: 'Diagnostic test') . ' has been removed from this admission.',
            'meta' => [
                'order_type' => $deletedDepartment,
                'test_name' => $deletedItemName,
                'released_paid_amount' => (float) ($reversalSummary['released_amount'] ?? 0),
                'available_credit_after_delete' => (float) ($reversalSummary['unallocated_credit'] ?? 0),
            ],
        ]);

        return response()->json(['status' => true, 'message' => 'Diagnostic test deleted successfully.']);
    }

    public function printDischargeSummary(BedAllocation $allocation)
    {
        $allocation = $this->findAllocation($allocation->id);
        $hospital = Hospital::query()->find($this->hospital_id);

        $notes = IpdProgressNote::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('bed_allocation_id', $allocation->id)
            ->orderBy('noted_at')
            ->orderBy('id')
            ->get();

        return view('hospital.ipd-patient.discharge-summary-print', [
            'allocation' => $allocation,
            'hospital' => $hospital,
            'notes' => $notes,
            'autoprint' => request()->boolean('autoprint'),
        ]);
    }

    public function printFinalBill(BedAllocation $allocation)
    {
        $allocation = $this->findAllocation($allocation->id);

        $episodeAllocations = $this->resolveEpisodeAllocations($allocation);
        $episodeCharges = $this->episodeChargeQuery($allocation, $episodeAllocations)
            ->orderBy('charged_at')
            ->orderBy('id')
            ->get();

        $episodeChargeIds = $episodeCharges->pluck('id')->all();

        $paymentAllocations = PatientPaymentAllocation::query()
            ->with(['payment', 'charge'])
            ->when(!empty($episodeChargeIds), function ($query) use ($episodeChargeIds) {
                $query->whereIn('patient_charge_id', $episodeChargeIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $allocationIds = $episodeAllocations->pluck('id')->all();
        $paymentIds = $paymentAllocations->pluck('payment_id')->filter()->unique()->values()->all();

        $payments = PatientPayment::query()
            ->where('patient_id', $allocation->patient_id)
            ->where(function ($query) use ($paymentIds, $allocationIds) {
                if (!empty($paymentIds)) {
                    $query->whereIn('id', $paymentIds);
                }

                $query->orWhere(function ($ipdPaymentQuery) use ($allocationIds) {
                    $ipdPaymentQuery->where('visitable_type', BedAllocation::class)
                        ->whereIn('visitable_id', $allocationIds);
                });
            })
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get()
            ->unique('id')
            ->values();

        $totalCharges = (float) $episodeCharges->sum('amount');
        $totalDiscount = (float) $episodeCharges->sum('discount_amount');
        $totalTax = (float) $episodeCharges->sum('tax_amount');
        $totalPaid = (float) $paymentAllocations->sum('amount');
        $totalDue = max(0, $totalCharges - $totalPaid);

        $allPatientPayments = PatientPayment::query()
            ->where('patient_id', $allocation->patient_id)
            ->sum('amount');
        $allPatientAllocations = PatientPaymentAllocation::query()
            ->whereHas('charge', function ($query) use ($allocation) {
                $query->where('patient_id', $allocation->patient_id);
            })
            ->sum('amount');

        $advanceCredit = max(0, (float) $allPatientPayments - (float) $allPatientAllocations);

        $hospital = Hospital::query()->find($this->hospital_id);
        $printTemplate = HeaderFooter::query()
            ->where('type', 'ipd_bill')
            ->first();

        return view('hospital.ipd-patient.final-bill-print', [
            'allocation' => $allocation,
            'episodeAllocations' => $episodeAllocations,
            'episodeCharges' => $episodeCharges,
            'paymentAllocations' => $paymentAllocations,
            'payments' => $payments,
            'totalCharges' => $totalCharges,
            'totalDiscount' => $totalDiscount,
            'totalTax' => $totalTax,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
            'advanceCredit' => $advanceCredit,
            'hospital' => $hospital,
            'printTemplate' => $printTemplate,
        ]);
    }

    public function showTransferForm(BedAllocation $allocation)
    {
        $allocation = $this->findAllocation($allocation->id);

        if ($allocation->discharge_date) {
            return response('Admission already discharged.', 422);
        }

        $availableBeds = $this->availableBedsQuery()
            ->where('beds.id', '!=', $allocation->bed_id)
            ->get();

        return view('hospital.ipd-patient.partials.transfer-form', [
            'allocation' => $allocation,
            'availableBeds' => $availableBeds,
        ]);
    }

    public function transfer(Request $request, BedAllocation $allocation, BedAllocationService $bedAllocationService, ChargeLedgerService $chargeLedger)
    {
        $allocation = $this->findAllocation($allocation->id);

        $validator = Validator::make($request->all(), [
            'new_bed_id' => 'required|exists:beds,id',
            'transfer_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $staffId = Staff::query()->where('user_id', auth()->id())->value('id');
            $currentAllocationId = $allocation->id;

            $newAllocation = $bedAllocationService->transferBed(
                $this->hospital_id,
                $currentAllocationId,
                (int) $request->new_bed_id,
                $staffId,
                $request->transfer_reason,
                [
                    'admission_no' => $this->generateAdmissionNo(),
                    'consultant_doctor_id' => $allocation->consultant_doctor_id,
                    'hr_department_id' => $allocation->hr_department_id,
                    'tpa_id' => $allocation->tpa_id,
                    'source_opd_patient_id' => $allocation->source_opd_patient_id,
                    'admission_source' => 'transfer',
                    'tpa_reference_no' => $allocation->tpa_reference_no,
                    'expected_discharge_date' => $allocation->expected_discharge_date,
                    'admission_reason' => $allocation->admission_reason,
                    'provisional_diagnosis' => $allocation->provisional_diagnosis,
                    'height' => $allocation->height,
                    'weight' => $allocation->weight,
                    'bp' => $allocation->bp,
                    'pulse' => $allocation->pulse,
                    'temperature' => $allocation->temperature,
                    'respiration' => $allocation->respiration,
                    'systolic_bp' => $allocation->systolic_bp,
                    'diastolic_bp' => $allocation->diastolic_bp,
                    'spo2' => $allocation->spo2,
                    'diabetes' => $allocation->diabetes,
                    'bmi' => $allocation->bmi,
                ]
            );

            $this->syncBedCharge($this->findAllocation($currentAllocationId), $chargeLedger);
            $this->syncBedCharge($newAllocation, $chargeLedger);

            return response()->json([
                'status' => true,
                'message' => 'Bed transferred successfully.',
                'redirect_url' => route('hospital.ipd-patient.profile', ['allocation' => $newAllocation->id]),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage() ?: 'Unable to transfer bed.',
            ], 422);
        }
    }

    public function showDischargeForm(BedAllocation $allocation, ChargeLedgerService $chargeLedger)
    {
        $allocation = $this->findAllocation($allocation->id);
        $allocation = $this->syncBedCharge($allocation, $chargeLedger);

        $episodeAllocations = $this->resolveEpisodeAllocations($allocation);
        $outstandingAmount = (float) $this->episodeChargeQuery($allocation, $episodeAllocations)
            ->get()
            ->sum(function (PatientCharge $charge) {
                return max(0, (float) $charge->amount - (float) $charge->paid_amount);
            });

        return view('hospital.ipd-patient.partials.discharge-form', [
            'allocation' => $allocation,
            'outstandingAmount' => $outstandingAmount,
            'canDischarge' => $outstandingAmount <= 0,
        ]);
    }

    public function discharge(
        Request $request,
        BedAllocation $allocation,
        BedAllocationService $bedAllocationService,
        ChargeLedgerService $chargeLedger,
        PatientTimelineService $timelineService
    ) {
        $allocation = $this->findAllocation($allocation->id);

        $validator = Validator::make($request->all(), [
            'discharge_date' => 'required|date_format:d-m-Y H:i',
            'discharge_status' => 'required|in:recovered,referred,lama,expired,transferred',
            'discharge_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $staffId = Staff::query()->where('user_id', auth()->id())->value('id');
            $dischargeDate = Carbon::createFromFormat('d-m-Y H:i', $request->discharge_date);

            $allocation = $this->syncBedCharge($allocation, $chargeLedger);
            $episodeAllocations = $this->resolveEpisodeAllocations($allocation);
            $outstandingAmount = (float) $this->episodeChargeQuery($allocation, $episodeAllocations)
                ->get()
                ->sum(function (PatientCharge $charge) {
                    return max(0, (float) $charge->amount - (float) $charge->paid_amount);
                });

            if ($outstandingAmount > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please clear IPD outstanding bill before discharge. Pending due: ' . number_format($outstandingAmount, 2),
                ], 422);
            }

            $allocation = $bedAllocationService->dischargeBed(
                $allocation->id,
                $staffId,
                $request->discharge_status,
                $request->discharge_notes,
                ['discharge_date' => $dischargeDate]
            );

            $allocation = $this->syncBedCharge($allocation, $chargeLedger);

            $timelineService->logForIpdAdmission($allocation, [
                'event_key' => 'ipd.discharge.summary',
                'title' => 'Discharge Summary Updated',
                'description' => $request->discharge_notes ?: 'Discharge process completed.',
                'meta' => [
                    'discharge_status' => $request->discharge_status,
                ],
                'logged_at' => $dischargeDate,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Patient discharged successfully.',
                'redirect_url' => route('hospital.ipd-patient.profile', ['allocation' => $allocation->id]),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage() ?: 'Unable to discharge patient.',
            ], 422);
        }
    }

    private function availableBedsQuery()
    {
        return Bed::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('bed_status_id', BedStatus::AVAILABLE)
            ->with(['room.ward.floor', 'bedType.chargeMaster'])
            ->orderBy('room_id')
            ->orderBy('bed_number');
    }

    private function findAllocation(int $allocationId): BedAllocation
    {
        return BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->with([
                'patient',
                'bed.room.ward.floor',
                'bed.bedType.chargeMaster',
                'consultantDoctor',
                'department',
                'tpa',
                'sourceOpdPatient',
                'admittedBy',
                'dischargedBy',
                'charges',
            ])
            ->findOrFail($allocationId);
    }

    private function upsertPatient(Request $request): Patient
    {
        if ($request->filled('selected_patient_id')) {
            $patient = Patient::query()
                ->where('hospital_id', $this->hospital_id)
                ->findOrFail($request->selected_patient_id);
        } else {
            $patient = Patient::query()
                ->where('hospital_id', $this->hospital_id)
                ->where('phone', $request->phone)
                ->first();

            if (!$patient) {
                $patient = new Patient();
                $patient->hospital_id = $this->hospital_id;
                $patient->patient_id = $this->generateHospitalWisePatientId();
            }
        }

        $patient->name = $request->name;
        $patient->guardian_name = $request->guardian_name;
        $patient->date_of_birth = $request->filled('date_of_birth')
            ? Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d')
            : null;
        $patient->age_years = $request->age_years;
        $patient->age_months = $request->age_months ?? 0;
        $patient->country_code = $request->country_code;
        $patient->phone = $request->phone;
        $patient->email = $request->email;
        $patient->gender = $request->gender;
        $patient->blood_group = $request->blood_group;
        $patient->marital_status = $request->marital_status;
        $patient->patient_category_id = $request->patient_category_id;
        $patient->nationality_id = $request->nationality_id;
        $patient->religion_id = $request->religion_id;
        $patient->address = $request->address;
        $patient->save();

        return $patient;
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
        } while (Patient::query()->where('patient_id', $patientCode)->exists());

        return $patientCode;
    }

    private function generateAdmissionNo(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'IPD' . str_pad((string) $this->hospital_id, 4, '0', STR_PAD_LEFT) . $datePart;

        $lastAllocation = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('admission_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastAllocation && str_starts_with((string) $lastAllocation->admission_no, $prefix)) {
            $nextNumber = ((int) substr((string) $lastAllocation->admission_no, -4)) + 1;
        }

        do {
            $admissionNo = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (BedAllocation::query()->where('admission_no', $admissionNo)->exists());

        return $admissionNo;
    }

    private function syncBedCharge(BedAllocation $allocation, ChargeLedgerService $chargeLedger): BedAllocation
    {
        $allocation = $this->findAllocation($allocation->id);

        $bedType = $allocation->bed?->bedType;
        if (!$bedType) {
            return $allocation;
        }

        $admissionDate = Carbon::parse($allocation->admission_date);
        $endDate = $allocation->discharge_date ? Carbon::parse($allocation->discharge_date) : now();
        $quantity = max(1, $admissionDate->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay()) + 1);
        $chargeMaster = $bedType->chargeMaster;
        $billingFrequency = $chargeMaster?->billing_frequency ?? 'per_day';
        $calculationType = $chargeMaster?->calculation_type ?? 'fixed';
        $effectiveQuantity = $billingFrequency === 'one_time' ? 1 : $quantity;

        $chargeAttributes = [
            'hospital_id' => $allocation->hospital_id,
            'patient_id' => $allocation->patient_id,
            'visitable_type' => BedAllocation::class,
            'visitable_id' => $allocation->id,
            'source_type' => BedAllocation::class,
            'source_id' => $allocation->id,
            'module' => 'ipd',
            'particular' => 'Bed Charge - ' . ($allocation->bed?->getFullBedIdentifier() ?: 'IPD Bed'),
            'charge_master_id' => $chargeMaster?->id,
            'charge_category' => $chargeMaster?->category ?? 'bed_charge',
            'calculation_type' => $calculationType,
            'billing_frequency' => $billingFrequency,
            'quantity' => $effectiveQuantity,
            'payer_type' => $allocation->tpa_id ? 'tpa' : 'self',
            'tpa_id' => $allocation->tpa_id,
            'charged_at' => $allocation->admission_date,
        ];

        if (!$chargeMaster) {
            $baseAmount = (float) $bedType->base_charge * $effectiveQuantity;
            $chargeAttributes['unit_rate'] = (float) $bedType->base_charge;
            $chargeAttributes['amount'] = $baseAmount;
            $chargeAttributes['net_amount'] = $baseAmount;
        }

        $charge = $chargeLedger->upsertCharge($chargeAttributes);

        return $allocation->fresh(['patient', 'bed.room.ward.floor', 'bed.bedType.chargeMaster', 'consultantDoctor', 'department', 'tpa', 'charges']);
    }

    private function resolveEpisodeAllocations(BedAllocation $allocation): Collection
    {
        $history = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('patient_id', $allocation->patient_id)
            ->orderByDesc('admission_date')
            ->orderByDesc('id')
            ->get();

        $index = $history->search(fn (BedAllocation $entry) => (int) $entry->id === (int) $allocation->id);
        if ($index === false) {
            return collect([$allocation]);
        }

        $episode = collect([$history[$index]]);
        for ($i = $index + 1; $i < $history->count(); $i++) {
            $candidate = $history[$i];
            if ((string) $candidate->discharge_status !== 'transferred') {
                break;
            }
            $episode->push($candidate);
        }

        return $episode->sortBy('admission_date')->values();
    }

    private function episodeChargeQuery(BedAllocation $allocation, Collection $episodeAllocations)
    {
        $allocationIds = $episodeAllocations->pluck('id')->all();
        $diagnosticItemIds = DiagnosticOrderItem::query()
            ->whereHas('order', function ($query) use ($allocationIds) {
                $query->where('visitable_type', BedAllocation::class)
                    ->whereIn('visitable_id', $allocationIds);
            })
            ->pluck('id')
            ->all();

        return PatientCharge::query()
            ->where('patient_id', $allocation->patient_id)
            ->where(function ($query) use ($allocation, $allocationIds, $diagnosticItemIds) {
                $query->where(function ($bedQuery) use ($allocationIds) {
                    $bedQuery->where('visitable_type', BedAllocation::class)
                        ->whereIn('visitable_id', $allocationIds);
                });

                if ((int) $allocation->source_opd_patient_id > 0) {
                    $query->orWhere(function ($opdQuery) use ($allocation) {
                        $opdQuery->where('visitable_type', OpdPatient::class)
                            ->where('visitable_id', (int) $allocation->source_opd_patient_id);
                    });
                }

                if (!empty($diagnosticItemIds)) {
                    $query->orWhere(function ($diagQuery) use ($diagnosticItemIds) {
                        $diagQuery->where('source_type', DiagnosticOrderItem::class)
                            ->whereIn('source_id', $diagnosticItemIds);
                    });
                }
            });
    }

    private function authorizeOrderType(string $orderType): void
    {
        $permission = $orderType === 'pathology' ? 'create-pathology-order' : 'create-radiology-order';
        abort_unless(auth()->user()->can($permission), 403, 'Unauthorized action.');
    }

    private function authorizeDeleteOrderType(string $orderType): void
    {
        $permission = $orderType === 'pathology' ? 'delete-pathology-order' : 'delete-radiology-order';
        abort_unless(auth()->user()->can($permission), 403, 'Unauthorized action.');
    }

    private function generateDiagnosticOrderNo(string $orderType): string
    {
        $prefix = $orderType === 'pathology' ? 'IPDPAT' : 'IPDRAD';
        $date = now()->format('Ymd');
        $sequence = DiagnosticOrder::withoutGlobalScopes()
            ->where('hospital_id', $this->hospital_id)
            ->where('order_type', $orderType)
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    private function resolveTestCharge(object $test, ?int $tpaId = null): float
    {
        $chargeMaster = $test->chargeMaster ?? null;
        if (!$chargeMaster) {
            return (float) ($test->standard_charge ?? 0);
        }

        if ($tpaId) {
            $tpaRate = collect($chargeMaster->tpaRates ?? [])->firstWhere('tpa_id', $tpaId);
            if ($tpaRate && isset($tpaRate->rate)) {
                return (float) $tpaRate->rate;
            }
        }

        return (float) $chargeMaster->standard_rate;
    }
}

