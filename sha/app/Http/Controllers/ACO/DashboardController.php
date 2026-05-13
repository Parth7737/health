<?php

namespace App\Http\Controllers\ACO;

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
        $pending_total = PreauthRegister::where('status',PreauthRegister::STATUS_CLAIM_APPROVED)->get()->count();
        $pending_erroneous_total = PreauthRegister::where('status',PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED)->get()->count();
        $approve_total = PreauthRegister::whereNotNull('claim_aco_approved_date')->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $erroneous_approve_total = PreauthRegister::whereNotNull('erroneous_claim_aco_approved_date')->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $reject_total = PreauthRegister::where('status', PreauthRegister::STATUS_ACO_CLAIM_REJECTED)->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $erroneous_rejected_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED)->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $query_total = PreauthRegister::where('status', PreauthRegister::STATUS_ACO_CLAIM_QUERIED)->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $erroneous_query_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED)->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $payment_rejected_total = PreauthRegister::where('status', PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK)->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $sha_claim_approve_total = PreauthRegister::where('status', PreauthRegister::STATUS_SHA_CLAIM_APPROVED)->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $claim_sent_to_bank_total = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_SENT_TO_BANK)->where('aco_status_added_by',auth()->user()->id)->get()->count();
        $claim_paid_by_bank_total = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_PAID_BY_BANK)->where('aco_status_added_by',auth()->user()->id)->get()->count();
       
        return view('aco.dashboard',compact('pending_total','pending_erroneous_total','approve_total','erroneous_approve_total', 'reject_total','erroneous_rejected_total','payment_rejected_total', 'query_total','erroneous_query_total','sha_claim_approve_total','claim_sent_to_bank_total','claim_paid_by_bank_total'));
    }
    public function dashboardUsers(Request $request){
        
        try {
            $length = $request->input('length', 10);
            $list_view = $request->input('list_view', 0);
            $status = $request->input('status', '');
            
            $query = PreauthRegister::query();
            if($status) {
                // if(PreauthRegister::STATUS_ACO_CLAIM_APPROVED == $status){
                //     $query->whereNotNull('claim_aco_approved_date');
                // }elseif(PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED == $status){
                //     $query->whereNotNull('erroneous_claim_aco_approved_date');
                // }else{
                    $query->where('status', $status);
                // }
                if($status != PreauthRegister::STATUS_CLAIM_APPROVED && $status != PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED){
                    $query->where('aco_status_added_by',auth()->user()->id);
                }
            } else {
                $statuses = [
                    PreauthRegister::STATUS_CLAIM_APPROVED,
                    PreauthRegister::STATUS_ACO_CLAIM_APPROVED,
                    PreauthRegister::STATUS_ACO_CLAIM_REJECTED,
                    PreauthRegister::STATUS_ACO_CLAIM_QUERIED,
                ];
                $query->whereIn('status', $statuses);
            }
            
            $query->orderBy('claim_approved_date','asc');
            $users = $query->paginate($length);
            
            $html = view('aco.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => view('aco._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
