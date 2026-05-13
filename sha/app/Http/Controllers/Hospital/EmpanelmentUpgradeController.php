<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserHfr;
use App\Models\{ Hospitals, FacilityOwnershipSubType, HospitalDistrict, HospitalState, HospitalHumanResource, HumanResource, HospitalTeam, Village, HospitalSpeciality, FacilityOwnershipType, WorkFlowHistory, User, UHospitals, UHospitalHumanResource, UHospitalTeam, TabStatus};

// UHospitals, UHospitalAddress, UHospitalSpeciality, UHospitalLicense, UHospitalServices, UHospitalCeo, UHospitalHumanResource, UHospitalTeam, UHospitalAccreditation, UFinancialInformation, UTaxDetails

use App\CentralLogics\Helpers;
use App\Models\MobileOtp;
use App\Rules\UniqueAcrossTables;
use App\Mail\StatusMail;
use Mail;
use DataTables;
use Illuminate\Validation\Rule;

class EmpanelmentUpgradeController extends Controller
{
    public function updateApplication(Request $request, $uuid) {
        $hosp = Hospitals::where('uuid', base64_decode($uuid))->first();
        $hospital = UHospitals::where('main_hospitalid', $hosp->id)->first();
        
        $code = @$hospital->facilityOwnershipType->name;
        $step = 1;
       
        return view('hospital.upgrade.form', compact('hospital', 'uuid', 'step'));
    }

