<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\{ EmailVerificationMail};
use App\Models\{User, HospitalState, Role};
use Illuminate\Support\Facades\Mail;
use App\Models\DraftRegister;
use Illuminate\Validation\ValidationException;
use Hash;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{

    public function __construct()
    {
    }

    public function login() 
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect(Helpers::getDashboardRedirect($user));
        }
        $loginRoles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('auth.login', compact('loginRoles'));
    }

    public function signup() 
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect(Helpers::getDashboardRedirect($user));
        }
        return view('auth.signup');
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'mobile_no' => 'required|digits:10'
        ]);

        if(User::where('email', $request->email)->exists()) {
            return response()->json(['success' => false, 'message' => 'User already Exists with this Email!!']);
        }
    
            
        if($request->email && $request->mobile_no) {
            $data = DraftRegister::email($request->email)->first();
            if($data) {
                $data->mobile_no = $request->mobile_no;
                $data->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Something Went wrong!!']);
            }
        }
      
        session(['uuid' => $data->uuid]);

        return response()->json(['success' => true, 'message' => 'Data is saved!!', 'route' => route("register.dashboard", $data->uuid)]);
    }

    public function updateProfile(Request $request, $uuid) {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required',
            'hospital_type' => 'required',
            'hospital' => 'required_if:hospital_type,Multi-Branch',
            'state' => 'required|max:255',
            'email' => 'required|email|max:255',
            'mobile_no' => 'required|digits:10',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'required|string',
            'confirmation_password' => 'required|string|same:password'
        ]);


        if ($request->hasFile('avatar')) {
            $filePath = $request->file('avatar')->store('profiles', 'public');
            $validatedData['avatar'] = $filePath;
        }

        if (!isset($validatedData['password'])) {
            unset($validatedData['password']);
        } else {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        unset($validatedData['confirmation_password']);
        unset($validatedData['hospital']);

        DraftRegister::where('uuid', $uuid)->update($validatedData);
        $draft_register = DraftRegister::where('uuid', $uuid)->first();
        $user = new User;
        $user->uuid = $draft_register->uuid;
        $user->name = $draft_register->name;
        $user->email = $draft_register->email;
        $user->password = $draft_register->password;
        $user->gender = $draft_register->gender;
        $user->state = $draft_register->state;
        $user->avatar = $draft_register->avatar;
        $user->mobile_no = $draft_register->mobile_no;
        $user->hospital_type = $draft_register->hospital_type;
        $user->parent_id = $request->hospital;
        $user->step = 2;
        $user->save();
        $user->assignRole('Admin');
        $draft_register->delete();
        \Auth::login($user);
        return response()->json(['success' => true, 'message' => 'Profile Registered Successfully!']);

    }

    public function dashboard(Request $request, $uuid) {
        $data = DraftRegister::where('uuid', $uuid)->first();

        $states = HospitalState::where('country_id', 101)->get();
        if(!$data) {
            abort(404);
        }
        return view('hospital.signup-profile-step', compact('data', 'states'));
    }


    public function sendEmailMail(Request $request) {
        
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please Enter a valid Email ID!',
                'errors' => $e->errors()
            ]); // 422 Unprocessable Entity
        }

        if($request->email == "") {
            return response()->json(['success' => false, 'message' => 'Please Enter Email ID!!']);
        }

        if(User::where('email', $request->email)->exists()) {
            return response()->json(['success' => false, 'message' => 'User already Exists with this Email!!']);
        }

        if(DraftRegister::where(['email' => $request->email, 'register_status' => 1])->exists()) {
            return response()->json(['success' => false, 'message' => 'Email Already Exists!!']);
        }
        $otp = 123456;//rand(000000, 999999);
        if($request->uuid) {
            $data = DraftRegister::where(['uuid' => $request->uuid, 'register_status' => 1])->first();

            if($data) {
                $data->otp = $otp;
                $data->email = $request->email;
                $data->save();
            }

            
            try {
                Mail::to($data->email)->send(new EmailVerificationMail($data));
            } catch (\Exception $e) {
                
            }

        } else {
          
            
            $data = DraftRegister::where(['email' => $request->email, 'register_status' => 0])->first();
            if($data) {
                $data->otp = $otp;
                $data->save();
            } else {
                $data = DraftRegister::create(['uuid' => Helpers::generateUUID(), 'email' => $request->email, 'otp' => $otp]);
            }
    
            try {
                Mail::to($data->email)->send(new EmailVerificationMail($data));
            } catch (\Exception $e) {
                
            }
        }

        return response()->json(['success' => true, 'message' => 'Otp sent in your mail ID']);
    }

    public function sendMobileMail(Request $request) {
        // Need to uncomment.
        if($request->mobile_no == "" || strlen(trim($request->mobile_no)) < 10) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid mobile number with at least 10 digits.']);
        }

        $otp = 123456;//rand(000000, 999999);

        if($request->uuid) {
            $data = DraftRegister::where(['uuid' => $request->uuid])->first();

            if($data->email == "") {
                return response()->json(['success' => false, 'message' => 'Please verify email and then verify mobile!!']);
            }
            
            if($data) {
                $data->otp = $otp;
                $data->email = $request->email;
                $data->mobile_no = $request->mobile_no;
                $data->save();
            }

            try {
                Mail::to($data->email)->send(new EmailVerificationMail($data));
            } catch (\Exception $e) {
                
            }

        } else {
            $data = DraftRegister::where(['email' => $request->email, 'register_status' => 0])->first();
            if($data) {
                $data->otp = $otp;
                $data->mobile_no = $request->mobile_no;
                $data->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Please Enter First Email!!']);
            }
        }

        try {
            Mail::to($data->email)->send(new EmailVerificationMail($data));
        } catch (\Exception $e) {
            
        }
        return response()->json(['success' => true, 'message' => 'Otp sent in your mail ID']);
    }

    public function resendOTP(Request $request) {
        $otp = 1234;//rand(000000, 999999);

        if($request->type == "Aadhaar") {
            $verifydata = ['aadhaar_no' => $request->email, 'register_status' => 0];
        } else {
            $verifydata = ['email' => $request->email, 'register_status' => 0];
        }
        if($request->uuid) {
            $data = DraftRegister::where(['uuid' => $request->uuid, 'register_status' => 1])->first();
        } else {
            $data = DraftRegister::where($verifydata)->first();
        }
        if($data) {
            if($data) {
                $data->otp = $otp;
                $data->save();
            }
    
            if($request->type == "Email" || $request->type == "Mobile") {
                try {
                    Mail::to($data->email)->send(new EmailVerificationMail($data));
                } catch (\Exception $e) {
                    
                }
            }
            return response()->json(['success' => true, 'message' => 'Otp re-sent successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong!!']);
        }

    }

    public function verifyEmailOtp(Request $request) {
        $otp = $request->otp;
        $type = $request->type;
        $email = $request->email;
        $aadhaar = $request->aadhaar;
        $referenceId = $request->reference_id;
        $data =  new DraftRegister();
        if($type == "Email") {
            $data = $data->email($email)->first();
        } else if($type == "Aadhaar") {
            $data = $data->aadhaar($aadhaar)->first();
        } else if($type == "Mobile") {
            $data = $data->where('mobile_no', $request->mobile_no)->first();
        }

        if($data) {
            if($data->otp == $otp) {
                return response()->json(['success' => true, 'message' => 'Otp Verified Successfully!!']);
            } else {
                return response()->json(['success' => false, 'message' => 'OTP is Incorrect']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'OTP is Incorrect']);
        }
    }
}
