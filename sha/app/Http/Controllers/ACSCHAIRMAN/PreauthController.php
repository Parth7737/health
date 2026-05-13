<?php

namespace App\Http\Controllers\ACSCHAIRMAN;

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
            return view('acschairman.preauth-request',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('acschairman.dashboard')->with('error','Register Not Found');
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
            return view('acschairman.past-history',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('acschairman.dashboard')->with('error','Register Not Found');
        }
    }
    public function approvePreauth(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'preauth_status' => 'required',
            'remarks' => 'required',
            'approved_amount' => 'required_if:preauth_status,Approve',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->where('status',PreauthRegister::STATUS_ACS_PENDING)->first();

        if($preauth_register){
            
            $status = '';
            $preauth_procedure = PreauthProcedure::where('preauth_register_id',$preauth_register->id)->first();
            if($request->preauth_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_PREAUTH_APPROVED;
                $preauth_register->acs_approved_date = Carbon::now();
                $preauth_register->preauth_approved_date = Carbon::now();
                $preauth_register->acs_approved_amount = $request->approved_amount;
                $status = 'Approved';

                $preauth_procedure->deducted_amount = $preauth_procedure->procedure_price-$request->approved_amount;
                $preauth_procedure->save();
            }elseif($request->preauth_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_ACS_REJECTED;
                $preauth_register->acs_approved_date = null;
                $preauth_register->preauth_approved_date = null;
                $preauth_register->acs_approved_amount = null;
                $preauth_register->preauth_approved_amount = null;
                $status = 'Rejected';
                $preauth_procedure->deducted_amount = 0;
                $preauth_procedure->save();
            }elseif($request->preauth_status == 'Query'){
                $preauth_register->status = PreauthRegister::STATUS_ACS_QUERIED;
                $preauth_register->acs_approved_date = null;
                $preauth_register->preauth_approved_date = null;
                $preauth_register->acs_approved_amount = null;
                $preauth_register->preauth_approved_amount = null;
                $status = 'Queried';
                $preauth_procedure->deducted_amount = 0;
                $preauth_procedure->save();
            }
            $preauth_register->preauth_approved_amount = Helpers::getPreauthAmountWithoutDeduction($preauth_register->id)-Helpers::getDeductionAmount($preauth_register->id);
            $preauth_register->preauth_amount_without_deduction = $preauth_register->preauth_approved_amount;
            $preauth_register->acs_remarks = $request->remarks;
            $preauth_register->acs_forwarded_by = auth()->user()->id;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Preauthorization - ACS/Chairman',
                'type' => 'Preauthorization - '.$status,
                'remarks' => $preauth_register->acs_remarks,
            );
            if($request->preauth_status == 'Approve'){
                Helpers::addCaseLog($preauth_register->id,$log_data,5,$request->approved_amount);
            }else{
                Helpers::addCaseLog($preauth_register->id,$log_data);
            }
            return response()->json(['success' => true, 'message' => $status.' Successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Only the pending preauth is to be approve!']);
        }
    }
    public function loadpdf(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        // if($request->id) {
        //     $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
        //     return view('acschairman._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
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
        return view('acschairman._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
    }

    public function verifydocument(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($request->type == 'preauth_query_supporting_doc' || $request->type == 'committee_query_supporting_doc' || $request->type == 'ceo_query_supporting_doc' || $request->type == 'acs_query_supporting_doc' || $request->type == 'born_baby_birth_certificate' || $request->type == 'preauth_query_add_doc' || $request->type == "hospital_declaration_form") {

            $documentStatus = $preauth_register->acs_status ? json_decode($preauth_register->acs_status, true) : [];
            $documentStatus[$request->type] = $request->status;
            $preauth_register->acs_status = json_encode($documentStatus);
            
            $preauth_register->save();

            $docname = ucwords(str_replace('_', ' ', $request->type));
            $documentsinfo = Helpers::checkDocStepSeen($id,'acs_status');
            return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $docname.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!']);
        } else if($request->type == 'enhancement'){

            if($request->id) {
                $investigation = $preauth_register->enhancement_docs()->where('id', $request->id)->first();
                $investigation->acs_status = $request->status;
                $investigation->save();

                $documentsinfo = Helpers::checkDocStepSeen($id,'acs_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!']);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else{
            if($request->id) {
                $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
                $investigation->acs_status = $request->status;
                $investigation->save();

                $documentsinfo = Helpers::checkDocStepSeen($id,'acs_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!']);
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
