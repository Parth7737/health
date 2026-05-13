<?php

namespace App\Http\Controllers\CEX;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
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
use DataTables;

class CaseSearchController extends Controller
{
    public function index(Request $request) {
        return view('cex.casesearch');
    }

    public function loadcasesearch(Request $request) {
        $query = PreauthRegister::with('benificiary', 'hospital');

        $status = $request->input('status', '');
        $date_range = $request->input('date', '');
        $from_date = '';$to_date = '';
        if($date_range != ''){
            $date_range_arr = explode(" - ",$date_range);
            $from_date = $date_range_arr[0];
            $to_date = $date_range_arr[1];
        }
        $s = $request->input('search', '');

        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween('created_at', [$from_date, $to_date]);
        }

        if($request->case_id) {
            $query->where('register_id', $request->case_id);
        }

        if (!empty($s['value'])) {
            $search = $s['value'];
            $query->where(function ($q) use ($search) {
                $q->where('register_id', 'like', "%$search%")
                  ->orWhereHas('benificiary', function ($q1) use ($search) {
                      $q1->where('name', 'like', "%$search%")
                         ->orWhere('age', 'like', "%$search%")
                         ->orWhere('gender', 'like', "%$search%")
                         ->orWhere('card_id', 'like', "%$search%")
                         ->orWhere('aabha_id', 'like', "%$search%")
                         ->orWhere('mobile_no', 'like', "%$search%")
                         ->orWhere('member_id', 'like', "%$search%")
                         ->orWhere('family_id', 'like', "%$search%");
                  })
                  ->orWhere('patient_type', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%")
                  ->orWhere('mobile_no', 'like', "%$search%");
            });
        }
      
        if ($request->scheme_type_id) {
            $query->where('scheme_id', $request->scheme_type_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('patient_name', function ($row) {
                return @$row->benificiary->name ? '<strong>'.@$row->benificiary->name."</strong>" : '-';
            })
            ->addColumn('submission_date', function($row) {
                return date("d/m/Y",strtotime($row->created_at));
            })
            ->addColumn('status', function($row) {
               return $row->status_label; 
            })   
            ->addColumn('action', function ($row) {
                $route = route('cex.viewSearch', base64_encode($row->id));
                return '<a href="'.$route.'" class="btn btn-primary btn-sm">></a>';
            })
            ->rawColumns(['action','patient_name'])        
            ->make(true);
    }

    public function viewSearch(Request $request, $id) {
        $id = base64_decode($id);
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
            return view('cex.casedetails',compact('preauth_register','pending_since','case_profile','hospital_profile','general_info','family_history','personal_history','authentication_consent','admission_details','preauth_diagnosis','hospital_speciality','procedures','teams','preauth_teams','investigations','preauth_investigations','preauth_investigation_status'));
        }else{
            return redirect()->route('cex.dashboard')->with('error','Register Not Found');
        }
    }
}
