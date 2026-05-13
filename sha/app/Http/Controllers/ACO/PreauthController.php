<?php

namespace App\Http\Controllers\ACO;

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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AdjustmentImport;

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
            return view('aco.preauth-request',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('aco.dashboard')->with('error','Register Not Found');
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
            return view('aco.past-history',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('aco.dashboard')->with('error','Register Not Found');
        }
    }
    public function approvePreauth(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'preauth_status' => 'required',
            'aco_remark' => 'required',
            'recovery_amount_adjusted' => 'nullable',
            'bank_details' => 'required',
            'tds_details' => 'required',
            'duplicate_bill' => 'required',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->where('status',PreauthRegister::STATUS_CLAIM_APPROVED)->first();

        if($preauth_register){

            $jsonData = [
                'bank_details' => [
                    'name' => 'Bank Amount Details as per HEM record',
                    'value' => @$validatedData['bank_details']??'InCorrect',
                ],
                'tds_details' => [
                    'name' => 'TDS Details as per HEM Records',
                    'value' => @$validatedData['tds_details']??'InCorrect',
                ],
                'duplicate_bill' => [
                    'name' => 'Is This is a Duplicate Bil?',
                    'value' => @$validatedData['duplicate_bill']??'No',
                ],
                'recovery_amount_adjusted' => [
                    'name' => 'Recovery Amount Adjusted',
                    'value' => @$validatedData['recovery_amount_adjusted']??'No',
                ]
            ];

            $status = '';
            if($request->preauth_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_ACO_CLAIM_APPROVED;
                $preauth_register->claim_aco_approved_date = Carbon::now();
                $status = 'Approved';
            }elseif($request->preauth_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_ACO_CLAIM_REJECTED;
                $status = 'Rejected';
                $preauth_register->claim_aco_approved_date = null;
            }elseif($request->preauth_status == 'Query'){
                $preauth_register->status = PreauthRegister::STATUS_ACO_CLAIM_QUERIED;
                $status = 'Queried';
                $preauth_register->claim_aco_approved_date = null;
            }
            $preauth_register->aco_remark = $request->aco_remark;
            $preauth_register->aco_status_added_by = auth()->user()->id;
            $preauth_register->aco_observation_details = json_encode($jsonData);

            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Claim - '.$status,
                'remarks' => $preauth_register->aco_remark,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,1);
            return response()->json(['success' => true, 'message' => $status.' Successfully!']);
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

        $preauthregister = PreauthRegister::whereIn('id',$request->preauthid)->where('status',PreauthRegister::STATUS_CLAIM_APPROVED)->get();
        foreach($preauthregister as $key => $value) {
            $value->status = PreauthRegister::STATUS_ACO_CLAIM_APPROVED;
            $value->aco_remark = $request->remarks;
            $value->aco_status_added_by = auth()->user()->id;    
            $value->claim_aco_approved_date = Carbon::now();
            $value->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'New',
                'remarks' => $value->aco_remark,
            );
            Helpers::addCaseLog($value->id,$log_data,1);
        }

        return response()->json(['success' => true, 'message' => 'Claim Actioned Successfully!']);
    }
    public function erroneousClaimAction(Request $request){
        
        $validatedData = $request->validate([
            'preauth_register_id' => 'required',
            'remarks' => 'required',
        ]);
        $preauth_register_id = $request->preauth_register_id;
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED])->first();
        if($preauth_register){
            $status = '';
            if($request->erroneous_status == 'Approve'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED;
                $preauth_register->erroneous_claim_aco_approved_date = Carbon::now();
                $status = 'Approved';
            }elseif($request->erroneous_status == 'Reject'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED;
                $preauth_register->erroneous_claim_aco_approved_date = null;
                $status = 'Rejected';
            }elseif($request->erroneous_status == 'Query'){
                $preauth_register->status = PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED;
                $preauth_register->erroneous_claim_aco_approved_date = null;
                $status = 'Queried';
            }
            $preauth_register->erroneous_aco_remarks = $request->remarks;
            $preauth_register->save();
            $log_data = array(
                'stage' => 'Claim',
                'type' => 'Erroneous Claim - '.$status,
                'remarks' => $preauth_register->erroneous_aco_remarks,
            );
            Helpers::addCaseLog($preauth_register->id,$log_data,4);
            return response()->json(['success' => true, 'message' => $status.' Successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Only the pending erroneous claim is to be approve!']);
        }
    }
    public function hospitalRecoveryAmount(){
        $hospitals = Hospitals::where('is_empanelled',1)->get();
        return view('aco.hospital-recovery-amount',compact('hospitals'));
    }
    public function searchRecoveryHospitals(Request $request)
    {
        $query = Hospitals::select('id','facility_name','hospital_id','facility_ownership_type','status','is_payment_stop')->where('is_empanelled', 1);

        if ($request->hospital_code) {
            $query->where('hospital_id', 'like', '%' . $request->hospital_code . '%');
        }
        if ($request->hospital_id) {
            $query->where('id', $request->hospital_id);
        }
        if ($request->facility_ownership_type) {
            $query->where('facility_ownership_type', $request->facility_ownership_type);
        }
        if ($request->district_id) {
            $query->whereHas('hospitalAddress',function($query2) use($request){
                $query2->where('district', $request->district_id);
            });
        }
        if ($request->scheme_type_id) {
            $query->where('scheme', $request->scheme_type_id);
        }

        return DataTables::of($query)
            // ->addColumn('actions', function ($row) {
            //     return '<button class="btn btn-sm btn-primary">View</button>';
            // })
            ->addColumn('facility_name', function ($row) {
                return '<div class="btn--container justify-content-center">
                            <a class="" 
                            href="' . route('aco.hospital-recovery', $row->id) . '">
                            ' . e($row->facility_name) . '
                            </a>
                        </div>';
            })
            ->addColumn('district', function ($row) {
                $district = HospitalDistrict::where('id',@$row->hospitalAddress->district)->first();
                return @$row->hospitalAddress ? $district->name : '-';
            })
            ->addColumn('facility_ownership_type', function ($row) {
                return @$row->facilityOwnershipType ? @$row->facilityOwnershipType->name : '-';
            })
            ->addColumn('is_payment_stop', function ($row) {
                return $row->is_payment_stop ? 'Yes' : 'No';
            })
            ->rawColumns(['actions','facility_name'])
            ->make(true);
    }
    public function hospitalRecovery($id){
        $hospital = Hospitals::find($id);
        return view('aco.hospital-recovery',compact('hospital'));
    }
    public function recoveryRequestSubmit(Request $request){

        $validatedData = $request->validate([
            'hospital_id' => 'required',
            'recovery_amount' => 'required',
            'remarks' => 'required',
            'recovery_supporting_doc' => 'nullable|mimes:pdf|max:5120',
        ]);

        $recovery_supporting_doc='';
        if ($request->hasFile('recovery_supporting_doc')) {
            $filePath = $request->file('recovery_supporting_doc')->store('recovery', 'public');
            $recovery_supporting_doc = $filePath;
        }
        $recovery = new Recovery;
        $recovery->hospital_id = $request->hospital_id;
        $recovery->recovery_amount = $request->recovery_amount;
        $recovery->remarks = $request->remarks;
        $recovery->status = Recovery::STATUS_REQUESTED;
        $recovery->recovery_supporting_doc = $recovery_supporting_doc;
        $recovery->save();

        return response()->json(['success' => true, 'message' => 'Recovery Request Successfully!']);
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
    public function adjustmentUpload(){
        return view('aco.adjustment-upload');
    }
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'upload_excel' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            if ($request->hasFile('upload_excel')) {
                $file = $request->file('upload_excel');
                $file = $request->file('upload_excel')->getPathname();

                $import = new AdjustmentImport();
                $return_array = $import->import($file);

                return response()->json([
                    'success' => true,
                    'message' => 'Total Inserted: ' . $return_array['inserted'] . 
                                ', Total Invalid: ' . $return_array['invalid'] . 
                                ', Total Already Updated: ' . $return_array['already']
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Excel Import Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function bankAccountDetails(Request $request) {
        $hospitals = Hospitals::where('is_empanelled',1)->get();
        return view('aco.bank-account-details', compact('hospitals'));
    }

    public function loadbankdetails(Request $request) {
        $query = Hospitals::with('schemeType', 'financialInformation', 'taxDetails')->select('id','facility_name','hospital_id','scheme', 'facility_ownership_type')->where('is_empanelled', 1);

        if ($request->hospital_id) {
            $query->where('id', $request->hospital_id);
        }

        if ($request->state) {
            $query->whereHas('hospitalAddress',function($query2) use($request){
                $query2->where('state', $request->state_id);
            });
        }

        if ($request->scheme_type_id) {
            $query->where('scheme', $request->scheme_type_id);
        }

        return DataTables::of($query)
            ->addColumn('facility_ownership_type', function ($row) {
                return @$row->facilityOwnershipType ? @$row->facilityOwnershipType->name : '-';
            })
            ->addColumn('tds', function ($row) {
                $tds_per = @$row->taxDetails->tds_exemption == 'No'? 10 : 0;
                return $tds_per;
            })
            ->addColumn('rf', function ($row) {
                return 0;
            })
            ->addColumn('hospital', function ($row) {
                $tds_per = @$row->taxDetails->tds_exemption == 'No'? 90 : 100;
                return $tds_per;
            })
            ->make(true);
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
        }  else if($request->type == 'preauth_query_add_doc') { 
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
        return view('aco._partials.loadpdf',  compact('investigation', 'id', 'preauth_register'));
    }
}