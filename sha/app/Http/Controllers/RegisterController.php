<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\{ AadhaarVerificationMail, EmailVerificationMail};
use App\Models\{User, HospitalState, EntityType, Entity, Role};
use Illuminate\Support\Facades\Mail;
use Mews\Captcha\Captcha;
use App\Models\DraftRegister;
use Illuminate\Validation\ValidationException;
use App\Models\AadhaarInformation;
use Hash;
use App\Services\AadhaarService;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    protected $aadhaarService;

    public function __construct()
    {
        $this->aadhaarService = new AadhaarService();
    }

    public function login() 
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect(Helpers::getDashboardRedirect($user));
        }
        return view('hospital.login');
    }

    public function signup() 
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect(Helpers::getDashboardRedirect($user));
        }
        return view('hospital.signup');
    }
    public function store(Request $request) {
        if($request->signupoption == "withemail") {
            $validated = $request->validate([
                'email' => 'required|email',
                'mobile_no' => 'required|digits:10'
            ]);

            if(User::where('email', $request->email)->exists()) {
                return response()->json(['success' => false, 'message' => 'User already Exists with this Email!!']);
            }
    
            
            // if(User::where('mobile_no', $request->mobile_no)->exists()) {
            //     return response()->json(['success' => false, 'message' => 'User already Exists with this Mobile No!!']);
            // }
        } else  if($request->signupoption == "withaadhaar") {
            $validated = $request->validate([
                'aadhaar_no' => 'required|digits:12'
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Please provide valid data!!']);
        }
       
        if($request->email && $request->mobile_no) {
            $data = DraftRegister::email($request->email)->where('mobile_no', $request->mobile_no)->first();
            if($data) {
                $data->register_status = 1;
                $data->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Something Went wrong!!']);
            }
        } else {    
            $data = DraftRegister::aadhaar($request->aadhaar_no)->first();
            if($data) {
                $aadhaardata = AadhaarInformation::where('aadhaar_no', $data->aadhaar_no)->first();
                if($aadhaardata) {
                    $data->name = $aadhaardata->name;
                    $data->age = $aadhaardata->age;
                    $data->state = $aadhaardata->state;

                    if($aadhaardata->gender == "M") {
                        $gender = "Male";
                    } else if($aadhaardata->gender == "F") {
                        $gender = "Female";
                    } else {
                        $gender = "";
                    }

                    $data->gender = $gender;
                }
                $data->register_status = 1;
                $data->email = '';
                $data->uuid = $this->generateUUID();
                $data->save();
            }  else {
                return response()->json(['success' => false, 'message' => 'Something Went wrong!!']);
            }
        }
      
        session(['uuid' => $data->uuid]);

        return response()->json(['success' => true, 'message' => 'Data is saved!!', 'route' => route("register.dashboard", $data->uuid)]);
    }

    public function updateProfile(Request $request, $uuid) {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|string',
            'state' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_no' => 'required|digits:10',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Optional file validation
        ]);

        $validatedData['is_user_update'] = 1;

        if ($request->hasFile('avatar')) {
            $filePath = $request->file('avatar')->store('profiles', 'public'); // Store in "storage/app/public/profiles"
            $validatedData['avatar'] = $filePath; // Add file path to data
        }

        DraftRegister::where('uuid', $uuid)->update($validatedData);

        return response()->json(['success' => true, 'message' => 'Profile is saved!!']);

    }

    public function dashboard(Request $request, $uuid) {
        $data = DraftRegister::where('uuid', $uuid)->first();
        if($data) {
            $aadhaardata = AadhaarInformation::where('aadhaar_no', $data->aadhaar_no)->first();
            if($aadhaardata) {
                $data->name = $aadhaardata->name;
                $data->age = $aadhaardata->age;
                $data->state = $aadhaardata->state;

                if($aadhaardata->gender == "M") {
                    $gender = "Male";
                } else if($aadhaardata->gender == "F") {
                    $gender = "Female";
                } else {
                    $gender = "";
                }
                
                $data->gender = $gender;
                $data->save();
            }
        }
        $states = HospitalState::where('country_id', 101)->get();
        $entityTypes = EntityType::get();
        $entities = Entity::get();
        $roles = Role::get();
        if(!$data) {
            abort(404);
        }
        return view('hospital.dashboard', compact('data', 'states', 'entityTypes', 'entities', 'roles'));
    }

    public function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, // Version 4 UUID
            mt_rand(0, 0x3fff) | 0x8000, // Variant 1
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
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
        $otp = 1234;//rand(000000, 999999);
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
                $data = DraftRegister::create(['uuid' => $this->generateUUID(), 'email' => $request->email, 'otp' => $otp]);
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

        if(User::where('email', $request->mobile_no)->exists()) {
            return response()->json(['success' => false, 'message' => 'User already Exists with this Email!!']);
        }
        
        if(!$request->uuid && DraftRegister::where(['mobile_no' => $request->mobile_no, 'register_status' => 1])->exists()) {
            return response()->json(['success' => false, 'message' => 'Mobile Already Exists!!']);
        }

        $otp = 1234;//rand(000000, 999999);

        if($request->uuid) {
            $data = DraftRegister::where(['uuid' => $request->uuid, 'register_status' => 1])->first();

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
    
            if($request->type == "Aadhaar") {
                $response = $this->aadhaarService->sendOtp($data->aadhaar_no);
                return response()->json($response);
            }
    
            return response()->json(['success' => true, 'message' => 'Otp re-sent successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong!!']);
        }

    }

    public function sendAadhaarMail(Request $request) {

        if($request->aadhaar == "") {
            return response()->json(['success' => false, 'message' => 'Please Enter Aadhaar No!!']);
        }

        if(DraftRegister::where(['aadhaar_no' => $request->aadhaar, 'register_status' => 1])->exists()) {
            return response()->json(['success' => false, 'message' => 'Aadhaar Already Exists!!']);
        }

        $otp = 1234;//rand(000000, 999999);
        $data = DraftRegister::where(['aadhaar_no' => $request->aadhaar, 'register_status' => 0])->first();
        if($data) {
            $data->otp = $otp;
            $data->aadhaar_no = $request->aadhaar;
            $data->email = $request->aadhaar.'@gmail.com';
            $data->save();
        } else {
            $data = DraftRegister::create(['otp' => $otp, 'aadhaar_no' => $request->aadhaar, 'email' => $request->aadhaar.'@gmail.com']);
        }

        $response = $this->aadhaarService->sendOtp($request->aadhaar);

        // Mail::to($data->email)->send(new AadhaarVerificationMail($data));

        return response()->json($response);
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
            if($type == "Aadhaar") {
                $response = $this->aadhaarService->verifyOtp($aadhaar, $otp, base64_decode($referenceId));
                return response()->json($response);
            } else {
                if($data->otp == $otp) {
                    return response()->json(['success' => true, 'message' => 'Otp Verified Successfully!!']);
                } else {
                    return response()->json(['success' => false, 'message' => 'OTP is Incorrect']);
                }
            }
        } else {
            return response()->json(['success' => false, 'message' => 'OTP is Incorrect']);
        }
    }

    public function updateentity(Request $request, $uuid) {
        $validatedData = $request->validate([
            'parent_entity' => 'required|string',
            'entity_type' => 'required',
            'entity_name' => 'required|string',
            'user_role' => 'required|string',
            'district' => 'nullable'
        ]);
        $validatedData['is_entity_update'] = 1;
        DraftRegister::where('uuid', $uuid)->update($validatedData);

        return response()->json(['success' => true, 'message' => 'Entity is saved!!']);
    }

    public function updateUserData(Request $request, $uuid) {
        $validatedData = $request->validate([
            'nature_of_employment' => 'required|string',
            'designation' => 'required',
            'userid' => 'required|string|unique:draft_registers,userid',
            'password' => 'nullable|string|',
            'confirmation_password' => 'nullable|string|same:password'
        ]);
        $validatedData['is_user_update'] = 1;
        if (!isset($validatedData['password'])) {
            unset($validatedData['password']);
        } else {
            // Hash the password if it's provided
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        unset($validatedData['confirmation_password']);
        // $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['uuid'] = $this->generateUUID();

        DraftRegister::where('uuid', $uuid)->update($validatedData);

        return response()->json(['success' => true, 'message' => 'User Data is saved!!']);
    }
}