    public function stepLoad(Request $request, $uuid, $hospital_id) {
        $validatedData = $request->validate([
            'step' => 'required',
        ]);

        $hospital = UHospitals::where('main_hospitalid', $hospital_id)->first();

        $ischangeData = Helpers::checkdataupdateornot($hospital_id);

        if($request->step == 1) {
            $facilityTypes = Helpers::getCommanData('FacilityType');
            $FacilityOwnershipType = Helpers::getCommanData('FacilityOwnershipType');
            $FacilityRegistrationCertificate = Helpers::getCommanData('FacilityRegistrationCertificate');
            $FacilitySpecialityType = Helpers::getCommanData('FacilitySpecialityType');
            $GovermentBenefits = Helpers::getCommanData('GovermentBenefits');
            $SystemMedicine = Helpers::getCommanData('SystemMedicine');
          
            return view('hospital.upgrade._partials.establishment_details', compact('uuid', 'hospital_id', 'hospital', 'facilityTypes', 'FacilityOwnershipType', 'FacilityRegistrationCertificate', 'FacilitySpecialityType', 'GovermentBenefits', 'SystemMedicine')); 
        } else if($request->step == 2) {
            $address = !empty($hospital) ? $hospital->hospitalAddress : '';
            $state = HospitalState::where('country_id', 101)->get();
            return view('hospital.upgrade._partials.address', compact('uuid', 'hospital_id', 'hospital', 'state', 'address'));
        } else if($request->step == 3) {
            $schemes =  Helpers::getCommanData('SchemeType');
            return view('hospital.upgrade._partials.scheme', compact('schemes','uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 4) {
            // $specialities =  Helpers::getCommanData('Speciality');
            $specialities =  Speciality::orderBy('name', 'ASC')->get();
            return view('hospital.upgrade._partials.speciality', compact('specialities','uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 5) {
            $services =  Helpers::getCommanData('Service');
            return view('hospital.upgrade._partials.services', compact('services', 'uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 6) {
            $licenses =  Helpers::getCommanData('Licenses');
            return view('hospital.upgrade._partials.licenses', compact('licenses', 'uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 7) {
            $mhr = HumanResource::where('type_slug', 'mhr')->where('name', '!=', 'Medical Superintendent')->get();
            $sshr = HumanResource::where('type_slug', 'sshr')->get();
            $specialities = $hospital->specialities()->where('offered',1)->get();
            return view('hospital.upgrade._partials.humanresources', compact('uuid', 'hospital_id', 'hospital', 'mhr', 'sshr', 'specialities'));
        } else if($request->step == 8) {
            return view('hospital.upgrade._partials.accreditation', compact('uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 9) {
            return view('hospital.upgrade._partials.finance', compact('uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 10) {
            return view('hospital.upgrade._partials.documents', compact('uuid', 'hospital_id', 'hospital', 'ischangeData'));
        }
    }

    public function upgradeEstablishmentDetails(Request $request, $uuid, $hospitalId) {

        $type3required = false;
        $type3textrequired = false;

        $subType2 =  FacilityOwnershipSubType::where('facility_ownership_type_id', $request->facility_ownership_type)->where('type_id', $request->facility_ownership_sub_type1)->where('id', $request->facility_ownership_sub_type2)->first();
        
        if ($request->facility_ownership_sub_type3text && $subType2 && (strtolower($subType2->name) == 'psu')) {
            $type3textrequired = true;
        }

        $facilitySubType2 = FacilityOwnershipSubType::where('facility_ownership_type_id', $request->facility_ownership_type)->where('type_id', $request->facility_ownership_sub_type1)->where('type2_id', $request->facility_ownership_sub_type2)->where('type', 2)->get();

        if ($request->facility_ownership_sub_type3 && $facilitySubType2->count() > 0) {
            $type3required = true;
        }

        $facilityOwnershipSubType3Rule = $type3required ? 'required' : 'nullable';
        $facility_ownership_sub_type3text = $type3textrequired ? 'required' : 'nullable';

        $subtype = FacilityOwnershipSubType::where('id', $request->facility_ownership_sub_type1)->first();

        $uuid = base64_decode($uuid);

        $check = UHospitals::where(['main_hospitalid' => $hospitalId])->first();

        $documentrequired = false;
        $documentnamerequired = false;
        $pdocumentrequired = false;
        $pdocumentnamerequired = false;

        if(!$check && $subtype && ($subtype->name == "Partnership" || $subtype->name == "Society" || $subtype->name == "Trust")) {
            $documentrequired = true;
            $documentnamerequired = true;
        }

        if(!$check && $subtype && $subtype->name == "Propiertship") {
            $pdocumentrequired = true;
            $pdocumentnamerequired = true;
        }        

        $sub_type_certificate_nameRule = $documentnamerequired ? 'required' : 'nullable';
        $sub_type_certificateRule = $documentrequired ? "required|mimes:pdf|max:10240" : 'nullable';
        $propritership_document_nameRule = $pdocumentnamerequired ? 'required' : 'nullable';
        $propritership_documentRule = $pdocumentrequired ? "required|mimes:pdf|max:10240" : 'nullable';

        $validatedData = $request->validate([
            'facility_name' => 'required',
            'facility_type' => 'required',
            'facility_speciality_type' => 'required',
            'facility_ownership_type' => 'required',
            'facility_ownership_sub_type1' => 'required',
            'sub_type_certificate_name' => $sub_type_certificate_nameRule,
            'sub_type_certificate' => $sub_type_certificateRule,
            'propritership_document' => $propritership_documentRule,
            'propritership_document_name' => $propritership_document_nameRule,
            'facility_ownership_sub_type2' => 'required',
            'facility_ownership_sub_type3' => $facilityOwnershipSubType3Rule,
            'facility_ownership_sub_type3text' => $facility_ownership_sub_type3text,
            'date_of_establishment' => 'required',
            'facility_registration_certificate' => 'required',
            'facility_registration_number' => 'required',
            'registration_certificate_expiry' => 'required',
            'system_of_medicine' => 'required',
            'gov_benifits' => 'nullable',
            'rohini_id' => 'nullable',
            'group_id' => 'nullable',
            'name_od_group' => 'nullable',
            'pg_dnb' => 'nullable',
        ]);
        
        if($request->facility_ownership_sub_type3text && $request->facility_ownership_sub_type3 == "") {
            $validatedData['facility_ownership_sub_type3'] = $request->facility_ownership_sub_type3text;
            unset($validatedData['facility_ownership_sub_type3text']);
        }
        if($request->facility_ownership_sub_type3text == "" && $request->facility_ownership_sub_type3) {
            $validatedData['facility_ownership_sub_type3'] = $request->facility_ownership_sub_type3;
            unset($validatedData['facility_ownership_sub_type3text']);
        }
        
        
        // $validatedData['hfr_id'] = $check->hfr_id;
        // $validatedData['uuid'] = $uuid;
        // $validatedData['user_id'] = auth()->user()->id;
        // $validatedData['is_added'] = 1;
        // $validatedData['status_update_date'] = date('Y-m-d H:i:s');

        if ($request->hasFile('sub_type_certificate')) {
            $filePath = $request->file('sub_type_certificate')->store('certificate', 'public'); 
            $validatedData['sub_type_certificate'] = $filePath;
            $validatedData['sub_type_certificate_name'] = $request->sub_type_certificate_name;
        }    
        
        if($request->hasFile('propritership_document')) {
            $filePath = $request->file('propritership_document')->store('certificate', 'public'); 
            $validatedData['sub_type_certificate'] = $filePath;
            $validatedData['sub_type_certificate_name'] = $request->propritership_document_name;
        }

        $hospital = Hospitals::where(['id' => $hospitalId])->first();

        $hospital->upgradeHospital()->updateOrCreate([
            'hospital_id' => $hospitalId,
        ],
        [
            'hospital_id' => $hospitalId,
            'establishment_details' => 1
        ]);
        

        $hospital->status = "Draft";
        $hospital->is_upgrade_application = 1;
        $hospital->save();

        $id = UHospitals::updateOrCreate(
            [
                'main_hospitalid' => $hospitalId, // Matching condition
            ],
            $validatedData
        );

        $check->update([
            'dec_verify_status' => null,
            'dec_verify_remark' => '',
            'dec_verify_id' => null,
            'dec_status' => null,
            'dec_remark' => '',
            'dec_id' => null,
            'sec_status' => null,
            'sec_remark' => '',
            'sec_id' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Establishment Details Upgrade Successfully!!', 'data' => auth()->user(), 'id' => $id]); 
    }

    public function upgradeAddressDetails(Request $request, $uuid, $hospitalId) {

        $validatedData = $request->validate([
            'address' => 'required',
            'pincode' => 'required',
            'block' => 'required',
            'village' => 'required',
            'city' => 'required',
            'district' => 'required',
            'state' => 'required',
            'landmark' => 'nullable',
            'telephone' => 'nullable',
            'std_code' => 'nullable',
            'mobile_no' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('hospital_addresses', 'email')
                ->where('hospital_id', $hospitalId)
                ->whereNot('email', $request->email)
            ],
            'website' => 'nullable',
            'police_station' => 'required',
            'locality' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        
        $checkHFR = UHospitals::where('main_hospitalid', $hospitalId)->first();
        // if(!$checkHFR) {
        //     return response()->json(['success' => false, 'message' => 'Enter First Facility Details', 'uuid' => $uuid]); 
        // }

        // $validatedData['is_added'] = 1;
        // $validatedData['uuid'] = Helpers::generateUUID();
        // $validatedData['hospital_id'] = $checkHFR->id;
        // $validatedData['step'] = 1; 
 
        $checkHFR->hospitalAddress()->updateOrCreate(
            ['hospital_id' => $checkHFR->main_hospitalid], 
            $validatedData 
        );
        $validatedData['status'] = "Draft";

        $hospital = Hospitals::where('id', $hospitalId)->first();

        $hospital->upgradeHospital()->updateOrCreate([
            'hospital_id' => $hospitalId,
        ],
        [
            'hospital_id' => $hospitalId,
            'address' => 1
        ]);

        $hospital->status = "Draft";
        $hospital->is_upgrade_application = 1;
        $hospital->save();

        $checkHFR->hospitalAddress()->update([
            'dec_verify_status' => null,
            'dec_verify_remark' => '',
            'dec_verify_id' => null,
            'dec_status' => null,
            'dec_remark' => '',
            'dec_id' => null,
            'sec_status' => null,
            'sec_remark' => '',
            'sec_id' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Address Details Update Successfully!!']); 
    }

    public function upgradeScheme(Request $request, $uuid, $hospitalId) {
        $check = UHospitals::where('main_hospitalid', $hospitalId)->first();

        $validatedData = $request->validate([
            'scheme' => 'required',
            'image' => $check->image == "" ? 'required|mimes:jpg,png,jpeg|dimensions:max_width=1920,max_height=1080' : 'nullable',
            'images' => 'nullable|array|max:5',
            'images.*' => 'mimes:jpg,png,jpeg,gif|dimensions:max_width=1920,max_height=1080',
            'hospital_ppt' => $check->hospital_ppt == "" ? 'required|mimes:pdf|max:10240' : 'nullable|mimes:pdf|max:10240',
        ]);     

        if($check) {
           $check->scheme = $request->scheme;
            if ($request->hasFile('image')) {
                $filePath = $request->file('image')->store('image', 'public'); 
                $check->image = $filePath; // Add file path to data
            }  

            if ($request->hasFile('hospital_ppt')) {
                $filePath = $request->file('hospital_ppt')->store('certificate', 'public'); 
                $check->hospital_ppt = $filePath; // Add file path to data
            }  

            $check->step = 4;
            $check->save();

            if ($request->hasFile('images')) {
                // $check->images()->delete();
                foreach ($request->file('images') as $image) {
                    $filePath = $image->store('images', 'public'); 
                    $array['image'] = $filePath;
                    $check->images()->create($array);
                }
            }

            $hospital = Hospitals::where('id', $hospitalId)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospitalId,
            ],
            [
                'hospital_id' => $hospitalId,
                'scheme' => 1
            ]);

            $check->update([
                'status' => "Draft",
                'is_upgrade_application' => 1,
                'dec_verify_status' => null,
                'dec_verify_remark' => '',
                'dec_verify_id' => null,
                'dec_status' => null,
                'dec_remark' => '',
                'dec_id' => null,
                'sec_status' => null,
                'sec_remark' => '',
                'sec_id' => null,
            ]);

            return response()->json(['success' => true, 'message' => 'Scheme Upgrade Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function upgradeSpecialities(Request $request, $uuid, $hospitalId) {
        // echo "<pre>";
        // print_r($request->all());
        // exit;
        // echo "</pre>";
        $check = UHospitals::where('main_hospitalid', $hospitalId)->first();
        $rules = [];
        $messages = [];
        foreach ($request->speciality_id as $value) {
            $available = (int) $request->input("available_{$value}", 0);
            $offered = (int) $request->input("offered_{$value}", 0);

            if ($available === 1 && $offered === 0) {
                $rules["not_offered_reason_{$value}"] = 'required|string|min:3|max:255';
                $messages["not_offered_reason_{$value}.required"] = 'Reason for not offering is required if speciality is available but not offered.';
            }
        }
        
        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->messages()
            ], 422);
        }

        if($check) {
            // HospitalSpeciality::where('hospital_id', $hospital_id)->delete();
            $isValid = 0;
            if($request->speciality_id) {
                $specialities = $request->speciality_id;
                foreach ($specialities as $key => $value) {
                    if($request->{'available_'.$value} == 0 || $request->{'available_'.$value} == 1) {
                        $isValid = 1;
                        $available = $request->{'available_'.$value};
                        $offered = $request->{'offered_'.$value};
                        $not_offered_reason = $request->{'not_offered_reason_'.$value};
                        $remark = $request->{'remark_'.$value};

                        $speciality = $check->specialities()->where('speciality_id', $value)->first();
                        if ($speciality) {
                            // Update existing record
                            $updateData = [
                                'available' => $available,
                                'offered' => $available == 1 ? $offered : 0,
                                'not_offered_reason' =>  $available == 1 ? $not_offered_reason : null,
                                'remark' => $remark
                            ];

                            if($offered != $speciality->offered || $available != $speciality->available || $not_offered_reason != $speciality->not_offered_reason || $remark != $speciality->remark) {
                                $updateData['dec_verify_status'] = null;
                                $updateData['dec_verify_remark'] = '';
                                $updateData['dec_verify_id'] = null;
                                $updateData['dec_status'] = null;
                                $updateData['dec_remark'] = '';
                                $updateData['dec_id'] = null;
                                $updateData['sec_status'] = null;
                                $updateData['sec_remark'] = '';
                                $updateData['sec_id'] = null;
                            }
                            $speciality->update($updateData);
                        } else {
                            $check->specialities()->create([
                                'uuid' => Helpers::generateUUID(),
                                'speciality_id' => $value,
                                'available' => $available,
                                'offered' => $offered,
                                'not_offered_reason' => $not_offered_reason,
                                'remark' => $remark
                            ]);
                        }

                        // $array = [
                        //     'uuid' => Helpers::generateUUID(),
                        //     'speciality_id' => $value,
                        //     'available' => $available,
                        //     'offered' => $offered,
                        //     'not_offered_reason' => $not_offered_reason,
                        //     'remark' => $remark
                        // ];
                        // $check->specialities()->create($array);
                    }
                }

                if($isValid) {
                    $hospital = Hospitals::where('id', $hospitalId)->first();
                    $hospital->status = "Draft";
                    $hospital->is_upgrade_application = 1;
                    $hospital->save();

                    $hospital->upgradeHospital()->updateOrCreate([
                        'hospital_id' => $hospitalId,
                    ],
                    [
                        'hospital_id' => $hospitalId,
                        'speciality' => 1
                    ]);

                    $check->step = 5;
                    $check->save();
    
                    return response()->json(['success' => true, 'message' => 'Specialities Saved Successfully!!']);
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

    public function upgradeServices(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $services =  Helpers::getCommanData('Service');

            foreach ($services as $key => $value) {
                if(sizeof($value->subServices) > 0) {
                    foreach ($value->subServices()->orderBy('sort_order', 'ASC')->get() as $k => $v) {
                        $isRequired = false;
                        if($v->is_required && empty($v->required_when)) {
                            $isRequired = true;
                        } else if($v->is_required && !empty($v->required_when) && in_array(@$check->facility_speciality_type, explode(',',$v->required_when))) {
                            $isRequired = true;
                        } 
                        if($isRequired) {
                            $name = str_replace(' ', '-', strtolower($v->name));
                            $checklicences = $check->services()->where(['service_id' => $value->id, 'sub_service_id' => $v->id])->first();
                            $rules[$value->id.'_'.$v->id.'_'.$name] = 'required';
                            if($request->{$value->id.'_'.$v->id.'_'.$name} == 1) {                               
                                $rules[$value->id.'_'.$v->id.'_'.$name.'_text'] = 'sometimes|required';
                                $rules[$value->id.'_'.$v->id.'_'.$name.'_image'] = $checklicences ? 'nullable|mimes:jpg,png,jpeg|max:2048' : 'sometimes|required|mimes:jpg,png,jpeg|max:2048';
                            }   
                           
                
                            $messages[$value->id.'_'.$v->id.'_'.$name] = 'This field is Required';
                            if($request->{$value->id.'_'.$v->id.'_'.$name} == 1) {
                                $messages[$value->id.'_'.$v->id.'_'.$name.'_text'] = 'This field is Required';
                                $messages[$value->id.'_'.$v->id.'_'.$name.'_image'] = 'This field is Required';
                                $messages[$value->id . '_' . $v->id . '_' . $name . '_image.mimes'] = 'Only JPG, PNG, and JPEG files are allowed.';
                                $messages[$value->id . '_' . $v->id . '_' . $name . '_image.max'] = 'The image size must not exceed 2MB.';
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
                                'service_value' => $request->{$value->id.'_'.$v->id.'_'.$name},
                                'text_value' => $request->{$value->id.'_'.$v->id.'_'.$name.'_text'},
                                'remark' => $request->{$value->id.'_'.$v->id.'_remark'}
                            ];

                            if ($request->hasFile($value->id.'_'.$v->id.'_'.$name.'_image')) {
                                $filePath = $request->file($value->id.'_'.$v->id.'_'.$name.'_image')->store('serviceimage', 'public'); 
                                $array['image'] = $filePath; // Add file path to data
                            }                  
    
                            $existservice = $check->services()->where(['service_id' => $value->id, 'sub_service_id' => $v->id])->first();
                            if($existservice->service_value != $request->{$value->id.'_'.$v->id.'_'.$name} || $existservice->text_value != $request->{$value->id.'_'.$v->id.'_'.$name.'_text'} || $existservice->remark != $request->{$value->id.'_'.$v->id.'_remark'} || $request->hasFile($value->id.'_'.$v->id.'_'.$name.'_image')) {
                                $existservice->dec_verify_status = null;
                                $existservice->dec_verify_service_value = '';
                                $existservice->dec_verify_text_value = '';
                                $existservice->dec_verify_image = '';
                                $existservice->dec_verify_remark = '';
                                $existservice->dec_verify_id = null;
                                $existservice->dec_status = null;
                                $existservice->dec_remark = '';
                                $existservice->dec_id = null;
                                $existservice->sec_status = null;
                                $existservice->sec_remark = '';
                                $existservice->sec_id = null;
                                $existservice->save();
                            }

                            $check->services()->updateOrCreate(['service_id' => $value->id, 'sub_service_id' => $v->id], $array);
                            // $check->services()->create($array);
                        }                        
                    }
                }
            }
            if($isValid) {
                $check->total_no_of_beds = $request->total_no_of_beds;
                $check->step = 6;
                $check->save();

                $hospital = Hospitals::where('id', $hospital_id)->first();
                $hospital->status = "Draft";
                $hospital->is_upgrade_application = 1;
                $hospital->save();

                $hospital->upgradeHospital()->updateOrCreate([
                    'hospital_id' => $hospital_id,
                ],[
                    'hospital_id' => $hospital_id,
                    'services' => 1
                ]);

                return response()->json(['success' => true, 'message' => 'Services Update Successfully!!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Please Select Any One.']);
            }           
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function upgradeLicenses(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            // $check->licenses()->delete();
            $licenses =  Helpers::getCommanData('Licenses');
            $rules = [];
            $messages = [];
            foreach ($licenses as $key => $value) {
                foreach ($value->licenseType as $k => $v) {
                    if($v->is_required) {
                        $checklicences = $check->licenses()->where(['license_id' => $value->id, 'license_type_id' => $v->id])->first();
                        $rules[$value->id . '_' . $v->id . '_dateissue'] = 'required|date';
                        $rules[$value->id . '_' . $v->id . '_dateexpiry'] = 'required|date';
                        $rules['document_' . $value->id . '_' . $v->id] = $checklicences ? 'nullable|mimes:pdf|max:10240' : 'required|mimes:pdf|max:10240';
            
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
                        
                        $existData = Helpers::getUSingleLicense($hospital_id, $value->id, $v->id);
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
                            
                            if($existData->issue_date != $issueDate || $existData->expiry_date != $expiryDate || $existData->remark != $request->{$value->id.'_'.$v->id.'_remark'} || $request->hasFile('document_' . $value->id . '_' . $v->id)) {
                                $array['dec_verify_status'] = null;
                                $array['dec_verify_remark'] = '';
                                $array['dec_verify_id'] = null;
                                $array['dec_status'] = null;
                                $array['dec_remark'] = '';
                                $array['dec_id'] = null;
                                $array['sec_status'] = null;
                                $array['sec_remark'] = '';
                                $array['sec_id'] = null;
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


            $check->step = 7;
            $check->save();

            $hospital = Hospitals::where('id', $hospital_id)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospital_id,
            ],[
                'hospital_id' => $hospital_id,
                'statutory_licences' => 1
            ]);

            return response()->json(['success' => true, 'message' => 'Licenses Update Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function upgradeCEO(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $validatedData = $request->validate([
                'name' => 'required',
                'designation' => 'required',
                'email' => 'required|email',
                'mobile_no' => 'required|digits:10',
            ]);
            if($check->ceo()->exists()) {
                $ceo = $check->ceo;
                $ceo->name = $request->name;
                $ceo->mobile_no = $request->mobile_no;
                $ceo->email = $request->email;
                $ceo->designation = $request->designation;
                $ceo->dec_verify_status = null;
                $ceo->dec_verify_remark = '';
                $ceo->dec_verify_id = null;
                $ceo->dec_status = null;
                $ceo->dec_remark = '';
                $ceo->dec_id = null;
                $ceo->sec_status = null;
                $ceo->sec_remark = '';
                $ceo->sec_id = null;
                $ceo->save();
            } else {
                $array = [
                    'uuid' => Helpers::generateUUID(),
                    'name' => $request->name,
                    'designation' => $request->designation,
                    'email' => $request->email,
                    'mobile_no' => $request->mobile_no,
                    'email_otp' => $request->email_otp,
                    'mobile_otp' => $request->mobile_otp,
                    'is_detail_added' => 1
                ];
    
                $check->ceo()->create($array);
            }  

            $hospital = Hospitals::where('id', $hospital_id)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospital_id,
            ],[
                'hospital_id' => $hospital_id,
                'human_resources' => 1
            ]);       

            return response()->json(['success' => true, 'message' => 'Ceo Saved Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function loadUHrTable(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $type = $request->json('type');
            $hrdata = $check->humanResources()->where('type', $type)->get();
            return view('hospital.upgrade._partials.humanresource.tables.hrtable', compact('uuid', 'hospital_id', 'hrdata'));
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function loadUHRSingleData(Request $request) {
        $id = $request->json('id');
        $hospital_id = $request->json('hospital_id');
        $hospital = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($hospital) {
            $type = $request->json('type');
            $mhr = HumanResource::where('type_slug', $type)->get();
            $hrdata = $hospital->humanResources()->where('type', $type)->where('id', $id)->first();
            $uuid = $hospital->uuid;
            return view('hospital.upgrade._partials.humanresource.tables.updateform', compact('uuid', 'hospital_id', 'hospital', 'hrdata', 'mhr'));
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function deleteUHR(Request $request) {
        $validatedData = $request->validate([
            'id' => 'required',
        ]);
        
        if(UHospitalHumanResource::where('id', $request->id)->exists()) {
            $isdata = 0;
            UHospitalHumanResource::where('id', $request->id)->delete();
            $checkisMore = UHospitalHumanResource::where('main_hospitalid', $request->hospital_id)->where('type', $request->type)->get();
            if(sizeof($checkisMore) > 0) {
                $isdata = 1;
            } 
            
            return response()->json(['success' => true, 'message' => 'Humanresource Deletd SuccessFully!!', 'is_data' => $isdata]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found!!']);
        }
    }

    public function saveUHR(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        $mhr = HumanResource::where(['type_slug' => 'mhr', 'id' => $request->sub_type_of_human_resource])->first();
        if($mhr && $mhr->name == "Medical Superintendent" && !$request->id) {
            $email = [
                'required',
                'email',
                new UniqueAcrossTables('hospital_human_resources', 'users', 'email'),
            ];

            $mobile_no = [
                'required',
                'digits:10',
                new UniqueAcrossTables('hospital_human_resources', 'users', 'mobile_no'),
            ];
        } else {
            $email = [
                'required',
                'email',
                // new UniqueAcrossTables('', 'users', 'email'),
            ];

            $mobile_no = [
                'required',
                'digits:10'
                // new UniqueAcrossTables('', 'users', 'mobile_no'),
            ];
        }
        if($check) {
            $validatedData = $request->validate([
                'healthcare_proffessionals_registry_id' => 'required',
                'type_of_human_resource' => 'required',
                'sub_type_of_human_resource' => 'required',
                'registration_number' => 'required',
                'name' => 'required',
                'registration_certificate' => $request->id ? 'nullable|mimes:pdf|max:10240' : 'required|mimes:pdf|max:10240',
                'declaration_certificate' => $request->id ? 'nullable|mimes:pdf|max:10240' : 'required|mimes:pdf|max:10240',
                'type' => 'nullable',
                'email' => $email,
                'mobile_no' => $mobile_no,
            ]);

            $validatedData['uuid'] = Helpers::generateUUID();
            if ($request->hasFile('registration_certificate')) {
                $filePath = $request->file('registration_certificate')->store('certificate', 'public'); 
                $validatedData['registration_certificate'] = $filePath; // Add file path to data
            }

            if ($request->hasFile('declaration_certificate')) {
                $filePath = $request->file('declaration_certificate')->store('certificate', 'public'); 
                $validatedData['declaration_certificate'] = $filePath; // Add file path to data
            }

            if($request->id) {
                $validatedData['dec_verify_status'] = null;
                $validatedData['dec_verify_remark'] = '';
                $validatedData['dec_verify_id'] = null;
                $validatedData['dec_status'] = null;
                $validatedData['dec_remark'] = '';
                $validatedData['dec_id'] = null;
                $validatedData['sec_status'] = null;
                $validatedData['sec_remark'] = '';
                $validatedData['sec_id'] = null;
                $check->humanResources()->where('id', $request->id)->update($validatedData);
            } else {
                $validatedData['main_hospitalid'] = $hospital_id;
                $check->humanResources()->create($validatedData);
            }

            $hospital = Hospitals::where('id', $hospital_id)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospital_id,
            ],[
                'hospital_id' => $hospital_id,
                'human_resources' => 1
            ]);    

            return response()->json(['success' => true, 'message' => 'Human Resource Update Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }
    
    public function saveUNoNHR(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $validatedData = $request->validate([
                'house_keeping' => 'required',
                'medico_count' => 'required',
            ]);

            if($request->house_keeping != $check->house_keeping || $request->medico_count != $check->medico_count) {
                $hospital = Hospitals::where('id', $hospital_id)->first();
                $hospital->status = "Draft";
                $hospital->is_upgrade_application = 1;
                $hospital->save();
    
                $hospital->upgradeHospital()->updateOrCreate([
                    'hospital_id' => $hospital_id,
                ],[
                    'hospital_id' => $hospital_id,
                    'human_resources' => 1
                ]);  
            }

            $check->house_keeping = $request->house_keeping;
            $check->medico_count = $request->medico_count;
            $check->save();

              

            return response()->json(['success' => true, 'message' => 'NonMedical Resource Update Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveUHumanSpecialities(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $validatedData = $request->validate([
                'hpr_id' => 'required',
                'designation' => 'required',
                'speciality_id' => 'required',
                'employement_type' => 'required',
                'name' => 'required',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('hospital_teams', 'email')->ignore($request->id),
                ],
                'registration_certificate' => $request->id ? 'nullable|mimes:pdf|max:10240' : 'required|mimes:pdf|max:10240',
                'declaration_certificate' => $request->id ? 'nullable|mimes:pdf|max:10240' : 'required|mimes:pdf|max:10240',
                'registration_certificate_expiry' => 'required|date',
                // 'declaration_certificate_expiry' => 'required|date',
                'mobile' => [
                    'required',
                    'digits:10',
                    Rule::unique('hospital_teams', 'mobile')->ignore($request->id),
                ],
                'registration_no' => 'required'
            ]);
            $validatedData['uuid'] = Helpers::generateUUID();
            if ($request->hasFile('registration_certificate')) {
                $filePath = $request->file('registration_certificate')->store('certificate', 'public'); 
                $validatedData['registration_certificate'] = $filePath; // Add file path to data
            }

            if ($request->hasFile('declaration_certificate')) {
                $filePath = $request->file('declaration_certificate')->store('certificate', 'public'); 
                $validatedData['declaration_certificate'] = $filePath; // Add file path to data
            }

            if($request->id) {
                $validatedData['dec_verify_status'] = null;
                $validatedData['dec_verify_remark'] = '';
                $validatedData['dec_verify_id'] = null;
                $validatedData['dec_status'] = null;
                $validatedData['dec_remark'] = '';
                $validatedData['dec_id'] = null;
                $validatedData['sec_status'] = null;
                $validatedData['sec_remark'] = '';
                $validatedData['sec_id'] = null;
                $check->hospitalTeam()->where('id', $request->id)->update($validatedData);
            } else {
                $validatedData['main_hospitalid'] = $hospital_id;
                $check->hospitalTeam()->create($validatedData);
            }

            $hospital = Hospitals::where('id', $hospital_id)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospital_id,
            ],[
                'hospital_id' => $hospital_id,
                'human_resources' => 1
            ]);    

            return response()->json(['success' => true, 'message' => 'Specialities Saved Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }


    public function loadUSpecialitiesTable(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $specialitiesData = $check->hospitalTeam;
            return view('hospital.upgrade._partials.humanresource.tables.specialities', compact('uuid', 'hospital_id', 'specialitiesData'));
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function loadUSpecialitiesSingleData(Request $request) {
        $id = $request->json('id');
        $hospital_id = $request->json('hospital_id');
        $hospital = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($hospital) {
            $specialities = $hospital->specialities()->where('offered',1)->get();
            $hrdata = $hospital->hospitalTeam()->where('id', $id)->first();
            $uuid = $hospital->uuid;
            return view('hospital.upgrade._partials.humanresource.tables.updatespecform', compact('uuid', 'hospital_id', 'hospital', 'hrdata', 'specialities'));
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function deleteUSpecialitiesHR(Request $request) {
        $validatedData = $request->validate([
            'id' => 'required',
        ]);
        
        if(UHospitalTeam::where('id', $request->id)->exists()) {
            $isdata = 0;
            UHospitalTeam::where('id', $request->id)->delete();
            $checkisMore = UHospitalTeam::where('main_hospitalid', $request->hospital_id)->get();
            if(sizeof($checkisMore) > 0) {
                $isdata = 1;
            }

            return response()->json(['success' => true, 'message' => 'Specialities Deletd SuccessFully!!', 'is_data' => $isdata]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found!!']);
        }
    }

    public function UaccreditationForm(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $hospital_accreditation = @$check->hospitalAccreditation;
            $validatedData = $request->validate([
                'accreditation' => 'required',
                'accreditation_id' => 'required_if:accreditation,Yes',
                'certificate_no' => 'required_if:accreditation,Yes',
                'valid_from' => 'required_if:accreditation,Yes|date',
                'valid_till' => 'required_if:accreditation,Yes|date',
                'certificate' => (@$hospital_accreditation->certificate || (@$hospital_accreditation->accreditation =='No' && $request->accreditation =='No')) ? 'nullable' : 'required_if:accreditation,Yes',
                'speciality_ids' => 'required_if:accreditation,Yes|array',
            ]);
            $certificate=$hospital_accreditation ? $hospital_accreditation->certificate : '';
            if ($request->hasFile('certificate')) {
                $filePath = $request->file('certificate')->store('certificate', 'public');
                $certificate = $filePath;
            }
            $array = [
                'uuid' => Helpers::generateUUID(),
                'accreditation' => $request->accreditation,
                'accreditation_id' => $request->accreditation_id,
                'certificate_no' => $request->certificate_no,
                'valid_from' => $request->valid_from,
                'valid_till' => $request->valid_till,
                'certificate' => $certificate,
                'speciality_ids' => json_encode($request->speciality_ids??[])
            ];

            $array['dec_verify_status'] = null;
            $array['dec_verify_remark'] = '';
            $array['dec_verify_id'] = null;
            $array['dec_status'] = null;
            $array['dec_remark'] = '';
            $array['dec_id'] = null;
            $array['sec_status'] = null;
            $array['sec_remark'] = '';
            $array['sec_id'] = null;
            
            $check->hospitalAccreditation()->updateOrCreate(
                ['hospital_id' => $check->id], // Conditions to find the record
                $array // Values to update or create
            );

            $hospital = Hospitals::where('id', $hospital_id)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospital_id,
            ],[
                'hospital_id' => $hospital_id,
                'quality_accreditation' => 1
            ]);

            return response()->json(['success' => true, 'message' => 'Quality & Accrediation Update Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function UfinancialForm(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $financial_information = @$check->financialInformation;
            $validatedData = $request->validate([
                'account_holder' => 'required',
                'account_no' => 'required|confirmed|digits_between:9,18',
                'ifsc_code' => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
                'bank_name' => 'required',
                'bank_branch_name' => 'required',
                'bank_address' => 'required',
                'micr' => 'required|digits:9',
                'account_type' => 'required',
                'authorised_signatory_name' => 'required',
                'bank_email' => 'required|email',
                'neft_enabled' => 'required',
                'bsr_code' => 'required|digits:7',
                'cancelled_cheque' => $financial_information ? 'nullable' : 'required',
            ]);
            $cancelled_cheque=$financial_information ? $financial_information->cancelled_cheque : '';
            if ($request->hasFile('cancelled_cheque')) {
                $filePath = $request->file('cancelled_cheque')->store('cancelled_cheque', 'public');
                $cancelled_cheque = $filePath;
            }
            $array = [
                'uuid' => Helpers::generateUUID(),
                'account_holder' => $request->account_holder,
                'account_no' => $request->account_no,
                'ifsc_code' => $request->ifsc_code,
                'bank_name' => $request->bank_name,
                'bank_branch_name' => $request->bank_branch_name,
                'bank_address' => $request->bank_address,
                'micr' => $request->micr,
                'account_type' => $request->account_type,
                'authorised_signatory_name' => $request->authorised_signatory_name,
                'bank_email' => $request->bank_email,
                'neft_enabled' => $request->neft_enabled,
                'bsr_code' => $request->bsr_code,
                'cancelled_cheque' => $cancelled_cheque,
            ];
            
            $array['dec_verify_status'] = null;
            $array['dec_verify_remark'] = '';
            $array['dec_verify_id'] = null;
            $array['dec_status'] = null;
            $array['dec_remark'] = '';
            $array['dec_id'] = null;
            $array['sec_status'] = null;
            $array['sec_remark'] = '';
            $array['sec_id'] = null;

            $check->financialInformation()->updateOrCreate(
                ['hospital_id' => $check->id],
                $array
            );

            $hospital = Hospitals::where('id', $hospital_id)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospital_id,
            ],[
                'hospital_id' => $hospital_id,
                'financial_information' => 1
            ]);

            return response()->json(['success' => true, 'message' => 'Bank Details Update Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }
    public function UtaxdetailsForm(Request $request, $uuid, $hospital_id) {
        $check = UHospitals::where('main_hospitalid', $hospital_id)->first();
        if($check) {
            $taxDetails = @$check->taxDetails;

            $validatedData = $request->validate([
                'pan_no' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                'pan_name' => 'required',
                'pan_certificate' => $taxDetails ? 'nullable' : 'required|file|mimes:pdf|max:10240',
                'tan_no' => 'required|regex:/^[A-Z]{4}[0-9]{5}[A-Z]{1}$/',
                'tan_holder_name' => 'required',
                'gst_no' => 'nullable|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}[Z]{1}[A-Z0-9]{1}$/',
                'gst_name' => 'nullable',
                'gst_certificate' => $taxDetails ? 'nullable' : 'nullable|required_if:gst_no,!null|file|mimes:pdf|max:10240',
                'tds_exemption' => 'required',
                'tds_exemption_id' => 'required_if:tds_exemption,Yes',
                'tds_rate' => 'required_if:tds_exemption,Yes',
                'after_tds_rate' => 'required_if:tds_exemption,Yes',
                'tds_exemption_certificate_no' => 'required_if:tds_exemption,Yes',
                'tds_exemption_certificate' => $taxDetails ? 'nullable' : 'required_if:tds_exemption,Yes|file|mimes:pdf|max:10240',
                'tds_exemption_valid_from' => 'required_if:tds_exemption,Yes',
                'tds_exemption_valid_till' => 'required_if:tds_exemption,Yes',
                'tds_exemption_amount' => 'required_if:tds_exemption,Yes',
            ]);
            $array = [
                'uuid' => Helpers::generateUUID(),
                'pan_no' => $request->pan_no,
                'pan_name' => $request->pan_name,
                'tan_no' => $request->tan_no,
                'tan_holder_name' => $request->tan_holder_name,
                'gst_no' => $request->gst_no,
                'gst_name' => $request->gst_name,
                'tds_exemption' => $request->tds_exemption,
                'tds_exemption_id' => $request->tds_exemption_id,
                'tds_rate' => $request->tds_rate,
                'after_tds_rate' => $request->after_tds_rate,
                'tds_exemption_certificate_no' => $request->tds_exemption_certificate_no,
                'tds_exemption_valid_from' => $request->tds_exemption_valid_from,
                'tds_exemption_valid_till' => $request->tds_exemption_valid_till,
                'tds_exemption_amount' => $request->tds_exemption_amount,
            ];

            if ($request->hasFile('pan_certificate')) {
                $filePath = $request->file('pan_certificate')->store('certificate', 'public');
                $array['pan_certificate'] = $filePath;
            }

            if ($request->hasFile('gst_certificate')) {
                $filePath = $request->file('gst_certificate')->store('certificate', 'public');
                $array['gst_certificate'] = $filePath;

            }

            if ($request->hasFile('tds_exemption_certificate')) {
                $filePath = $request->file('tds_exemption_certificate')->store('certificate', 'public');
                $array['tds_exemption_certificate'] = $filePath;
            }
            
            $array['dec_verify_status'] = null;
            $array['dec_verify_remark'] = '';
            $array['dec_verify_id'] = null;
            $array['dec_status'] = null;
            $array['dec_remark'] = '';
            $array['dec_id'] = null;
            $array['sec_status'] = null;
            $array['sec_remark'] = '';
            $array['sec_id'] = null;

            $check->taxDetails()->updateOrCreate(
                ['hospital_id' => $check->id],
                $array
            );

            $hospital = Hospitals::where('id', $hospital_id)->first();
            $hospital->status = "Draft";
            $hospital->is_upgrade_application = 1;
            $hospital->save();

            $hospital->upgradeHospital()->updateOrCreate([
                'hospital_id' => $hospital_id,
            ],[
                'hospital_id' => $hospital_id,
                'tax_details' => 1
            ]);
            return response()->json(['success' => true, 'message' => 'Tax Details Update Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function hospitalReSubmit(Request $request, $uuid, $hospital_id) {
        $hospital = Hospitals::where('id', $hospital_id)->first();
        $hospital->status = 'Upgradation Request';
        $hospital->is_declaration = 1;
        $hospital->status_update_date = date('Y-m-d H:i:s');
        $hospital->save();
        
        $uhosp = $hospital->upgradeHospital;

        $UHospital = UHospitals::where('main_hospitalid', $hospital_id)->first();
        $UHospital->status = 'Upgradation Request';
        $UHospital->is_declaration = 1;
        $UHospital->status_update_date = date('Y-m-d H:i:s');
        $UHospital->save();
        
        $hospital->hospitalReport()->update(
        [
            'document_type' => null,
            'document' => null,
            'description' => null,
            'remark' => null,
            'latitude' => null,
            'longitude' => null,
            'verifier_id' => null,
            'dec_action' => null,
            'dec_document' => null,
            'dec_remarks' => null,
            'dec_verifier_id' => null,
            'sec_action' => null,
            'sec_document' => null,
            'sec_remarks' => null,
            'sec_verifier_id' => null,
        ]);

        $updates = [
            'establishment_details' => ['tab' => 1, 'type' => 'establishment_details'],
            'address' => ['tab' => 1, 'type' => 'address'],
            'scheme' => ['tab' => 1, 'type' => 'establishment_details'],
            'speciality' => ['tab' => 2],
            'services' => ['tab' => 3],
            'statutory_licences' => ['tab' => 4],
            'human_resources' => ['tab' => 5],
            'quality_accreditation' => ['tab' => 6],
            'financial_information' => ['tab' => 7, 'type' => 'finance_details'],
            'tax_details' => ['tab' => 7, 'type' => 'tax_details']
        ];

        foreach ($updates as $key => $update) {
            if (!empty($uhosp->$key)) {
                TabStatus::where(array_merge(['hospital_id' => $hospital_id, 'tab' => $update['tab']], 
                    isset($update['type']) ? ['type' => $update['type']] : []
                ))->update(['is_verifier' => 0, 'is_dec' => 0, 'is_sec' => 0]);
                // break; // Exit loop after the first matching condition
            }
        }

        $url = route('hospital.dashboard');
        return response()->json(['success' => true, 'message' => $hospital->hospital_id.' Hospital Upgradation Request Successfully!!', 'url' => $url]);
    }
}
 