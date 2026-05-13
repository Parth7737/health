<?php

namespace App\Http\Controllers\PPD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    PreauthRegister,
    GeneralInfo,
    Benificiary,
    Hospitals,
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
    CaseLog,
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
            return view('ppd.preauth-request',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('ppd.dashboard')->with('error','Register Not Found');
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
            return view('ppd.past-history',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('ppd.dashboard')->with('error','Register Not Found');
        }
    }
    public function approvePreauth(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'preauth_status' => 'required',
            'remarks' => 'required',
            'procedure_status' => 'required|array',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->where('status',PreauthRegister::STATUS_PREAUTH_PENDING)->first();
        if($preauth_register){
            $reasons = $request->reason;
            $rejected_implant_ids=[];
            $rejected_ids=[];
            foreach($request->procedure_status as $procedure_id => $procedure_status){
                $preauth_procedure = PreauthProcedure::where('id',$procedure_id)->first();
                if($procedure_status == 'Rejected' || $procedure_status == 'Query'){
                    if(isset($reasons[$procedure_id])){
                        $preauth_procedure->preauth_reason = $reasons[$procedure_id];
                    }else{
                        $preauth_procedure->preauth_reason = '';
                    }
                }
                $preauth_procedure->preauth_status = $procedure_status;
                $preauth_procedure->save();
                if($procedure_status == 'Rejected'){
                    $rejected_ids[] = $procedure_id;
                }
            }
            if(isset($request->implant_status)){
                $reasons = $request->implant_reason;
                foreach($request->implant_status as $procedure_id => $implant_status){
                    $preauth_procedure = PreauthProcedure::where('id',$procedure_id)->first();
                    if($implant_status == 'Rejected' || $implant_status == 'Query'){
                        if(isset($reasons[$procedure_id])){
                            $preauth_procedure->preauth_implant_reason = $reasons[$procedure_id];
                        }else{
                            $preauth_procedure->preauth_implant_reason = '';
                        }
                    }
                    $preauth_procedure->preauth_implant_status = $implant_status;
                    $preauth_procedure->save();
                    
                    if($implant_status == 'Rejected'){
                        $rejected_implant_ids[] = $procedure_id;
                    }
                }
            }
            $status = '';
            if($request->preauth_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_PREAUTH_APPROVED;
                $preauth_register->preauth_approved_date = Carbon::now();
                $status = 'Approved';

            }elseif($request->preauth_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_PREAUTH_REJECTED;
                $preauth_register->preauth_approved_date = null;
                $status = 'Rejected';
            }elseif($request->preauth_status == 'Query'){
                $preauth_register->status = PreauthRegister::STATUS_PREAUTH_QUERIED;
                $preauth_register->preauth_approved_date = null;
                $status = 'Queried';
            }elseif($request->preauth_status == 'Forwarded To Medical Committee'){
                $preauth_register->status = PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING;
                $preauth_register->preauth_approved_date = null;
                $status = 'Forwarded To Medical Committee';
            }
            $preauth_register->preauth_approve_remarks = $request->remarks;
            $preauth_register->preauth_approve_reject_query_by = auth()->user()->id;
            $preauth_register->save();
            
            Helpers::checkandUpdateSurgicalPackage($preauth_register_id);
            $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
            $preauth_register->preauth_approved_amount = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id,0)-Helpers::getDeductionAmount($preauth_register_id);
            $preauth_register->preauth_amount_without_deduction = Helpers::getPreauthAmountWithoutDeduction($preauth_register_id,0);
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Preauthorization',
                'type' => 'Preauthorization - '.$status,
                'remarks' => $preauth_register->preauth_approve_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data);
            return response()->json(['success' => true, 'message' => $status.' Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Only the pending preauth is to be approve!']);
        }
    }
    public function calculateTotal(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $procedures = Helpers::updatePackageCalculation($preauth_register_id,$request->rejected_ids,$request->rejected_implant_ids);
        $finance_total_html = view('ppd._partials.finance-total',['procedures'=>$procedures])->render();
        
        return response()->json(['success' => true,'finance_total_html'=>$finance_total_html]);
    }

    public function loadpdf(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        // if($request->id) {
        //     $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
        //     return view('ppd._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
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
        } else if($request->type == 'enhancement') { 
            if($request->id) {
                $investigation = $preauth_register->enhancement_docs()->where('id', $request->id)->first();
                $investigation->type = 'enhancement';
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        }  else {
            if($request->id) {
                $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        }
        return view('ppd._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
    }

    public function verifydocument(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($request->type == 'preauth_query_supporting_doc' || $request->type == 'committee_query_supporting_doc' || $request->type == 'ceo_query_supporting_doc' || $request->type == 'acs_query_supporting_doc' || $request->type == 'born_baby_birth_certificate' || $request->type == 'preauth_query_add_doc' || $request->type == 'hospital_declaration_form') {

            $documentStatus = $preauth_register->ppd_status ? json_decode($preauth_register->ppd_status, true) : [];
            $documentStatus[$request->type] = $request->status;
            $preauth_register->ppd_status = json_encode($documentStatus);
            
            $preauth_register->save();

            $docname = ucwords(str_replace('_', ' ', $request->type));
            $documentsinfo = Helpers::checkDocStepSeen($id,'ppd_status');
            return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $docname.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!']);
        } else if($request->type == 'enhancement'){

            if($request->id) {
                $investigation = $preauth_register->enhancement_docs()->where('id', $request->id)->first();
                $investigation->ppd_status = $request->status;
                $investigation->ppd_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();

                $documentsinfo = Helpers::checkDocStepSeen($id,'ppd_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!']);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else{
            if($request->id) {
                $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
                $investigation->ppd_status = $request->status;
                $investigation->ppd_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();

                $documentsinfo = Helpers::checkDocStepSeen($id,'ppd_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!']);
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
    public function caseLog(Request $request){
        $case_logs = CaseLog::where("preauth_register_id",$request->case_id)->get();
        if($case_logs){
            return response()->json(['success' => true, 'html' => view('ppd._partials.case-logs',['case_logs'=>$case_logs])->render()]);
        }else{
            return response()->json(['success' => false, 'message' => 'Case is not found!!']);
        }
    }
    public function caseProfile(Request $request){
        $preauth_register = PreauthRegister::where("id",$request->case_id)->first();
        if($preauth_register){
            return response()->json(['success' => true, 'html' => view('ppd._partials.case-profile',['preauth_register'=>$preauth_register])->render()]);
        }else{
            return response()->json(['success' => false, 'message' => 'Case is not found!!']);
        }
    }
    public function hospitalProfile(Request $request){
        $hospital = Hospitals::where("id",$request->hospital_id)->first();
        if($hospital){
            return response()->json(['success' => true, 'html' => view('ppd._partials.hospital-profile',['hospital'=>$hospital])->render()]);
        }else{
            return response()->json(['success' => false, 'message' => 'Hospital is not found!!']);
        }
    }
}
