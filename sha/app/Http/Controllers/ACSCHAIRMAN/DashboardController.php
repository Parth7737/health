<?php

namespace App\Http\Controllers\ACSCHAIRMAN;

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
        $pending_total = PreauthRegister::where('status',PreauthRegister::STATUS_ACS_PENDING)->get()->count();
        $approved_total = PreauthRegister::whereNotNull('acs_approved_date')->where('acs_forwarded_by',auth()->user()->id)->get()->count();
        $rejected_total = PreauthRegister::where('status',PreauthRegister::STATUS_ACS_REJECTED)->where('acs_forwarded_by',auth()->user()->id)->get()->count();
        $query_total = PreauthRegister::where('status',PreauthRegister::STATUS_ACS_QUERIED)->where('acs_forwarded_by',auth()->user()->id)->get()->count();
       
        return view('acschairman.dashboard',compact('pending_total','approved_total','rejected_total','query_total'));
    }
    public function dashboardUsers(Request $request){
        
        try {
            $length = $request->input('length', 10);
            $list_view = $request->input('list_view', 0);
            $status = $request->input('status', '');
            
            $query = PreauthRegister::query();
            if($status){
                if(PreauthRegister::STATUS_ACS_PENDING != $status){
                    $query->where('acs_forwarded_by',auth()->user()->id);
                }
                $query->where('status', $status);
            }else{
                $statuses = [
                    PreauthRegister::STATUS_ACS_PENDING,
                    PreauthRegister::STATUS_ACS_APPROVED,
                    PreauthRegister::STATUS_ACS_REJECTED,
                    PreauthRegister::STATUS_ACS_QUERIED,
                ];
                $query->whereIn('status', $statuses);
            }
            $query->orderBy('preauth_submission_date','asc');
            $users = $query->paginate($length);
            
            $html = view('acschairman.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => view('acschairman._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
