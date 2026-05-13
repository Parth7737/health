<?php

namespace App\Http\Controllers\SHA;

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
    Hospitals,
    Recovery,
};
use Carbon\Carbon;
use DataTables;

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
            return view('sha.preauth-request',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('sha.dashboard')->with('error','Register Not Found');
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
            return view('sha.past-history',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('sha.dashboard')->with('error','Register Not Found');
        }
    }
    public function approvePreauth(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'preauth_status' => 'required',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->where('status',PreauthRegister::STATUS_ACO_CLAIM_APPROVED)->first();

        if($preauth_register){

            $preauth_register->status = $request->preauth_status;
            $preauth_register->sha_remark = $request->sha_remark;
            $preauth_register->sha_status_added_by = auth()->user()->id;
            $preauth_register->sha_status_update_date = date('Y-m-d H:i:s');
            if($request->preauth_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_SHA_CLAIM_APPROVED;
                $preauth_register->claim_paid_date = Carbon::now();
                $status = 'Approved';
                
                $recovery = Recovery::where('status',Recovery::STATUS_APPROVED)->where('hospital_id',$preauth_register->hospital_id)->first();
                if($recovery && $preauth_register->claim_approved_amount > 0){
                    if($recovery->recovery_amount != $recovery->recovered_amount){
                        $pending_recovery = $recovery->recovery_amount-$recovery->recovered_amount;
                        if($preauth_register->claim_approved_amount >= $pending_recovery){
                            $preauth_register->recovery_amount = $pending_recovery;
                            $recovery->recovered_amount = $recovery->recovered_amount+$pending_recovery;
                            $recovery->status = Recovery::STATUS_COMPLETED;
                        }else{
                            $partial_recovery_amount = $preauth_register->claim_approved_amount;
                            $preauth_register->recovery_amount = $partial_recovery_amount;
                            $recovery->recovered_amount = $recovery->recovered_amount+$partial_recovery_amount;
                        }
                    }
                    $recovery->save();
                }

            }elseif($request->preauth_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_SHA_CLAIM_REJECTED;
                $status = 'Rejected';
                $preauth_register->claim_paid_date = null;
            }elseif($request->preauth_status == 'Revoked to CPD'){
                $preauth_register->status = PreauthRegister::STATUS_SHA_CLAIM_QUERIED;
                $status = 'Revoked to CPD';
                $preauth_register->claim_paid_date = null;
                $preauth_register->claim_aco_approved_date = null;
            }

            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'New',
                'remarks' => $preauth_register->sha_remark,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,1);
            return response()->json(['success' => true, 'message' => 'Claim Actioned Successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Only the pending claim is to be Submitted!']);
        }
    }

    public function bulkApprove(Request $request) {
        $validatedData = $request->validate([
            'preauthid' => 'required|array',
            'preauth_status' => 'required',
            'remarks' => 'required'
        ]);

        $preauthregister = PreauthRegister::whereIn('id',$request->preauthid)->where('status',PreauthRegister::STATUS_ACO_CLAIM_APPROVED)->get();
        foreach($preauthregister as $key => $value) {
            $value->status = PreauthRegister::STATUS_SHA_CLAIM_APPROVED;
            $value->sha_remark = $request->remarks;
            $value->sha_status_added_by = auth()->user()->id;    
            $value->sha_status_update_date = date('Y-m-d H:i:s');
            $recovery = Recovery::where('status',Recovery::STATUS_APPROVED)->where('hospital_id',$value->hospital_id)->first();
            if($recovery && $value->claim_approved_amount > 0){
                if($recovery->recovery_amount != $recovery->recovered_amount){
                    $pending_recovery = $recovery->recovery_amount-$recovery->recovered_amount;
                    if($value->claim_approved_amount >= $pending_recovery){
                        $value->recovery_amount = $pending_recovery;
                        $recovery->recovered_amount = $recovery->recovered_amount+$pending_recovery;
                        $recovery->status = Recovery::STATUS_COMPLETED;
                    }else{
                        $partial_recovery_amount = $value->claim_approved_amount;
                        $value->recovery_amount = $partial_recovery_amount;
                        $recovery->recovered_amount = $recovery->recovered_amount+$partial_recovery_amount;
                    }
                }
                $recovery->save();
            }
            $value->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'New',
                'remarks' => $value->sha_remark,
            );
            Helpers::addCaseLog($value->id,$log_data,1);
        }

        return response()->json(['success' => true, 'message' => 'Claim Actioned Successfully!']);
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
        return view('sha._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
        
    }

    public function verifydocument(Request $request, $id) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($request->type == 'preauth_query_supporting_doc' || $request->type == 'committee_query_supporting_doc' || $request->type == 'ceo_query_supporting_doc' || $request->type == 'acs_query_supporting_doc' || $request->type =='born_baby_birth_certificate' || $request->type == 'preauth_query_add_doc' || $request->type == 'claim_query_supporting_doc' || $request->type == 'claim_query_add_doc' || $request->type == 'death_certificate' || $request->type == 'death_summary' || $request->type == 'mortality_audit_report' || $request->type == 'in_treatment_photo' || $request->type == 'post_surgery_photo' || $request->type == 'discharge_summary' || $request->type == 'feedback_form' || $request->type == 'beneficiary_verification_form' ||  $request->type == 'hospital_certificate' || $request->type == 'hospital_bill' || $request->type == 'claim_other_doc' || $request->type == 'erroneous_raise_supporting_doc' || $request->type == 'erroneous_query_supporting_doc' || $request->type == "hospital_declaration_form") {

            $documentStatus = $preauth_register->sha_status ? json_decode($preauth_register->sha_status, true) : [];
            $documentStatus[$request->type] = $request->status;
            $preauth_register->sha_status = json_encode($documentStatus);
           
            $preauth_register->save();

            $docname = ucwords(str_replace('_', ' ', $request->type));
            $html = '';//view('sha._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

            $documentsinfo = Helpers::checkDocStepSeen($id,'sha_status');
            return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $docname.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
        } else if($request->type == 'claim') {
            if($request->id) {
                $investigation = $preauth_register->claim_investigations()->where('id', $request->id)->first();
                $investigation->sha_status = $request->status;
                $investigation->sha_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = '';//view('sha._partials.documentdetails',['preauth_register'=>$preauth_register])->render();
    
                $documentsinfo = Helpers::checkDocStepSeen($id,'sha_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else if($request->type == 'enhancement'){

            if($request->id) {
                $investigation = $preauth_register->enhancement_docs()->where('id', $request->id)->first();
                $investigation->sha_status = $request->status;
                $investigation->sha_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();

                $documentsinfo = Helpers::checkDocStepSeen($id,'sha_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!']);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        } else {
            if($request->id) {
                $investigation = $preauth_register->investigations()->where('id', $request->id)->first();
                $investigation->sha_status = $request->status;
                $investigation->sha_status_verify_date = date('Y-m-d H:i:s');
                $investigation->save();
                $html = '';//view('sha._partials.documentdetails',['preauth_register'=>$preauth_register])->render();

                $documentsinfo = Helpers::checkDocStepSeen($id,'sha_status');
                return response()->json(['success' => true,'documentsinfo'=> $documentsinfo, 'message' => $investigation->investigation->name.' document '. ($request->status == "Correct" ? 'Verified' : 'Declined') .' successfully!!', 'html' => $html]);
            } else{
                return response()->json(['success' => false, 'message' => 'Please provide a ID']);
            }
        }       
    }
    public function erroneousClaimAction(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'remarks' => 'required',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED])->first();
        if($preauth_register){
            $status = '';
            if($request->erroneous_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_APPROVED;
                $preauth_register->erroneous_claim_paid_date = Carbon::now();
                $status = 'Approved';
            }elseif($request->erroneous_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_REJECTED;
                $preauth_register->erroneous_claim_paid_date = null;
                $status = 'Rejected';
            }elseif($request->erroneous_status == 'Revoked to CPD'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED;
                $preauth_register->erroneous_claim_paid_date = null;
                $preauth_register->erroneous_claim_aco_approved_date = null;
                $status = 'Revoked to CPD';
            }
            $preauth_register->erroneous_sha_remarks = $request->remarks;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Erroneous Claim - '.$status,
                'remarks' => $preauth_register->erroneous_sha_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,4);
            return response()->json(['success' => true, 'message' => $status.' Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Only the pending erroneous claim is to be approve!']);
        }
    }
    
    public function hospitalRecoveryAmount(){
        $hospitals = Hospitals::where('is_empanelled',1)->get();
        return view('sha.hospital-recovery-amount',compact('hospitals'));
    }
    public function searchRecoveryHospitals(Request $request)
    {
        $query = Recovery::select('id','hospital_id','recovery_amount','recovered_amount','status','remarks','recovery_supporting_doc','created_at');
        $query->whereHas('hospital',function($query2) use($request){
            $query2->where('is_empanelled', 1);
        });
        if ($request->hospital_code) {
            $query->whereHas('hospital',function($query2) use($request){
                $query2->where('hospital_id', 'like', '%' . $request->hospital_id . '%');
            });
        }
        if ($request->hospital_id) {
            $query->where('hospital_id', $request->hospital_id);
        }
        if ($request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->facility_ownership_type) {
            $query->whereHas('hospital',function($query2) use($request){
                $query2->where('facility_ownership_type', $request->facility_ownership_type);
            });
        }
        if ($request->district_id) {
            $query->whereHas('hospital',function($query2) use($request){
                $query2->whereHas('hospitalAddress',function($query3) use($request){
                    $query3->where('district', $request->district_id);
                });
            });
        }
        if ($request->scheme_type_id) {
            $query->whereHas('hospital',function($query2) use($request){
                $query2->where('scheme', $request->scheme_type_id);
            });
        }

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return $row->status == 0? '<button class="btn btn-sm  btn-outline-primary me-2" onclick="updateStatus('.$row->id.',1)">Approve</button><button class="btn btn-sm btn-outline-danger" onclick="updateStatus('.$row->id.',2)">Reject</button>':'';
            })
            ->addColumn('facility_name', function ($row) {
                return '<div class="btn--container justify-content-center">
                            <a class="" 
                            href="' . route('sha.hospital-recovery', $row->hospital_id) . '">
                            ' . e($row->hospital->facility_name) . '
                            </a>
                        </div>';
            })
            ->addColumn('hospital_code', function ($row) {
                return @$row->hospital->hospital_id?@$row->hospital->hospital_id:'-';
            })
            ->addColumn('requested_date', function ($row) {
                return date('d/m/Y H:i A', strtotime($row->created_at));
            })
            ->addColumn('status', function ($row) {
                return $row->status_label;
            })
            ->addColumn('is_payment_stop', function ($row) {
                return @$row->hospital->is_payment_stop ? 'Yes' : 'No';
            })
            ->addColumn('recovery_supporting_doc', function ($row) {
                return $row->recovery_supporting_doc ? '<div class="btn--container justify-content-center">
                            <a href="' . url('storage/' . $row->recovery_supporting_doc) . '" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                        </div>' : '-';
            })
            ->rawColumns(['action','facility_name','recovery_supporting_doc'])
            ->make(true);
    }
    public function hospitalRecovery($id){
        $hospital = Hospitals::find($id);
        return view('sha.hospital-recovery',compact('hospital'));
    }
    public function hospitalRecoveryHistory(Request $request, $id)
    {
        $query = Recovery::select('id', 'hospital_id', 'approved_date', 'status', 'created_at', 'remarks', 'recovery_supporting_doc', 'recovery_amount', 'recovered_amount')
            ->where('hospital_id', $id)
            ->latest();

        return DataTables::of($query)
            ->addColumn('date', function ($row) {
                return date('d/m/Y H:i A', strtotime($row->created_at));
            })
            ->addColumn('status', function ($row) {
                return $row->status_label;
            })
            ->addColumn('recovery_supporting_doc', function ($row) {
                return $row->recovery_supporting_doc ? '<div class="btn--container justify-content-center">
                            <a href="' . url('storage/' . $row->recovery_supporting_doc) . '" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                        </div>' : '-';
            })
            ->rawColumns(['recovery_supporting_doc'])
            ->make(true);
    }
    public function updateRecoveryStatus(Request $request){
        $recovery = Recovery::where('id',$request->id)->where('status',Recovery::STATUS_REQUESTED)->first();
        if($recovery){
            if($request->status == 1){
                $recovery->status = Recovery::STATUS_APPROVED;
            }else{
                $recovery->status = Recovery::STATUS_REJECTED;
            }
            $recovery->save();
            return response()->json(['success' => true, 'message' => 'Updated Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Only the pending request is to be approve or reject!']);
        }
    }
}
