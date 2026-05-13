<?php

namespace App\Http\Controllers\SHA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\PreauthRegister;
use Illuminate\Support\Facades\Validator;
use App\Exports\CustomReportExport;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $preauth_pending_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_PENDING)->get()->count();
        $approve_total = PreauthRegister::whereNotNull('claim_paid_date')->where('sha_status_added_by', auth()->user()->id)->get()->count();
        $reject_total = PreauthRegister::where('status', PreauthRegister::STATUS_SHA_CLAIM_REJECTED)->where('sha_status_added_by', auth()->user()->id)->get()->count();
        $query_total = PreauthRegister::where('status', PreauthRegister::STATUS_SHA_CLAIM_QUERIED)->where('sha_status_added_by', auth()->user()->id)->get()->count();

        $u100_pending_total = PreauthRegister::whereIn('status',[PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING])->get()->count();
        $u100_approved_total = PreauthRegister::whereNotNull('ceo_approved_date')->orWhereNotNull('acs_approved_date')->get()->count();
        $u100_rejected_total = PreauthRegister::whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED])->get()->count();
        $u100_query_total = PreauthRegister::whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED])->get()->count();

        $preapproved_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_APPROVED)->get()->count();
        $prerejected_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_REJECTED)->get()->count();
        $preauth_queries_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_QUERIED)->get()->count();
        $preauth_cancelled_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_CANCELLED)->get()->count();
        $claim_cpd_pending_total = PreauthRegister::where('status', PreauthRegister::STATUS_CPD_CLAIM_PENDING)->get()->count();
        $claim_query = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_REJECTED)->get()->count();
        $claim_approved = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_APPROVED)->get()->count();
        $claim_rejected = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_REJECTED)->get()->count();
        $erroneous_claim_pending_cpd_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING)->get()->count();
        $erroneous_claim_approve_cpd_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED)->get()->count();
        $erroneous_claim_query_cpd_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED)->get()->count();
        $erroneous_claim_rejected_cpd_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED)->get()->count();
        $claim_aco_pending_total = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_APPROVED)->get()->count();
        $claim_aco_approve_total = PreauthRegister::where('status',PreauthRegister::STATUS_ACO_CLAIM_APPROVED)->get()->count();
        $claim_aco_query_total = PreauthRegister::where('status', PreauthRegister::STATUS_ACO_CLAIM_QUERIED)->get()->count();
        $claim_aco_rejected_total = PreauthRegister::where('status', PreauthRegister::STATUS_ACO_CLAIM_REJECTED)->get()->count();
        $claim_sha_pending_total = PreauthRegister::where('status', PreauthRegister::STATUS_ACO_CLAIM_APPROVED)->get()->count();
        $claim_sha_approve_total = PreauthRegister::where('status',PreauthRegister::STATUS_SHA_CLAIM_APPROVED)->get()->count();
        $claim_sha_rejected_total = PreauthRegister::where('status', PreauthRegister::STATUS_SHA_CLAIM_REJECTED)->get()->count();
        $claim_sent_to_bank_total = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_SENT_TO_BANK)->get()->count();
        $claim_paid_by_bank_total = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_PAID_BY_BANK)->get()->count();
        $payment_rejected_by_bank_total = PreauthRegister::where('status', PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK)->get()->count();
        $erroneous_claim_sha_pending_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED)->get()->count();
        $erroneous_claim_sha_approve_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_APPROVED)->get()->count();
        $erroneous_claim_sha_rejected_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_REJECTED)->get()->count();
        $erroneous_claim_sha_query_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED)->get()->count();
        $erroneous_claim_sha_paid_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_PAID)->get()->count();
        return view('sha.dashboard', compact('preauth_pending_total','claim_aco_approve_total','approve_total', 'reject_total', 'query_total','u100_pending_total','u100_approved_total','u100_rejected_total','u100_query_total','claim_cpd_pending_total', 'claim_query', 'claim_approved', 'claim_rejected','erroneous_claim_pending_cpd_total','erroneous_claim_approve_cpd_total','erroneous_claim_query_cpd_total','erroneous_claim_rejected_cpd_total','claim_aco_pending_total','claim_aco_query_total','claim_aco_rejected_total','claim_sha_pending_total','claim_sha_approve_total','claim_sha_rejected_total','claim_sent_to_bank_total','claim_paid_by_bank_total','payment_rejected_by_bank_total','erroneous_claim_sha_pending_total','erroneous_claim_sha_approve_total','erroneous_claim_sha_rejected_total','erroneous_claim_sha_query_total','erroneous_claim_sha_paid_total', 'preapproved_total', 'prerejected_total', 'preauth_queries_total','preauth_cancelled_total'));
    }
    public function dashboardUsers(Request $request){
        
        try {
            $length = $request->input('length', 10);
            $list_view = $request->input('list_view', 0);
            $status = $request->input('status', '');
            $date_range = $request->input('date', '');
            $from_date = '';$to_date = '';
            if($date_range != ''){
                $date_range_arr = explode(" - ",$date_range);
                $from_date = $date_range_arr[0];
                $to_date = $date_range_arr[1];
            }
            $search = $request->input('search', '');
            
            $query = PreauthRegister::query();
            if($status) {
                if($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING){
                    $query->whereIn('status',[PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING]);
                }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED){
                    $query->whereNotNull('ceo_approved_date')->orWhereNotNull('acs_approved_date');
                }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED){
                    $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED]);
                }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED){
                    $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED]);
                }else{
                    $query->where('status', $status);
                }
            } else {
                $statuses = [
                    PreauthRegister::STATUS_ACO_CLAIM_APPROVED,
                    PreauthRegister::STATUS_SHA_CLAIM_APPROVED,
                    PreauthRegister::STATUS_SHA_CLAIM_REJECTED,
                    PreauthRegister::STATUS_SHA_CLAIM_QUERIED,
                    PreauthRegister::STATUS_CLAIM_QUERIED,
                    PreauthRegister::STATUS_CLAIM_APPROVED,
                    PreauthRegister::STATUS_CLAIM_REJECTED,
                ];
                $query->whereIn('status', $statuses);
            }
            if (!empty($from_date) && !empty($to_date)) {
                $query->whereBetween('created_at', [$from_date, $to_date]);
            }
            if (!empty($search)) {
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
            
            
            $users = $query->paginate($length);
            
            $html = view('sha.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'data_count' => $users->count(),
                'html' => $html,
                'pagination' => view('sha._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadreport(Request $request) {
        $export = new CustomReportExport();
        $filePath = $export->generate();
    
        return response()->download($filePath, 'uttarakhand_report.xlsx')->deleteFileAfterSend(true);
    }
}
