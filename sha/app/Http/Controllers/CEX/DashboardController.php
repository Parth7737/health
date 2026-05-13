<?php

namespace App\Http\Controllers\CEX;

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
        $existingCase = PreauthRegister::where('assigned_to_cex',auth()->user()->id)
            ->where('status', PreauthRegister::STATUS_CLAIM_PENDING)
            ->first();
    
        if (!$existingCase) {
            $pendingCase = PreauthRegister::where('status', PreauthRegister::STATUS_CLAIM_PENDING)
                        ->whereNull('assigned_to_cex')
                        ->orderBy('claim_submited_date', 'asc')
                        ->first();
            if ($pendingCase) {
                $pendingCase->assigned_to_cex = auth()->user()->id;
                $pendingCase->save();
            }
        }
        $pending_total = PreauthRegister::where('status',PreauthRegister::STATUS_CLAIM_PENDING)->get()->count();
        $forward_total = PreauthRegister::where('claim_forwarded_by',auth()->user()->id)->get()->count();
       
        return view('cex.dashboard',compact('pending_total','forward_total'));
    }
    public function dashboardUsers(Request $request){
        
        try {
            $length = $request->input('length', 10);
            $list_view = $request->input('list_view', 0);
            $status = $request->input('status', '');
            
            $query = PreauthRegister::query();
            if($status){
                if($status == PreauthRegister::STATUS_CPD_CLAIM_PENDING){
                    $query->where('claim_forwarded_by',auth()->user()->id);
                }
                $query->where('status', $status);
                if($status == PreauthRegister::STATUS_CLAIM_PENDING){
                    $query->where('assigned_to_cex',auth()->user()->id);
                }
            }else{
                $statuses = [
                    PreauthRegister::STATUS_CLAIM_PENDING,
                    PreauthRegister::STATUS_CPD_CLAIM_PENDING,
                ];
                $query->whereIn('status', $statuses);
            }
            $query->orderBy('claim_submited_date','asc');
            $users = $query->paginate($length);
            
            $html = view('cex.dashboard-users', compact('users','list_view'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => view('cex._partials.users-pagination', ['users' => $users])->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
