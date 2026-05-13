<?php

namespace App\Http\Controllers\CPD;

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
use Carbon\Carbon;

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
            return view('cpd.preauth-request',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('cpd.dashboard')->with('error','Register Not Found');
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
            return view('cpd.past-history',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('cpd.dashboard')->with('error','Register Not Found');
        }
    }
   
    public function approvePreauthClaim(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'preauth_status' => 'required',
            'remarks' => 'required',
            'procedure_status' => 'required|array',
            'diagnosis_support_evidence_name' => 'required',
            'diagnosis_support_evidence' => 'required',
            'diagnosis_support_evidence_procedure_id' => 'required_if:diagnosis_support_evidence,N|array',
            'case_as_per_stg_name' => 'required',
            'case_as_per_stg' => 'required',
            'case_as_per_stg_procedure_id' => 'required_if:case_as_per_stg,N|array',
            'weather_duration_treatment_name' => 'required',
            'weather_duration_treatment' => 'required',
            'weather_duration_treatment_procedure_id' => 'required_if:weather_duration_treatment,N|array',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_CPD_CLAIM_PENDING, PreauthRegister::STATUS_ACO_CLAIM_QUERIED, PreauthRegister::STATUS_SHA_CLAIM_QUERIED])->first();
        if($preauth_register){
            $reasons = $request->reason;
            foreach($request->procedure_status as $procedure_id => $procedure_status){
                $preauth_procedure = PreauthProcedure::where('id',$procedure_id)->first();
                if($procedure_status == 'Rejected' || $procedure_status == 'Query'){
                    if(isset($reasons[$procedure_id])){
                        $preauth_procedure->preauth_claim_reason = $reasons[$procedure_id];
                    }else{
                        $preauth_procedure->preauth_claim_reason = '';
                    }
                }
                $preauth_procedure->preauth_claim_status = $procedure_status;
                $preauth_procedure->save();
            }
            if(isset($request->implant_status)){
                $reasons = $request->implant_reason;
                foreach($request->implant_status as $procedure_id => $implant_status){
                    $preauth_procedure = PreauthProcedure::where('id',$procedure_id)->first();
                    if($implant_status == 'Rejected' || $implant_status == 'Query'){
                        if(isset($reasons[$procedure_id])){
                            $preauth_procedure->preauth_claim_implant_reason = $reasons[$procedure_id];
                        }else{
                            $preauth_procedure->preauth_claim_implant_reason = '';
                        }
                    }
                    $preauth_procedure->preauth_claim_implant_status = $implant_status;
                    $preauth_procedure->save();
                }
            }
            $status = '';
            if($request->preauth_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_CLAIM_APPROVED;
                $preauth_register->claim_approved_date = Carbon::now();
                $status = 'Approved';
            }elseif($request->preauth_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_CLAIM_REJECTED;
                $status = 'Rejected';
                $preauth_register->claim_approved_date = null;
            }elseif($request->preauth_status == 'Query'){
                $preauth_register->status = PreauthRegister::STATUS_CLAIM_QUERIED;
                $status = 'Queried';
                $preauth_register->claim_approved_date = null;
            }
            $preauth_register->claim_approve_remarks = $request->remarks;
            $preauth_register->claim_approve_reject_query_by = auth()->user()->id;

            $jsonData = [
                'diagnosis_support' => [
                    'name' => $validatedData['diagnosis_support_evidence_name'],
                    'radio_value' => $validatedData['diagnosis_support_evidence'],
                    'procedure_id' => $validatedData['diagnosis_support_evidence_procedure_id'] ?? []
                ],
                'case_as_per_stg' => [
                    'name' => $validatedData['case_as_per_stg_name'],
                    'radio_value' => $validatedData['case_as_per_stg'],
                    'procedure_id' => $validatedData['case_as_per_stg_procedure_id'] ?? []
                ],
                'weather_duration_treatment' => [
                    'name' => $validatedData['weather_duration_treatment_name'],
                    'radio_value' => $validatedData['weather_duration_treatment'],
                    'procedure_id' => $validatedData['weather_duration_treatment_procedure_id'] ?? []
                ]
            ];

            $preauth_register->cpd_extra_details = json_encode($jsonData);
            $preauth_register->save();
            Helpers::checkandUpdateSurgicalPackage($preauth_register_id);
            
            $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
            $total_preauth_amount = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id,1,1);
            $total_deducted_amount = Helpers::getDeductionAmount($preauth_register_id);
            if($total_preauth_amount-$total_deducted_amount > 0){
                $preauth_register->claim_approved_amount = $total_preauth_amount-$total_deducted_amount;
            }else{
                $preauth_register->claim_approved_amount = 0;
            }
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Claim - '.$status,
                'remarks' => $preauth_register->claim_approve_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,1);
            return response()->json(['success' => true, 'message' => $status.' Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Only the pending preauth is to be approve!']);
        }
    }
    public function erroneousClaimAction(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'erroneous_status' => 'required',
            'erroneous_appoved_amount' => 'required_if:erroneous_status,Approve',
            'remarks' => 'required',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING,PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED])->first();
        if($preauth_register){
            $status = '';
            if($request->erroneous_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED;
                $preauth_register->erroneous_claim_approved_date = Carbon::now();
                $preauth_register->erroneous_appoved_amount = $request->erroneous_appoved_amount;
                $status = 'Approved';
            }elseif($request->erroneous_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED;
                $preauth_register->erroneous_appoved_amount = null;
                $status = 'Rejected';
                $preauth_register->erroneous_claim_approved_date = null;
            }elseif($request->erroneous_status == 'Query'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED;
                $status = 'Queried';
                $preauth_register->erroneous_claim_approved_date = null;
                $preauth_register->erroneous_appoved_amount = null;
            }
            $preauth_register->erroneous_remarks = $request->remarks;
            $preauth_register->erroneous_claim_approve_reject_query_by = auth()->user()->id;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Erroneous Claim - '.$status,
                'remarks' => $preauth_register->erroneous_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,4);
            return response()->json(['success' => true, 'message' => $status.' Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Only the pending erroneous claim is to be approve!']);
        }
    }
    
    public function loadpdf(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        // if($request->id) {
        //     $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
        //     return view('cpd._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
        // } else{
        //     return response()->json(['success' => false, 'message' => 'Please provide a ID']);
        // }

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
        return view('cpd._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
        
    }

    public function verifydocument(Request $request, $id) {
        // $preauth_register = PreauthRegister::where('id',$id)->first();
        // if($request->id) {
        //     $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
        //     $investigation->cpd_status = $request->status;
        //     $investigation->cpd_status_verify_date = date('Y-m-d H:i:s');
        //     $investigation->save();

        //     return response()->json(['success' => true, 'message' => $investigation->investigation->name.' document verified successfully!!']);
        // } else{
        //     return response()->json(['success' => false, 'message' => 'Please provide a ID']);
        // }

        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($request->type == 'preauth_query_supporting_doc' || $request->type == 'committee_query_supporting_doc' || $request->type == 'ceo_query_supporting_doc' || $request->type == 'acs_query_supporting_doc' || $request->type == 'born_baby_birth_certificate' || $request->type == 'preauth_query_add_doc' || $request->type == 'claim_query_supporting_doc' || $request->type == 'claim_query_add_doc' || $request->type == 'death_certificate' || $request->type == 'death_summary' || $request->type == 'mortality_audit_report' || $request->type == 'in_treatment_photo' || $request->type == 'post_surgery_photo' || $request->type == 'discharge_summary' || $request->type == 'feedback_form' ||$request->type == 'beneficiary_verification_form' || $request->type == 'beneficiary_verification_form' || $request->type == 'hospital_certificate' || $request->type == 'hospital_bill' || $request->type == 'claim_other_doc' || $request->type == 'erroneous_raise_supporting_doc' || $request->type == 'erroneous_query_supporting_doc' || $request->type == "hospital_declaration_form") {

            $documentStatus = $preauth_register->cpd_status ? json_decode($preauth_register->cpd_status, true) : [];
            $documentStatus[$request->type] = $request->status;
            $preauth_register->cpd_status = json_encode($documentStatus);
            
            $preauth_register->save();

            $docname = ucwords(str_replace('_', ' ', $request->type));
            $html = view('cpd._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

            $documentsinfo = Helpers::checkDocStepSeen($id,'cpd_status');
            return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $docname.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
        } else if($request->type == 'claim') {
            if($request->id) {
                $investigation = $preauth_register->claim_investigations()->where('id', $request->id)->first();
                $investigation->cpd_status = $request->status;
                $investigation->cpd_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = view('cpd._partials.documentdetails',['preauth_register'=>$preauth_register])->render();
    
                $documentsinfo = Helpers::checkDocStepSeen($id,'cpd_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else if($request->type == 'enhancement'){

            if($request->id) {
                $investigation = $preauth_register->enhancement_docs()->where('id', $request->id)->first();
                $investigation->cpd_status = $request->status;
                $investigation->cpd_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = view('cpd._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

                $documentsinfo = Helpers::checkDocStepSeen($id,'cpd_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        }  else {
            if($request->id) {
                $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
                $investigation->cpd_status = $request->status;
                $investigation->cpd_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = view('cpd._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

                $documentsinfo = Helpers::checkDocStepSeen($id,'cpd_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        }   
    }

    public function loadremark(Request $request, $id) {
        $procedure_id = $request->id;
        $procedure = PreauthProcedure::where('id',$procedure_id)->where('preauth_register_id', $id)->first();
        if($procedure) {
            $chats = $procedure->preauthRemark()->where('type', $request->type)->get();
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
    public function getDeduction(Request $request){
        $preauth_procedure = PreauthProcedure::where("id",$request->procedure_id)->first();
        if($preauth_procedure) {
            $deduction_reason = $preauth_procedure->deduction_reason;
            $deducted_amount = $preauth_procedure->deducted_amount;
            $deduction_remarks = $preauth_procedure->deduction_remarks;

            
            if(@$preauth_procedure->procedure_price == 0 && $preauth_procedure->stratification_price != 0 && $preauth_procedure->no_of_days > 1){
                $preauth_procedure->procedure_price = $preauth_procedure->stratification_price*intval($preauth_procedure->no_of_days ?$preauth_procedure->no_of_days-1: 0);
            }
            $sub_total = @$preauth_procedure->procedure_price+@$preauth_procedure->incentive+@$preauth_procedure->stratification_price;

            return response()->json(['success' => true, 'deduction_reason'=>$deduction_reason,'deducted_amount'=>$deducted_amount,'max'=>$sub_total,'deduction_remarks'=>$deduction_remarks]);
        } else {
            return response()->json(['success' => false, 'message' => "Something Wen't wrong"]);
        }
    }
    public function saveDeduction(Request $request){

        $validatedData = $request->validate([
            'deduction_procedure_id' => 'required',
            'remarks' => 'required_unless:deduct_amount,0',
            'deduct_amount' => 'required|numeric',
        ]);
        $preauth_procedure = PreauthProcedure::where("id",$request->deduction_procedure_id)->first();
        if($preauth_procedure) {
            
            if(@$preauth_procedure->procedure_price == 0 && $preauth_procedure->stratification_price != 0 && $preauth_procedure->no_of_days > 1){
                $preauth_procedure->procedure_price = $preauth_procedure->stratification_price*intval($preauth_procedure->no_of_days ?$preauth_procedure->no_of_days-1: 0);
            }
            // @$preauth_procedure->incentive+
            $sub_total = @$preauth_procedure->procedure_price+@$preauth_procedure->stratification_price;
            if($request->deduct_amount > $sub_total){
                return response()->json(['success' => false, 'message' => "You cannot deduct more than ".$sub_total." amount."]);
            }else{
                $preauth_procedure->deduction_reason = $request->deduction_reason;
                $preauth_procedure->deducted_amount = $request->deduct_amount??0;
                $preauth_procedure->deduction_remarks = $request->remarks;
                if(@$preauth_procedure->incentive_per > 0) {
                    $newprocedureprice = @$preauth_procedure->procedure_price - $preauth_procedure->deducted_amount;
                    $newincentive = (($newprocedureprice * @$preauth_procedure->incentive_per) / 100);
                    $preauth_procedure->incentive = $newincentive;
                }
                $preauth_procedure->save();
                $procedures = PreauthProcedure::where('preauth_register_id',$preauth_procedure->preauth_register_id)->get();
                $finance_total_html = view('cpd._partials.finance-total',['procedures'=>$procedures])->render();
                return response()->json(['success' => true,'deducted_amount'=>$preauth_procedure->deducted_amount,'deducted_amount_format'=>"₹".number_format($preauth_procedure->deducted_amount,2),'total_amount'=>"₹".number_format(@$sub_total-$preauth_procedure->deducted_amount,2), 'finance_total_html'=>$finance_total_html]);
            }
        } else {
            return response()->json(['success' => false, 'message' => "Something Wen't wrong"]);
        }
    }

    public function calculateTotal(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $rejected_ids = PreauthProcedure::where('preauth_register_id', $request->preauth_register_id)->where('preauth_status', 'Rejected')->pluck('id')->toArray();
        $rejected_implant_ids = PreauthProcedure::where('preauth_register_id', $request->preauth_register_id)->where('preauth_implant_status', 'Rejected')->pluck('id')->toArray();

        $request->rejected_ids = array_unique(array_merge(
            is_array($request->rejected_ids) ? $request->rejected_ids : [],
            $rejected_ids
        ));

        $request->rejected_implant_ids = array_unique(array_merge(
            is_array($request->rejected_implant_ids) ? $request->rejected_implant_ids : [],
            $rejected_implant_ids
        ));

        $procedures = Helpers::updatePackageCalculation($preauth_register_id,$request->rejected_ids,$request->rejected_implant_ids);
        $finance_total_html = view('cpd._partials.finance-total',['procedures'=>$procedures])->render();
        
        return response()->json(['success' => true,'finance_total_html'=>$finance_total_html]);
    }

}
