<?php

namespace App\Http\Controllers\SHAAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\{ DraftRegister, Role, HospitalState };
use DataTables;
use Hash;
use App\CentralLogics\Helpers;

class DashboardController extends Controller
{
    public function index() { 
        $role = Role::where('name', 'ISA Admin')->first();

        $roleids = [];
        if(auth()->user()->role->name == "ISA Admin") {
            $roleids = [13,14,15];
        } else {
            $roleids = [2,16,17,6,7,8];
        }

        $pending =  DraftRegister::with('role')->whereIn('user_role', $roleids)->whereNull('is_approve')->whereNotNull('userid')->whereNotNull('password')->count();
        $reject =  DraftRegister::with('role')->whereIn('user_role', $roleids)->where('is_approve', '2')->count();
        $approved =  User::with('role')->whereIn('role_id', $roleids)->count();
        $isa =  User::with('role')->where('role_id', $role->id)->count();
        return view('shaadmin.index', compact('pending', 'approved', 'reject', 'isa'));
    }

    public function getData(Request $request) {
        $role = Role::where('name', 'ISA Admin')->first();

        if ($request->ajax()) {

            $roleids = [];
            if(auth()->user()->role->name == "ISA Admin") {
                $roleids = [13,14,15];
            } else {
                $roleids = [2,16,17,6,7,8];
            }
            
            if($request->status == "Pending") {
                $data = DraftRegister::with('role')->whereIn('user_role', $roleids)->whereNull('is_approve')->whereNotNull('userid')->whereNotNull('password')->orderBy('id', 'desc');    
            } else if($request->status == "Rejected") {
                $data = DraftRegister::with('role')->whereIn('user_role', $roleids)->where('is_approve', 2);
            } else if ($request->status == "Approved") {
                $data = User::with('role')->whereIn('role_id', $roleids)->orderBy('id', 'desc'); 
            } else if ($request->status == "ISA") {
                $data = User::with('role')->whereIn('role_id', [$role->id])->orderBy('id', 'desc'); 
            }

            // if(auth()->user()->role_id == 2) {
            //     $data->where('user_id' , auth()->user()->id);
            // }
            
            // if (!empty($request->status)) {
            //     if($request->status == 'Queried') {
            //         $data->whereIn('status', [$request->status, 'Response Required From Facility', 'Query On Upgradation Request From Facility']);
            //     } else if($request->status == 'Submitted' || $request->status == 'Re-Submitted') {
            //         $data->whereIn('status', [$request->status, "Re-Submitted"]);
            //     } else if($request->status == "Rejected") {
            //         $data->whereIn('status', [$request->status, 'Empanelment Not Recommended by DEC']);
            //     } else {
            //         $data->where('status', $request->status); // Apply filter if status is provided
            //     }
            // }

            return DataTables::of($data)
                ->addIndexColumn() 
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? date('d/m/Y', strtotime($row->created_at)) : '-';
                })
                ->addColumn('action', function ($row) use ($request) {
                    $statuschange = $view = $reject = '';
                    if($request->status == "Pending") {
                        if(empty($request->is_approve)) {
                            $statuschange = '<a href="javascript:;" onclick="changeStatus('.$row->id.', `Approve`);" class="btn btn-success"><i class="ri-user-follow-line text-white"></i></a>';
                            $reject = '<a href="javascript:;" onclick="changeStatus('.$row->id.', `Reject`);" class="btn btn-danger"><i class="ri-user-unfollow-line text-white"></i></a>';
                        }
                    }


                    $routeprefix = '';
                    if(auth()->user()->role->name == "ISA Admin") {
                        $routeprefix = 'isaadmin.userInfo';
                    } else {
                        $routeprefix = 'shaadmin.userInfo';
                    }

                    if($request->status == "Pending" || $request->status == "Rejected") {
                        $viewRoute = route($routeprefix, [base64_encode($row->id), base64_encode('d')]);
                    } else {
                        $viewRoute = route($routeprefix, [base64_encode($row->id), base64_encode('u')]);
                    }
                    $view = '<a href="'.$viewRoute.'" class="btn btn-info"><i class="ri-eye-fill text-white"></i></a>';

                    return '<div class="btn--container d-flex justify-content-center gap-2">'.$statuschange.' '.$reject.' '.$view.'</div>';
                })
                ->rawColumns(['action']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }

