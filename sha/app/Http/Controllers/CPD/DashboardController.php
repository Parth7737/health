<?php

namespace App\Http\Controllers\CPD;

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
        $existingCase = PreauthRegister::where('assigned_to_cpd',auth()->user()->id)
            ->where('status', PreauthRegister::STATUS_CPD_CLAIM_PENDING)
            ->first();
    
        if (!$existingCase) {
            $pendingCase = PreauthRegister::where('status', PreauthRegister::STATUS_CPD_CLAIM_PENDING)
                        ->whereNull('assigned_to_cpd')
                        ->orderBy('claim_submited_date', 'asc')
                        ->first();
            if ($pendingCase) {
                $pendingCase->assigned_to_cpd = auth()->user()->id;
                $pendingCase->save();
            }
        }
        $pending_total = PreauthRegister::where('status', PreauthRegister::STATUS_CPD_CLAIM_PENDING)->get()->count();
        $approve_total = PreauthRegister::whereNotNull('claim_approved_date')->where('claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $reject_total = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_REJECTED)->where('claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $query_total = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_QUERIED)->where('claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $erroneous_claim_pending_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING)->get()->count();
        $erroneousQueryByACO = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED)->where('erroneous_claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $erroneousQueryBySHA = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED)->where('erroneous_claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $erroneous_claim_pending_total = $erroneous_claim_pending_total+$erroneousQueryByACO+$erroneousQueryBySHA;
        $erroneous_claim_approved_total = PreauthRegister::whereNotNull('erroneous_claim_approved_date')->where('erroneous_claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $erroneous_claim_rejected_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED)->where('erroneous_claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $erroneous_claim_query_total = PreauthRegister::where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED)->where('erroneous_claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $queryByACO = PreauthRegister::where('status', PreauthRegister::STATUS_ACO_CLAIM_QUERIED)->where('claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $queryBySHA = PreauthRegister::where('status', PreauthRegister::STATUS_SHA_CLAIM_QUERIED)->where('claim_approve_reject_query_by',auth()->user()->id)->get()->count();
        $pending_total = $pending_total+$queryBySHA+$queryByACO;
        return view('cpd.dashboard',compact('pending_total','approve_total','reject_total','query_total','erroneous_claim_pending_total','erroneous_claim_approved_total','erroneous_claim_rejected_total','erroneous_claim_query_total', 'queryBySHA'));
    }
    public function dashboardUsers(Request $request){
        
        try {
            $length = $request->input('length', 10);
            $list_view = $request->input('list_view', 0);
            $status = $request->input('status', '');
            
            $query = PreauthRegister::query();
            if($status){
                // if(PreauthRegister::STATUS_CLAIM_APPROVED == $status){
                //     $query->whereNotNull('claim_approved_date');
                // }else{
                    // }
                if(PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING == $status){
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING,PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED,PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED]);
                    });
                }else if(PreauthRegister::STATUS_CPD_CLAIM_PENDING != $status){
                    $query->where('status', $status);
                    $query->where('claim_approve_reject_query_by',auth()->user()->id);
                }else{
                    $query->where('assigned_to_cpd',auth()->user()->id);
                    
                    $query->where(function($q){
                        $q->whereIn('status',[PreauthRegister::STATUS_CPD_CLAIM_PENDING,PreauthRegister::STATUS_ACO_CLAIM_QUERIED,PreauthRegister::STATUS_SHA_CLAIM_QUERIED]);
                        // $q->where('claim_approve_reject_query_by',auth()->user()->id);
                    });
                }
            }else{
                $statuses = [
                    PreauthRegister::STATUS_CPD_CLAIM_PENDING,
                    PreauthRegister::STATUS_CLAIM_APPROVED,
                    PreauthRegister::STATUS_CLAIM_REJECTED,
                    PreauthRegister::STATUS_CLAIM_QUERIED,
                    PreauthRegister::STATUS_SHA_CLAIM_QUERIED,
                ];
                $query->whereIn('status', $statuses);
            }
            
            $query->orderBy('claim_submited_date','asc');
            $users = $query->paginate($length);
            
            $html = view('cpd.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => view('cpd._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
