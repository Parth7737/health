<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserHfr;
use App\Models\{ Hospital, HospitalDistrict, HospitalState, User, HospitalSpeciality, HospitalService, HospitalLicense};
use App\CentralLogics\Helpers;
use App\Rules\UniqueAcrossTables;
use App\Models\MobileOtp;
use App\Mail\StatusMail;
use Mail;
use DataTables;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmpanelmentRegistrationController extends Controller
{
    public function create(Request $request) {  
        $user = auth()->user();
        $hospital = Hospital::where('id', auth()->user()->hospital_id)->first();
        $uuid = $user->uuid;
        $step = $user->step??1; 
        if($step == 0 && $uuid == ''){
            \Auth::logout();
            return redirect('/');
        }
        $allStepCompleted = Helpers::checkAllStepIsCompleteOrNot($uuid);
        return view('hospital.empanelment.form',compact('step','uuid','user','hospital','allStepCompleted'));
    }

    public function hospitalInfo(Request $request, $uuid) {

        $hospital = Hospital::where('id', auth()->user()->hospital_id)->first();
        $user = User::where('hospital_id', auth()->user()->hospital_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Chairman');
                })->first();

        // Base validation rules
        $rules = [
            'name' => 'required',
            'code' => 'required',
            'type_id' => 'required',
            'hospital_phone' => 'required',
            'hospital_email' => 'required|email',
            'pincode' => 'required',
            'city' => 'required',
            'address' => 'required',
        ];

        if(auth()->user()->hospital_type == 'Multi-Branch' && auth()->user()->parent_id == 0){
            $rules['chairman_name'] = 'required';
            
            $rules['chairman_email'] = [
                'required',
                'email',
                Rule::unique('users', 'email')
            ];
            if ($user) {
                $rules['chairman_email'] = [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id)
                ];
                $rules['password'] = 'nullable|string';
                $rules['confirmation_password'] = 'same:password';
            } else {
                $rules['password'] = 'required|string';
                $rules['confirmation_password'] = 'required|string|same:password';
            }
        }

        // Validate request with dynamic rules
        $validatedData = $request->validate($rules);

        if(!$hospital){
            $hospital = new Hospital;
        }
        $hospital->name = $request->name;
        $hospital->user_id = auth()->user()->id;
        $hospital->parent_id = auth()->user()->parent_id;
        $hospital->hospital_type = auth()->user()->hospital_type;
        $hospital->uuid = auth()->user()->uuid;
        $hospital->email = $request->hospital_email;
        $hospital->code = $request->code;
        $hospital->type_id = $request->type_id;
        $hospital->phone = $request->hospital_phone;
        $hospital->address = $request->address;
        $hospital->city = $request->city;
        $hospital->landmark = $request->landmark;
        $hospital->pincode = $request->pincode;
        $hospital->save();

        if($hospital->hospital_type == 'Multi-Branch' && $hospital->parent_id == 0){
            if(!$user){
                $user = new User;
                $user->assignRole('Chairman');
            }
            $user->name = $request->chairman_name;
            $user->email = $request->chairman_email;
            $user->password = Hash::make($request->password);
            $user->hospital_id = $hospital->id;
            $user->save();
        }
        $enable_step = Helpers::get_settings('empanelment_step_status');
        $enable_step_decoded = json_decode($enable_step);
        if($enable_step_decoded->speciality_status == 1){
            $step = 3;
        }elseif($enable_step_decoded->service_status == 1){
            $step = 4;
        }elseif($enable_step_decoded->licenses_status == 1){
            $step = 5;
        }else{
            $step = 6;
        }
        auth()->user()->update(['hospital_id' => $hospital->id,'step' => $step,'enable_step' => $enable_step]);

        return response()->json(['success' => true, 'message' => 'Information Saved Successfully!!','step' => $step]); 
    }

    public function saveSpecialities(Request $request, $uuid, $hospital_id) {
        $check = Hospital::where('id', $hospital_id)->first();
        $rules = [];
        $messages = [];
        foreach ($request->speciality_id as $value) {
            $available = (int) $request->input("available_{$value}", 0);
        }
        
        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->messages()
            ], 422);
        }

        if($check) {
            HospitalSpeciality::where('hospital_id', $hospital_id)->delete();
            $isValid = 0;
            if($request->speciality_id) {
                $specialities = $request->speciality_id;
                foreach ($specialities as $key => $value) {
                    if($request->{'available_'.$value}) {
                        $isValid = 1;
                        $available = $request->{'available_'.$value};
                        $remark = $request->{'remark_'.$value};
                        
                        $array = [
                            'uuid' => Helpers::generateUUID(),
                            'speciality_id' => $value,
                            'available' => $available,
                            'remark' => $remark
                        ];
                        $check->specialities()->create($array);
                    }
                }

                if($isValid) {
                    $enable_step = auth()->user()->enable_step;
                    $enable_step_decoded = json_decode($enable_step);
                    if($enable_step_decoded->service_status == 1){
                        $step = 4;
                    }elseif($enable_step_decoded->licenses_status == 1){
                        $step = 5;
                    }else{
                        $step = 6;
                    }
                    auth()->user()->update(['hospital_id' => $hospital_id,'step' => $step]);
    
                    return response()->json(['success' => true, 'message' => 'Specialities Saved Successfully!!','step' => $step]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
                }
               
            } else {
                return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
            }
         } else {
             return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
         }
    }

    public function saveServices(Request $request, $uuid, $hospital_id) {
        $check = Hospital::where('id', $hospital_id)->first();
        if($check) {
            $services =  Helpers::getCommanData('Service');

            foreach ($services as $key => $value) {
                if(sizeof($value->subServices) > 0) {
                    foreach ($value->subServices()->orderBy('sort_order', 'ASC')->get() as $k => $v) {
                        if($v->is_required) {
                            $name = str_replace(' ', '-', strtolower($v->name));
                            $checklicences = $check->services()->where(['service_id' => $value->id, 'sub_service_id' => $v->id])->first();
                            $rules[$value->id.'_'.$v->id.'_'.$name] = 'required';
                            if($request->{$value->id.'_'.$v->id.'_'.$name} == 1) {                               
                                $rules[$value->id.'_'.$v->id.'_'.$name.'_text'] = 'sometimes|required';
                                $rules[$value->id.'_'.$v->id.'_'.$name.'_image'] = $checklicences ? 'nullable' : 'sometimes|required|mimes:jpg,png,jpeg';
                            }   
                           
                
                            $messages[$value->id.'_'.$v->id.'_'.$name] = 'This field is Required';
                            if($request->{$value->id.'_'.$v->id.'_'.$name} == 1) {
                                $messages[$value->id.'_'.$v->id.'_'.$name.'_text'] = 'This field is Required';
                                $messages[$value->id.'_'.$v->id.'_'.$name.'_image'] = 'This field is Required';
                            } 
                            // $messages[$value->id.'_'.$v->id.'_'.$name.'_text'] = 'This field is Required';
                            // $messages[$value->id.'_'.$v->id.'_'.$name.'_image'] = 'This field is Required';
                        }                   
                    }
                }
            }
            // $validatedData = $request->validate($rules);
            $validator = \Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                // Format the errors for AJAX response
                $errors = $validator->errors();
    
                return response()->json([
                    'message' => $errors->first(), // Get the first error message
                    'errors' => $errors->messages() // Get all errors keyed by input field
                ], 422);
            }
            $isValid = 0;
            // $check->services()->delete();
            foreach ($services as $key => $value) {
                if(sizeof($value->subServices) > 0) {
                    foreach ($value->subServices()->orderBy('sort_order', 'ASC')->get() as $k => $v) {
                        $name = str_replace(' ', '-', strtolower($v->name));
                        if($request->{$value->id.'_'.$v->id.'_'.$name} == 0 || $request->{$value->id.'_'.$v->id.'_'.$name} == 1 || $request->{$value->id.'_'.$v->id.'_'.$name} != '') {
                            $isValid = 1;
                            $array = [
                                'uuid' => Helpers::generateUUID(),
                                'service_id' => $value->id,
                                'sub_service_id' => $v->id,
                                'action_id' => $request->{$value->id.'_'.$v->id.'_'.$name.'_action'},
                                'service_value' => $request->{$value->id.'_'.$v->id.'_'.$name},
                                'text_value' => $request->{$value->id.'_'.$v->id.'_'.$name.'_text'},
                                'remark' => $request->{$value->id.'_'.$v->id.'_remark'}
                            ];

                            if ($request->hasFile($value->id.'_'.$v->id.'_'.$name.'_image')) {
                                $filePath = $request->file($value->id.'_'.$v->id.'_'.$name.'_image')->store('serviceimage', 'public'); 
                                $array['image'] = $filePath; // Add file path to data
                            }                  
    
                            $check->services()->updateOrCreate(['service_id' => $value->id, 'sub_service_id' => $v->id], $array);
                            // $check->services()->create($array);
                        }                        
                    }
                }
            }
            if($isValid) {
                $enable_step = auth()->user()->enable_step;
                $enable_step_decoded = json_decode($enable_step);
                if($enable_step_decoded->licenses_status == 1){
                    $step = 5;
                }else{
                    $step = 6;
                }
                auth()->user()->update(['hospital_id' => $hospital_id,'step' => $step]);
                return response()->json(['success' => true, 'message' => 'Services Saved Successfully!!','step' => $step]);
            } else {
                return response()->json(['success' => false, 'message' => 'Please Select Any One.']);
            }           
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveLicenses(Request $request, $uuid, $hospital_id) {
        $check = Hospital::where('id', $hospital_id)->first();
        if($check) {
            // $check->licenses()->delete();
            $licenses =  Helpers::getCommanData('License');
            $rules = [];
            $messages = [];
            foreach ($licenses as $key => $value) {
                foreach ($value->licenseType as $k => $v) {
                    if($v->is_required) {
                        $checklicences = $check->licenses()->where(['license_id' => $value->id, 'license_type_id' => $v->id])->first();
                        $rules[$value->id . '_' . $v->id . '_dateissue'] = 'required|date';
                        $rules[$value->id . '_' . $v->id . '_dateexpiry'] = 'required|date';
                        $rules['document_' . $value->id . '_' . $v->id] = $checklicences ? 'nullable|mimes:pdf' : 'required|mimes:pdf';
            
                        $messages[$value->id . '_' . $v->id . '_dateissue.required'] = 'The Date of Issue for ' . $v->name . ' is required.';
                        $messages[$value->id . '_' . $v->id . '_dateexpiry.required'] = 'The Date of Expiry for ' . $v->name . ' is required.';
                        $messages['document_' . $value->id . '_' . $v->id . '.required'] = 'The Document for ' . $v->name . ' is required.';
                        $messages['document_' . $value->id . '_' . $v->id . '.mimes'] = 'The Document for ' . $v->name . ' must be a file of type: pdf.';
                    }                   
                }
            }
            // $validatedData = $request->validate($rules);
            $validator = \Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                // Format the errors for AJAX response
                $errors = $validator->errors();
    
                return response()->json([
                    'message' => $errors->first(), // Get the first error message
                    'errors' => $errors->messages() // Get all errors keyed by input field
                ], 422);
            }

            foreach ($licenses as $key => $value) {
                foreach ($value->licenseType as $k => $v) {
                    if($request->{$value->id.'_'.$v->id.'_dateissue'} && $request->{$value->id.'_'.$v->id.'_dateexpiry'}) {
                        $issueDate = date('Y-m-d', strtotime($request->{$value->id.'_'.$v->id.'_dateissue'}));
                        $expiryDate = date('Y-m-d', strtotime($request->{$value->id.'_'.$v->id.'_dateexpiry'}));
                        
                        $existData = Helpers::getSingleLicense($hospital_id, $value->id, $v->id);
                        if($existData) {
                            $array = [
                                'uuid' => Helpers::generateUUID(),
                                'license_id' => $value->id,
                                'license_type_id' => $v->id,
                                'issue_date' => $issueDate,
                                'expiry_date' => $expiryDate,
                                'remark' => $request->{$value->id.'_'.$v->id.'_remark'}
                            ];
    
                            if ($request->hasFile('document_' . $value->id . '_' . $v->id)) {
                                $filePath = $request->file('document_' . $value->id . '_' . $v->id)->store('certificate', 'public'); 
                                $array['document'] = $filePath; // Add file path to data
                            }                  
    
                            $check->licenses()->updateOrCreate(['license_id' => $value->id, 'license_type_id' => $v->id], $array);
                        } else {
                            if ($request->hasFile('document_' . $value->id . '_' . $v->id)) {
                                $array = [
                                    'uuid' => Helpers::generateUUID(),
                                    'license_id' => $value->id,
                                    'license_type_id' => $v->id,
                                    'issue_date' => $issueDate,
                                    'expiry_date' => $expiryDate,
                                    'remark' => $request->{$value->id.'_'.$v->id.'_remark'}
                                ];
        
                                if ($request->hasFile('document_' . $value->id . '_' . $v->id)) {
                                    $filePath = $request->file('document_' . $value->id . '_' . $v->id)->store('certificate', 'public'); 
                                    $array['document'] = $filePath; // Add file path to data
                                }                  
        
                                $check->licenses()->updateOrCreate(['license_id' => $value->id, 'license_type_id' => $v->id], $array);
                            }
                        }                       
                    }
                }
            }
            auth()->user()->update(['hospital_id' => $hospital_id,'step' => 6]);
            return response()->json(['success' => true, 'message' => 'Licenses Saved Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }
    public function saveHospitalDocuments(Request $request, $uuid, $hospital_id) {
        $check = Hospital::where('id', $hospital_id)->first();
        if($check) {
            $documents =  Helpers::getCommanData('EmpanelmentDocument');
            $rules = [];
            $messages = [];
            foreach ($documents as $key => $value) {
                if($value->is_required) {
                    $checkdocument = $check->documents()->where(['document_id' => $value->id])->first();
                    $rules['document_' . $value->id.'_doc'] = $checkdocument ? 'nullable' : 'required|mimes:pdf|max:10240';
                    $messages['document_' . $value->id.'_doc'] = 'The Document for ' . $value->name . ' is required.';
                    $messages['document_' . $value->id . '_doc.mimes'] = 'The Document for ' . $value->name . ' must be a file of type: pdf.';
                }    
            }
            $validator = \Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors();
    
                return response()->json([
                    'message' => $errors->first(), // Get the first error message
                    'errors' => $errors->messages() // Get all errors keyed by input field
                ], 422);
            }

            foreach ($documents as $key => $value) {
                    $array = [
                        'uuid' => Helpers::generateUUID(),
                        'document_id' => $value->id,
                        'remarks' => $request->{$value->id.'_remarkdoc'}
                    ];
                    if ($request->hasFile('document_' . $value->id.'_doc')) {
                        $filePath = $request->file('document_' . $value->id.'_doc')->store('certificate', 'public'); 
                        $array['document'] = $filePath;
                    }                  

                    $check->documents()->updateOrCreate(['document_id' => $value->id], $array);
            }

            $check->save();
            $allStepComplete = Helpers::checkAllStepIsCompleteOrNot($uuid);
            $user = User::where('uuid',$uuid)->first();
            $multi_branches = '';
            return response()->json(['success' => true, 'message' => 'Document Saved Successfully!!', 'is_complete' => $allStepComplete,'multi_branches' => $multi_branches]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }
    public function getDistrict(Request $request) {
        $stateId = $request->state_id;
        $data = HospitalDistrict::where('state_id', $stateId)->get();
        return response()->json($data);
    }

    public function stepLoad(Request $request, $uuid) {
        $validatedData = $request->validate([
            'step' => 'required',
        ]);

        $user = User::where('uuid', $uuid)->first();

        $hospital = '';
        $hospital_id = '';
        if($user->hospital_id){
            $hospital_id = $user->hospital_id;
            $hospital = Hospital::where('id',$user->hospital_id)->first();
        }
        $allStepCompleted = Helpers::checkAllStepIsCompleteOrNot($uuid);
        $is_multi_branch = $user->hospital_type == 'Multi-Branch'?true:false;
        if($request->step == 1) {
            return view('hospital.empanelment._partials.basic-info', compact('user','uuid'));
        } else if($request->step == 2) {
            return view('hospital.empanelment._partials.hospital-info', compact('uuid','hospital'));
        } else if($request->step == 3) {
            if(!$hospital){
                return '<h3 class="theme-color">Please Complete a first hospital information tab</h3>';
            }else{
                $specialities =  Helpers::getCommanData('Speciality');
                return view('hospital.empanelment._partials.speciality', compact('uuid', 'hospital','specialities'));
            }
        } else if($request->step == 4) {
            if(!$hospital){
                return '<h3 class="theme-color">Please Complete a first hospital information tab</h3>';
            }else{
                $services =  Helpers::getCommanData('Service');
                return view('hospital.empanelment._partials.services', compact('uuid', 'hospital','services'));
            }
        } else if($request->step == 5) {
            if(!$hospital){
                return '<h3 class="theme-color">Please Complete a first hospital information tab</h3>';
            }else{
                $licenses =  Helpers::getCommanData('License');
                return view('hospital.empanelment._partials.licenses', compact('uuid', 'hospital','licenses'));
            }
        } else if($request->step == 6) {
            if(!$hospital){
                return '<h3 class="theme-color">Please Complete a first hospital information tab</h3>';
            }else{
                return view('hospital.empanelment._partials.documents', compact('uuid', 'hospital','allStepCompleted','is_multi_branch'));
            }
        }
    }

    public function hospitalSubmit(Request $request, $uuid, $hospital_id) {
        $check = Helpers::checkAllStepIsCompleteOrNot($uuid);
        if($check) {
            $hospital = Hospital::where('id', $hospital_id)->first();
            if (!$hospital) {
                return response()->json(['success' => false, 'message' => 'Hospital not found']);
            }
            // Allow resubmit when Rejected - clear reject reason on resubmit
            $hospital->reject_reason = null;
            $hospital->status = 'Submitted';
            $hospital->status_update_date = date('Y-m-d H:i:s');
            $hospital->save();
            $url = route('hospital.dashboard');
            return response()->json(['success' => true, 'message' => $hospital->code.' Hospital Payment Is Initiated!!', 'url' => $url]);
        } else {
            return response()->json(['success' => false, 'message' => 'Please fill all details first of hospital']);
        }
    }

    public function paymentIntiate(Request $request, $uuid, $hospital_id) {
        return view('hospital.payment.initiate');
    }

    public function ccResponse(Request $request) {
        $working_key = env('CCAVENUE_WORKING_KEY'); 
        $encResponse = $request->input("encResp");

        $rcvdString = Helpers::decryptCC($encResponse, $working_key);        
        $decryptValues = explode('&', $rcvdString);
        $responseData = [];
        echo "<pre>";
        print_r($decryptValues);
        echo "<pre>";
        foreach ($decryptValues as $value) {
            $information = explode('=', $value);
            if(count($information) == 2) {
                $responseData[$information[0]] = urldecode($information[1]);
            }
        }

        $order_status = $responseData['order_status'] ?? 'Failure';
        print_r($responseData);
        exit;
        if ($order_status === 'Success') {
            return redirect('/success')->with('message', 'Payment successful!');
        } else {
            return redirect('/failed')->with('message', 'Payment failed. Please try again.');
        }
    }

    public function paymentSuccess(Request $request) {

    }

    public function paymentFail(Request $request) {

    }

}
