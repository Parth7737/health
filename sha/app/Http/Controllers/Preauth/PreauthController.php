<?php

namespace App\Http\Controllers\Preauth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Models\{
    PreauthRegister,
    GeneralInfo,
    Benificiary,
    HospitalState,
    HospitalDistrict,
    MobileOtp,
    FamilyHistory,
    PersonalHistory,
    AuthenticationConsent,
    AdmissionDetails,
    Diagnosis,
    PreauthDiagnosis,
    Speciality,
    HospitalSpeciality,
    Procedure,
    PreauthProcedure,
    ProcedureCategory,
    HospitalTeam,
    PreauthCareTeam,
    PreauthInvestigation,
    PreauthEnhancementDoc,
    Investigation,
    Implant,
    Stratification,
    HospitalAccreditation,
    FollowupProcedure,
    AddOnProcedure,
    NonAddOnProcedure,
    AddOnSpeciality,
};
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Services\AadhaarService;
use Illuminate\Support\Facades\Mail;

class PreauthController extends Controller
{
    protected $aadhaarService;

    public function __construct()
    {
        $this->aadhaarService = new AadhaarService();
    }
    /**
     * Display a listing of the resource.
     */
    public function fetchCard(Request $request)
    {
        $benificiary = Benificiary::
            where(function ($query) use ($request) {
                $query->where('aabha_id', $request->search)
                    ->orWhere('card_id', $request->search)
                    ->orWhere('mobile_no', $request->search);
            })
            ->first();
        if(!$benificiary){
            return response()->json(['success' => false,'msg'=>'Benificiary Not Found']);
        }
        $source_types = ['gp','ge'];
        if($request->scheme_id == 1){
            if(!in_array(strtolower($benificiary->source_type),$source_types)){
                return response()->json(['success' => false,'msg'=> 'Beneficiary found, but does not match the selected scheme.']);
            }
        }else{
            if(in_array(strtolower($benificiary->source_type),$source_types)){
                return response()->json(['success' => false,'msg'=> 'Beneficiary found, but does not match the selected scheme.']);
            }
        }

        return response()->json(['success' =>true, 'benificiary' => $benificiary]);
    }
    public function verifyCard(Request $request)
    {
        try {
            $benificiary = Benificiary::where(function ($query) use ($request) {
                $query->where('aabha_id', $request->search)
                    ->orWhere('card_id', $request->search)
                    ->orWhere('mobile_no', $request->search);
            })->first();

            if (!$benificiary) {
                return response()->json(['success' => false, 'msg' => 'Beneficiary Not Found']);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_SSL_OPTIONS => CURLSSLOPT_ALLOW_BEAST,
                ]
            ])
            ->post('https://betasha.uk.gov.in/AyushAPI/BIS/TMSVerification', [
                "nhaid" => $benificiary->card_id,
                "yearOfBirth" => "",
                "hhid" => $benificiary->family_id,
                "hhdtype" => "",
                "address" => [
                    "pinCode" => "",
                    "statelgdCode" => "",
                    "address" => "",
                    "subdistrictlgdCode" => "",
                    "districtlgdCode" => "",
                    "ruralUrban" => "",
                    "village_townlgdCode" => ""
                ],
                "gender" => "",
                "memberName" => "",
                "mobileNumber" => "",
                "member_id" => $benificiary->member_id
            ]);

            if ($response->successful()) {
                $data_arr = $response->json();
                $grade = $data_arr['grade'] ?? 'G1';
            } else {
                $this->logTmsResponse($response->body());
                $grade = 'G1';
            }

            if ($request->scheme_id == 1) {
                \Session::put('grade', $grade);
            } else {
                \Session::put('grade', "");
            }

