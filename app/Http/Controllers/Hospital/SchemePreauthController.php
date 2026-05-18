<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\AdmissionDetails;
use App\Models\AuthenticationConsent;
use App\Models\BedAllocation;
use App\Models\FamilyHistory;
use App\Models\GeneralInfo;
use App\Models\HospitalSpeciality;
use App\Models\Staff;
use App\Models\Patient;
use App\Models\PersonalHistory;
use App\Models\PreauthCareTeam;
use App\Models\PreauthDiagnosis;
use App\Models\PreauthDiagnosisMaster;
use App\Models\PreauthEnhancementDoc;
use App\Models\PreauthInvestigation;
use App\Models\PreauthProcedure;
use App\Models\PreauthRegister;
use App\Models\Speciality;
use App\Models\TreatmentPlanImplant;
use App\Models\TreatmentPlanProcedure;
use App\Models\TreatmentPlanStratification;
use App\CentralLogics\Helpers;
use App\Services\SchemePreauthRegisterService;
use App\Support\SchemePreauthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SchemePreauthController extends Controller
{
    protected int $hospital_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->hospital_id = (int) auth()->user()->hospital_id;

            return $next($request);
        });
    }

    /**
     * Open or create a draft scheme preauth for an active IPD allocation (Patient 360).
     */
    public function start(Request $request, SchemePreauthRegisterService $schemePreauthRegisterService)
    {
        $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'bed_allocation_id' => 'required|integer|exists:bed_allocations,id',
        ]);

        $allocation = BedAllocation::query()
            ->whereKey((int) $request->input('bed_allocation_id'))
            ->where('hospital_id', $this->hospital_id)
            ->firstOrFail();

        abort_unless((int) $allocation->patient_id === (int) $request->input('patient_id'), 403);
        abort_unless(filled($allocation->scheme_type_id), 422, 'This admission has no government scheme payer linked.');

        $patient = Patient::query()->whereKey($allocation->patient_id)->firstOrFail();

        $register = PreauthRegister::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('bed_allocation_id', $allocation->id)
            ->where('status', PreauthRegister::STATUS_REGISTER)
            ->first();

        if (! $register) {
            $register = $schemePreauthRegisterService->createOrGetDraftForAdmission(
                $this->hospital_id,
                $patient,
                $allocation,
                [
                    'scheme_type_id' => $allocation->scheme_type_id,
                    'kyc_type' => $allocation->scheme_kyc_type,
                    'is_new_born_baby' => $allocation->scheme_is_newborn ? 1 : 0,
                ]
            );
        }

        return redirect()->route('hospital.patient-management.scheme-preauth.show', ['preauthRegister' => $register->id]);
    }

    public function show(PreauthRegister $preauthRegister)
    {
        $this->authorizeRegister($preauthRegister);
        Session::put('preauth_register_id', $preauthRegister->id);

        $patient = $preauthRegister->patient ?? Patient::query()->find($preauthRegister->patient_id);
        abort_unless($patient, 404);

        $allocation = $preauthRegister->bedAllocation ?? null;

        $general_info = GeneralInfo::query()->where('preauth_register_id', $preauthRegister->id)->first();

        $family_history = FamilyHistory::query()->where('preauth_register_id', $preauthRegister->id)->first();
        $personal_history = PersonalHistory::query()->where('preauth_register_id', $preauthRegister->id)->first();
        $authentication_consent = AuthenticationConsent::query()->where('preauth_register_id', $preauthRegister->id)->first();
        $admission_details = AdmissionDetails::query()->where('preauth_register_id', $preauthRegister->id)->first();

        $preauth_diagnosis = PreauthDiagnosis::query()->where('preauth_register_id', $preauthRegister->id)->get();
        $procedures = PreauthProcedure::query()->where('preauth_register_id', $preauthRegister->id)->get();

        $hospital_speciality = HospitalSpeciality::query()
            ->join('specialities', 'specialities.id', '=', 'hospital_specialities.speciality_id')
            ->where('hospital_specialities.hospital_id', $this->hospital_id)
            ->where('hospital_specialities.available', 1)
            ->select([
                'hospital_specialities.id as hospital_speciality_id',
                'hospital_specialities.speciality_id',
                'specialities.name',
                'specialities.code',
            ])
            ->orderBy('specialities.name')
            ->get();

        $us = Speciality::query()->where('name', 'Unspecified Surgical Package')->where('code', 'US')->first();

        $careTeamDoctors = $this->careTeamDoctorsQuery()->get();

        $preauth_investigations = PreauthInvestigation::query()->where('preauth_register_id', $preauthRegister->id)->get();
        $investigations = SchemePreauthHelper::getInvestigations($preauthRegister->id);
        $post_investigations = collect();
        $preauth_investigation_status = SchemePreauthHelper::getPreauthInvestigationsStatus($preauthRegister->id);
        $preauth_teams = $this->preauthTeamsQuery($preauthRegister->id)->get();

        $preauthBeneficiary = (object) [
            'image_url' => $patient->image ? asset('storage/'.$patient->image) : null,
            'name' => $patient->full_name,
            'age' => $patient->age_years,
            'gender' => $patient->gender,
            'care_plan' => optional($allocation?->schemeType)->name ?? '—',
            'card_id' => $patient->ayushman_bharat_id ?? $patient->abha_number ?? '—',
            'aabha_id' => $patient->abha_number ?? '—',
            'mobile_no' => $patient->mobile_no ?? $patient->phone ?? '—',
            'address' => $patient->address ?? '—',
        ];

        $schemePreauthAfterSubmitUrl = route('hospital.patient-management.patient-360', ['patient' => $patient->id]);

        $case_profile = $preauthRegister->id;

        if ((int) $preauthRegister->status !== PreauthRegister::STATUS_REGISTER) {
            return view('hospital.scheme-preauth.submitted', compact(
                'preauthRegister',
                'preauthBeneficiary',
                'patient',
                'schemePreauthAfterSubmitUrl'
            ));
        }

        return view('hospital.scheme-preauth.preauth-request', compact(
            'preauthRegister',
            'case_profile',
            'general_info',
            'family_history',
            'personal_history',
            'authentication_consent',
            'admission_details',
            'preauth_diagnosis',
            'hospital_speciality',
            'us',
            'procedures',
            'careTeamDoctors',
            'preauth_teams',
            'investigations',
            'post_investigations',
            'preauth_investigations',
            'preauth_investigation_status',
            'preauthBeneficiary',
            'schemePreauthAfterSubmitUrl',
            'patient'
        ));
    }

    protected function authorizeRegister(PreauthRegister $preauthRegister): void
    {
        abort_unless((int) $preauthRegister->hospital_id === $this->hospital_id, 403);
    }

    protected function sessionRegisterId(): int
    {
        $id = (int) Session::get('preauth_register_id');
        abort_if($id <= 0, 403);

        return $id;
    }

    protected function careTeamDoctorsQuery()
    {
        return Staff::query()
            ->where('hospital_id', $this->hospital_id)
            ->doctor()
            ->active()
            ->with(['designation', 'specialist'])
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    protected function preauthTeamsQuery(int $preauthRegisterId)
    {
        return PreauthCareTeam::query()
            ->where('preauth_register_id', $preauthRegisterId)
            ->with(['staff.designation', 'staff.specialist', 'hospital_team']);
    }

    public function generalInformation(Request $request)
    {
        $request->validate([
            'temprature' => 'required',
            'pulserate' => 'required',
            'height' => 'required',
        ]);
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $general_info = GeneralInfo::query()->firstOrNew(['preauth_register_id' => $preauth_register_id]);
        $general_info->preauth_register_id = $preauth_register_id;
        $general_info->temprature = $request->temprature;
        $general_info->pulserate = $request->pulserate;
        $general_info->height = $request->height;
        $general_info->weight = $request->weight;
        $general_info->bmi = $request->bmi;
        $general_info->cyanosis = $request->cyanosis ?? 'No';
        $general_info->pallor = $request->pallor ?? 'No';
        $general_info->malnutration = $request->malnutration ?? 'No';
        $general_info->oedema = $request->oedema ?? 'No';
        $general_info->save();

        return response()->json([
            'success' => true,
            'message' => 'General Information Saved Successfully!',
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function familyHistory(Request $request)
    {
        $request->validate([
            'diabetes' => 'required',
            'hypertension' => 'required',
            'heartdisease' => 'required',
            'stroke' => 'required',
            'cancer' => 'required',
            'tuberculosis' => 'required',
            'asthma' => 'required',
        ]);
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $family_history = FamilyHistory::query()->firstOrNew(['preauth_register_id' => $preauth_register_id]);
        $family_history->preauth_register_id = $preauth_register_id;
        $family_history->diabetes_id = $request->diabetes;
        $family_history->hypertension_id = $request->hypertension;
        $family_history->heartdisease_id = $request->heartdisease;
        $family_history->stroke_id = $request->stroke;
        $family_history->cancer_id = $request->cancer;
        $family_history->tuberculosis_id = $request->tuberculosis;
        $family_history->asthma_id = $request->asthma;
        $family_history->save();

        return response()->json([
            'success' => true,
            'message' => 'Family History Saved Successfully!',
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function personalHistory(Request $request)
    {
        $request->validate([
            'appetite' => 'required',
            'bowels' => 'required',
            'nutrition' => 'required',
            'diet' => 'required',
            'known_allergies' => 'required',
            'allergy_detail' => 'required_if:known_allergies,Yes',
            'habits' => 'required',
            'habits_detail' => 'required_if:habits,Yes',
        ]);
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $personal_history = PersonalHistory::query()->firstOrNew(['preauth_register_id' => $preauth_register_id]);
        $personal_history->preauth_register_id = $preauth_register_id;
        $personal_history->appetite_id = $request->appetite;
        $personal_history->bowels_id = $request->bowels;
        $personal_history->nutrition_id = $request->nutrition;
        $personal_history->diet_id = $request->diet;
        $personal_history->known_allergies = $request->known_allergies;
        $personal_history->allergy_detail = $request->allergy_detail;
        $personal_history->habits = $request->habits;
        $personal_history->habits_detail = $request->habits_detail;
        $personal_history->save();

        return response()->json([
            'success' => true,
            'message' => 'Personal History Saved Successfully!',
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function authenticationConsent(Request $request)
    {
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $existing = AuthenticationConsent::query()->where('preauth_register_id', $preauth_register_id)->first();
        $request->validate([
            'hospital_declaration_form' => ($existing && $existing->hospital_declaration_form)
                ? 'nullable|file|mimes:pdf,xlsx,docx|max:2048'
                : 'required|file|mimes:pdf,xlsx,docx|max:2048',
        ]);

        $authentication_consent = AuthenticationConsent::query()->firstOrNew(['preauth_register_id' => $preauth_register_id]);
        $authentication_consent->preauth_register_id = $preauth_register_id;
        if ($request->hasFile('hospital_declaration_form')) {
            $authentication_consent->hospital_declaration_form = $request->file('hospital_declaration_form')->store('authentication', 'public');
        }
        $authentication_consent->remarks = $request->remarks;
        $authentication_consent->save();

        return response()->json([
            'success' => true,
            'message' => 'Authentication Consent Saved Successfully!',
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function admissionDetails(Request $request)
    {
        $request->validate([
            'admission_date' => 'required',
            'surgery_date' => 'required',
            'admission_type' => 'required',
            'legal_case' => 'required',
            'fir_doc' => 'required_if:legal_case,Yes|nullable|mimes:pdf|max:10240',
        ]);
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $admission_details = AdmissionDetails::query()->firstOrNew(['preauth_register_id' => $preauth_register_id]);
        if ($request->hasFile('fir_doc')) {
            $admission_details->fir_doc = $request->file('fir_doc')->store('fir', 'public');
        }
        $admission_details->preauth_register_id = $preauth_register_id;
        $admission_details->admission_date = $request->admission_date;
        $admission_details->surgery_date = $request->surgery_date;
        $admission_details->admission_type_id = $request->admission_type;
        $admission_details->legal_case = $request->legal_case;
        $admission_details->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin Details Saved Successfully!',
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function diagnosis(Request $request)
    {
        $diagnosis = PreauthDiagnosisMaster::query()->find($request->diagnosis_id);
        $request->validate([
            'diagnosis_id' => 'required',
            'diagnosis_type' => 'required',
            'other_diagnosis' => $diagnosis && $diagnosis->name === 'Other' ? 'required' : 'nullable',
        ]);
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        if ($diagnosis && $diagnosis->name === 'Other') {
            $preauth_diagnosis_row = new PreauthDiagnosis;
        } else {
            $preauth_diagnosis_row = PreauthDiagnosis::query()->firstOrNew([
                'preauth_register_id' => $preauth_register_id,
                'diagnosis_id' => $request->diagnosis_id,
            ]);
        }
        $preauth_diagnosis_row->preauth_register_id = $preauth_register_id;
        $preauth_diagnosis_row->diagnosis_id = $request->diagnosis_id;
        $preauth_diagnosis_row->diagnosis_type = $request->diagnosis_type;
        $preauth_diagnosis_row->other_diagnosis = $request->other_diagnosis;
        $preauth_diagnosis_row->save();

        $preauth_diagnosis = PreauthDiagnosis::query()->where('preauth_register_id', $preauth_register_id)->get();
        $html = view('hospital.scheme-preauth._partials.diagnosis', ['preauth_diagnosis' => $preauth_diagnosis])->render();

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis Saved Successfully!',
            'html' => $html,
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function deleteDiagnosis(Request $request)
    {
        $preauth_diagnosis = PreauthDiagnosis::query()->whereKey($request->id)->firstOrFail();
        $this->authorizeRegister(PreauthRegister::query()->findOrFail($preauth_diagnosis->preauth_register_id));
        $preauth_register_id = $preauth_diagnosis->preauth_register_id;
        $preauth_diagnosis->delete();
        $preauth_diagnosis = PreauthDiagnosis::query()->where('preauth_register_id', $preauth_register_id)->get();
        $html = view('hospital.scheme-preauth._partials.diagnosis', ['preauth_diagnosis' => $preauth_diagnosis])->render();

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis Delete Successfully!',
            'html' => $html,
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function getProcedures(Request $request)
    {
        $specialityId = (int) $request->id;
        $html = '<option value="">Select Procedure</option>';
        if ($specialityId <= 0) {
            return response()->json(['success' => true, 'html' => $html]);
        }

        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $query = TreatmentPlanProcedure::query()
            ->with('package')
            ->where('speciality_id', $specialityId)
            ->where(function ($q) {
                $q->whereNull('procedure_label')
                    ->orWhere('procedure_label', 'Regular Procedure');
            })
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereIn('status', ['1', 'active', 'Active', 'ACTIVE']);
            });

        $schemeId = (int) ($preauth_register->scheme_id ?? 0);
        if ($schemeId > 0) {
            $query->where(function ($q2) use ($schemeId) {
                $q2->whereNull('scheme_type_id')
                    ->orWhere('scheme_type_id', $schemeId);
            });
        }

        foreach ($query->orderBy('procedure_name')->orderBy('name')->orderBy('id')->get() as $procedure) {
            $pkg = $procedure->package->code ?? '';
            $code2 = $procedure->procedure_code_2 ?? '';
            $pname = $procedure->procedure_name ?? ('Procedure #'.$procedure->id);
            $label = trim($pkg.' ('.$code2.') '.$pname);
            $html .= '<option value="'.(int) $procedure->id.'">'.e($label).'</option>';
        }

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function getProcedureDetail(Request $request)
    {
        $procedure_detail = TreatmentPlanProcedure::query()->findOrFail((int) $request->id);
        $stratification_options = '<option value="">Select Stratification</option>';
        if ($procedure_detail->stratification_criteria === 'Yes') {
            $pid = $procedure_detail->id;
            $rows = TreatmentPlanStratification::query()
                ->where(function ($q) use ($pid) {
                    $q->where('procedure_id', $pid);
                    if (Schema::hasTable('stratification_procedures')) {
                        $q->orWhereHas('procedures', function ($q2) use ($pid) {
                            $q2->where('procedures.id', $pid);
                        });
                    }
                })
                ->orderBy('name')
                ->get();
            foreach ($rows as $stratification) {
                $stratification_options .= '<option value="'.(int) $stratification->id.'">'.e($stratification->name.' - ('.($stratification->code ?? '').')').'</option>';
            }
        }
        $implants_options = '<option value="">Select Implant</option>';
        if ($procedure_detail->implants_high_end_consumables === 'Yes') {
            $pid = $procedure_detail->id;
            $implants = TreatmentPlanImplant::query()
                ->where(function ($q) use ($pid) {
                    $q->where('procedure_id', $pid);
                    if (Schema::hasTable('implant_procedures')) {
                        $q->orWhereHas('procedures', function ($q2) use ($pid) {
                            $q2->where('procedures.id', $pid);
                        });
                    }
                })
                ->orderBy('name')
                ->get();
            foreach ($implants as $implant) {
                $implants_options .= '<option value="'.(int) $implant->id.'">'.e($implant->name.' - ('.($implant->code ?? '').')').'</option>';
            }
        }

        $is_read_only = true;
        if ((float) $procedure_detail->price != 0) {
            $losVal = $procedure_detail->los;
            $los = ($losVal !== null && $losVal !== '' && (float) $losVal != 0) ? (string) $losVal : 'N/A';
        } else {
            $los = '1';
        }
        if ($request->boolean('is_enhancement') && (float) $procedure_detail->price == 0) {
            $is_read_only = false;
        }
        $usp = (($procedure_detail->procedure_code_1 ?? '') === 'U100');

        return response()->json([
            'success' => true,
            'no_of_days' => $los,
            'is_read_only' => $is_read_only,
            'price' => (float) ($procedure_detail->price ?? 0),
            'usp' => $usp,
            'icd_code' => (string) ($procedure_detail->icd_code ?? ''),
            'is_implant' => $procedure_detail->implants_high_end_consumables === 'Yes',
            'is_stratification' => $procedure_detail->stratification_criteria === 'Yes',
            'stratification_options' => $stratification_options,
            'implants_options' => $implants_options,
        ]);
    }

    public function getStratificationDetail(Request $request)
    {
        $row = TreatmentPlanStratification::query()->findOrFail((int) $request->id);

        return response()->json(['success' => true, 'price' => (float) ($row->price ?? 0)]);
    }

    public function getImplantDetail(Request $request)
    {
        $implant_detail = TreatmentPlanImplant::query()->findOrFail((int) $request->id);
        $max = max(1, (int) ($implant_detail->no_of_multiplier ?? 1));
        $is_read_only = $max <= 1;
        $qty = 1;

        return response()->json([
            'success' => true,
            'qty' => $qty,
            'max' => $max,
            'is_read_only' => $is_read_only,
            'price' => (float) ($implant_detail->price ?? 0),
        ]);
    }

    public function procedure(Request $request)
    {
        $procedure = TreatmentPlanProcedure::query()->findOrFail((int) $request->procedure_id);
        $request->validate([
            'speciality_id' => 'required',
            'procedure_id' => 'required',
            'no_of_days' => 'required',
            'u100_amount' => [
                function ($attribute, $value, $fail) use ($procedure) {
                    if ($procedure && $procedure->procedure_code_1 === 'U100' && empty($value)) {
                        $fail('The Unverfied Amount field is required when procedure code is U100.');
                    }
                },
            ],
        ]);
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $preauth_procedure = PreauthProcedure::query()->firstOrNew([
            'preauth_register_id' => $preauth_register_id,
            'procedure_id' => $request->procedure_id,
        ]);
        $preauth_procedure->preauth_register_id = $preauth_register_id;
        $preauth_procedure->procedure_id = $request->procedure_id;
        $preauth_procedure->speciality_id = $request->speciality_id;
        if ($request->filled('implant_id')) {
            $implant = TreatmentPlanImplant::query()->find((int) $request->implant_id);
            $preauth_procedure->implant_id = $request->implant_id;
            $preauth_procedure->implant_price = (float) ($implant->price ?? 0);
            $preauth_procedure->implant_qty = (int) ($request->implant_qty ?: 1);
        }
        if ($request->filled('stratification_id')) {
            $stratification = TreatmentPlanStratification::query()->find((int) $request->stratification_id);
            $preauth_procedure->stratification_id = $request->stratification_id;
            $preauth_procedure->stratification_price = (float) ($stratification->price ?? 0);
        }

        if ((int) $preauth_register->scheme_id === 1) {
            $procedure_price = (float) ($procedure->price ?? 0);
        } else {
            $procedure_price = $request->filled('u100_amount')
                ? (float) $request->u100_amount
                : (float) ($procedure->price ?? 0);
        }
        $preauth_procedure->original_price = $procedure_price;
        $preauth_procedure->procedure_price = $procedure_price;
        $preauth_procedure->incentive = 0;
        $preauth_procedure->incentive_per = 0;
        $preauth_procedure->no_of_days = $request->no_of_days;
        $preauth_procedure->save();

        SchemePreauthHelper::checkandUpdateSurgicalPackage($preauth_register_id);

        return $this->procedureJsonResponse($preauth_register_id, 'Procedure Saved Successfully!');
    }

    public function deleteProcedure(Request $request)
    {
        $preauth_procedure = PreauthProcedure::query()->whereKey($request->id)->firstOrFail();
        $this->authorizeRegister(PreauthRegister::query()->findOrFail($preauth_procedure->preauth_register_id));
        $preauth_register_id = $preauth_procedure->preauth_register_id;
        $preauth_doc_ids = $preauth_procedure->procedure->mandatory_documents_pre_auth ?? null;
        if ($preauth_doc_ids) {
            PreauthInvestigation::query()
                ->where('preauth_register_id', $preauth_register_id)
                ->whereIn('investigation_id', explode(',', (string) $preauth_doc_ids))
                ->delete();
        }
        $preauth_procedure->delete();
        SchemePreauthHelper::checkandUpdateSurgicalPackage($preauth_register_id);

        return $this->procedureJsonResponse($preauth_register_id, 'Procedure Delete Successfully!');
    }

    public function deleteImplant(Request $request)
    {
        $preauth_procedure = PreauthProcedure::query()->whereKey($request->id)->firstOrFail();
        $this->authorizeRegister(PreauthRegister::query()->findOrFail($preauth_procedure->preauth_register_id));
        $preauth_register_id = $preauth_procedure->preauth_register_id;
        $preauth_procedure->implant_id = null;
        $preauth_procedure->implant_price = 0;
        $preauth_procedure->save();
        SchemePreauthHelper::checkandUpdateSurgicalPackage($preauth_register_id);

        return $this->procedureJsonResponse($preauth_register_id, 'Implant Delete Successfully!');
    }

    protected function procedureJsonResponse(int $preauth_register_id, string $message)
    {
        $procedures = PreauthProcedure::query()->where('preauth_register_id', $preauth_register_id)->get();
        $html = view('hospital.scheme-preauth._partials.procedures', ['procedures' => $procedures])->render();
        $investigation_html = view('hospital.scheme-preauth._partials.investigations', [
            'investigations' => SchemePreauthHelper::getInvestigations($preauth_register_id),
            'preauth_register_id' => $preauth_register_id,
        ])->render();
        $preauth_investigation_status = SchemePreauthHelper::getPreauthInvestigationsStatus($preauth_register_id);
        $finance_html = view('hospital.scheme-preauth._partials.finance', ['procedures' => $procedures])->render();
        $finance_total_html = view('hospital.scheme-preauth._partials.finance-total', ['procedures' => $procedures])->render();

        return response()->json([
            'success' => true,
            'message' => $message,
            'html' => $html,
            'finance_html' => $finance_html,
            'finance_total_html' => $finance_total_html,
            'investigation_html' => $investigation_html,
            'preauth_investigation_status' => $preauth_investigation_status,
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function careTeam(Request $request)
    {
        $request->validate(['care_team_id' => 'required|integer']);
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $doctor = $this->careTeamDoctorsQuery()
            ->whereKey((int) $request->care_team_id)
            ->first();

        if (! $doctor) {
            return response()->json(['success' => false, 'message' => 'Please select a valid doctor from your hospital.']);
        }

        $exists = PreauthCareTeam::query()
            ->where('preauth_register_id', $preauth_register_id)
            ->where('staff_id', $doctor->id)
            ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Care Team Doctor already added. You can\'t add more!']);
        }
        $row = new PreauthCareTeam;
        $row->preauth_register_id = $preauth_register_id;
        $row->staff_id = $doctor->id;
        $row->save();

        $preauth_teams = $this->preauthTeamsQuery($preauth_register_id)->get();
        $html = view('hospital.scheme-preauth._partials.teams', ['preauth_teams' => $preauth_teams])->render();

        return response()->json([
            'success' => true,
            'message' => 'Team Saved Successfully!',
            'html' => $html,
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function deleteTeam(Request $request)
    {
        $preauth_care_team = PreauthCareTeam::query()->whereKey($request->id)->firstOrFail();
        $this->authorizeRegister(PreauthRegister::query()->findOrFail($preauth_care_team->preauth_register_id));
        $preauth_register_id = $preauth_care_team->preauth_register_id;
        $preauth_care_team->delete();
        $preauth_teams = $this->preauthTeamsQuery($preauth_register_id)->get();
        $html = view('hospital.scheme-preauth._partials.teams', ['preauth_teams' => $preauth_teams])->render();

        return response()->json([
            'success' => true,
            'message' => 'Team Delete Successfully!',
            'html' => $html,
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
        ]);
    }

    public function investigation(Request $request)
    {
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $is_resubmission = (int) ($request->is_resubmission ?? 0);
        $is_enhancement = (int) ($request->is_enhancement ?? 0);
        $initiate_flag = ($is_resubmission === 1 || $is_enhancement === 1) ? 1 : 0;
        $investigations = SchemePreauthHelper::getInvestigations($preauth_register_id, $initiate_flag);

        $rules = [];
        $messages = [];
        foreach ($investigations as $investigation) {
            $preauth_investigation = PreauthInvestigation::query()
                ->where('preauth_register_id', $preauth_register_id)
                ->where('investigation_id', $investigation->id)
                ->first();
            if (! $preauth_investigation && $investigation->is_required) {
                $rules['investigation_'.$investigation->id.'_doc'] = 'required|mimes:pdf|max:10240';
                $messages['investigation_'.$investigation->id.'_doc'] = 'File Type / Size is not in correct format';
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->messages(),
            ], 422);
        }

        foreach ($investigations as $investigation) {
            if ($request->hasFile('investigation_'.$investigation->id.'_doc')) {
                $filePath = $request->file('investigation_'.$investigation->id.'_doc')->store('investigation', 'public');
                $check_investigation = PreauthInvestigation::query()
                    ->where('preauth_register_id', $preauth_register_id)
                    ->where('investigation_id', $investigation->id)
                    ->first();
                $array = [
                    'preauth_register_id' => $preauth_register_id,
                    'investigation_id' => $investigation->id,
                    'file' => $filePath,
                ];
                if (! $check_investigation) {
                    $array['is_resubmission'] = $initiate_flag;
                }
                $preauth_register->investigations()->updateOrCreate(['investigation_id' => $investigation->id], $array);
            }
        }

        $investigation_html = view('hospital.scheme-preauth._partials.investigations', [
            'investigations' => SchemePreauthHelper::getInvestigations($preauth_register_id, $initiate_flag),
            'preauth_register_id' => $preauth_register_id,
        ])->render();

        return response()->json([
            'success' => true,
            'message' => 'Investigation Saved Successfully!',
            'investigation_html' => $investigation_html,
            'inhancement_docs_html' => '',
            'steps' => SchemePreauthHelper::fillCompleteStep($preauth_register_id),
            'preauth_investigation_status' => SchemePreauthHelper::getPreauthInvestigationsStatus($preauth_register_id),
        ]);
    }

    public function validateForm(Request $request)
    {
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $general_info = GeneralInfo::query()->where('preauth_register_id', $preauth_register_id)->first();
        $family_history = FamilyHistory::query()->where('preauth_register_id', $preauth_register_id)->first();
        $personal_history = PersonalHistory::query()->where('preauth_register_id', $preauth_register_id)->first();
        $authentication_consent = AuthenticationConsent::query()->where('preauth_register_id', $preauth_register_id)->first();
        $admission_details = AdmissionDetails::query()->where('preauth_register_id', $preauth_register_id)->first();
        $preauth_diagnosis = PreauthDiagnosis::query()->where('preauth_register_id', $preauth_register_id)->get();
        $preauth_teams = $this->preauthTeamsQuery($preauth_register_id)->get();

        $is_resubmission = (int) ($request->is_resubmission ?? 0);
        $procedures = PreauthProcedure::query()->where('preauth_register_id', $preauth_register_id)->get();
        $investigations = PreauthInvestigation::query()->where('preauth_register_id', $preauth_register_id)->get();
        $preauth_investigation_status = SchemePreauthHelper::getPreauthInvestigationsStatus($preauth_register_id, $is_resubmission === 1 ? 1 : 0);
        $preauth_package_check_status = SchemePreauthHelper::getPreauthPackageStatus($preauth_register_id, $is_resubmission === 1 ? 1 : 0);
        $u100_package_check_status = SchemePreauthHelper::getU100PackageStatus($preauth_register_id, $is_resubmission === 1 ? 1 : 0);

        $enhancement_doc_status = true;
        $bed_side_photo = '';
        $clinical_notes = '';
        $any_other_doc = '';

        $validate = true;
        $msg = '';
        if (! $general_info) {
            $validate = false;
            $msg .= 'General info is pending.<br>';
        }
        if (! $family_history) {
            $validate = false;
            $msg .= 'Family history is pending.<br>';
        }
        if (! $personal_history) {
            $validate = false;
            $msg .= 'Personal history is pending.<br>';
        }
        if (! $authentication_consent) {
            $validate = false;
            $msg .= 'Authentication consent is pending.<br>';
        }
        if (! $admission_details) {
            $validate = false;
            $msg .= 'Admission details is pending.<br>';
        }
        if ($preauth_diagnosis->count() === 0) {
            $validate = false;
            $msg .= 'Diagnosis is pending.<br>';
        }
        if ($procedures->count() === 0) {
            $validate = false;
            $msg .= 'Procedures is pending.<br>';
        }
        if (! $preauth_investigation_status) {
            $validate = false;
            $msg .= 'Investigations is pending.<br>';
        }
        if (! $enhancement_doc_status) {
            $validate = false;
            $msg .= 'Enhancement Investigations is pending.<br>';
        }
        if ($preauth_package_check_status) {
            $validate = false;
            $msg .= 'Medical or Surgical both packages can\'t allow to same preauth request.<br>';
        }
        if ($u100_package_check_status) {
            $validate = false;
            $msg .= 'U100 package can allow only alone package.<br>';
        }
        if ($preauth_teams->count() === 0) {
            $validate = false;
            $msg .= 'Care team is pending.<br>';
        }

        if ($family_history) {
            $family_history->load(['diabetes', 'hypertension', 'heartdisease', 'stroke', 'cancer', 'tuberculosis', 'asthma']);
        }
        if ($personal_history) {
            $personal_history->load(['appetite', 'bowels', 'nutrition', 'diet']);
        }
        if ($admission_details) {
            $admission_details->load('admission_type');
        }

        $html = '';
        if ($validate) {
            $html = view('hospital.scheme-preauth._partials.preview-request', compact(
                'general_info',
                'family_history',
                'personal_history',
                'authentication_consent',
                'admission_details',
                'preauth_diagnosis',
                'procedures',
                'preauth_teams',
                'investigations',
                'bed_side_photo',
                'clinical_notes',
                'any_other_doc'
            ))->render();
        }

        return response()->json(['success' => true, 'message' => $msg, 'validate' => $validate, 'html' => $html]);
    }

    public function requestFormSumbit(Request $request)
    {
        $preauth_register_id = $this->sessionRegisterId();
        $preauth_register = PreauthRegister::query()->findOrFail($preauth_register_id);
        $this->authorizeRegister($preauth_register);

        $preauth_register->status = PreauthRegister::STATUS_PREAUTH_PENDING;
        $preauth_register->preauth_submission_date = now();
        $preauth_register->preauth_initiated_amount = SchemePreauthHelper::getPreauthIntiateAmount($preauth_register_id);
        $preauth_register->preauth_approved_amount = SchemePreauthHelper::getPreauthAmountWithoutDeduction($preauth_register_id)
            - SchemePreauthHelper::getDeductionAmount($preauth_register_id);
        $preauth_register->preauth_amount_without_deduction = SchemePreauthHelper::getPreauthAmountWithoutDeduction($preauth_register_id);
        $preauth_register->save();

        return response()->json([
            'success' => true,
            'message' => 'Pre-Authorization Submitted Successfully!',
            'case_id' => $preauth_register->register_id,
        ]);
    }
}
