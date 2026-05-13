<?php

namespace App\Http\Controllers\CEX;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
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
    PreauthDiagnosis,
    HospitalSpeciality,
    Procedure,
    PreauthProcedure,
    HospitalTeam,
    PreauthCareTeam,
    PreauthInvestigation,
    Investigation,
    Implant,
    Stratification,
    HospitalAccreditation,
    FollowupProcedure,
    AddOnProcedure,
    NonAddOnProcedure,
};

class PreauthController extends Controller
{
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
            $hospital_speciality = HospitalSpeciality::where('hospital_id',auth()->user()->hospital_id)->get();
            $speciality_ids = $hospital_speciality->pluck('speciality_id')->filter();
            $teams = HospitalTeam::where('hospital_id',auth()->user()->hospital_id)->whereIn('speciality_id', $speciality_ids)->get();
            $preauth_investigations = PreauthInvestigation::where('preauth_register_id', $preauth_register->id)->get();
            $investigations=Helpers::getInvestigations($preauth_register->id);
            $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register->id);
            $preauth_teams = PreauthCareTeam::where('preauth_register_id',$preauth_register->id)->get();
            $pending_since = $preauth_register->preauth_submission_date;
            $case_profile = $preauth_register->id;
            $hospital_profile = $preauth_register->hospital_id;
            return view('cex.preauth-request',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('cex.dashboard')->with('error','Register Not Found');
        }
    }
    public function pastHistory($id){
        $preauth_register = PreauthRegister::find($id);
        if($preauth_register){
            $general_info = GeneralInfo::where('preauth_register_id',$preauth_register->id)->first();
            $family_history = FamilyHistory::where('preauth_register_id',$preauth_register->id)->first();
            $personal_history = PersonalHistory::where('preauth_register_id',$preauth_register->id)->first();
            $authentication_consent = AuthenticationConsent::where('preauth_register_id',$preauth_register->id)->first();
            $admission_details = AdmissionDetails::where('preauth_register_id',$preauth_register->id)->first();
            $preauth_diagnosis = PreauthDiagnosis::where('preauth_register_id',$preauth_register->id)->get();
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register->id)->get();
            $hospital_speciality = HospitalSpeciality::where('hospital_id',auth()->user()->hospital_id)->get();
            $speciality_ids = $hospital_speciality->pluck('speciality_id')->filter();
            $teams = HospitalTeam::where('hospital_id',auth()->user()->hospital_id)->whereIn('speciality_id', $speciality_ids)->get();
            $preauth_investigations = PreauthInvestigation::where('preauth_register_id', $preauth_register->id)->get();
            $investigations=Helpers::getInvestigations($preauth_register->id);
            $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register->id);
            $preauth_teams = PreauthCareTeam::where('preauth_register_id',$preauth_register->id)->get();
            $pending_since = $preauth_register->preauth_submission_date;
            $case_profile = $preauth_register->id;
            $hospital_profile = $preauth_register->hospital_id;
            return view('cex.past-history',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('cex.dashboard')->with('error','Register Not Found');
        }
    }
    public function approvePreauth(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'preauth_status' => 'required',
            'cex_remark' => 'required'
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->where('status',PreauthRegister::STATUS_CLAIM_PENDING)->first();

        if($preauth_register){
            $preauth_register->status = PreauthRegister::STATUS_CPD_CLAIM_PENDING;
            $preauth_register->cex_remark = $request->cex_remark;
            $preauth_register->claim_forwarded_by = auth()->user()->id;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'New',
                'remarks' => $preauth_register->cex_remark,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data);
            return response()->json(['success' => true, 'message' => 'Forwarded Successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Only the pending preauth is to be forward!']);
        }
    }
    public function loadpdf(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        
        if($request->type == 'preauth_query_supporting_doc') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->preauth_query_supporting_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'preauth_query_supporting_doc';
        }else if($request->type == 'committee_query_supporting_doc') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->committee_query_supporting_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'committee_query_supporting_doc';
        }else if($request->type == 'ceo_query_supporting_doc') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->ceo_query_supporting_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'ceo_query_supporting_doc';
        }else if($request->type == 'acs_query_supporting_doc') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->acs_query_supporting_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'acs_query_supporting_doc';
        } else if($request->type == 'born_baby_birth_certificate') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->born_baby_birth_certificate;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'born_baby_birth_certificate';
        } else if($request->type == 'hospital_declaration_form') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->hospital_declaration_form;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'hospital_declaration_form';
        } else if($request->type == 'preauth_query_add_doc') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->preauth_query_add_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'preauth_query_add_doc';
        } else if($request->type == 'claim_query_supporting_doc') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->claim_query_supporting_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'claim_query_supporting_doc';
        } else if($request->type == 'claim_query_add_doc') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->claim_query_add_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'claim_query_add_doc';
        } else if($request->type == 'death_certificate') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->death_certificate;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'death_certificate';

        } else if($request->type == 'death_summary') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->death_summary;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'death_summary';
        } else if($request->type == 'mortality_audit_report') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->mortality_audit_report;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'mortality_audit_report';
        } else if($request->type == 'in_treatment_photo') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->in_treatment_photo;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'in_treatment_photo';
        } else if($request->type == 'post_surgery_photo') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->post_surgery_photo;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'post_surgery_photo';
        } else if($request->type == 'discharge_summary') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->discharge_summary;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'discharge_summary';
        } else if($request->type == 'feedback_form') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->feedback_form;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'feedback_form';
        } else if($request->type == 'beneficiary_verification_form') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->beneficiary_verification_form;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'beneficiary_verification_form';
        } else if($request->type == 'hospital_certificate') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->hospital_certificate;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'hospital_certificate';
        } else if($request->type == 'hospital_bill') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->hospital_bill;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'hospital_bill';
        } else if($request->type == 'claim_other_doc') { 
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->claim_other_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'claim_other_doc';
        } else if($request->type == 'erroneous_raise_supporting_doc') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->erroneous_raise_supporting_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'erroneous_raise_supporting_doc';
        } else if($request->type == 'erroneous_query_supporting_doc') {
            $investigation = new \stdClass(); // Create a generic object
            $investigation->file = $preauth_register->erroneous_query_supporting_doc;
            $investigation->id = $preauth_register->id;
            $investigation->type = 'erroneous_query_supporting_doc';
        } else if($request->type == 'claim') {
            if($request->id) {
                $investigation = $preauth_register->claim_investigations()->where('id', $request->id)->first();
                $investigation->type = 'claim';
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else if($request->type == 'enhancement') { 
            if($request->id) {
                $investigation = $preauth_register->enhancement_docs()->where('id', $request->id)->first();
                $investigation->type = 'enhancement';
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else {
            if($request->id) {
                $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        }
        return view('cex._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
        
    }

    public function verifydocument(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($request->type == 'preauth_query_supporting_doc' || $request->type == 'committee_query_supporting_doc' || $request->type == 'ceo_query_supporting_doc' || $request->type == 'acs_query_supporting_doc' || $request->type == 'born_baby_birth_certificate' || $request->type == 'preauth_query_add_doc' || $request->type == 'claim_query_supporting_doc' || $request->type == 'claim_query_add_doc' || $request->type == 'death_certificate' || $request->type == 'death_summary' || $request->type == 'mortality_audit_report' || $request->type == 'in_treatment_photo' || $request->type == 'post_surgery_photo' || $request->type == 'discharge_summary' || $request->type == 'feedback_form' || $request->type == 'beneficiary_verification_form' || $request->type == 'hospital_certificate' || $request->type == 'hospital_bill' || $request->type == 'claim_other_doc' || $request->type == 'erroneous_raise_supporting_doc' || $request->type == 'erroneous_query_supporting_doc' || $request->type == "hospital_declaration_form") {

            $documentStatus = $preauth_register->cex_status ? json_decode($preauth_register->cex_status, true) : [];
            $documentStatus[$request->type] = $request->status;
            $preauth_register->cex_status = json_encode($documentStatus);
            if($request->type == "hospital_bill" && $request->cex_hospital_bill_date) {
                $preauth_register->cex_hospital_bill_date = date('Y-m-d', strtotime($request->cex_hospital_bill_date));
            }
            if($request->type == "discharge_summary" && $request->cex_admission_date) {
                $preauth_register->cex_admission_date = date('Y-m-d', strtotime($request->cex_admission_date));
            }
            if($request->type == "discharge_summary" && $request->cex_discharge_date) {
                $preauth_register->cex_discharge_date = date('Y-m-d', strtotime($request->cex_discharge_date));
            }
            $preauth_register->save();

            $docname = ucwords(str_replace('_', ' ', $request->type));
            $html = view('cex._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

            $documentsinfo = Helpers::checkDocStepSeen($id,'cex_status');
            return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $docname.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
        } else if($request->type == 'claim') {
            if($request->id) {
                $investigation = $preauth_register->claim_investigations()->where('id', $request->id)->first();
                $investigation->cex_status = $request->status;
                $investigation->cex_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = view('cex._partials.documentdetails',['preauth_register'=>$preauth_register])->render();
    
                $documentsinfo = Helpers::checkDocStepSeen($id,'cex_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else if($request->type == 'enhancement'){

            if($request->id) {
                $investigation = $preauth_register->enhancement_docs()->where('id', $request->id)->first();
                $investigation->cex_status = $request->status;
                $investigation->cex_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = view('cex._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

                $documentsinfo = Helpers::checkDocStepSeen($id,'cex_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else {
            if($request->id) {
                $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
                $investigation->cex_status = $request->status;
                $investigation->cex_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = view('cex._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

                $documentsinfo = Helpers::checkDocStepSeen($id,'cex_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        }       
    }

    public function openTabs(Request $request) {
        $preauth_register = PreauthRegister::where('id',$request->id)->first();
        if($preauth_register) {
            $array = [
                'type' => $request->type,
                'tab' => $request->tab,
                'is_open' => $request->is_open,
            ];
            $preauth_register->tabs()->updateOrCreate(['type' => $request->type, 'tab' => $request->tab], $array);

            return response()->json(['success' => true, 'message' => 'Data Seen Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => "Something Wen't wrong"]);
        }
    }
}
