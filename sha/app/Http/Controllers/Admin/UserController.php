<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DraftRegister;
use App\Models\User;
use App\Models\Hospitals;
use App\Models\Role;
use App\Models\EntityType;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = DraftRegister::whereNull('is_approve')->whereNotNull('userid')->whereNotNull('password')->orderBy('created_at', 'desc')->get();        
        return view('admin-views.users.register-requests',compact('users'));
    }
    
    public function approve(Request $request,$id)
    {
        $draft_user = DraftRegister::find($id);
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
        // $user->role_id = 2;
        $user->save();

        $draft_user->is_approve = 1;
        $draft_user->save();
        return redirect()->back()->with('success','Request Approve Successfully.');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {

    }

    public function indexUser()
    {
        $users = User::with(['role', 'hospital', 'entityType'])->latest()->get();
        return view('admin-views.users.index', compact('users'));
    }

    public function view($id)
    {
        $user = DraftRegister::findOrFail($id);
        return view('admin-views.users.view', compact('user'));
    }

}