            return response()->json(['success' => true,'grade'=>$grade]);

        } catch (\Throwable $e) {
            $this->logTmsResponse($e->getMessage());

            $grade = 'G1';
           
            if ($request->scheme_id == 1) {
                \Session::put('grade', $grade);
            } else {
                \Session::put('grade', "");
            }

            return response()->json(['success' => true,'grade'=>$grade]);
        }
    }
    private function logTmsResponse($data)
    {
        try {
            $logPath = public_path("tms-vertification-logs.txt");
            $logContent = json_encode([
                'timestamp' => now()->toDateTimeString(),
                'data' => $data
            ]) . "\n\n";
    
            file_put_contents($logPath, $logContent, FILE_APPEND);
        } catch (\Exception $e) {
            \Log::error("TMS Logging Error: " . $e->getMessage());
        }
    }
    
    public function registerPatientSession(Request $request){

        $benificiary = Benificiary::
            where(function ($query) use ($request) {
                $query->where('aabha_id', $request->search)
                    ->orWhere('card_id', $request->search)
                    ->orWhere('mobile_no', $request->search);
            })
            ->first();
            $check = PreauthRegister::where('benificiary_id',$benificiary->id)->whereIn('status',[PreauthRegister::STATUS_REGISTER,PreauthRegister::STATUS_PREAUTH_PENDING,PreauthRegister::STATUS_PREAUTH_APPROVED,PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_CLAIM_SUBMITTED,PreauthRegister::STATUS_CLAIM_PENDING,PreauthRegister::STATUS_CLAIM_QUERIED,PreauthRegister::STATUS_DISCHARGE,PreauthRegister::STATUS_CPD_CLAIM_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_APPROVED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_PENDING,PreauthRegister::STATUS_ACS_QUERIED])->first();
        if($check){
            return response()->json(['success' => false,'message'=>'Patient is already registered or preath process or under treatment or claim process.']);
        }
        if($request->kyc_type != 'without_auth' && $request->terms == ''){
            return response()->json(['success' => false,'message'=>'consent is required.']);
        }
        \Session::put('benificiary_id', $benificiary->id);
        \Session::put('scheme_id', $request->scheme_id);
        \Session::put('kyc_type', $request->kyc_type);
        \Session::put('aadhar_no', $request->aadhar_no);
        return response()->json(['success' => true]);
    }
    public function registerPatient(){

        $benificiary_id = \Session::get('benificiary_id');
        $kyc_type = \Session::get('kyc_type');
        $benificiary = Benificiary::find($benificiary_id);
        if($benificiary){
            $states = HospitalState::all();
            $districts = HospitalDistrict::all();
            return view('preauth.register-patient',compact('benificiary','states','districts','kyc_type'));
        }else{
            return redirect()->route('preauth.dashboard');
        }
    }
    public function sendOTPOnAadhar(Request $request) {
        $otp = rand(000000, 999999);
        // $data = MobileOtp::where(['mobile_no' => $request->aadhar_no])->first();
        // if($data) {
        //     $data->otp = $otp;
        //     $data->status = 0;
        //     $data->save();
        // } else {
        //     $data = MobileOtp::create([ 'mobile_no' => $request->aadhar_no, 'otp' => $otp]);
        // }

        $response = $this->aadhaarService->sendOtp($request->json('aadhar_no'));

        return response()->json($response);
    }
    public function reSendOTPOnAadhar(Request $request) {
        $otp = rand(000000, 999999);
        // $data = MobileOtp::where(['mobile_no' => $request->aadhar_no])->first();
        // if($data) {
        //     $data->otp = $otp;
        //     $data->status = 0;
        //     $data->save();
        // }

        $response = $this->aadhaarService->sendOtp($request->json('aadhar_no'));
        
        return response()->json($response);

    }
    public function verifyAadharOtp(Request $request){

        // $mobile_otp = MobileOtp::where(['mobile_no' => $request->aadhar_no, 'status' => 0])->latest()->first();
        // if($mobile_otp->otp == $request->otp){
        //     $mobile_otp->status=1;
        //     $mobile_otp->save();
        //     return response()->json(['success' => true, 'message' => 'OTP Verified Successfully!']);
        // }else{
        //     return response()->json(['success' => false, 'message' => 'Wrong OTP!']);
        // }
        $response = $this->aadhaarService->verifyOtp($request->json('aadhar_no'), $request->json('otp'), base64_decode($request->json('reference_id')));
        return response()->json($response);
    }
    public function registerPatientStore(Request $request){

        $kyc_type = \Session::get('kyc_type');
        $validatedData = $request->validate([
            'mobile_no' => 'required',
            'pincode' => 'required',
            'state_id' => 'required',
            'district_id' => 'required',
            'city' => 'required',
            'address' => 'required',
            'patient_type' => 'required',
            'full_name' => 'required',
            'relationship' => 'required_if:attendant_patient,other',
            'other_relation' => 'required_if:relationship,Other',
            'born_baby_dob' => 'required_if:new_born_baby,1',
            'born_baby_name' => 'required_if:new_born_baby,1',
            'born_baby_name' => 'required_if:new_born_baby,1',
            'born_baby_gender' => 'required_if:new_born_baby,1',
            'born_baby_birth_certificate' => 'required_if:new_born_baby,1|mimes:pdf|max:5120',
            'hospital_declaration_form' => [
                Rule::requiredIf($request->kyc_type === 'without_auth'),
                'file',
                'mimes:pdf,xlsx,docx',
                'max:2048'
            ],
            'remarks' => Rule::requiredIf($kyc_type === 'without_auth'),
        ]);
        $benificiary_id = \Session::get('benificiary_id');
        $aadhar_no = \Session::get('aadhar_no');
        $grade = \Session::get('grade');
        $scheme_id = \Session::get('scheme_id');
        $benificiary = Benificiary::find($benificiary_id);
        if($benificiary){
            $hospital_declaration_form = '';
            if ($request->hasFile('hospital_declaration_form')) {
                $filePath = $request->file('hospital_declaration_form')->store('authentication', 'public'); // Store in "storage/app/public/profiles"
                $hospital_declaration_form = $filePath;
            }
            $born_baby_birth_certificate = '';
            if ($request->hasFile('born_baby_birth_certificate')) {
                $filePath = $request->file('born_baby_birth_certificate')->store('authentication', 'public'); // Store in "storage/app/public/profiles"
                $born_baby_birth_certificate = $filePath;
            }
            $preauth_register = new PreauthRegister;
            $preauth_register->hospital_id = auth()->user()->hospital_id;
            $preauth_register->register_id = Helpers::getRegisterID();
            $preauth_register->benificiary_id = $benificiary->id;
            $preauth_register->scheme_id = $scheme_id;
            $preauth_register->kyc_type = $kyc_type;
            $preauth_register->grade = $grade;
            $preauth_register->aadhar_no = $aadhar_no;
            $preauth_register->mobile_no = $request->mobile_no;
            $preauth_register->is_new_born_baby = $request->new_born_baby??0;
            $preauth_register->born_baby_dob = $request->born_baby_dob;
            $preauth_register->born_baby_name = $request->born_baby_name;
            $preauth_register->born_baby_gender = $request->born_baby_gender;
            $preauth_register->born_baby_birth_certificate = $born_baby_birth_certificate;
            $preauth_register->pincode = $request->pincode;
            $preauth_register->state_id = $request->state_id;
            $preauth_register->district_id = $request->district_id;
            $preauth_register->city = $request->city;
            $preauth_register->address = $request->address;
            $preauth_register->address_2 = $request->address_2;
            $preauth_register->patient_type = $request->patient_type;
            $preauth_register->attendant_patient = $request->attendant_patient;
            $preauth_register->full_name = $request->full_name;
            $preauth_register->relationship = $request->relationship;
            $preauth_register->other_relation = $request->other_relation;
            $preauth_register->mobile_no = $request->mobile_no;
            $preauth_register->hospital_declaration_form = $hospital_declaration_form;
            $preauth_register->remarks = $request->remarks;
            $preauth_register->status = PreauthRegister::STATUS_REGISTER;
            $preauth_register->save();

            \Session::forget(['benificiary_id', 'kyc_type', 'aadhar_no', 'scheme_type','grade']);
            return response()->json(['success' => true, 'message' => 'Register Successfully','register_id'=>$preauth_register->register_id]); 
        }else{
            throw ValidationException::withMessages([
                'full_name' => 'Benificiary not found.',
            ]);
        }

    }
    public function sendOTPOnMobile(Request $request) {
        $otp = rand(000000, 999999);
        $data = MobileOtp::where(['mobile_no' => $request->mobile])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 0;
            $data->save();
        } else {
            $data = MobileOtp::create([ 'mobile_no' => $request->mobile, 'otp' => $otp]);
        }
        return response()->json(['success' => true, 'message' => 'Otp sent in your Mobile No','otp'=>$otp]);
    }
    public function reSendOTPOnMobile(Request $request) {
        $otp = rand(000000, 999999);
        $data = MobileOtp::where(['mobile_no' => $request->mobile])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 0;
            $data->save();
        }

        return response()->json(['success' => true, 'message' => 'OTP Re-sent Successfully!','otp'=>$otp]);

    }
    public function verifyMobileOtp(Request $request){

        $mobile_otp = MobileOtp::where(['mobile_no' => $request->mobile, 'status' => 0])->latest()->first();
        if($mobile_otp->otp == $request->otp){
            $mobile_otp->status=1;
            $mobile_otp->save();
            return response()->json(['success' => true, 'message' => 'OTP Verified Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Wrong OTP!']);
        }
    }
    public function preauthRequest($id){
        Helpers::checkPermission($id);
        $preauth_register = PreauthRegister::find($id);
        if($preauth_register){
            \Session::put('preauth_register_id', $preauth_register->id);
            $general_info = GeneralInfo::where('preauth_register_id',$preauth_register->id)->first();
            $family_history = FamilyHistory::where('preauth_register_id',$preauth_register->id)->first();
            $personal_history = PersonalHistory::where('preauth_register_id',$preauth_register->id)->first();
            $authentication_consent = AuthenticationConsent::where('preauth_register_id',$preauth_register->id)->first();
            $admission_details = AdmissionDetails::where('preauth_register_id',$preauth_register->id)->first();
            $preauth_diagnosis = PreauthDiagnosis::where('preauth_register_id',$preauth_register->id)->get();
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register->id)->get();
            $hospital_speciality = HospitalSpeciality::where('hospital_id',auth()->user()->hospital_id)->where('available',1)->where('offered',1)->whereHas('speciality',function($query) use($preauth_register){
                $query->where('scheme_type_id',$preauth_register->scheme_id);
            })->get();
            $us='';
            if($preauth_register->scheme_id == 2){
                $us = Speciality::where('name','Unspecified Surgical Package')->where('code','US')->first();
            }
            $speciality_ids = $hospital_speciality->pluck('speciality_id')->filter();
            $teams = HospitalTeam::where('hospital_id',auth()->user()->hospital_id)->whereIn('speciality_id', $speciality_ids)->get();
            $preauth_investigations = PreauthInvestigation::where('preauth_register_id', $preauth_register->id)->get();
            $investigations=Helpers::getInvestigations($preauth_register->id);
            $post_investigations=Helpers::getPostInvestigations($preauth_register->id);
            $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register->id);
            $preauth_teams = PreauthCareTeam::where('preauth_register_id',$preauth_register->id)->get();
            
            $case_profile = $preauth_register->id;
            if($preauth_register->status == PreauthRegister::STATUS_REGISTER){
                return view('preauth.preauth-request',compact('preauth_register','case_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','us','procedures','teams','preauth_teams','investigations','post_investigations','preauth_investigations','preauth_investigation_status'));
            }else{
                return view('preauth.preauth-request-preview',compact('preauth_register','case_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','us','procedures','teams','preauth_teams','investigations','post_investigations','preauth_investigations','preauth_investigation_status'));
            }
            
        }else{
            return redirect()->route('preauth.dashboard')->with('error','Register Not Found');
        }
    }
    public function validateForm(Request $request){
        $preauth_register_id = \Session::get('preauth_register_id');
        
        $general_info = GeneralInfo::where('preauth_register_id',$preauth_register_id)->first();
        $family_history = FamilyHistory::where('preauth_register_id',$preauth_register_id)->first();
        $personal_history = PersonalHistory::where('preauth_register_id',$preauth_register_id)->first();
        $authentication_consent = AuthenticationConsent::where('preauth_register_id',$preauth_register_id)->first();
        $admission_details = AdmissionDetails::where('preauth_register_id',$preauth_register_id)->first();
        $preauth_diagnosis = PreauthDiagnosis::where('preauth_register_id',$preauth_register_id)->get();
        $preauth_teams = PreauthCareTeam::where('preauth_register_id',$preauth_register_id)->get();

        $enhancement_doc_status=true;
        $bed_side_photo='';
        $clinical_notes='';
        $any_other_doc='';
        if($request->is_resubmission == 1 || $request->is_resubmission == 2) {
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
            $investigations = PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
            $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id,1);
            $preauth_package_check_status = Helpers::getPreauthPackageStatus($preauth_register_id,1);
            $u100_package_check_status = Helpers::getU100PackageStatus($preauth_register_id,1);
            $temp_enhancement_id = \Session::get('temp_enhancement_id');
            if($request->is_resubmission == 2){
                $bed_side_photo = PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Bed Side Photo')->first();
                $clinical_notes = PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Clinical Notes')->first();
                $any_other_doc = PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Any Other Document')->first();
                if(!$bed_side_photo || !$clinical_notes){
                    $enhancement_doc_status=false;
                }
            }
        } else {
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)->get();
            $investigations = PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->get();
            $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id);
            $preauth_package_check_status = Helpers::getPreauthPackageStatus($preauth_register_id);
            $u100_package_check_status = Helpers::getU100PackageStatus($preauth_register_id);
        }
        $validate=true;
        $msg='';
        if(!$general_info){
            $validate = false;
            $msg .= 'General info is pending.<br>';
        }
        if(!$family_history){
            $validate = false;
            $msg .= 'Family history is pending.<br>';
        }
        if(!$personal_history){
            $validate = false;
            $msg .= 'Personal history is pending.<br>';
        }
        if(!$authentication_consent){
            $validate = false;
            $msg .= 'Authentication consent is pending.<br>';
        }
        if(!$admission_details){
            $validate = false;
            $msg .= 'Admission details is pending.<br>';
        }
        if($preauth_diagnosis->count() == 0){
            $validate = false;
            $msg .= 'Diagnosis is pending.<br>';
        }
        if($procedures->count() == 0){
            $validate = false;
            $msg .= 'Procedures is pending.<br>';
        }
        if(!$preauth_investigation_status){
            $validate = false;
            $msg .= 'Investigations is pending.<br>';
        }
        if(!$enhancement_doc_status){
            $validate = false;
            $msg .= 'Enhancement Investigations is pending.<br>';
        }
        if($preauth_package_check_status){
            $validate = false;
            $msg .= 'Medical or Surgical both packages can\'t allow to same preauth request.<br>';
        }
        if($u100_package_check_status){
            $validate = false;
            $msg .= 'U100 package can allow only alone package.<br>';
        }
        if($preauth_teams->count() == 0){
            $validate = false;
            $msg .= 'Care team is pending.<br>';
        }
        $html = '';
        if($validate){
            $html = view('preauth._partials.preview-request',compact('general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','procedures','preauth_teams','investigations','bed_side_photo','clinical_notes','any_other_doc'))->render();
        }
        return response()->json(['success' => true, 'message' => $msg,'validate'=>$validate,'html'=>$html]);
    }
    public function generalInformation(Request $request){
        
        $validatedData = $request->validate([
            'temprature' => 'required',
            'pulserate' => 'required',
            'height' => 'required',
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');
        $general_info = GeneralInfo::where('preauth_register_id',$preauth_register_id)->first();
        if(!$general_info){
            $general_info = new GeneralInfo;
        }
        $general_info->preauth_register_id = $preauth_register_id;
        $general_info->temprature = $request->temprature;
        $general_info->pulserate = $request->pulserate;
        $general_info->height = $request->height;
        $general_info->weight = $request->weight;
        $general_info->bmi = $request->bmi;
        $general_info->cyanosis = $request->cyanosis??'No';
        $general_info->pallor = $request->pallor??'No';
        $general_info->malnutration = $request->malnutration??'No';
        $general_info->oedema = $request->oedema??'No';
        $general_info->save();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'General Information Saved Successfully!','steps'=>$steps]);
    }
    public function familyHistory(Request $request){
        
        $validatedData = $request->validate([
            'diabetes' => 'required',
            'hypertension' => 'required',
            'heartdisease' => 'required',
            'stroke' => 'required',
            'cancer' => 'required',
            'tuberculosis' => 'required',
            'asthma' => 'required',
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');
        $family_history = FamilyHistory::where('preauth_register_id',$preauth_register_id)->first();
        if(!$family_history){
            $family_history = new FamilyHistory;
        }
        $family_history->preauth_register_id = $preauth_register_id;
        $family_history->diabetes_id = $request->diabetes;
        $family_history->hypertension_id = $request->hypertension;
        $family_history->heartdisease_id = $request->heartdisease;
        $family_history->stroke_id = $request->stroke;
        $family_history->cancer_id = $request->cancer;
        $family_history->tuberculosis_id = $request->tuberculosis;
        $family_history->asthma_id = $request->asthma;
        $family_history->save();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Family History Saved Successfully!','steps'=>$steps]);
    }
    public function personalHistory(Request $request){
        
        $validatedData = $request->validate([
            'appetite' => 'required',
            'bowels' => 'required',
            'nutrition' => 'required',
            'diet' => 'required',
            'known_allergies' => 'required',
            'allergy_detail' => 'required_if:known_allergies,Yes',
            'habits' => 'required',
            'habits_detail' => 'required_if:habits,Yes',
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');
        $personal_history = PersonalHistory::where('preauth_register_id',$preauth_register_id)->first();
        if(!$personal_history){
            $personal_history = new PersonalHistory;
        }
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
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Personal History Saved Successfully!','steps'=>$steps]);
    }
    public function authenticationConsent(Request $request){
        
        $validatedData = $request->validate([
            'hospital_declaration_form' => 'required',
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');
        if ($request->hasFile('hospital_declaration_form')) {
            $filePath = $request->file('hospital_declaration_form')->store('authentication', 'public'); // Store in "storage/app/public/profiles"
            $hospital_declaration_form = $filePath; // Add file path to data
        }
        $authentication_consent = AuthenticationConsent::where('preauth_register_id',$preauth_register_id)->first();
        if(!$authentication_consent){
            $authentication_consent = new AuthenticationConsent;
        }else{
            if(!$hospital_declaration_form){
                $hospital_declaration_form = $authentication_consent->hospital_declaration_form;
            }
        }
        $authentication_consent->preauth_register_id = $preauth_register_id;
        $authentication_consent->hospital_declaration_form = $hospital_declaration_form;
        $authentication_consent->remarks = $request->remarks;
        $authentication_consent->save();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Authentication Consent Saved Successfully!','steps'=>$steps]);
    }
    public function admissionDetails(Request $request){
        
        $validatedData = $request->validate([
            'admission_date' => 'required',
            'surgery_date' => 'required',
            'admission_type' => 'required',
            'legal_case' => 'required',
            'fir_doc' => 'required_if:legal_case,Yes|mimes:pdf|max:10240',
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');

        $admission_details = AdmissionDetails::where('preauth_register_id',$preauth_register_id)->first();
        if(!$admission_details){
            $admission_details = new AdmissionDetails;
        }
        if ($request->hasFile('fir_doc')) {
            $filePath = $request->file('fir_doc')->store('fir', 'public');
            $fir_doc = $filePath;
            $admission_details->fir_doc = $fir_doc;
        }
        $admission_details->preauth_register_id = $preauth_register_id;
        $admission_details->admission_date = $request->admission_date;
        $admission_details->surgery_date = $request->surgery_date;
        $admission_details->admission_type_id = $request->admission_type;
        $admission_details->legal_case = $request->legal_case;
        $admission_details->save();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Admin Details Saved Successfully!','steps'=>$steps]);
    }
    public function diagnosis(Request $request){
        
        $diagnosis = Diagnosis::find($request->diagnosis_id);

        $validatedData = $request->validate([
            'diagnosis_id' => 'required',
            'diagnosis_type' => 'required',
            'other_diagnosis' => $diagnosis && $diagnosis->name === 'Other' ? 'required' : '',
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');
        if($diagnosis->name === 'Other'){
            $preauth_diagnosis = [];
        }else{
            $preauth_diagnosis = PreauthDiagnosis::where('preauth_register_id',$preauth_register_id)->where('diagnosis_id',$request->diagnosis_id)->first();
        }
        if(!$preauth_diagnosis){
            $preauth_diagnosis = new PreauthDiagnosis;
        }
        $preauth_diagnosis->preauth_register_id = $preauth_register_id;
        $preauth_diagnosis->diagnosis_id = $request->diagnosis_id;
        $preauth_diagnosis->diagnosis_type = $request->diagnosis_type;
        $preauth_diagnosis->other_diagnosis = $request->other_diagnosis;
        $preauth_diagnosis->save();

        $preauth_diagnosis = PreauthDiagnosis::where('preauth_register_id',$preauth_register_id)->get();
        $html = view('preauth._partials.diagnosis',['preauth_diagnosis'=>$preauth_diagnosis])->render();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Diagnosis Saved Successfully!','html'=>$html,'steps'=>$steps]);
    }
    public function deleteDiagnosis(Request $request){
        
        $preauth_diagnosis = PreauthDiagnosis::where('id',$request->id)->first();
        $preauth_register_id = $preauth_diagnosis->preauth_register_id;
       
        PreauthDiagnosis::where('id',$request->id)->delete();
        $preauth_diagnosis = PreauthDiagnosis::where('preauth_register_id',$preauth_register_id)->get();
        $html = view('preauth._partials.diagnosis',['preauth_diagnosis'=>$preauth_diagnosis])->render();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Diagnosis Delete Successfully!','html'=>$html,'steps'=>$steps]);
    }
    public function getProcedures(Request $request){
        $id = $request->id;
        if($id){
            $procedures = Procedure::where('speciality_id', $id)
            ->where('procedure_label', 'Regular Procedure');
            
            $preauth_register_id = \Session::get('preauth_register_id');
            if ($preauth_register_id) {
                $preauth_register = PreauthRegister::where('id', $preauth_register_id)->first();
                $procedures = $procedures->where('scheme_type_id',$preauth_register->scheme_id);
                if($preauth_register->grade){
                    $category = ProcedureCategory::where('code',$preauth_register->grade)->first();
                    if($category){
                        $procedures = $procedures->where('procedure_category_id',$category->id);
                    }
                }
                $previous_preauth_registers_ids = PreauthRegister::where('benificiary_id', $preauth_register->benificiary_id)->pluck('id');
                $preauth_procedure_ids = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                    ->pluck('procedure_id');
                if ($preauth_procedure_ids->isNotEmpty()) {
                    $procedures = $procedures->whereNot('procedure_code_1', 'U100');
                }
                $preauth_procedure_speciality_ids = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                    ->pluck('speciality_id');

                if(isset($request->enhance_type) && $request->enhance_type == 2){
                    $procedures = $procedures->whereIn('id', $preauth_procedure_ids);
                }elseif(isset($request->enhance_type) && $request->enhance_type == 3){
                    $add_on_ids = AddOnProcedure::whereIn('procedure_id', $preauth_procedure_ids)
                        ->pluck('add_on_id');

                    $procedures = $procedures->whereIn('id', $add_on_ids);
                    
                    $add_on_speciality_ids = AddOnSpeciality::whereIn('speciality_id', $preauth_procedure_speciality_ids)
                    ->pluck('add_on_id');

                    if ($add_on_speciality_ids->isNotEmpty()) {
                        $procedures = $procedures->orWhereIn('id', $add_on_speciality_ids);
                    }
                }else{
                    if ($previous_preauth_registers_ids->isNotEmpty()) {
                        $previous_preauth_procedure_ids = PreauthProcedure::whereIn('preauth_register_id', $previous_preauth_registers_ids)->distinct()
                        ->pluck('procedure_id');
                        if ($previous_preauth_procedure_ids->isNotEmpty()) {
                            $follow_up_ids = FollowupProcedure::whereIn('procedure_id', $previous_preauth_procedure_ids)
                                ->pluck('follow_up_id');
                            if ($follow_up_ids->isNotEmpty()) {
                                $procedures = $procedures->orWhereIn('id', $follow_up_ids);
                            }
                        }


                        $add_on_ids = AddOnProcedure::whereIn('procedure_id', $preauth_procedure_ids)
                            ->pluck('add_on_id');

                        if ($add_on_ids->isNotEmpty()) {
                            $procedures = $procedures->orWhereIn('id', $add_on_ids);
                        }

                        $add_on_speciality_ids = AddOnSpeciality::whereIn('speciality_id', $preauth_procedure_speciality_ids)
                            ->pluck('add_on_id');
                        if ($add_on_speciality_ids->isNotEmpty()) {
                            $procedures = $procedures->orWhereIn('id', $add_on_speciality_ids);
                        }

                        $non_add_on_ids = NonAddOnProcedure::whereIn('procedure_id', $preauth_procedure_ids)
                            ->pluck('non_add_on_id');

                        if ($non_add_on_ids->isNotEmpty()) {
                            $procedures = $procedures->WhereNotIn('id', $non_add_on_ids);
                        }
                    }
                }
            }
            $procedures = $procedures->get();
        }else{
            $procedures = array();
        }
        $html='<option value="">Select Procedure</option>';
        foreach($procedures as $procedure){
            $html .= '<option value="'.$procedure->id.'">'.@$procedure->package->code." (".$procedure->procedure_code_2.") ".$procedure->procedure_name.'</option>';
        }
        return response()->json(['success' => true, 'html'=>$html]);
    }
    public function getProcedureDetail(Request $request){
        $procedure_detail = Procedure::where('id',$request->id)->first();
        $stratifications=[];
        $implants=[];
        $stratification_options='';
        $implants_options='';
        if($procedure_detail->stratification_criteria == 'Yes'){
            $stratifications = Stratification::whereRaw("FIND_IN_SET(?, procedure_id)", [$procedure_detail->id])->get();
            $stratification_options='<option value="">Select Stratification</option>';
            foreach($stratifications as $stratification){
                $stratification_options .= '<option value="'.$stratification->id.'">'.$stratification->name.' - ('.$stratification->code.')</option>';
            }
        }
        if($procedure_detail->implants_high_end_consumables == 'Yes'){
            $implants = Implant::whereRaw("FIND_IN_SET(?, procedure_id)", [$procedure_detail->id])->get();
            $implants_options='<option value="">Select Implant</option>';
            foreach($implants as $implant){
                $implants_options .= '<option value="'.$implant->id.'">'.$implant->name.' - ('.$implant->code.')</option>';
            }
        }
        $is_read_only = true;
        $los = '';
        if($procedure_detail->price != 0){
            $los = $procedure_detail->los != 0?$procedure_detail->los:'N/A';
        }else{
            $los = 1;
        }
        if(@$request->is_enhancement){
            if($procedure_detail->price == 0){
                $is_read_only = false;
            }
        }
        $usp=false;
        if($procedure_detail->procedure_code_1 == 'U100'){
            $usp=true;
        }
        return response()->json(['success' => true, 'no_of_days'=>$los,'is_read_only'=>$is_read_only,'price'=>$procedure_detail->price,'usp'=>$usp, 'icd_code'=>$procedure_detail->icd_code,'is_implant'=>$procedure_detail->implants_high_end_consumables=='Yes'?true:false, 'is_stratification'=>$procedure_detail->stratification_criteria=='Yes'?true:false,'stratification_options'=>$stratification_options, 'implants_options'=>$implants_options]);
    }
    public function getStratificationDetail(Request $request){
        $stratification_detail = Stratification::where('id',$request->id)->first();
        return response()->json(['success' => true,'price'=>$stratification_detail->price]);
    }
    public function getImplantDetail(Request $request){
        $implant_detail = Implant::where('id',$request->id)->first();
        $is_read_only = true;
        $los = '';
        $max=1;
        $qty=1;
        if($implant_detail->no_of_multiplier > 1){
            $max = $implant_detail->no_of_multiplier;
            $is_read_only = false;
        }
        return response()->json(['success' => true,'qty'=>$qty,'max'=>$max,'is_read_only'=>$is_read_only,'price'=>$implant_detail->price]);
    }
    public function procedure(Request $request){
        
        $procedure = Procedure::where('id',$request->procedure_id)->first();
        $validatedData = $request->validate([
            'speciality_id' => 'required',
            'procedure_id' => 'required',
            'no_of_days' => 'required',
            'u100_amount' => [
                function ($attribute, $value, $fail) use ($procedure) {
                    if ($procedure && $procedure->procedure_code_1 == 'U100' && empty($value)) {
                        $fail('The Unverfied Amount field is required when procedure code is U100.');
                    }
                },
            ],
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
        $preauth_procedure = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->where('procedure_id',$request->procedure_id)->first();
        if(!$preauth_procedure){
            $preauth_procedure = new PreauthProcedure;
        }
        $preauth_procedure->preauth_register_id = $preauth_register_id;
        $preauth_procedure->procedure_id = $request->procedure_id;
        $preauth_procedure->speciality_id = $request->speciality_id;
        $preauth_procedure->implant_id = $request->implant_id;
        if($request->implant_id != ''){
            $preauth_procedure->implant_id = $request->implant_id;
            $implant = Implant::where('id',$request->implant_id)->first();
            $preauth_procedure->implant_price = $implant->price;
            $preauth_procedure->implant_qty = $request->implant_qty;
        }
        if($request->stratification_id != ''){
            $preauth_procedure->stratification_id = $request->stratification_id;
            $stratification = Stratification::where('id',$request->stratification_id)->first();
            $preauth_procedure->stratification_price = $stratification->price;
        }
        $hospital_accreditation = HospitalAccreditation::where('hospital_id',auth()->user()->hospital_id)->first();
        if($preauth_register->scheme_id == 1){
            if($hospital_accreditation && $hospital_accreditation->accreditation =='Yes' && $hospital_accreditation->accreditation_id !=''){
                $procedure_price = $procedure->price;
            }else{
                $procedure_price = $procedure->non_nabh_price;
            }
        }else{
            if($request->u100_amount){
                $procedure_price = $request->u100_amount;
            }else{
                $procedure_price = $procedure->price;
            }
        }
        $preauth_procedure->original_price = $procedure_price;
        $preauth_procedure->procedure_price = $procedure_price;
        if($hospital_accreditation && $hospital_accreditation->accreditation =='Yes' && $hospital_accreditation->accreditation_id !='' && $preauth_register->scheme_id != 1){
            if(@$hospital_accreditation->accred->percentage && @$hospital_accreditation->accred->percentage != 0){
                $preauth_procedure->incentive = (@$hospital_accreditation->accred->percentage*$procedure_price)/100;
                $preauth_procedure->incentive_per = @$hospital_accreditation->accred->percentage;
            }
        }
        $preauth_procedure->no_of_days = $request->no_of_days;
        $preauth_procedure->save();
        Helpers::checkandUpdateSurgicalPackage($preauth_register_id);
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->get();
        $html = view('preauth._partials.procedures',['procedures'=>$procedures])->render();
        $investigation_html = view('preauth._partials.investigations',['investigations'=>Helpers::getInvestigations($preauth_register_id),'preauth_register_id'=>$preauth_register_id])->render();
        $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id);
        $finance_html = view('preauth._partials.finance',['procedures'=>$procedures])->render();
        $finance_total_html = view('preauth._partials.finance-total',['procedures'=>$procedures])->render();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Procedure Saved Successfully!','html'=>$html,'finance_html'=>$finance_html,'finance_total_html'=>$finance_total_html,'investigation_html'=>$investigation_html,'preauth_investigation_status'=>$preauth_investigation_status,'steps'=>$steps]);
    }
    public function deleteProcedure(Request $request){
        
        $preauth_procedure = PreauthProcedure::where('id',$request->id)->first();
        $preauth_register_id = $preauth_procedure->preauth_register_id;
        $preauth_procedure = PreauthProcedure::where('id',$request->id)->first();
        $preauth_doc_ids = @$preauth_procedure->procedure->mandatory_documents_pre_auth;
        if($preauth_doc_ids){
            PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->whereIn('investigation_id',explode(",",$preauth_doc_ids))->delete();
        }
        PreauthProcedure::where('id',$request->id)->delete();
        Helpers::checkandUpdateSurgicalPackage($preauth_register_id);
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->get();
        $html = view('preauth._partials.procedures',['procedures'=>$procedures])->render();
        $investigation_html = view('preauth._partials.investigations',['investigations'=>Helpers::getInvestigations($preauth_register_id),'preauth_register_id'=>$preauth_register_id])->render();
        $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id);
        $finance_html = view('preauth._partials.finance',['procedures'=>$procedures])->render();
        $finance_total_html = view('preauth._partials.finance-total',['procedures'=>$procedures])->render();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Procedure Delete Successfully!','html'=>$html,'finance_html'=>$finance_html,'finance_total_html'=>$finance_total_html,'investigation_html'=>$investigation_html,'preauth_investigation_status'=>$preauth_investigation_status,'steps'=>$steps]);
    }
    public function deleteImplant(Request $request){
        
        $preauth_procedure = PreauthProcedure::where('id',$request->id)->first();
        $preauth_register_id = $preauth_procedure->preauth_register_id;
        $preauth_procedure->implant_id = null;
        $preauth_procedure->implant_price = 0;
        $preauth_procedure->save();
        Helpers::checkandUpdateSurgicalPackage($preauth_register_id);
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->get();
        $html = view('preauth._partials.procedures',['procedures'=>$procedures])->render();
        $investigation_html = view('preauth._partials.investigations',['investigations'=>Helpers::getInvestigations($preauth_register_id),'preauth_register_id'=>$preauth_register_id])->render();
        $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id);
        $finance_html = view('preauth._partials.finance',['procedures'=>$procedures])->render();
        $finance_total_html = view('preauth._partials.finance-total',['procedures'=>$procedures])->render();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Implant Delete Successfully!','html'=>$html,'finance_html'=>$finance_html,'finance_total_html'=>$finance_total_html,'investigation_html'=>$investigation_html,'preauth_investigation_status'=>$preauth_investigation_status,'steps'=>$steps]);
    }
    public function careTeam(Request $request){
        
        $validatedData = $request->validate([
            'care_team_id' => 'required',
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');

        $preauth_care_team = PreauthCareTeam::where('preauth_register_id',$preauth_register_id)->where('hospital_team_id',$request->care_team_id)->first();
        if($preauth_care_team){
            return response()->json(['success' => false, 'message' => 'Care Team Doctor already added. You can\'t add more!']);
        }
        $preauth_care_team = new PreauthCareTeam;
        $preauth_care_team->preauth_register_id = $preauth_register_id;
        $preauth_care_team->hospital_team_id = $request->care_team_id;
        $preauth_care_team->save();

        $preauth_teams = PreauthCareTeam::where('preauth_register_id',$preauth_register_id)->get();
        $html = view('preauth._partials.teams',['preauth_teams'=>$preauth_teams])->render();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Team Saved Successfully!','html'=>$html,'steps'=>$steps]);
    }
    public function deleteTeam(Request $request){
        
        $preauth_care_team = PreauthCareTeam::where('id',$request->id)->first();
        $preauth_register_id = $preauth_care_team->preauth_register_id;
       
        PreauthCareTeam::where('id',$request->id)->delete();
        $preauth_teams = PreauthCareTeam::where('preauth_register_id',$preauth_register_id)->get();
        $html = view('preauth._partials.teams',['preauth_teams'=>$preauth_teams])->render();
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Team Delete Successfully!','html'=>$html,'steps'=>$steps]);
    }
    public function investigation(Request $request){

        $preauth_register_id = \Session::get('preauth_register_id');
        $is_resubmission = @$request->is_resubmission??0;
        $is_enhancement = @$request->is_enhancement??0;
        $initiate_flag = ($is_resubmission == 1 || $is_enhancement == 1)?1:0;
        $investigations=Helpers::getInvestigations($preauth_register_id,$initiate_flag);
        $rules = [];
        $messages = [];
        foreach ($investigations as $key => $investigation) {
            $preauth_investigation = PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('investigation_id',$investigation->id)->first();
            if(!$preauth_investigation && $investigation->is_required){
                $rules['investigation_' . $investigation->id.'_doc'] = 'required|mimes:pdf|max:10240';
                $messages['investigation_' . $investigation->id.'_doc'] = 'File Type / Size is not in correct format';
            }
        }
        if($is_enhancement == 1){
            
            $temp_enhancement_id = \Session::get('temp_enhancement_id');
            $bed_side_photo = PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Bed Side Photo')->first();
            if(!$bed_side_photo){
                $rules['bed_side_photo'] = 'required|mimes:pdf|max:10240';
                $messages['bed_side_photo.required'] = 'The Document for Bed Side Photo is required.';
                $messages['bed_side_photo.mimes'] = 'The Document for Bed Side Photo must be a file of type: pdf.';
            }
            $clinical_notes = PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Clinical Notes')->first();
            if(!$clinical_notes){
                $rules['clinical_notes'] = 'required|mimes:pdf|max:10240';
                $messages['clinical_notes.required'] = 'The Document for Clinical Notes is required.';
                $messages['clinical_notes.mimes'] = 'The Document for Clinical Notes must be a file of type: pdf.';
            }
        }
        // $validatedData = $request->validate($rules);
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }
        $check = PreauthRegister::where('id',$preauth_register_id)->first();
        foreach ($investigations as $key => $investigation) {
            
            if ($request->hasFile('investigation_' . $investigation->id.'_doc')) {
                $filePath = $request->file('investigation_' . $investigation->id.'_doc')->store('investigation', 'public'); // Store in "storage/app/public/profiles"
                $ctg_file = $filePath;
                        
                $check_investigation = PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('investigation_id',$investigation->id)->first();
                $array = [
                    'preauth_register_id' => $preauth_register_id,
                    'investigation_id' => $investigation->id,
                    'file' => $filePath,
                ];
                if(!$check_investigation){
                    $array['is_resubmission'] = $initiate_flag;
                }
                $check->investigations()->updateOrCreate(['investigation_id' => $investigation->id], $array);
            }
        }
        
        if($is_enhancement == 1){
            
            if ($request->hasFile('bed_side_photo')) {
                $filePath = $request->file('bed_side_photo')->store('investigation', 'public');
                        
                $array = [
                    'temp_enhancement_id' => $temp_enhancement_id,
                    'preauth_register_id' => $preauth_register_id,
                    'name' => 'Bed Side Photo',
                    'file' => $filePath,
                ];
                $check->enhancement_docs()->updateOrCreate(['temp_enhancement_id' => $temp_enhancement_id,'name' => 'Bed Side Photo'], $array);
            }
            if ($request->hasFile('clinical_notes')) {
                $filePath = $request->file('clinical_notes')->store('investigation', 'public');
                        
                $array = [
                    'temp_enhancement_id' => $temp_enhancement_id,
                    'preauth_register_id' => $preauth_register_id,
                    'name' => 'Clinical Notes',
                    'file' => $filePath,
                ];
                $check->enhancement_docs()->updateOrCreate(['temp_enhancement_id' => $temp_enhancement_id,'name' => 'Clinical Notes'], $array);
            }
            if ($request->hasFile('any_other_document')) {
                $filePath = $request->file('any_other_document')->store('investigation', 'public');
                        
                $array = [
                    'temp_enhancement_id' => $temp_enhancement_id,
                    'preauth_register_id' => $preauth_register_id,
                    'name' => 'Any Other Document',
                    'file' => $filePath,
                ];
                $check->enhancement_docs()->updateOrCreate(['temp_enhancement_id' => $temp_enhancement_id,'name' => 'Any Other Document'], $array);
            }
        }
        $inhancement_docs_html = '';
        $investigation_html = view('preauth._partials.investigations',['investigations'=>Helpers::getInvestigations($preauth_register_id,$initiate_flag),'preauth_register_id'=>$preauth_register_id])->render();
        
        if($is_enhancement == 1){
            $inhancement_docs_html = view('preauth._partials.inhancement-docs',['preauth_register_id'=>$preauth_register_id,'temp_enhancement_id'=>$temp_enhancement_id])->render();
        }
        $steps = Helpers::fillCompleteStep($preauth_register_id);
        return response()->json(['success' => true, 'message' => 'Investigation Saved Successfully!','investigation_html'=>$investigation_html,'inhancement_docs_html'=>$inhancement_docs_html,'steps'=>$steps]);
    }
    public function requestFormSumbit(Request $request){
        $preauth_register_id = \Session::get('preauth_register_id');
        $checkActive = Helpers::checkHospitalActive($preauth_register_id);
        if(!$checkActive){
            return response()->json(['success' => false, 'message' => 'The hospital is inactive. Therefore, you cannot submit a preauth.']);
        }
        $resubmission = $request->is_resubmission;
        $enhancement = $request->is_enhancement;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
        if($resubmission) {
            PreauthProcedure::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_resubmission_delete', 1)->delete();
            PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_resubmission_delete',1)->delete();
            
            PreauthProcedure::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_resubmission', 1)->update(['is_resubmission'=>0]);
            PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_resubmission',1)->update(['is_resubmission'=>0]);
            $preauth_register->is_resubmit_done=1;
        }
        if($enhancement) {
            PreauthProcedure::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_enhancement', 1)->update(['is_enhancement'=>0]);
            PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_enhancement',1)->update(['is_enhancement'=>0]);
            
            $temp_enhancement_id = \Session::get('temp_enhancement_id');
            PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->update(['is_draft'=>0]);
            \Session::forget(['temp_enhancement_id']);
        }
        $preauth_register->status=PreauthRegister::STATUS_PREAUTH_PENDING;
        $preauth_register->preauth_submission_date=date('Y-m-d');
        $preauth_register->preauth_initiated_amount = Helpers::getPreauthIntiateAmount($preauth_register_id);
        $preauth_register->preauth_approved_amount = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id)-Helpers::getDeductionAmount($preauth_register_id);
        $preauth_register->preauth_amount_without_deduction = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id);
        $preauth_register->save();
        
        if($resubmission) {
            $log_data = array(
                'stage' => 'Preauthorization',
                'type' => 'Resubmission',
                'remarks' => 'N/A',
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,3);
            $msg = 'Pre-Authorization Re-Submited Successfully!';
        } else if($enhancement) {
            $log_data = array(
                'stage' => 'Preauthorization',
                'type' => 'Enhancement',
                'remarks' => 'N/A',
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,3);
            $msg = 'Pre-Authorization Enhancement Successfully!';
        } else {
            $log_data = array(
                'stage' => 'Preauthorization',
                'type' => 'New',
                'remarks' => 'N/A',
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,3);
            $msg = 'Pre-Authorization Submited Successfully!';
        }
        
        return response()->json(['success' => true, 'message' => $msg,'case_id'=>$preauth_register->register_id]);        
    }
    public function cancelRegistration(Request $request){
        
        $validatedData = $request->validate([
            'registration_id' => 'required',
            'cancel_reason' => 'required',
        ]);
        $preauth_register_id = $request->registration_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->where('status',PreauthRegister::STATUS_REGISTER)->first();
        if($preauth_register){
            $preauth_register->status = PreauthRegister::STATUS_CANCELLED;
            $preauth_register->cancel_reason = $request->cancel_reason;
            $preauth_register->cancel_remarks = $request->remarks;
            $preauth_register->save();
            return response()->json(['success' => true, 'message' => 'Cancelled Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Only the registered beneficiary is to be cancel!']);
        }
    }
    public function cancelPreauth(Request $request){
        
        $validatedData = $request->validate([
            'registration_id' => 'required',
            'cancel_reason' => 'required',
        ]);
        $preauth_register_id = $request->registration_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_PREAUTH_PENDING])->first();
        if($preauth_register){
            $preauth_register->status = PreauthRegister::STATUS_PREAUTH_CANCELLED;
            $preauth_register->cancel_reason = $request->cancel_reason;
            $preauth_register->cancel_remarks = $request->remarks;
            $preauth_register->save();
            return response()->json(['success' => true, 'message' => 'Cancelled Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not cancel this preauth!']);
        }
    }
    public function dischargePatient(Request $request){
        
        $validatedData = $request->validate([
            'discharge_patient_id' => 'required',
            'discharge_type' => 'required',
            'discharge_stage' => 'required',
            'lama_date' => 'required_if:discharge_type,LAMA/DAMA/DOPR',
            'surgery_date' => 'required_if:discharge_type,LAMA/DAMA/DOPR,DAMA,Normal,RHC',
            'death_date' => 'required_if:discharge_type,Death',
            'discharge_date' => 'required_if:discharge_type,DAMA,Normal,RHC',
            'provide_medicine' => 'required_if:discharge_type,LAMA/DAMA/DOPR,Normal,RHC',
            'death_certificate' => 'required_if:discharge_type,Death',
            'death_summary' => 'required_if:discharge_type,Death',
            'mortality_audit_report' => 'required_if:discharge_type,Death',
            'in_treatment_photo' => 'required_if:discharge_type,LAMA/DAMA/DOPR,DAMA',
            'feedback_form' => 'required_if:discharge_type,Normal,RHC,LAMA/DAMA/DOPR,DAMA',
            'beneficiary_verification_form' => 'required_if:discharge_type,Normal,RHC,LAMA/DAMA/DOPR,DAMA',
            'hospital_certificate' => 'required_if:discharge_type,Normal,RHC,LAMA/DAMA/DOPR,DAMA',
            'post_surgery_photo' => 'required_if:discharge_type,Normal,RHC',
            'discharge_summary' => 'required_if:discharge_type,Normal,RHC',
        ]);
        $preauth_register_id = $request->discharge_patient_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_PREAUTH_APPROVED])->first();
        if($preauth_register){
            $death_certificate='';
            $death_summary='';
            $mortality_audit_report='';
            $in_treatment_photo='';
            $post_surgery_photo='';
            $discharge_summary='';
            $feedback_form='';
            $beneficiary_verification_form='';
            $hospital_certificate='';
            if ($request->hasFile('death_certificate')) {
                $filePath = $request->file('death_certificate')->store('discharge', 'public');
                $death_certificate = $filePath;
            }
            if ($request->hasFile('death_summary')) {
                $filePath = $request->file('death_summary')->store('discharge', 'public');
                $death_summary = $filePath;
            }
            if ($request->hasFile('mortality_audit_report')) {
                $filePath = $request->file('mortality_audit_report')->store('discharge', 'public');
                $mortality_audit_report = $filePath;
            }
            if ($request->hasFile('in_treatment_photo')) {
                $filePath = $request->file('in_treatment_photo')->store('discharge', 'public');
                $in_treatment_photo = $filePath;
            }
            if ($request->hasFile('post_surgery_photo')) {
                $filePath = $request->file('post_surgery_photo')->store('discharge', 'public');
                $post_surgery_photo = $filePath;
            }
            if ($request->hasFile('discharge_summary')) {
                $filePath = $request->file('discharge_summary')->store('discharge', 'public');
                $discharge_summary = $filePath;
            }
            if ($request->hasFile('feedback_form')) {
                $filePath = $request->file('feedback_form')->store('discharge', 'public');
                $feedback_form = $filePath;
            }
            if ($request->hasFile('beneficiary_verification_form')) {
                $filePath = $request->file('beneficiary_verification_form')->store('discharge', 'public');
                $beneficiary_verification_form = $filePath;
            }
            if ($request->hasFile('hospital_certificate')) {
                $filePath = $request->file('hospital_certificate')->store('discharge', 'public');
                $hospital_certificate = $filePath;
            }
            $preauth_register->status = PreauthRegister::STATUS_CLAIM_SUBMITTED;
            $preauth_register->claim_submited_date = Carbon::now();
            $preauth_register->discharge_type = $request->discharge_type;
            $preauth_register->discharge_stage = $request->discharge_stage;
            $preauth_register->lama_date = $request->lama_date;
            $preauth_register->surgery_date = $request->surgery_date;
            $preauth_register->discharge_date = $request->discharge_date;
            $preauth_register->provide_medicine = $request->provide_medicine;
            $preauth_register->death_certificate = $death_certificate;
            $preauth_register->death_summary = $death_summary;
            $preauth_register->mortality_audit_report = $mortality_audit_report;
            $preauth_register->in_treatment_photo = $in_treatment_photo;
            $preauth_register->post_surgery_photo = $post_surgery_photo;
            $preauth_register->discharge_summary = $discharge_summary;
            $preauth_register->feedback_form = $feedback_form;
            $preauth_register->beneficiary_verification_form = $beneficiary_verification_form;
            $preauth_register->hospital_certificate = $hospital_certificate;
            $check_surgical_procedure = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
            ->whereHas('procedure', function ($query) {
                $query->where('medical_or_surgical', 'Surgical')
                    ->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%'])
                    ->orderBy('price', 'desc');
            })
            ->with(['procedure' => function ($query) {
                $query->orderBy('price', 'desc');
            }])
            ->get()->first();
            if($check_surgical_procedure && $request->discharge_type != 'Normal'){
                $total_preauth_amount = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id,0);
                if($request->discharge_type == 'LAMA/DAMA/DOPR' && $request->discharge_stage == 'Before Surgery'){
                    $preauth_register->deduction_discharge_amount = $total_preauth_amount;
                    $preauth_register->deduction_discharge_text = 'LAMA/DAMA/DOPR Before Surgery Dischange Deduction';
                }elseif($request->discharge_type == 'LAMA/DAMA/DOPR' && $request->discharge_stage == 'After Surgery'){
                    $preauth_register->deduction_discharge_amount = round($total_preauth_amount*0.25);
                    $preauth_register->deduction_discharge_text = 'LAMA/DAMA/DOPR After Surgery Dischange Deduction';
                }elseif($request->discharge_type == 'Death' && $request->discharge_stage == 'Before Surgery'){
                    $preauth_register->deduction_discharge_amount = $total_preauth_amount;
                    $preauth_register->deduction_discharge_text = 'Death Before Surgery Dischange Deduction';
                }elseif($request->discharge_type == 'Death' && $request->discharge_stage == 'During Surgery'){
                    $preauth_register->deduction_discharge_amount = round($total_preauth_amount*0.25);
                    $preauth_register->deduction_discharge_text = 'Death During Surgery Dischange Deduction';
                }elseif($request->discharge_type == 'RHC' && $request->discharge_stage == 'Refer before PAC and surgery'){
                    $preauth_register->deduction_discharge_amount = $total_preauth_amount;
                    $preauth_register->deduction_discharge_text = 'RHC Refer Before PAC and Surgery Dischange Deduction';
                }elseif($request->discharge_type == 'RHC' && $request->discharge_stage == 'Refer after PAC but before surgery'){
                    $preauth_register->deduction_discharge_amount = round($total_preauth_amount*0.85);
                    $preauth_register->deduction_discharge_text = 'RHC Refer After PAC But Before Surgery Dischange Deduction';
                }elseif($request->discharge_type == 'RHC' && $request->discharge_stage == 'Refer after surgery'){
                    $preauth_register->deduction_discharge_amount = round($total_preauth_amount*0.85);
                    $preauth_register->deduction_discharge_text = 'RHC Refer After Surgery Dischange Deduction';
                }

            }else{
                $preauth_register->deduction_discharge_amount = null;
                $preauth_register->deduction_discharge_text = null;
            }
            $preauth_register->save();
            
            $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
            $preauth_register->preauth_initiated_amount = Helpers::getPreauthIntiateAmount($preauth_register_id);
            $preauth_register->preauth_approved_amount = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id)-Helpers::getDeductionAmount($preauth_register_id);
            $preauth_register->preauth_amount_without_deduction = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id);
            $preauth_register->save();
            return response()->json(['success' => true, 'message' => 'Patient Discharged Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not discharge this preauth!']);
        }
    }
    public function claimPatient(Request $request){
        
        $preauth_register_id = \Session::get('preauth_register_id');
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_CLAIM_SUBMITTED])->first();
        if($preauth_register){
            $rules = [];
            $messages = [];
            $rules['claim_patient_id'] = 'required';
            $rules['bill_no'] = 'required';
            $rules['bill_date'] = 'required|date';
            $rules['claim_amount'] = 'required';
            $rules['hospital_bill'] = 'required|mimes:pdf|max:5120';
            $rules['claim_other_doc'] = 'nullable|mimes:pdf|max:5120';
            $investigations=Helpers::getPostInvestigations($preauth_register_id);
            foreach ($investigations as $key => $investigation) {
                if($investigation->is_required){
                    $rules['investigation_' . $investigation->id.'_doc'] = 'required|mimes:pdf|max:5120';
                    $messages['investigation_' . $investigation->id.'_doc'] = 'File Type / Size is not in correct format';
                }
            }
            // $validatedData = $request->validate($rules);
            $validator = \Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();

                return response()->json([
                    'message' => $errors->first(), // Get the first error message
                    'errors' => $errors->messages() // Get all errors keyed by input field
                ], 422);
            }
            $preauth_register_id = $request->claim_patient_id;
            $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
            foreach ($investigations as $key => $investigation) {
                
                if ($request->hasFile('investigation_' . $investigation->id.'_doc')) {
                    $filePath = $request->file('investigation_' . $investigation->id.'_doc')->store('claim', 'public');
                    
                    $array = [
                        'preauth_register_id' => $preauth_register_id,
                        'investigation_id' => $investigation->id,
                        'file' => $filePath,
                    ];
                    $preauth_register->claim_investigations()->updateOrCreate(['investigation_id' => $investigation->id], $array);
                }
            }
            $hospital_bill='';
            $claim_other_doc='';
            if ($request->hasFile('hospital_bill')) {
                $filePath = $request->file('hospital_bill')->store('claim', 'public');
                $hospital_bill = $filePath;
            }
            if ($request->hasFile('claim_other_doc')) {
                $filePath = $request->file('claim_other_doc')->store('claim', 'public');
                $claim_other_doc = $filePath;
            }
            $preauth_register->bill_no = $request->bill_no;
            $preauth_register->bill_date = $request->bill_date;
            $preauth_register->claim_amount = $request->claim_amount;
            $preauth_register->hospital_bill = $hospital_bill;
            $preauth_register->claim_other_doc = $claim_other_doc;
            $preauth_register->status = PreauthRegister::STATUS_CLAIM_PENDING;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'New',
                'remarks' => 'N/A',
            );
            Helpers::addCaseLog($preauth_register->id,$log_data);
            return response()->json(['success' => true, 'message' => 'Patient Claim Initiate Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not claim this preauth!']);
        }
    }
    public function queryPreauth(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'query_remarks' => 'required',
            'preauth_query_supporting_doc' => 'required|mimes:pdf',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED])->first();
        if($preauth_register){
            $preauth_query_supporting_doc='';
            $preauth_query_add_doc='';
            if ($request->hasFile('preauth_query_add_doc')) {
                $filePath = $request->file('preauth_query_add_doc')->store('preauth', 'public');
                $preauth_query_add_doc = $filePath;
            }
            if ($request->hasFile('preauth_query_supporting_doc')) {
                $filePath = $request->file('preauth_query_supporting_doc')->store('preauth', 'public');
                $preauth_query_supporting_doc = $filePath;
            }
            $preauth_register->status = PreauthRegister::STATUS_PREAUTH_PENDING;
            $preauth_register->preauth_query_supporting_doc = $preauth_query_supporting_doc;
            $preauth_register->preauth_query_add_doc = $preauth_query_add_doc;
            $preauth_register->query_remarks = $request->query_remarks;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Preauthorization',
                'type' => 'Preauthorization - Queried',
                'remarks' => $preauth_register->query_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data);
            return response()->json(['success' => true, 'message' => 'Query Submited Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not submit query this preauth!']);
        }
    }
    public function u100QueryPreauth(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'u100_query_remarks' => 'required',
            'query_supporting_doc' => 'required|mimes:pdf',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED])->first();
        if($preauth_register){
            $query_supporting_doc='';
            if ($request->hasFile('query_supporting_doc')) {
                $filePath = $request->file('query_supporting_doc')->store('preauth', 'public');
                $query_supporting_doc = $filePath;
            }
            if($preauth_register->status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED){
                $preauth_register->committee_query_supporting_doc = $query_supporting_doc;
                $preauth_register->committee_query_response_remarks = $request->u100_query_remarks;
                $preauth_register->status = PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING;
            }elseif($preauth_register->status == PreauthRegister::STATUS_CEO_QUERIED){
                $preauth_register->ceo_query_supporting_doc = $query_supporting_doc;
                $preauth_register->ceo_query_response_remarks = $request->u100_query_remarks;
                $preauth_register->status = PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED;
            }elseif($preauth_register->status == PreauthRegister::STATUS_ACS_QUERIED){
                $preauth_register->acs_query_supporting_doc = $query_supporting_doc;
                $preauth_register->acs_query_response_remarks = $request->u100_query_remarks;
                $preauth_register->status = PreauthRegister::STATUS_ACS_PENDING;
            }
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Preauthorization',
                'type' => 'Preauthorization - Queried',
                'remarks' => $request->u100_query_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data);
            return response()->json(['success' => true, 'message' => 'Query Submited Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not submit query this preauth!']);
        }
    }
    public function queryClaim(Request $request){
        
        $validatedData = $request->validate([
            'claim_register_id' => 'required',
            'claim_query_remarks' => 'required',
            'claim_query_supporting_doc' => 'required|mimes:pdf',
        ]);
        $preauth_register_id = $request->claim_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_CLAIM_QUERIED])->first();
        if($preauth_register){
            $claim_query_supporting_doc='';
            $claim_query_add_doc='';
            if ($request->hasFile('claim_query_add_doc')) {
                $filePath = $request->file('claim_query_add_doc')->store('preauth', 'public');
                $claim_query_add_doc = $filePath;
            }
            if ($request->hasFile('claim_query_supporting_doc')) {
                $filePath = $request->file('claim_query_supporting_doc')->store('preauth', 'public');
                $claim_query_supporting_doc = $filePath;
            }
            $preauth_register->status = PreauthRegister::STATUS_CPD_CLAIM_PENDING;
            $preauth_register->claim_query_supporting_doc = $claim_query_supporting_doc;
            $preauth_register->claim_query_add_doc = $claim_query_add_doc;
            $preauth_register->claim_query_remarks = $request->claim_query_remarks;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Claim - Queried',
                'remarks' => $preauth_register->claim_query_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data);
            return response()->json(['success' => true, 'message' => 'Query Submited Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not submit query this preauth!']);
        }
    }
    public function raiseErrorneousClaim(Request $request){
        
        $validatedData = $request->validate([
            'erroneous_register_id' => 'required',
            'erroneous_raise_amount' => 'required|numeric',
            'erroneous_raise_remarks' => 'required',
            'erroneous_raise_supporting_doc' => 'required|mimes:pdf',
        ]);
        $preauth_register_id = $request->erroneous_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_CLAIM_PAID_BY_BANK])->first();
        if($preauth_register){
            $erroneous_raise_supporting_doc='';
            if ($request->hasFile('erroneous_raise_supporting_doc')) {
                $filePath = $request->file('erroneous_raise_supporting_doc')->store('preauth', 'public');
                $erroneous_raise_supporting_doc = $filePath;
            }
            $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING;
            $preauth_register->erroneous_raise_supporting_doc = $erroneous_raise_supporting_doc;
            $preauth_register->erroneous_raise_amount = $request->erroneous_raise_amount;
            $preauth_register->erroneous_raise_remarks = $request->erroneous_raise_remarks;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Claim - Erroneous Raised',
                'remarks' => $preauth_register->erroneous_raise_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,2);
            return response()->json(['success' => true, 'message' => 'Erroneous Raised Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not submit erroneous this preauth!']);
        }
    }
    public function errorneousQueryClaim(Request $request){
        
        $validatedData = $request->validate([
            'erroneous_register_id' => 'required',
            'erroneous_raise_amount' => 'required|numeric',
            'erroneous_raise_remarks' => 'required',
            'erroneous_query_supporting_doc' => 'required|mimes:pdf',
        ]);
        $preauth_register_id = $request->erroneous_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED])->first();
        if($preauth_register){
            $erroneous_query_supporting_doc='';
            if ($request->hasFile('erroneous_query_supporting_doc')) {
                $filePath = $request->file('erroneous_query_supporting_doc')->store('preauth', 'public');
                $erroneous_query_supporting_doc = $filePath;
            }
            $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING;
            $preauth_register->erroneous_query_supporting_doc = $erroneous_query_supporting_doc;
            $preauth_register->erroneous_raise_amount = $request->erroneous_raise_amount;
            $preauth_register->erroneous_raise_remarks = $request->erroneous_raise_remarks;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Claim - Erroneous Query Response',
                'remarks' => $preauth_register->erroneous_raise_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,2);
            return response()->json(['success' => true, 'message' => 'Erroneous Responsed Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'You can not submit erroneous this preauth!']);
        }
    }

    public function loadremark(Request $request, $id) {
        $procedure_id = $request->id;
        $procedure = PreauthProcedure::where('id',$procedure_id)->where('preauth_register_id', $id)->first();
        if($procedure) {
            $chats = $procedure->preauthRemark()->where('type', $request->type)->get();;
            return view('remarklist', compact('chats'));
        } else {
            return response()->json(['success' => false, 'message' => 'Preauth Procedure is not found!!'], 422);
        }
    }

    public function addRemark(Request $request, $id) {
        $procedureid = $request->procedureid;
        $procedure = PreauthProcedure::where('id',$procedureid)->where('preauth_register_id', $id)->first();
        if($procedure) {
            $validatedData = $request->validate([
                'content' => 'required',
            ]);
            $data = $procedure->preauthRemark()->create([
                'content' => $request->content,
                'content_id' => $procedureid,
                'type' => $request->type,
                'added_by' => auth()->user()->id, // Optional
            ]);

            $role = $data->user->role->name;
            $time = date('G:i A', strtotime($data->created_at));
            return response()->json(['success' => true, 'message' => 'Remark Added successfully!!', 'content' => $request->content, 'time' => $time, 'role' => $role]);
        } else {
            return response()->json(['success' => false, 'message' => 'Preauth Procedure is not found!!']);
        }
    }
    public function resubmitPreauth(Request $request){
        
        $procedure = Procedure::where('id',$request->procedure_id)->first();
        $validatedData = $request->validate([
            'speciality_id' => 'required',
            'procedure_id' => 'required',
            'no_of_days' => 'required',
            'type' => 'required',
            'u100_amount' => [
                function ($attribute, $value, $fail) use ($procedure) {
                    if ($procedure && $procedure->procedure_code_1 == 'U100' && empty($value)) {
                        $fail('The Unverfied Amount field is required when procedure code is U100.');
                    }
                },
            ],
        ]);
        $preauth_register_id = \Session::get('preauth_register_id');
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();

        $preauth_procedure = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('procedure_id',$request->procedure_id)->first();
        $preauth_procedure = new PreauthProcedure;
            
        if($request->type == 'resubmission'){
            $preauth_procedure->is_resubmission = 1;
            $preauth_procedure->is_resubmission_delete = 0;
        }else if($request->type == 'enhancement'){
            $preauth_procedure->is_enhancement = 1;
            $preauth_procedure->is_resubmission = 0;
            $preauth_procedure->is_resubmission_delete = 0;
        }
        $preauth_procedure->preauth_register_id = $preauth_register_id;
        $preauth_procedure->procedure_id = $request->procedure_id;
        $preauth_procedure->speciality_id = $request->speciality_id;
        $preauth_procedure->implant_id = $request->implant_id;
        if($request->implant_id != ''){
            $preauth_procedure->implant_id = $request->implant_id;
            $implant = Implant::where('id',$request->implant_id)->first();
            $preauth_procedure->implant_price = $implant->price;
            $preauth_procedure->implant_qty = $request->implant_qty;
        }
        if($request->stratification_id != ''){
            $preauth_procedure->stratification_id = $request->stratification_id;
            $stratification = Stratification::where('id',$request->stratification_id)->first();
            $preauth_procedure->stratification_price = $stratification->price;
        }
        $hospital_accreditation = HospitalAccreditation::where('hospital_id',auth()->user()->hospital_id)->first();
        if($preauth_register->scheme_id == 1){
            if($hospital_accreditation && $hospital_accreditation->accreditation =='Yes' && $hospital_accreditation->accreditation_id !=''){
                $procedure_price = $procedure->price;
            }else{
                $procedure_price = $procedure->non_nabh_price;
            }
        }else{
            if($request->u100_amount){
                $procedure_price = $request->u100_amount;
            }else{
                $procedure_price = $procedure->price;
            }
        }
        $preauth_procedure->original_price = $procedure_price;
        $preauth_procedure->procedure_price = $procedure_price;
        if($hospital_accreditation && $hospital_accreditation->accreditation =='Yes' && $hospital_accreditation->accreditation_id !='' && $preauth_register->scheme_id != 1){
            if(@$hospital_accreditation->accred->percentage && @$hospital_accreditation->accred->percentage != 0){
                $preauth_procedure->incentive = (@$hospital_accreditation->accred->percentage*$procedure_price)/100;
                $preauth_procedure->incentive_per = @$hospital_accreditation->accred->percentage;
            }
        }
        $preauth_procedure->no_of_days = $request->no_of_days;
        $preauth_procedure->save();

        $preauth_doc_ids = @$preauth_procedure->procedure->mandatory_documents_pre_auth;
        if($preauth_doc_ids){
            PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->whereIn('investigation_id',explode(",",$preauth_doc_ids))->update(['is_resubmission_delete'=>0]);
        }
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
        if($request->type == 'resubmission'){
            $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        }else{
            $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_enhancement'=>1])->render();
        }
        $investigation_html = view('preauth._partials.investigations',['investigations'=>Helpers::getInvestigations($preauth_register_id,1),'preauth_register_id'=>$preauth_register_id])->render();
        $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id,1);
        $finance_html = view('preauth._partials.finance',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        $finance_total_html = view('preauth._partials.finance-total',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        return response()->json(['success' => true, 'message' => 'Procedure Saved Successfully!','html'=>$html,'finance_html'=>$finance_html,'finance_total_html'=>$finance_total_html,'investigation_html'=>$investigation_html,'preauth_investigation_status'=>$preauth_investigation_status]);
    }
    public function procedureDeleteTemp(Request $request){
        
        $preauth_procedure = PreauthProcedure::where('id',$request->id)->withoutGlobalScopes()->first();
        $preauth_register_id = $preauth_procedure->preauth_register_id;
        $preauth_doc_ids = @$preauth_procedure->procedure->mandatory_documents_pre_auth;
        if($preauth_doc_ids){
            PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->whereIn('investigation_id',explode(",",$preauth_doc_ids))->update(['is_resubmission_delete'=>1]);
        }
        PreauthProcedure::where('id',$request->id)->withoutGlobalScopes()->update(['is_resubmission_delete'=>1]);
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
        $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        $investigation_html = view('preauth._partials.investigations',['investigations'=>Helpers::getInvestigations($preauth_register_id,1),'preauth_register_id'=>$preauth_register_id])->render();
        $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id);
        $finance_html = view('preauth._partials.finance',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        $finance_total_html = view('preauth._partials.finance-total',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        return response()->json(['success' => true, 'message' => 'Procedure Delete Successfully!','html'=>$html,'finance_html'=>$finance_html,'finance_total_html'=>$finance_total_html,'investigation_html'=>$investigation_html,'preauth_investigation_status'=>$preauth_investigation_status]);
    }
    public function procedureDeleteTempImplant(Request $request){
        
        $preauth_procedure = PreauthProcedure::where('id',$request->id)->withoutGlobalScopes()->first();
        $preauth_register_id = $preauth_procedure->preauth_register_id;
        $preauth_procedure->is_implant_enhance_or_resubmission = 1;
        $preauth_procedure->save();
        
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
        if($request->type == 'resubmission'){
            $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        }else{
            $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_enhancement'=>1])->render();
        }
        $finance_html = view('preauth._partials.finance',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        $finance_total_html = view('preauth._partials.finance-total',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        return response()->json(['success' => true, 'message' => 'Implant Delete Successfully!','html'=>$html,'finance_html'=>$finance_html,'finance_total_html'=>$finance_total_html]);
    }
    public function procedureEnhancementDelete(Request $request){
        
        $preauth_procedure = PreauthProcedure::where('id',$request->id)->withoutGlobalScopes()->first();
        $preauth_register_id = $preauth_procedure->preauth_register_id;
        $preauth_doc_ids = @$preauth_procedure->procedure->mandatory_documents_pre_auth;
        if($preauth_doc_ids){
            PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->whereIn('investigation_id',explode(",",$preauth_doc_ids))->update(['is_resubmission_delete'=>1]);
        }
        PreauthProcedure::where('id',$request->id)->withoutGlobalScopes()->delete();
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
        $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_enhancement'=>1])->render();
        $investigation_html = view('preauth._partials.investigations',['investigations'=>Helpers::getInvestigations($preauth_register_id,1),'preauth_register_id'=>$preauth_register_id])->render();
        $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register_id);
        $finance_html = view('preauth._partials.finance',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        $finance_total_html = view('preauth._partials.finance-total',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        return response()->json(['success' => true, 'message' => 'Procedure Delete Successfully!','html'=>$html,'finance_html'=>$finance_html,'finance_total_html'=>$finance_total_html,'investigation_html'=>$investigation_html,'preauth_investigation_status'=>$preauth_investigation_status]);
    }
    public function refreshResubmit(Request $request){
        $preauth_register_id = $request->resubmission_patient_id;
        PreauthProcedure::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->update(['is_resubmission_delete'=>0,'is_implant_enhance_or_resubmission'=>0]);
        PreauthProcedure::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_resubmission',1)->delete();
        PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_resubmission',1)->delete();
        PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->update(['is_resubmission_delete'=>0]);

        PreauthProcedure::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_enhancement',1)->delete();
        PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_enhancement',1)->delete();

        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
        if($request->type == 'resubmission'){
            $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_resubmission'=>1])->render();
        }else{
            PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->withoutGlobalScopes()->where('is_draft','0')->delete();
            $temp_enhancement_id = $preauth_register_id."-".rand(99999,999999);
            \Session::put('temp_enhancement_id', $temp_enhancement_id);
            $html = view('preauth._partials.procedures',['procedures'=>$procedures,'is_enhancement'=>1])->render();
        }
        return response()->json(['success' => true,'html'=>$html]);
    }
    public function test_verification(){
        
        try {
            $benificiary = Benificiary::where('card_id','P6Q9ELGLP')->first();

            if (!$benificiary) {
                return response()->json(['success' => false, 'msg' => 'Beneficiary Not Found']);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_SSL_OPTIONS => CURLSSLOPT_ALLOW_BEAST,
                ]
            ])
            ->post('https://betasha.uk.gov.in/AyushAPI/BIS/TMSVerification', [
                "nhaid" => $benificiary->card_id,
                "yearOfBirth" => "",
                "hhid" => $benificiary->family_id,
                "hhdtype" => "",
                "address" => [
                    "pinCode" => "",
                    "statelgdCode" => "",
                    "address" => "",
                    "subdistrictlgdCode" => "",
                    "districtlgdCode" => "",
                    "ruralUrban" => "",
                    "village_townlgdCode" => ""
                ],
                "gender" => "",
                "memberName" => "",
                "mobileNumber" => "",
                "member_id" => $benificiary->member_id
            ]);

            if ($response->successful()) {
                $data_arr = $response->json();
                echo "<pre>";print_r($data_arr);exit;
            }

        } catch (\Throwable $e) {
            echo "Errors :".$e->getMessage();
        }
    }
    public function updateOldPreauthAmount(){
        $registers = PreauthRegister::get();
        foreach($registers as $preauth_register){
            
            $total_preauth_amount = Helpers::getPreauthAmountWithoutDeduction($preauth_register->id);
            $total_deducted_amount = Helpers::getDeductionAmount($preauth_register->id);
            if($total_preauth_amount-$total_deducted_amount > 0){
                $preauth_register->claim_approved_amount = $total_preauth_amount-$total_deducted_amount;
            }else{
                $preauth_register->claim_approved_amount = 0;
            }
            $preauth_register->preauth_initiated_amount = Helpers::getPreauthIntiateAmount($preauth_register->id);
            $preauth_register->preauth_approved_amount = $preauth_register->claim_approved_amount;
            $preauth_register->preauth_amount_without_deduction = $total_preauth_amount;
            $preauth_register->save();
        }
    }
    
    public function updateOldPreauthProcedures(){
        $procedures = PreauthProcedure::get();
        foreach($procedures as $procedure){
            $procedure->original_price = @$procedure->procedure->price;
            $procedure->save();
        }
    }
    public function testMail(Request $request)
    {
        $toEmail = $request->input('email', 'parthdholariya7738@gmail.com');
    
        try {
            Mail::raw('This is a plain text test email sent via Laravel SMTP.', function ($message) use ($toEmail) {
                $message->to($toEmail)
                        ->subject('Test Mail from Laravel SMTP');
            });
    
            return response()->json(['status' => 'Email sent successfully to ' . $toEmail]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Error sending email', 'message' => $e->getMessage()], 500);
        }
    }
}
