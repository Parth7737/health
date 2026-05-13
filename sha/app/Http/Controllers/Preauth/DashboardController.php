<?php

namespace App\Http\Controllers\Preauth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\PreauthRegister;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $submited_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_REGISTER)->get()->count();
        $pending_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->whereIn('status',[PreauthRegister::STATUS_PREAUTH_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING])->get()->count();
        $cancelled_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->whereIn('status',[PreauthRegister::STATUS_PREAUTH_CANCELLED])->get()->count();
        $approved_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_PREAUTH_APPROVED)->get()->count();
        $preauth_rejected_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED])->get()->count();
        $preauth_queries_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED])->get()->count();
        $claim_submited_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CLAIM_SUBMITTED)->get()->count();
        $claim_query_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CLAIM_QUERIED)->get()->count();
        $claim_initiate_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CLAIM_PENDING)->get()->count();
        $claim_forward_cex_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CPD_CLAIM_PENDING)->get()->count();
        $cpd_claim_rejected_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CLAIM_REJECTED)->get()->count();
        $aco_claim_approved_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_ACO_CLAIM_APPROVED)->get()->count();
        $aco_claim_query_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_ACO_CLAIM_QUERIED)->get()->count();
        $aco_claim_rejected_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_ACO_CLAIM_REJECTED)->get()->count();
        $aco_claim_reinitiate_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_PAYMENT_REINITIATE_BY_ACO)->get()->count();
        $sha_claim_approved_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_SHA_CLAIM_APPROVED)->get()->count();
        $sha_claim_rejected_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_SHA_CLAIM_REJECTED)->get()->count();
        $claim_sent_bank_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CLAIM_SENT_TO_BANK)->get()->count();
        $claim_paid_bank_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CLAIM_PAID_BY_BANK)->get()->count();
        $claim_payment_rejected_bank_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK)->get()->count();
        $erroneous_claim_initiated_medco_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING)->get()->count();
        $erroneous_claim_aprroved_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED,PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_APPROVED])->get()->count();
        $erroneous_claim_query_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED,PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED])->get()->count();
        $erroneous_claim_rejected_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED,PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_REJECTED])->get()->count();
        $erroneous_claim_paid_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_ERRONEOUS_CLAIM_PAID)->get()->count();
        $claim_approved_total = PreauthRegister::where('hospital_id',auth()->user()->hospital_id)->where('status',PreauthRegister::STATUS_CLAIM_APPROVED)->get()->count();
        return view('preauth.dashboard',compact('submited_total','pending_total','cancelled_total','approved_total','preauth_rejected_total','preauth_queries_total','claim_submited_total','claim_query_total','claim_initiate_total','claim_forward_cex_total','cpd_claim_rejected_total','aco_claim_approved_total','aco_claim_query_total','aco_claim_rejected_total','aco_claim_reinitiate_total','sha_claim_approved_total','sha_claim_rejected_total','claim_sent_bank_total','claim_paid_bank_total','claim_payment_rejected_bank_total','erroneous_claim_initiated_medco_total','erroneous_claim_aprroved_total','erroneous_claim_query_total','erroneous_claim_rejected_total','erroneous_claim_paid_total','claim_approved_total'));
    }
    public function searchBeneficiary(){
        
        return view('preauth.search-beneficiary');
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
            $hospital_id = auth()->user()->hospital_id;
            $query = PreauthRegister::query();
            if($status || $status == 0){
                if($status == PreauthRegister::STATUS_PREAUTH_PENDING){
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_PREAUTH_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING]);
                    });
                }elseif($status == PreauthRegister::STATUS_PREAUTH_QUERIED){
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED]);
                    });
                }elseif($status == PreauthRegister::STATUS_PREAUTH_REJECTED){
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED]);
                    });
                }elseif($status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED){
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED]);
                    });
                }elseif($status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED){
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED,PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_APPROVED]);
                    });
                }elseif($status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED){
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED,PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_REJECTED]);
                    });
                }else{
                    $query->where('status', $status);
                }
            }else{
                $statuses = [
                    PreauthRegister::STATUS_REGISTER,
                    PreauthRegister::STATUS_PREAUTH_PENDING,
                    PreauthRegister::STATUS_CANCELLED,
                    PreauthRegister::STATUS_PREAUTH_CANCELLED,
                    PreauthRegister::STATUS_PREAUTH_APPROVED,
                    PreauthRegister::STATUS_PREAUTH_REJECTED,
                    PreauthRegister::STATUS_PREAUTH_QUERIED,
                    PreauthRegister::STATUS_CLAIM_SUBMITTED,
                    PreauthRegister::STATUS_CLAIM_QUERIED,
                    PreauthRegister::STATUS_CLAIM_PENDING,
                    PreauthRegister::STATUS_CLAIM_REJECTED,
                    PreauthRegister::STATUS_SHA_CLAIM_REJECTED,
                    PreauthRegister::STATUS_CLAIM_APPROVED,
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
            
            $query->where('hospital_id',$hospital_id);
            $users = $query->paginate($length);
            
            $html = view('preauth.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => view('preauth._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
