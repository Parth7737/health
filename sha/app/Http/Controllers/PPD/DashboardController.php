<?php

namespace App\Http\Controllers\PPD;

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
        $existingCase = PreauthRegister::where('assigned_to_ppd',auth()->user()->id)
            ->where('status', PreauthRegister::STATUS_PREAUTH_PENDING)
            ->first();
    
        if (!$existingCase) {
            $pendingCase = PreauthRegister::where('status', PreauthRegister::STATUS_PREAUTH_PENDING)
                        ->whereNull('assigned_to_ppd')
                        ->orderBy('preauth_submission_date', 'asc')
                        ->first();
            if ($pendingCase) {
                $pendingCase->assigned_to_ppd = auth()->user()->id;
                $pendingCase->save();
            }
        }
        $pending_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_PENDING)->get()->count();
        $approve_total = PreauthRegister::whereNotNull('preauth_approved_date')->whereNot('status',PreauthRegister::STATUS_PREAUTH_PENDING)->where('preauth_approve_reject_query_by',auth()->user()->id)->get()->count();
        $reject_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_REJECTED)->where('preauth_approve_reject_query_by',auth()->user()->id)->get()->count();
        $query_total = PreauthRegister::where('status',PreauthRegister::STATUS_PREAUTH_QUERIED)->where('preauth_approve_reject_query_by',auth()->user()->id)->get()->count();
        return view('ppd.dashboard',compact('pending_total','approve_total','reject_total','query_total'));
    }
    public function dashboardUsers(Request $request){
        
        try {
            $length = $request->input('length', 10);
            $list_view = $request->input('list_view', 0);
            $status = $request->input('status', '');
            
            $query = PreauthRegister::query();
            if($status){
                // if(PreauthRegister::STATUS_PREAUTH_APPROVED == $status){
                //     $query->whereNotNull('preauth_approved_date');
                // }else{
                    $query->where('status', $status);
                // }
                if(PreauthRegister::STATUS_PREAUTH_PENDING != $status){
                    $query->where('preauth_approve_reject_query_by',auth()->user()->id);
                }else{
                    $query->where('assigned_to_ppd',auth()->user()->id);
                }
            }else{
                $statuses = [
                    PreauthRegister::STATUS_PREAUTH_PENDING,
                    PreauthRegister::STATUS_PREAUTH_APPROVED,
                    PreauthRegister::STATUS_PREAUTH_REJECTED,
                    PreauthRegister::STATUS_PREAUTH_QUERIED,
                ];
                $query->whereIn('status', $statuses);
            }
            $query->orderBy('preauth_submission_date','asc');
            $users = $query->paginate($length);
            
            $html = view('ppd.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => view('ppd._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
