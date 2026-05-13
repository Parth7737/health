<?php

namespace App\Http\Controllers\MedicalCommittee;

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
        $pending_total = PreauthRegister::where('status',PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING)->get()->count();
        $approved_total = PreauthRegister::whereNotNull('committee_approved_date')->where('committee_forwarded_by',auth()->user()->id)->get()->count();
        $rejected_total = PreauthRegister::where('status',PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED)->where('committee_forwarded_by',auth()->user()->id)->get()->count();
        $query_total = PreauthRegister::where('status',PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED)->where('committee_forwarded_by',auth()->user()->id)->get()->count();
       
        return view('medical-committee.dashboard',compact('pending_total','approved_total','rejected_total','query_total'));
    }
    public function dashboardUsers(Request $request){
        
        try {
            $length = $request->input('length', 10);
            $list_view = $request->input('list_view', 0);
            $status = $request->input('status', '');
            
            $query = PreauthRegister::query();
            if($status){
                if(PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING != $status){
                    $query->where('committee_forwarded_by',auth()->user()->id);
                }
                $query->where('status', $status);
            }else{
                $statuses = [
                    PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,
                    PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,
                    PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,
                    PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,
                ];
                $query->whereIn('status', $statuses);
            }
            $query->orderBy('preauth_submission_date','asc');
            $users = $query->paginate($length);
            
            $html = view('medical-committee.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => view('medical-committee._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