    public function changestatus(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'status' => 'required'
        ]);
        
        $id = $request->id;
        $status = $request->status;
        
        $draft_user = DraftRegister::find($id);
        if($draft_user) {
            if($status == "Approve") {
                $user = new User;
                $user->name = $draft_user->name;
                $user->email = $draft_user->email;
                $user->password = $draft_user->password;
                $user->userid = $draft_user->userid;
                $user->uuid = $draft_user->uuid;
                $user->aadhaar_no = $draft_user->aadhaar_no;
                $user->kyc_mode = $draft_user->kyc_mode;
                $user->otp = $draft_user->otp;
                $user->gender = $draft_user->gender;
                $user->age = $draft_user->age;
                $user->state = $draft_user->state;
                $user->avatar = $draft_user->avatar;
                $user->mobile_no = $draft_user->mobile_no;
                $user->nature_of_employment = $draft_user->nature_of_employment;
                $user->designation = $draft_user->designation;
                $user->parent_entity = $draft_user->parent_entity;
                $user->entity_type = $draft_user->entity_type;
                $user->entity_name = $draft_user->entity_name;
                $user->district = $draft_user->district;
                $user->role_id = $draft_user->user_role;
                $user->approved_by = auth()->user()->id;
                $user->approved_date = date('Y-m-d');
                // $user->role_id = 2;
                $user->save();
    
                $draft_user->is_approve = 1;
                $draft_user->user_id = $user->id;
                $draft_user->save();
            } else if ($status == 'Reject') {
    
                $draft_user->is_approve = 2;
                $draft_user->save();
            }
            return response(['success' => true, 'message' => 'Request '.$status.' Successfully.']);
        } else {
            return response(['success' => false, 'message' => 'Request Not Found!!']);
        }
    }

    public function userInfo(Request $request, $id, $type) {
        if(base64_decode($type) == "d") {
            $user = DraftRegister::where('id', base64_decode($id))->first();
        } else if(base64_decode($type) == "u"){
            $user = User::where('id', base64_decode($id))->first();
        }

        return view('shaadmin.viewuser', compact('user'));
    }

    public function createisa(Request $request) {
        $roles = Role::where('entity', 'SHA Entity')->get();
        $states = HospitalState::where('country_id', 101)->get();
        return view('shaadmin.createisa', compact('roles', 'states'));
    }

    public function registerIsaUser(Request $request) {
        $state_name = auth()->user()->state;
        $state = HospitalState::where('name', $state_name)->first();
        if(User::where('role_id', $request->role_id)->where('state', $state->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already one ISA Officer is found!!']);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|string',
            'state' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|digits:10|unique:users,mobile_no',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'role_id' => 'required',
            'nature_of_employment' => 'required',
            'designation' => 'required',
            'userid' => 'required|string|unique:users,userid',
            'password' => 'required|string|',
            'confirmation_password' => 'required|string|same:password'
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->age = $request->age;
        $user->gender = $request->gender;
        $user->state = $request->state;
        $user->email = $request->email;
        $user->mobile_no = $request->mobile_no;
        $user->avatar = $request->avatar;
        $user->role_id = $request->role_id;
        $user->nature_of_employment = $request->nature_of_employment;
        $user->designation = $request->designation;
        $user->userid = $request->userid;
        
        if ($request->hasFile('avatar')) {
            $filePath = $request->file('avatar')->store('profiles', 'public'); // Store in "storage/app/public/profiles"
            $user->avatar = $filePath; // Add file path to data
        }

        $user->uuid = Helpers::generateUUID();
        $user->password = Hash::make($request->password);
        $user->parent_entity = $request->state;
        $user->entity_type = 'SHA';
        $user->entity_name = 'SHA Entity';
        $user->approved_date = date('Y-m-d');
        $user->approved_by = auth()->user()->id;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Isa user create successfully!!']);
    }
}
