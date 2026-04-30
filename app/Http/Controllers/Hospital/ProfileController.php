<?php

namespace App\Http\Controllers\Hospital;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public $routes = [];
    
    public function index(Request $request) {
        $this->routes = ['update_profile' => route('hospital.update_profile'), 'changepassword' => route('hospital.changepassword')];
        return view('hospital.profile', ['routes' => $this->routes,'pathurl'=>'hospital-profile']);
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
            return response()->json(['errors' => Helpers::error_processor($validator)],422);
        }
        $data = [];

        if ($request->hasFile('avatar')) {
            $filePath = $request->file('avatar')->store('avatar', 'public'); 
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
