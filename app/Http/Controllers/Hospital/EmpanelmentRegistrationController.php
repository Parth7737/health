<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmpanelmentEligibility;
use App\Models\UserHfr;
use App\Models\{ Hospital, HospitalDistrict, HospitalState, HospitalType, User, HospitalSpeciality, HospitalService, HospitalLicense};
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
        $unlockedMaxStep = 8;
        $initialWizardStep = $this->mapLegacyStepToWizardStep((int) ($user->step ?? 1));
        return view('hospital.empanelment.form', compact('step', 'uuid', 'user', 'hospital', 'allStepCompleted', 'unlockedMaxStep', 'initialWizardStep'));
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
            'type_id' => 'required|integer|exists:hospital_types,id',
            'hospital_phone' => 'required',
            'hospital_email' => 'required|email',
            'pincode' => 'required',
            'city' => 'required',
            'address' => 'required',
            'onboarding_meta' => 'nullable|array',
            'onboarding_meta.local_name' => 'nullable|string|max:255',
            'onboarding_meta.establishment_year' => 'nullable|integer|min:1800|max:' . (int) date('Y'),
            'onboarding_meta.registration_no' => 'nullable|string|max:255',
            'onboarding_meta.ownership' => 'nullable|string|max:120',
            'onboarding_meta.sub_category' => 'nullable|string|max:120',
            'onboarding_meta.district' => 'nullable|string|max:120',
            'onboarding_meta.block' => 'nullable|string|max:120',
            'onboarding_meta.village' => 'nullable|string|max:120',
            'onboarding_meta.latitude' => 'nullable|string|max:32',
            'onboarding_meta.longitude' => 'nullable|string|max:32',
            'onboarding_meta.ms_name' => 'nullable|string|max:255',
            'onboarding_meta.ms_mobile' => 'nullable|string|max:15',
            'onboarding_meta.landline' => 'nullable|string|max:32',
            'onboarding_meta.helpline' => 'nullable|string|max:32',
            'onboarding_meta.website' => 'nullable|string|max:512'
        ];

        // if(auth()->user()->hospital_type == 'Multi-Branch' && auth()->user()->parent_id == 0){
        //     $rules['chairman_name'] = 'required';
            
        //     $rules['chairman_email'] = [
        //         'required',
        //         'email',
        //         Rule::unique('users', 'email')
        //     ];
        //     if ($user) {
        //         $rules['chairman_email'] = [
        //             'required',
        //             'email',
        //             Rule::unique('users', 'email')->ignore($user->id)
        //         ];
        //         $rules['password'] = 'nullable|string';
        //         $rules['confirmation_password'] = 'same:password';
        //     } else {
        //         $rules['password'] = 'required|string';
        //         $rules['confirmation_password'] = 'required|string|same:password';
        //     }
        // }

        $wizardObForType = (array) (auth()->user()->wizard_onboarding ?? []);
        if (!$request->filled('type_id') && !empty($wizardObForType['type_id'])) {
            $request->merge(['type_id' => $wizardObForType['type_id']]);
        }

        // Validate request with dynamic rules
        $validatedData = $request->validate($rules);

        if(!$hospital){
            $hospital = new Hospital;
        }
        $hospital->name = $request->name;
        $hospital->user_id = auth()->user()->id;
        $hospital->parent_id = auth()->user()->parent_id;
        $hospital->state_id = auth()->user()->state_id;
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
        $existingMeta = is_array($hospital->onboarding_meta) ? $hospital->onboarding_meta : [];
        $incomingMeta = $request->input('onboarding_meta', []);
        if (!is_array($incomingMeta)) {
            $incomingMeta = [];
        }
        $authUser = auth()->user();
        $wizardOb = (array) ($authUser->wizard_onboarding ?? []);
        if (!empty($wizardOb['type_id'])) {
            $incomingMeta['wizard_facility_type_id'] = (int) $wizardOb['type_id'];
        }
        if (!empty($wizardOb['facility_type'])) {
            $incomingMeta['wizard_facility_type'] = $wizardOb['facility_type'];
        }
        $hospital->onboarding_meta = array_merge($existingMeta, array_filter($incomingMeta, static function ($v) {
            return $v !== null && $v !== '';
        }));
        $hospital->save();

        // if($hospital->hospital_type == 'Multi-Branch' && $hospital->parent_id == 0){
        //     if(!$user){
        //         $user = new User;
        //         $user->assignRole('Chairman');
        //     }
        //     $user->name = $request->chairman_name;
        //     $user->email = $request->chairman_email;
        //     if ($request->filled('password')) {
        //         $user->password = Hash::make($request->password);
        //     }
        //     $user->hospital_id = $hospital->id;
        //     $user->save();
        // }
        $enable_step = Helpers::get_settings('empanelment_step_status');
        $enable_step_decoded = json_decode($enable_step ?: '{}');
        if (!is_object($enable_step_decoded)) {
            $enable_step_decoded = (object) [
                'speciality_status' => 1,
                'service_status' => 1,
                'licenses_status' => 1,
            ];
        }
        // if(($enable_step_decoded->speciality_status ?? 0) == 1){
        //     $step = 3;
        // }elseif(($enable_step_decoded->service_status ?? 0) == 1){
        //     $step = 4;
        // }elseif(($enable_step_decoded->licenses_status ?? 0) == 1){
        //     $step = 5;
        // }else{
        //     $step = 6;
        // }
        $step = 3;
        auth()->user()->update(['hospital_id' => $hospital->id,'step' => $step,'enable_step' => $enable_step]);

        return response()->json(['success' => true, 'message' => 'Information Saved Successfully!!', 'step' => $step, 'wizard_step' => 3]); 
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
    
                    return response()->json(['success' => true, 'message' => 'Specialities Saved Successfully!!', 'step' => $step, 'wizard_step' => 4]);
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
                return response()->json(['success' => true, 'message' => 'Services Saved Successfully!!', 'step' => $step, 'wizard_step' => 5]);
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
            auth()->user()->update(['hospital_id' => $hospital_id, 'step' => 6]);

            return response()->json(['success' => true, 'message' => 'Licenses Saved Successfully!!', 'step' => 6, 'wizard_step' => 5]);
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
            return response()->json(['success' => true, 'message' => 'Document Saved Successfully!!', 'is_complete' => $allStepComplete, 'multi_branches' => $multi_branches, 'wizard_step' => 6, 'step' => 6]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }
    public function saveFacilityType(Request $request, $uuid)
    {
        $request->validate([
            'type_id' => 'required|integer|exists:hospital_types,id',
        ]);
        $user = User::where('uuid', $uuid)->first();
        if (!$user || (int) $user->id !== (int) auth()->id()) {
            abort(403);
        }
        $type = HospitalType::find($request->type_id);
        $w = (array) ($user->wizard_onboarding ?? []);
        $w['type_id'] = (int) $request->type_id;
        $w['facility_type'] = $type ? $type->name : '';
        $user->wizard_onboarding = $w;
        $user->step = 2;
        $user->save();

        if ($user->hospital_id) {
            $h = Hospital::where('id', $user->hospital_id)->first();
            if ($h) {
                $h->type_id = (int) $request->type_id;
                $h->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Facility type saved.', 'wizard_step' => 2, 'step' => 2]);
    }

    public function saveHospitalInfrastructure(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        if (!$user || !$user->hospital_id) {
            return response()->json(['success' => false, 'message' => 'Save basic information first.'], 422);
        }
        $hospital = Hospital::where('id', $user->hospital_id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found.'], 422);
        }
        $request->validate([
            'onboarding_meta' => 'nullable|array',
            // Bed Strength
            'onboarding_meta.infra_sanctioned_beds'   => 'nullable|integer|min:0|max:99999',
            'onboarding_meta.infra_functional_beds'   => 'nullable|integer|min:0|max:99999',
            'onboarding_meta.infra_icu_beds'          => 'nullable|integer|min:0|max:99999',
            'onboarding_meta.infra_nicu_picu_beds'    => 'nullable|integer|min:0|max:99999',
            'onboarding_meta.infra_hdu_beds'          => 'nullable|integer|min:0|max:99999',
            'onboarding_meta.infra_labour_room_beds'  => 'nullable|integer|min:0|max:99999',
            'onboarding_meta.infra_isolation_beds'    => 'nullable|integer|min:0|max:99999',
            'onboarding_meta.infra_burns_trauma_beds' => 'nullable|integer|min:0|max:99999',
            // OT, Lab & Imaging
            'onboarding_meta.infra_ot'                => 'nullable|integer|min:0|max:999',
            'onboarding_meta.infra_labour_rooms'      => 'nullable|integer|min:0|max:999',
            'onboarding_meta.infra_inhouse_lab'       => 'nullable|string|max:32',
            'onboarding_meta.infra_blood_bank'        => 'nullable|string|max:64',
            'onboarding_meta.infra_xray'              => 'nullable|string|max:32',
            'onboarding_meta.infra_ultrasound'        => 'nullable|string|max:32',
            'onboarding_meta.infra_ct_scan'           => 'nullable|string|max:32',
            'onboarding_meta.infra_mri'               => 'nullable|string|max:32',
            // Utilities & Connectivity
            'onboarding_meta.infra_power_backup'      => 'nullable|string|max:64',
            'onboarding_meta.infra_water_supply'      => 'nullable|string|max:64',
            'onboarding_meta.infra_internet'          => 'nullable|string|max:64',
            'onboarding_meta.infra_ambulance'         => 'nullable|string|max:64',
            'onboarding_meta.infra_oxygen_supply'     => 'nullable|string|max:64',
            'onboarding_meta.infra_pharmacy'          => 'nullable|string|max:64',
            'onboarding_meta.infra_waste_management'  => 'nullable|string|max:64',
            'onboarding_meta.infra_fire_noc'          => 'nullable|string|max:64',
        ]);
        $existingMeta = is_array($hospital->onboarding_meta) ? $hospital->onboarding_meta : [];
        $incomingMeta = $request->input('onboarding_meta', []);
        if (!is_array($incomingMeta)) {
            $incomingMeta = [];
        }
        $onlyInfra = [];
        foreach ($incomingMeta as $k => $v) {
            if (is_string($k) && strncmp($k, 'infra_', 6) === 0) {
                $onlyInfra[$k] = $v;
            }
        }
        $hospital->onboarding_meta = array_merge($existingMeta, array_filter($onlyInfra, static function ($v) {
            return $v !== null && $v !== '';
        }));
        $hospital->save();

        return response()->json(['success' => true, 'message' => 'Infrastructure saved.', 'wizard_step' => 4, 'step' => 4]);
    }

    public function saveStaffStrength(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        if (!$user || !$user->hospital_id) {
            return response()->json(['success' => false, 'message' => 'Save basic information first.'], 422);
        }
        $hospital = Hospital::where('id', $user->hospital_id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found.'], 422);
        }

        $request->validate([
            'staff_strength'                   => 'nullable|array',
            'staff_strength.*.sanctioned'      => 'nullable|integer|min:0|max:99999',
            'staff_strength.*.in_position'     => 'nullable|integer|min:0|max:99999',
        ]);

        $incoming = $request->input('staff_strength', []);
        $cleaned  = [];
        foreach ((array) $incoming as $id => $row) {
            $sanc  = isset($row['sanctioned'])  && $row['sanctioned']  !== '' ? (int) $row['sanctioned']  : null;
            $inpos = isset($row['in_position']) && $row['in_position'] !== '' ? (int) $row['in_position'] : null;
            if ($sanc !== null || $inpos !== null) {
                $cleaned[(int) $id] = array_filter([
                    'sanctioned'  => $sanc,
                    'in_position' => $inpos,
                ], static fn($v) => $v !== null);
            }
        }

        $meta                   = is_array($hospital->onboarding_meta) ? $hospital->onboarding_meta : [];
        $meta['staff_strength'] = $cleaned;
        $hospital->onboarding_meta = $meta;
        $hospital->save();

        return response()->json(['success' => true, 'message' => 'Staff strength saved successfully.']);
    }

    public function saveStaffServices(Request $request, $uuid, $hospital_id)
    {
        $user = User::where('uuid', $uuid)->first();
        if (!$user || (int) $user->hospital_id !== (int) $hospital_id) {
            return response()->json(['success' => false, 'message' => 'Hospital record required.'], 422);
        }

        $hospital = Hospital::where('id', $hospital_id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found.'], 422);
        }

        $enableStep = auth()->user()->enable_step
            ? json_decode(auth()->user()->enable_step)
            : json_decode(Helpers::get_settings('empanelment_step_status') ?: '{}');

        if (!is_object($enableStep)) {
            $enableStep = (object) ['speciality_status' => 0, 'service_status' => 0, 'licenses_status' => 0];
        }

        $rules = [
            'staff_strength' => 'nullable|array',
            'staff_strength.*.sanctioned' => 'nullable|integer|min:0|max:99999',
            'staff_strength.*.in_position' => 'nullable|integer|min:0|max:99999',
        ];
        $messages = [];

        $services = collect();
        if (!empty($enableStep->service_status)) {
            $services = Helpers::getCommanData('Service');
            foreach ($services as $service) {
                if (sizeof($service->subServices) > 0) {
                    foreach ($service->subServices()->orderBy('sort_order', 'ASC')->get() as $subService) {
                        if ($subService->is_required) {
                            $name = str_replace(' ', '-', strtolower($subService->name));
                            $field = $service->id . '_' . $subService->id . '_' . $name;
                            $existingService = $hospital->services()
                                ->where(['service_id' => $service->id, 'sub_service_id' => $subService->id])
                                ->first();

                            $rules[$field] = 'required';
                            $messages[$field . '.required'] = 'This field is Required';

                            if ($request->{$field} == 1) {
                                $rules[$field . '_text'] = 'sometimes|required';
                                $rules[$field . '_image'] = $existingService ? 'nullable|mimes:jpg,png,jpeg' : 'sometimes|required|mimes:jpg,png,jpeg';
                                $messages[$field . '_text.required'] = 'This field is Required';
                                $messages[$field . '_image.required'] = 'This field is Required';
                                $messages[$field . '_image.mimes'] = 'Only jpg, png and jpeg images are allowed.';
                            }
                        }
                    }
                }
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->messages()
            ], 422);
        }

        if (!empty($enableStep->speciality_status)) {
            $specialityIds = (array) $request->input('speciality_id', []);
            $hasAvailableSpeciality = false;
            foreach ($specialityIds as $specialityId) {
                if ((int) $request->input('available_' . $specialityId, 0) === 1) {
                    $hasAvailableSpeciality = true;
                    break;
                }
            }

            if (!$hasAvailableSpeciality) {
                return response()->json(['success' => false, 'message' => 'Please Select One Speciality!!']);
            }
        }

        $incoming = $request->input('staff_strength', []);
        $cleaned = [];
        foreach ((array) $incoming as $id => $row) {
            $sanc = isset($row['sanctioned']) && $row['sanctioned'] !== '' ? (int) $row['sanctioned'] : null;
            $inpos = isset($row['in_position']) && $row['in_position'] !== '' ? (int) $row['in_position'] : null;
            if ($sanc !== null || $inpos !== null) {
                $cleaned[(int) $id] = array_filter([
                    'sanctioned' => $sanc,
                    'in_position' => $inpos,
                ], static fn($v) => $v !== null);
            }
        }

        $meta = is_array($hospital->onboarding_meta) ? $hospital->onboarding_meta : [];
        $meta['staff_strength'] = $cleaned;
        $hospital->onboarding_meta = $meta;
        $hospital->save();

        if (!empty($enableStep->speciality_status)) {
            HospitalSpeciality::where('hospital_id', $hospital_id)->delete();
            foreach ((array) $request->input('speciality_id', []) as $specialityId) {
                if ((int) $request->input('available_' . $specialityId, 0) === 1) {
                    $hospital->specialities()->create([
                        'uuid' => Helpers::generateUUID(),
                        'speciality_id' => $specialityId,
                        'available' => 1,
                        'remark' => $request->input('remark_' . $specialityId),
                    ]);
                }
            }
        }

        if (!empty($enableStep->service_status)) {
            $isServiceValid = 0;
            foreach ($services as $service) {
                if (sizeof($service->subServices) > 0) {
                    foreach ($service->subServices()->orderBy('sort_order', 'ASC')->get() as $subService) {
                        $name = str_replace(' ', '-', strtolower($subService->name));
                        $field = $service->id . '_' . $subService->id . '_' . $name;

                        if ($request->has($field) && $request->{$field} !== '') {
                            $isServiceValid = 1;
                            $serviceData = [
                                'uuid' => Helpers::generateUUID(),
                                'service_id' => $service->id,
                                'sub_service_id' => $subService->id,
                                'action_id' => $request->{$field . '_action'},
                                'service_value' => $request->{$field},
                                'text_value' => $request->{$field . '_text'},
                                'remark' => $request->{$service->id . '_' . $subService->id . '_remark'}
                            ];

                            if ($request->hasFile($field . '_image')) {
                                $serviceData['image'] = $request->file($field . '_image')->store('serviceimage', 'public');
                            }

                            $hospital->services()->updateOrCreate(
                                ['service_id' => $service->id, 'sub_service_id' => $subService->id],
                                $serviceData
                            );
                        }
                    }
                }
            }

            if (!$isServiceValid) {
                return response()->json(['success' => false, 'message' => 'Please Select Any One.']);
            }
        }

        auth()->user()->update(['hospital_id' => $hospital_id, 'step' => 5]);

        return response()->json([
            'success' => true,
            'message' => 'Staff strength and services saved successfully.',
            'step' => 5,
            'wizard_step' => 5
        ]);
    }
    public function saveLicensesDocuments(Request $request, $uuid, $hospital_id)
    {
        $hospital = Hospital::where('id', $hospital_id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found.'], 422);
        }

        // ========== LICENSES VALIDATION ==========
        $licenses = Helpers::getCommanData('License');
        $licensesRules = [];
        $licensesMessages = [];
        foreach ($licenses as $key => $value) {
            foreach ($value->licenseType as $k => $v) {
                if ($v->is_required) {
                    $checkLicense = $hospital->licenses()->where(['license_id' => $value->id, 'license_type_id' => $v->id])->first();
                    $licensesRules[$value->id . '_' . $v->id . '_dateissue'] = 'required|date';
                    $licensesRules[$value->id . '_' . $v->id . '_dateexpiry'] = 'required|date';
                    $licensesRules['document_' . $value->id . '_' . $v->id] = $checkLicense ? 'nullable|mimes:pdf' : 'required|mimes:pdf';

                    $licensesMessages[$value->id . '_' . $v->id . '_dateissue.required'] = 'The Date of Issue for ' . $v->name . ' is required.';
                    $licensesMessages[$value->id . '_' . $v->id . '_dateexpiry.required'] = 'The Date of Expiry for ' . $v->name . ' is required.';
                    $licensesMessages['document_' . $value->id . '_' . $v->id . '.required'] = 'The Document for ' . $v->name . ' is required.';
                    $licensesMessages['document_' . $value->id . '_' . $v->id . '.mimes'] = 'The Document for ' . $v->name . ' must be a file of type: pdf.';
                }
            }
        }

        // ========== DOCUMENTS VALIDATION ==========
        $documents = Helpers::getCommanData('EmpanelmentDocument');
        $documentsRules = [];
        $documentsMessages = [];
        foreach ($documents as $key => $value) {
            if ($value->is_required) {
                $checkDocument = $hospital->documents()->where(['document_id' => $value->id])->first();
                $documentsRules['document_' . $value->id . '_doc'] = $checkDocument ? 'nullable' : 'required|mimes:pdf|max:10240';
                $documentsMessages['document_' . $value->id . '_doc.required'] = 'The Document for ' . $value->name . ' is required.';
                $documentsMessages['document_' . $value->id . '_doc.mimes'] = 'The Document for ' . $value->name . ' must be a file of type: pdf.';
            }
        }

        $validator = Validator::make($request->all(), array_merge($licensesRules, $documentsRules), array_merge($licensesMessages, $documentsMessages));
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'message' => $errors->first(),
                'errors' => $errors->messages()
            ], 422);
        }

        // ========== SAVE LICENSES ==========
        foreach ($licenses as $key => $value) {
            foreach ($value->licenseType as $k => $v) {
                if ($request->{$value->id . '_' . $v->id . '_dateissue'} && $request->{$value->id . '_' . $v->id . '_dateexpiry'}) {
                    $issueDate = date('Y-m-d', strtotime($request->{$value->id . '_' . $v->id . '_dateissue'}));
                    $expiryDate = date('Y-m-d', strtotime($request->{$value->id . '_' . $v->id . '_dateexpiry'}));

                    $array = [
                        'uuid' => Helpers::generateUUID(),
                        'license_id' => $value->id,
                        'license_type_id' => $v->id,
                        'issue_date' => $issueDate,
                        'expiry_date' => $expiryDate,
                        'remark' => $request->{$value->id . '_' . $v->id . '_remark'}
                    ];

                    if ($request->hasFile('document_' . $value->id . '_' . $v->id)) {
                        $filePath = $request->file('document_' . $value->id . '_' . $v->id)->store('certificate', 'public');
                        $array['document'] = $filePath;
                    }

                    $hospital->licenses()->updateOrCreate(['license_id' => $value->id, 'license_type_id' => $v->id], $array);
                }
            }
        }

        // ========== SAVE DOCUMENTS ==========
        foreach ($documents as $key => $value) {
            $array = [
                'uuid' => Helpers::generateUUID(),
                'document_id' => $value->id,
                'remarks' => $request->{$value->id . '_remarkdoc'}
            ];

            if ($request->hasFile('document_' . $value->id . '_doc')) {
                $filePath = $request->file('document_' . $value->id . '_doc')->store('certificate', 'public');
                $array['document'] = $filePath;
            }

            $hospital->documents()->updateOrCreate(['document_id' => $value->id], $array);
        }

        auth()->user()->update(['hospital_id' => $hospital_id, 'step' => 6]);

        return response()->json(['success' => true, 'message' => 'Licenses and Documents Saved Successfully!!', 'step' => 6, 'wizard_step' => 6]);
    }
    public function saveWizardMeta(Request $request, $uuid)
    {
        $request->validate([
            'section' => 'required|in:ab,hmis',
            'ab_empanelment' => 'nullable|array',
            'ab_empanelment.eligibility' => 'nullable|array',
            'ab_empanelment.eligibility.*' => 'integer|exists:empanelment_eligibilities,id',
            'ab_empanelment.specialities' => 'nullable|array',
            'ab_empanelment.specialities.*' => 'integer|exists:specialities,id',
            'ab_empanelment.sha_code' => 'nullable|string|max:64',
            'ab_empanelment.rohini_id' => 'nullable|string|max:64',
            'ab_empanelment.bank_account' => 'nullable|string|max:64',
            'ab_empanelment.ifsc' => 'nullable|string|max:32',
            'hmis_setup' => 'nullable|array',
            'hmis_setup.admin_username' => 'nullable|string|max:120',
            'hmis_setup.admin_email' => 'nullable|email|max:255',
            'hmis_setup.role_preset' => 'nullable|string|max:64',
            'hmis_setup.two_fa' => 'nullable|string|max:64',
            'hmis_setup.admin_password' => 'nullable|string|max:255',
            'hmis_setup.abha_integration' => 'nullable|string|max:32',
            'hmis_setup.nic_integration' => 'nullable|string|max:32',
            'hmis_setup.hims_data_reporting' => 'nullable|string|max:32',
            'hmis_setup.ambulance_integration' => 'nullable|string|max:32',
            'hmis_setup.cmss_integration' => 'nullable|string|max:32',
            'hmis_setup.payroll_integration' => 'nullable|string|max:32',
            'hmis_setup.modules' => 'nullable|array',
            'hmis_setup.modules.*' => 'string|max:64',
        ]);
        $user = User::where('uuid', $uuid)->first();
        if (!$user || !$user->hospital_id) {
            return response()->json(['success' => false, 'message' => 'Hospital record required.'], 422);
        }
        $hospital = Hospital::where('id', $user->hospital_id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found.'], 422);
        }
        $meta = is_array($hospital->onboarding_meta) ? $hospital->onboarding_meta : [];
        if ($request->section === 'ab') {
            $ab = (array) ($meta['ab_empanelment'] ?? []);
            $incomingAb = (array) $request->input('ab_empanelment', []);
            $incomingAb['eligibility'] = $request->input('ab_empanelment.eligibility', []);
            $incomingAb['specialities'] = $request->input('ab_empanelment.specialities', []);
            $ab = array_merge($ab, $incomingAb);
            $meta['ab_empanelment'] = array_filter($ab, static function ($v) {
                return $v !== null && $v !== '';
            });
            $wizardStep = 7;
        } else {
            $hm = array_merge((array) ($meta['hmis_setup'] ?? []), (array) $request->input('hmis_setup', []));
            if (!empty($hm['admin_password'])) {
                $hm['admin_password_hash'] = Hash::make($hm['admin_password']);
            }
            unset($hm['admin_password']);
            $meta['hmis_setup'] = array_filter($hm, static function ($v) {
                return $v !== null && $v !== '';
            });
            $wizardStep = 8;
        }
        $hospital->onboarding_meta = $meta;
        $hospital->save();

        return response()->json(['success' => true, 'message' => 'Saved successfully.', 'wizard_step' => $wizardStep, 'step' => $wizardStep]);
    }

    public function getDistrict(Request $request) {
        $stateId = $request->state_id;
        $data = HospitalDistrict::where('state_id', $stateId)->get();
        return response()->json($data);
    }

    public function stepLoad(Request $request, $uuid) {
        $request->validate([
            'step' => 'required|integer|min:1|max:8',
        ]);
        $step = (int) $request->step;

        $user = User::where('uuid', $uuid)->first();

        $hospital = '';
        $hospital_id = '';
        if ($user->hospital_id) {
            $hospital_id = $user->hospital_id;
            $hospital = Hospital::where('id', $user->hospital_id)->first();
        }
        switch ($step) {
            case 1:
                return view('hospital.empanelment._partials.wizard-facility-type', compact('uuid', 'user', 'hospital'));
            case 2:
                return view('hospital.empanelment._partials.wizard-basic-information', compact('uuid', 'user', 'hospital'));
            case 3:
                if (!$hospital) {
                    return response('<div class="eo-card"><div class="eo-card-body text-warning">Save <strong>Basic information</strong> (step 2) first.</div></div>', 200);
                }

                return view('hospital.empanelment._partials.hospital-info-infrastructure', compact('uuid', 'hospital'));
            case 4:
                if (!$hospital) {
                    return response('<div class="eo-card"><div class="eo-card-body text-warning">Save <strong>Basic information</strong> first.</div></div>', 200);
                }

                return view('hospital.empanelment._partials.wizard-staff-services', compact('uuid', 'hospital'));
            case 5:
                if (!$hospital) {
                    return response('<div class="eo-card"><div class="eo-card-body text-warning">Save <strong>Basic information</strong> first.</div></div>', 200);
                }

                return view('hospital.empanelment._partials.wizard-documents', compact('uuid', 'hospital'));
            case 6:
                if (!$hospital) {
                    return response('<div class="eo-card"><div class="eo-card-body text-warning">Save <strong>Basic information</strong> first.</div></div>', 200);
                }

                $eligibilities = EmpanelmentEligibility::orderBy('id')->get();
                $specialities = Helpers::getCommanData('Speciality');
                return view('hospital.empanelment._partials.wizard-ab-empanelment', compact('uuid', 'hospital', 'eligibilities', 'specialities'));
            case 7:
                if (!$hospital) {
                    return response('<div class="eo-card"><div class="eo-card-body text-warning">Save <strong>Basic information</strong> first.</div></div>', 200);
                }

                return view('hospital.empanelment._partials.wizard-hmis-setup', compact('uuid', 'hospital'));
            case 8:
                if (!$hospital) {
                    return response('<div class="eo-card"><div class="eo-card-body text-warning">Save <strong>Basic information</strong> first.</div></div>', 200);
                }

                return view('hospital.empanelment._partials.wizard-review-submit', compact('uuid', 'hospital', 'user'));
        }
    }

    public function hospitalSubmit(Request $request, $uuid, $hospital_id) {
        $check = Helpers::checkAllStepIsCompleteOrNot($uuid);
        if($check) {
            $hospital = Hospital::where('id', $hospital_id)->first();
            if (!$hospital) {
                return response()->json(['success' => false, 'message' => 'Hospital not found']);
            }

            // Preserve existing application ID on rejected resubmission.
            if (!$hospital->application_id) {
                $hospital->application_id = $this->generateApplicationId();
            }

            $hospital->reject_reason = null;
            $hospital->status = 'Submitted';
            $hospital->status_update_date = date('Y-m-d H:i:s');
            $hospital->save();
            $url = route('hospital.dashboard');

            return response()->json([
                'success' => true,
                'application_id' => $hospital->application_id,
                'url' => $url
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Please fill all details first of hospital']);
        }
    }

    private function generateApplicationId(): string
    {
        $prefix = 'ONB-UTT';
        $financialYear = $this->getFinancialYearPrefix();
        $pattern = $prefix . '-' . $financialYear . '-%';

        $lastApplicationId = Hospital::where('application_id', 'like', $pattern)
            ->orderBy('application_id', 'desc')
            ->value('application_id');

        $nextSequence = 1;
        if ($lastApplicationId) {
            $segments = explode('-', $lastApplicationId);
            $lastSequence = intval(end($segments));
            $nextSequence = $lastSequence + 1;
        }

        do {
            $applicationId = sprintf('%s-%s-%05d', $prefix, $financialYear, $nextSequence);
            $exists = Hospital::where('application_id', $applicationId)->exists();
            if ($exists) {
                $nextSequence++;
            }
        } while ($exists);

        return $applicationId;
    }

    private function getFinancialYearPrefix(): string
    {
        $month = (int) date('n');
        $year = (int) date('Y');

        if ($month >= 4) {
            return (string) $year;
        }

        return (string) ($year - 1);
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

    private function mapLegacyStepToWizardStep(int $legacyStep): int
    {
        if ($legacyStep <= 1) {
            return 1;
        }
        if ($legacyStep <= 2) {
            return 2;
        }
        if ($legacyStep === 3) {
            return 4;
        }
        if ($legacyStep === 4) {
            return 4;
        }
        if ($legacyStep === 5) {
            return 5;
        }
        if ($legacyStep >= 6) {
            return 8;
        }

        return 1;
    }

}
