<?php

namespace App\Http\Controllers\Admin;
use App\Models\Module;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public $routes = [];
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('admin-views.dashboard.index');
    }

    public function profile(Request $request) {
        $this->routes = ['update_profile' => route('admin.update_profile'), 'changepassword' => route('admin.changepassword')];
        return view('admin-views.users.profile', ['routes' => $this->routes, 'pathurl' => 'users']);
    }

    public function update_profile(Request $request) {
        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'email' => 'required',
            'name' => 'required',
            'gender' => 'nullable',
            'mobile_no' => 'nullable',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }
        $data = [];

        if ($request->hasFile('avatar')) {
            $filePath = $request->file('avatar')->store('profile', 'public'); 
            $data['avatar'] = $filePath;
        } 
         
        $data['email'] = $request->email;
        $data['name'] = $request->name;
        $data['gender'] = $request->gender;
        $data['mobile_no'] = $request->mobile_no;

        auth()->user()->update($data);

        return response()->json(['status' => true, 'message' => "Profile Update Successfully!!"]);
    }

    public function changepassword(Request $request) {

        $request->validate([
            'old_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', Password::defaults()],
            'confirmation_password' => ['required', 'same:new_password'],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully!',
        ]);
    }
}
