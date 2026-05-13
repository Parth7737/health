<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserHfr;
use App\Models\{ Hospitals, FacilityOwnershipSubType, HospitalDistrict, HospitalState, HospitalHumanResource, HumanResource, HospitalTeam, Village, HospitalSpeciality, FacilityOwnershipType, WorkFlowHistory, User, UHospitals, Speciality, ExistsHospital, Block};
use App\CentralLogics\Helpers;
use App\Models\MobileOtp;
use App\Rules\UniqueAcrossTables;
use App\Mail\StatusMail;
use Mail;
use DataTables;
use Validator;

class EmpanelmentRegistrationController extends Controller
{
    public function create(Request $request) {  
        return view('hospital.empanelment.registration');
    }

    public function addHfrId(Request $request) {
        $validatedData = $request->validate([
            'hfr_id' => 'nullable|string|size:12',
            'mobile_no' => 'nullable|size:10',
        ]);

        if (!empty(auth()->user()->hospital_id)) {
            return response()->json([
                'success' => false, 
                'message' => "You have already added a hospital (".auth()->user()->hospital->facility_name."). Adding a second hospital is not allowed."
            ]);
        }

        if($request->hfr_id) {
            $checkHFR = UserHfr::where([
                'user_id' => auth()->user()->id, // Matching condition
                'hfr_id' => $request->hfr_id     // Matching condition
            ])->first();
            if(!$checkHFR) {
                $checkHFR = UserHfr::create([
                    'user_id' => auth()->user()->id, // Matching condition
                    'hfr_id' => $request->hfr_id ,
                    'hospital_uuid_id' => Helpers::generateUUID()  // Matching condition
                ]);
            } else {
                if($checkHFR->hospital_uuid_id == "") {
                    $checkHFR->hospital_uuid_id = Helpers::generateUUID();
                    $checkHFR->save();
                }
            }
        }

        if($request->mobile_no) {
            $checkHFR = UserHfr::where([
                'user_id' => auth()->user()->id, // Matching condition
                'mobile_no' => $request->mobile_no     // Matching condition
            ])->first();
            if(!$checkHFR) {
                $checkHFR = UserHfr::create([
                    'user_id' => auth()->user()->id, // Matching condition
                    'mobile_no' => $request->mobile_no ,
                    'hospital_uuid_id' => Helpers::generateUUID()  // Matching condition
                ]);
            } else {
                if($checkHFR->hospital_uuid_id == "") {
                    $checkHFR->hospital_uuid_id = Helpers::generateUUID();
                    $checkHFR->save();
                }
            }
        }
        // $checkHFR = UserHfr::updateOrCreate(
        //     [
        //         'user_id' => auth()->user()->id, // Matching condition
        //         'hfr_id' => $request->hfr_id     // Matching condition
        //     ],
        //     [
        //         'user_id' => auth()->user()->id, // Matching condition
        //         'hfr_id' => $request->hfr_id ,
        //         'hospital_uuid_id' => Helpers::generateUUID()  // Matching condition
        //     ]
        // );
        // session(['IDENTITY' => base64_encode($request->hfr_id)]);

        $url = route('hospital.empanelmentRegistration.establismentDetails',  base64_encode($checkHFR->hospital_uuid_id));

        return response()->json(['success' => true, 'message' => 'VERIFIED SUCCESSFULLY!!', 'data' => auth()->user(),'url' => $url]); 
    }

    public function establismentDetails($uuid) {
        $facilityTypes = Helpers::getCommanData('FacilityType');
        $FacilityOwnershipType = Helpers::getCommanData('FacilityOwnershipType');
        $FacilityRegistrationCertificate = Helpers::getCommanData('FacilityRegistrationCertificate');
        $FacilitySpecialityType = Helpers::getCommanData('FacilitySpecialityType');
        $GovermentBenefits = Helpers::getCommanData('GovermentBenefits');
        $SystemMedicine = Helpers::getCommanData('SystemMedicine');
        $state = HospitalState::where('country_id', 101)->get();
        $hospitalDetail = Hospitals::with('hospitalAddress')->where('uuid', base64_decode($uuid))->first();
        $address = !empty($hospitalDetail) ? $hospitalDetail->hospitalAddress : '';

        if(!empty($hospitalDetail) && !empty($address)) {
            return redirect()->route('hospital.empanelmentRegistration.schemeDetails', $uuid);
        }
        $hfrdata = UserHfr::where('hospital_uuid_id', base64_decode($uuid))->first();

        return view('hospital.empanelment.create', compact('facilityTypes', 'FacilityOwnershipType', 'FacilityRegistrationCertificate', 'FacilitySpecialityType', 'GovermentBenefits', 'SystemMedicine', 'uuid', 'hospitalDetail', 'address', 'state', 'hfrdata'));
    }

    public function hospitalsCreate(Request $request, $uuid) {

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
        $checkHFR = UserHfr::where('hospital_uuid_id', $uuid)->first();

        $check = Hospitals::where(['user_id' => auth()->user()->id, 'hfr_id' => ($checkHFR->hfr_id) ? $checkHFR->hfr_id : $checkHFR->mobile_no, 'uuid' => $uuid])->first();

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
        
     
       
        if(!$checkHFR) {
            return response()->json(['success' => false, 'message' => 'Wrong data submitted!', 'uuid' => $uuid]); 
        }

        if($request->facility_ownership_sub_type3text && $request->facility_ownership_sub_type3 == "") {
            $validatedData['facility_ownership_sub_type3'] = $request->facility_ownership_sub_type3text;
            unset($validatedData['facility_ownership_sub_type3text']);
        }
        if($request->facility_ownership_sub_type3text == "" && $request->facility_ownership_sub_type3) {
            $validatedData['facility_ownership_sub_type3'] = $request->facility_ownership_sub_type3;
            unset($validatedData['facility_ownership_sub_type3text']);
        }
        $checkHFR->mobile_no;
        $validatedData['hfr_id'] = !empty($checkHFR->hfr_id) ? $checkHFR->hfr_id : $checkHFR->mobile_no;
        $validatedData['uuid'] = $uuid;
        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['is_added'] = 1;
        $validatedData['status_update_date'] = date('Y-m-d H:i:s');
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

        $codename = FacilityOwnershipType::where('id', $request->facility_ownership_type)->first();
        if($codename)
        {
            $hospitalid = Helpers::generateHospitalId($codename->name);
        } else {
            $hospitalid = '';
        }

        $id = Hospitals::updateOrCreate(
            [
                'user_id' => auth()->user()->id, // Matching condition
                'hfr_id' => !empty($checkHFR->hfr_id) ? $checkHFR->hfr_id : $checkHFR->mobile_no,
                'uuid' => $uuid     // Matching condition
            ],
            $validatedData
        );

        $lastInsertId = $id->id;
        $hospital = Hospitals::where('id', $lastInsertId)->first();
        if($hospital) {
            if($hospital->hospital_id == "") {
                $hospital->hospital_id = $hospitalid;
                $hospital->save();
            }
        }
    
        if (auth()->check()) {
            auth()->user()->update(['hospital_id' => $lastInsertId]);
        }

        return response()->json(['success' => true, 'message' => 'Establishment Details Save Successfully!!', 'data' => auth()->user(), 'id' => $id]); 
    }

    public function facility_ownership_sub_type(Request $request) {
        $typeId = $request->facility_ownership_type_id;
        $facilitySubType = FacilityOwnershipSubType::where('facility_ownership_type_id', $typeId)->where('type', 0)->get();
        $facilitySubType2 = FacilityOwnershipSubType::where('facility_ownership_type_id', $typeId)->where('type', 1)->get();

        return response()->json(['type1' => $facilitySubType, 'type2' => $facilitySubType2]);
    }

    public function facility_ownership_sub_type2(Request $request) {
        $typeId = $request->facility_ownership_type_id;
        $type1 = $request->type1;
        $facilitySubType2 = FacilityOwnershipSubType::where('facility_ownership_type_id', $typeId)->where('type_id', $type1)->where('type', 1)->get();
        if(sizeof($facilitySubType2) <= 0) {
            $facilitySubType2 = FacilityOwnershipSubType::where('facility_ownership_type_id', $typeId)->where('type', 1)->get();
        }
        return response()->json($facilitySubType2);
    }

    public function facility_ownership_sub_type3(Request $request) {
        $typeId = $request->facility_ownership_type_id;
        $type1 = $request->type1;
        $type2 = $request->type2;
        $facilitySubType2 = FacilityOwnershipSubType::where('facility_ownership_type_id', $typeId)->where('type_id', $type1)->where('type2_id', $type2)->where('type', 2)->get();
        return response()->json($facilitySubType2);
    }

    public function getDistrict(Request $request) {
        $stateId = $request->state_id;
        $data = HospitalDistrict::where('state_id', $stateId)->get();
        return response()->json($data);
    }

    public function getBlocks(Request $request) {
        $district_id = $request->district_id;
        $data = Block::where('district_id', $district_id)->get();
        return response()->json($data);
    }

    public function getVillage(Request $request) {        
        $district_id = $request->district_id;
        $data = Village::where('district_id', $district_id)->where('block_id', $request->block_id)->get();
        return response()->json($data);
    }

    public function hospitalsAddressCreate(Request $request, $uuid) {

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
            'email' => 'required|email|unique:hospital_addresses,email',
            'website' => 'nullable',
            'police_station' => 'required',
            'locality' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        
        $uuid = base64_decode($uuid);
        $checkHFR = Hospitals::where('uuid', $uuid)->first();
        if(!$checkHFR) {
            return response()->json(['success' => false, 'message' => 'Enter First Establishment Details', 'uuid' => $uuid]); 
        }

        $validatedData['is_added'] = 1;
        $validatedData['uuid'] = Helpers::generateUUID();
        $validatedData['hospital_id'] = $checkHFR->id;
        $validatedData['step'] = 1;

        $checkHFR->hospitalAddress()->updateOrCreate(
            ['hospital_id' => $checkHFR->id], // Match condition
            $validatedData // Data to update or insert
        );
      
        return response()->json(['success' => true, 'message' => 'Address Details Save Successfully!!',]); 
    }

    public function sendOTPOnMobile(Request $request) {
       
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'digits:10']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }
            
        if(UserHfr::where('mobile_no', $request->mobile)->exists()) {
            return response()->json(['success' => false, 'message' => 'MobileNo is already registered in our system']);
        }
        $otp = rand(000000, 999999);
        $data = MobileOtp::where(['mobile_no' => $request->mobile, 'status' => 1])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 1;
            $data->save();
        } else {
            $data = MobileOtp::create([ 'mobile_no' => $request->mobile, 'otp' => $otp, 'status' => 1]);
        }
        return response()->json(['success' => true, 'message' => 'Otp sent in your Mobile No','otp'=>$otp]);
    }
    public function reSendOTPOnMobile(Request $request) {

        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'digits:10']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        if(UserHfr::where('mobile_no', $request->mobile)->exists()) {
            return response()->json(['success' => false, 'message' => 'MobileNo is already registered in our system']);
        }
        
        $otp = rand(000000, 999999);
        $data = MobileOtp::where(['mobile_no' => $request->mobile, 'status' => 1])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 1;
            $data->save();
        } else {
            return response()->json(['success' => false, 'message' => 'Mobile no Not found',]);
        }

        return response()->json(['success' => true, 'message' => 'Otp re-sent successfully!!','otp'=>$otp]);

    }

    public function VerifyOtp(Request $request) {
        $otp = $request->otp;
        $mobile_no = $request->mobile_no;
        $check = MobileOtp::where(['mobile_no' => $mobile_no, 'otp' => $otp, 'status' => 1])->first();
        if($check) {
            $check->delete();
            return response()->json(['success' => true, 'message' => 'Otp Verify successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Otp is Incorrect!!']);
        }
    }

    public function schemeDetails(Request $request, $uuid) {
        $hospital = Hospitals::with('hospitalAddress')->where('uuid', base64_decode($uuid))->first();

        $code = $hospital->facilityOwnershipType->name;

        $completedStep = $this->checkstepComplete($hospital->id);
        $allStepComplete = $this->checkAllStepIsCompleteOrNot($hospital->id);
        $step = 1;
        if($completedStep['financial_informationstep'] && $completedStep['taxdetailsstep'] && $completedStep['accreditationtstep'] && $completedStep['medicalstep'] && $completedStep['servicestep'] && $completedStep['specialiststep'] && $hospital->licenses()->count() > 0 && $hospital->services()->count() > 0 && $hospital->specialities()->count() > 0 && $hospital->scheme != "" && $hospital->image != '') {
            $step = 8;
        } else if($completedStep['accreditationtstep'] && $completedStep['medicalstep'] && $completedStep['servicestep'] && $completedStep['specialiststep'] && $hospital->licenses()->count() > 0 && $hospital->services()->count() > 0 && $hospital->specialities()->count() > 0 && $hospital->scheme != "" && $hospital->image != '') {
            $step = 7;
        } else if($completedStep['medicalstep'] && $completedStep['servicestep'] && $completedStep['specialiststep'] && $hospital->licenses()->count() > 0 && $hospital->services()->count() > 0 && $hospital->specialities()->count() > 0 && $hospital->scheme != "" && $hospital->image != '') {
            $step = 6;
        } else if ($hospital->licenses()->count() > 0 && $hospital->services()->count() > 0 && $hospital->specialities()->count() > 0 && $hospital->scheme != "" && $hospital->image != '') {
            $step = 5;
        } else if ($hospital->services()->count() > 0 && $hospital->specialities()->count() > 0 && $hospital->scheme != "" && $hospital->image != '') {
            $step = 4;
        } else if ($hospital->specialities()->count() > 0 && $hospital->scheme != "" && $hospital->image != '') {
            $step = 3;
        } else if($hospital->scheme != "" && $hospital->image != '') {
            $step = 2;
        }

        // if($code) {
        //     $hospitalid = Helpers::generateHospitalId($code);
        //     $hospital->hospital_id = $hospitalid;
        // }

        $hospital->step = $step;
        $hospital->save();
        return view('hospital.empanelment.form', compact('hospital', 'uuid', 'step', 'completedStep', 'allStepComplete'));
    }

    public function stepLoad(Request $request, $uuid, $hospital_id) {
        $validatedData = $request->validate([
            'step' => 'required',
        ]);

        $hospital = Hospitals::where('id', $hospital_id)->first();

        if($request->step == 1) {
            $schemes =  Helpers::getCommanData('SchemeType');
            return view('hospital.empanelment._partials.scheme', compact('schemes','uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 2) {
            $specialities =  Speciality::orderBy('name', 'ASC')->get();
            return view('hospital.empanelment._partials.speciality', compact('specialities','uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 3) {
            $services =  Helpers::getCommanData('Service');
            return view('hospital.empanelment._partials.services', compact('services', 'uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 4) {
            $licenses =  Helpers::getCommanData('Licenses');
            return view('hospital.empanelment._partials.licenses', compact('licenses', 'uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 5) {
            $mhr = HumanResource::where('type_slug', 'mhr')->get();
            $sshr = HumanResource::where('type_slug', 'sshr')->get();
            $checkstepComplete = $this->checkstepComplete($hospital_id);
            $specialities = $hospital->specialities()->where('offered',1)->get();
            return view('hospital.empanelment._partials.humanresources', compact('uuid', 'hospital_id', 'hospital', 'mhr', 'sshr', 'checkstepComplete', 'specialities'));
        } else if($request->step == 6) {
            return view('hospital.empanelment._partials.accreditation', compact('uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 7) {
            $checkstepComplete = $this->checkstepComplete($hospital_id);
            return view('hospital.empanelment._partials.finance', compact('uuid', 'hospital_id', 'hospital','checkstepComplete'));
        } else if($request->step == 8) {
            $allStepCompleted = $this->checkAllStepIsCompleteOrNot($hospital_id);

            return view('hospital.empanelment._partials.documents', compact('uuid', 'hospital_id', 'hospital', 'allStepCompleted'));
        }
    }

    public function saveScheme(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();

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

            $check->step = 2;
            $check->save();

            if ($request->hasFile('images')) {
                $check->images()->delete();
                foreach ($request->file('images') as $image) {
                    $filePath = $image->store('images', 'public'); 
                    $array['image'] = $filePath;
                    $check->images()->create($array);
                }
            }

            return response()->json(['success' => true, 'message' => 'Scheme Saved Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveSpecialities(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
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
                    // if($request->{'available_'.$value}) {
                    //     $isValid = 1;
                    //     $available = $request->{'available_'.$value};
                    //     $offered = $request->{'offered_'.$value};
                    //     $not_offered_reason = $request->{'not_offered_reason_'.$value};
                    //     $remark = $request->{'remark_'.$value};
                        
                    //     $array = [
                    //         'uuid' => Helpers::generateUUID(),
                    //         'speciality_id' => $value,
                    //         'available' => $available,
                    //         'offered' => $offered,
                    //         'not_offered_reason' => $not_offered_reason,
                    //         'remark' => $remark
                    //     ];
                    //     $check->specialities()->create($array);
                    // }

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
                    }
                }

                if($isValid) {
                    $check->step = 3;
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

    public function saveServices(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $services =  Helpers::getCommanData('Service');
            $rules = [];
            $messages = [];
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
    
                            $check->services()->updateOrCreate(['service_id' => $value->id, 'sub_service_id' => $v->id], $array);
                            // $check->services()->create($array);
                        }                        
                    }
                }
            }
            if($isValid) {
                $check->total_no_of_beds = $request->total_no_of_beds;
                $check->step = 4;
                $check->save();
                return response()->json(['success' => true, 'message' => 'Services Saved Successfully!!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Please Select Any One.']);
            }           
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveLicenses(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
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


            $check->step = 5;
            $check->save();
            return response()->json(['success' => true, 'message' => 'Licenses Saved Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveCEO(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $validatedData = $request->validate([
                'name' => 'required',
                'designation' => 'required',
                'email' => 'required',
                'mobile_no' => 'required',
            ]);
            if($check->ceo()->exists()) {
                $ceo = $check->ceo;
                $ceo->name = $request->name;
                $ceo->mobile_no = $request->mobile_no;
                $ceo->email = $request->email;
                $ceo->designation = $request->designation;
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

            $completedStep = $this->checkstepComplete($hospital_id);
            return response()->json(['success' => true, 'message' => 'Ceo Saved Successfully!!', 'completedStep' => $completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function verifyHPRId(Request $request, $uuid, $hospital_id) {
        $healthcare_proffessionals_registry_id = $request->healthcare_proffessionals_registry_id;
        if($healthcare_proffessionals_registry_id) {
            $check = Hospitals::where('id', $hospital_id)->first();
            if($check) {
                $data = $check->humanResources()->where('healthcare_proffessionals_registry_id', $request->healthcare_proffessionals_registry_id)->first();
                return response()->json(['success' => true, 'message' => 'HPR Id Verified Successfully!!', 'data' => $data]);   
            } else {
                return response()->json(['success' => false, 'message' => 'Please Enter HPR ID!!']);
            }           
        } else {
            return response()->json(['success' => false, 'message' => 'Please Enter HPR ID!!']);
        }
    }

    public function saveHR(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        $mhr = HumanResource::where(['type_slug' => 'mhr', 'id' => $request->sub_type_of_human_resource])->first();
        if($mhr && $mhr->name == "Medical Superintendent") {
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
                'registration_certificate' => 'required|mimes:pdf|max:10240',
                'declaration_certificate' => 'required|mimes:pdf|max:10240',
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

            $check->humanResources()->create($validatedData);
            $completedStep = $this->checkstepComplete($hospital_id);

            return response()->json(['success' => true, 'message' => 'Human Resource Saved Successfully!!', 'completedStep' => $completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function loadHrTable(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $type = $request->json('type');
            $hrdata = $check->humanResources()->where('type', $type)->get();
            return view('hospital.empanelment._partials.humanresource.tables.hrtable', compact('uuid', 'hospital_id', 'hrdata'));
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function deleteHR(Request $request) {
        $validatedData = $request->validate([
            'id' => 'required',
        ]);
        
        if(HospitalHumanResource::where('id', $request->id)->exists()) {
            $isdata = 0;
            HospitalHumanResource::where('id', $request->id)->delete();
            $checkisMore = HospitalHumanResource::where('hospital_id', $request->hospital_id)->where('type', $request->type)->get();
            if(sizeof($checkisMore) > 0) {
                $isdata = 1;
            } 
            $completedStep = $this->checkstepComplete($request->hospital_id);
            
            return response()->json(['success' => true, 'message' => 'Humanresource Deletd SuccessFully!!', 'is_data' => $isdata, 'completedStep' => $completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found!!']);
        }
    }

    public function saveNoNHR(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $validatedData = $request->validate([
                'house_keeping' => 'required',
                'medico_count' => 'required',
            ]);

            $check->house_keeping = $request->house_keeping;
            $check->medico_count = $request->medico_count;
            $check->save();
            $completedStep = $this->checkstepComplete($hospital_id);

            return response()->json(['success' => true, 'message' => 'NonMedical Resource Save Successfully!!', 'completedStep' => $completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }
     
    public function accreditationForm(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $hospital_accreditation = @$check->hospitalAccreditation;
            $validatedData = $request->validate([
                'accreditation' => 'required',
                'accreditation_id' => 'required_if:accreditation,Yes',
                'certificate_no' => 'required_if:accreditation,Yes',
                'valid_from' => 'required_if:accreditation,Yes|date',
                'valid_till' => 'required_if:accreditation,Yes|date',
                'certificate' => (@$hospital_accreditation->certificate || (@$hospital_accreditation->accreditation=='No' && $request->accreditation =='No')) ? 'nullable' : 'required_if:accreditation,Yes',
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
            
            $check->hospitalAccreditation()->updateOrCreate(
                ['hospital_id' => $check->id], // Conditions to find the record
                $array // Values to update or create
            );

            return response()->json(['success' => true, 'message' => 'Quality & Accrediation Saved Successfully!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function checkstepComplete($hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        $medicalForm = false;	
        $mhr = $check->humanResources()->where('type', 'mhr')->get();
        $sshr = $check->humanResources()->where('type', 'sshr')->get();
        $specialities =  $check->hospitalTeam()->get();
        $accreditation =  $check->hospitalAccreditation;
        $financial_information =  $check->financialInformation;
        $tax_details =  $check->taxDetails;

        $medicalstep = false;
        $servicestep = false;
        $specialiststep = false;
        $accreditationtstep = $accreditation?true:false;
        $financial_informationstep = $financial_information?true:false;
        $taxdetailsstep = $tax_details?true:false;
	    if($check->ceo && sizeof($mhr) > 0 && $check->medico_count && $check->house_keeping) {
            $medicalstep = true;
        }

        if(sizeof($sshr) > 0) {
            $servicestep = true;
        }

        if(sizeof($specialities) > 0) {
            $specialiststep = true;
        }

        if($medicalstep && $servicestep && $specialiststep) {
           $check->step = 6;
           $check->save(); 
        } else {
            $check->step = 5;
            $check->save();
        }
        return ['medicalstep' => $medicalstep, 'servicestep' => $servicestep, 'specialiststep' => $specialiststep, 'accreditationtstep' => $accreditationtstep, 'financial_informationstep' => $financial_informationstep, 'taxdetailsstep' => $taxdetailsstep];
    }

    public function saveHumanSpecialities(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $validatedData = $request->validate([
                'hpr_id' => 'required',
                'designation' => 'required',
                'speciality_id' => 'required',
                'employement_type' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:hospital_teams,email',
                'registration_certificate' => 'required|mimes:pdf|max:10240',
                'declaration_certificate' => 'required|mimes:pdf|max:10240',
                'registration_certificate_expiry' => 'required|date',
                // 'declaration_certificate_expiry' => 'required|date',
                'mobile' => 'required|unique:hospital_teams,mobile',
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

            $check->hospitalTeam()->create($validatedData);
            $completedStep = $this->checkstepComplete($hospital_id);

            return response()->json(['success' => true, 'message' => 'Specialities Saved Successfully!!', 'completedStep' => $completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }


    public function loadSpecialitiesTable(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $specialitiesData = $check->hospitalTeam;
            return view('hospital.empanelment._partials.humanresource.tables.specialities', compact('uuid', 'hospital_id', 'specialitiesData'));
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function deleteSpecialitiesHR(Request $request) {
        $validatedData = $request->validate([
            'id' => 'required',
        ]);
        
        if(HospitalTeam::where('id', $request->id)->exists()) {
            $isdata = 0;
            HospitalTeam::where('id', $request->id)->delete();
            $checkisMore = HospitalTeam::where('hospital_id', $request->hospital_id)->get();
            if(sizeof($checkisMore) > 0) {
                $isdata = 1;
            }

            $completedStep = $this->checkstepComplete($request->hospital_id);

            return response()->json(['success' => true, 'message' => 'Specialities Deletd SuccessFully!!', 'is_data' => $isdata, 'completedStep' => $completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found!!']);
        }
    }
    public function financialForm(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
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
            
            $check->financialInformation()->updateOrCreate(
                ['hospital_id' => $check->id],
                $array
            );

            $completedStep = $this->checkstepComplete($hospital_id);
            return response()->json(['success' => true, 'message' => 'Bank Details Saved Successfully!!','completedStep'=>$completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }
    public function taxdetailsForm(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
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
            
            $check->taxDetails()->updateOrCreate(
                ['hospital_id' => $check->id],
                $array
            );

            $completedStep = $this->checkstepComplete($hospital_id);
            return response()->json(['success' => true, 'message' => 'Tax Details Saved Successfully!!','completedStep'=>$completedStep]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveHospitalDocuments(Request $request, $uuid, $hospital_id) {
        $check = Hospitals::where('id', $hospital_id)->first();
        if($check) {
            $documents =  Helpers::getCommanData('EmpanelmentDocument');
            $rules = [];
            $messages = [];
            foreach ($documents as $key => $value) {
                if($value->is_required) {
                    $checkdocument = $check->documents()->where(['document_id' => $value->id])->first();
                    // $rules[$value->id . '_dateissuedoc'] = 'required|date';
                    // $rules[$value->id . '_dateexpirydoc'] = 'required|date';
                    $rules['document_' . $value->id.'_doc'] = $checkdocument ? 'nullable' : 'required|mimes:pdf|max:10240';
        
                    // $messages[$value->id . '_dateissuedoc.required'] = 'The Date of Issue for ' . $value->name . ' is required.';
                    // $messages[$value->id . '_dateexpirydoc.required'] = 'The Date of Expiry for ' . $value->name . ' is required.';
                    $messages['document_' . $value->id.'_doc'] = 'The Document for ' . $value->name . ' is required.';
                    $messages['document_' . $value->id . '_doc.mimes'] = 'The Document for ' . $value->name . ' must be a file of type: pdf.';
                }    
            }
            // $validatedData = $request->validate($rules);
            $validator = \Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors();
    
                return response()->json([
                    'message' => $errors->first(), // Get the first error message
                    'errors' => $errors->messages() // Get all errors keyed by input field
                ], 422);
            }

            foreach ($documents as $key => $value) {
                // if($request->{$value->id.'_dateissuedoc'} && $request->{$value->id.'_dateexpirydoc'}) {
                //     $issueDate = date('Y-m-d', strtotime($request->{$value->id.'_dateissuedoc'}));
                //     $expiryDate = date('Y-m-d', strtotime($request->{$value->id.'_dateexpirydoc'}));
                    
                    $array = [
                        'uuid' => Helpers::generateUUID(),
                        'document_id' => $value->id,
                        // 'issue_date' => $issueDate,
                        // 'expiry_date' => $expiryDate,
                        'remarks' => $request->{$value->id.'_remarkdoc'}
                    ];

                    if ($request->hasFile('document_' . $value->id.'_doc')) {
                        $filePath = $request->file('document_' . $value->id.'_doc')->store('certificate', 'public'); 
                        $array['document'] = $filePath; // Add file path to data
                    }                  

                    $check->documents()->updateOrCreate(['document_id' => $value->id], $array);
                // }
            }

            $check->step = 8;
            $check->save();
            $allStepComplete = $this->checkAllStepIsCompleteOrNot($check->id);
            return response()->json(['success' => true, 'message' => 'Document Saved Successfully!!', 'is_complete' => $allStepComplete]);
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function checkAllStepIsCompleteOrNot($hospitalId) {
        $hospital = Hospitals::where('id', $hospitalId)->first();
        $completedStep = $this->checkstepComplete($hospital->id);
        $documentsdata = Helpers::getCommanData('EmpanelmentDocument');
        $isDocumentTrue = false;
        if(sizeof($documentsdata) > 0 && $hospital->documents()->count() > 0) {
            $isDocumentTrue = true;
        } else if(sizeof($documentsdata) <= 0) {
            $isDocumentTrue = true;
        }
        if($isDocumentTrue && $completedStep['financial_informationstep'] && $completedStep['taxdetailsstep'] && $completedStep['accreditationtstep'] && $completedStep['medicalstep'] && $completedStep['servicestep'] && $completedStep['specialiststep'] && $hospital->licenses()->count() > 0 && $hospital->services()->count() > 0 && $hospital->specialities()->count() > 0 && $hospital->scheme != "" && $hospital->image != '') {
            return true;
        } else {
            return false;
        }
    }

    public function hospitalSubmit(Request $request, $uuid, $hospital_id) {
        $check = $this->checkAllStepIsCompleteOrNot($hospital_id);
        if($check) {
            $hospital = Hospitals::where('id', $hospital_id)->first();
            // $address = $hospital->hospitalAddress;
            // $amount = Helpers::get_settings('hospital_empanelment_fee');
            // $input['amount'] = $amount;
            // $input['order_id'] = uniqid('order');
            // $input['currency'] = "INR";
            // $input['redirect_url'] = route('hospital.empanelmentRegistration.ccResponse');
            // $input['cancel_url'] = route('hospital.empanelmentRegistration.ccResponse');
            // $input['language'] = "EN";
            // $input['merchant_id'] = env('CCAVENUE_MERCHANT_ID');
            // $input['billing_name'] = $hospital->facility_name;
            // $input['billing_email'] = $address->email;
            // $input['billing_tel'] = $address->mobile_no;


            // $merchant_data = "";

            // $working_key = env('CCAVENUE_WORKING_KEY'); 
            // $access_code = env('CCAVENUE_ACCESS_CODE');
        
            // foreach ($input as $key => $value) {
            //     $merchant_data .= $key . '=' . urlencode($value) . '&';
            // }

            // $input['hospital_uuid'] = $hospital->uuid;
            // $input['status'] = 'Pending';
            // $input['user_id'] = auth()->user()->id;
            // $input['uuid'] = Helpers::generateUUID();

            // $hospital->payments()->updateOrCreate(['hospital_id' => $hospital_id, 'user_id' => auth()->user()->id], $input);
        
            // $encrypted_data = Helpers::encryptCC($merchant_data, $working_key);
            // $url = env('CCAVENUE_URL') . '/transaction/transaction.do?command=initiateTransaction&encRequest=' . $encrypted_data . '&access_code=' . $access_code;
            $hospital->status = 'Submitted';
            $hospital->is_declaration = 1;
            $hospital->status_update_date = date('Y-m-d H:i:s');
            $hospital->save();
            $url = route('hospital.dashboard');
            return response()->json(['success' => true, 'message' => $hospital->hospital_id.' Hospital Payment Is Initiated!!', 'url' => $url]);
        } else {
            return response()->json(['success' => false, 'message' => 'Please fill all details first of hospital']);
        }
    }

    public function directSubmit(Request $request, $uuid, $hospital_id) {
        $check = $this->checkAllStepIsCompleteOrNot(base64_decode($hospital_id));
        if($check) {
            $hospital = Hospitals::where('id', base64_decode($hospital_id))->first();
            $hospital->status = 'Submitted';
            $hospital->exists_hospital_id = $request->hospital_id;
            $hospital->is_declaration = 1;
            $hospital->status_update_date = date('Y-m-d H:i:s');
            $hospital->save();

            $existsHospital = ExistsHospital::where('hospital_id', $request->hospital_id)->where('is_added', 0)->first();
            if($existsHospital) {
               $existsHospital->is_added = 1;
               $existsHospital->save();
            }
            $url = route('hospital.dashboard');
            return response()->json(['success' => true, 'message' => $hospital->hospital_id.' Hospital Submitted Successfully!!', 'url' => $url]);
        } else {
            return response()->json(['success' => false, 'message' => 'Please fill all details first of hospital']);
        }
    }    

    public function checkexisthospital(Request $request) {
        if($request->hospital_id) {
            $existsHospital = ExistsHospital::where('hospital_id', $request->hospital_id)->where('is_added', 0)->first();
            if($existsHospital) {
                return response()->json(['success' => true, 'message' => 'Hospital found!!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Hospital not found!!']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Hospital not found!!']);
        }
    }

    public function paymentIntiate(Request $request, $uuid, $hospital_id) {
        return view('hospital.payment.initiate');
    }

    public function hospitalpreview(Request $request, $uuid, $hospital_id) {
        $hospital = Hospitals::where('id', $hospital_id)->first();
        return view('hospital.empanelment.preview', compact('hospital'));
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

    public function submitResponse(Request $request, $hospitalId, $uuid) {
        $validatedData = $request->validate([
            'dec_action' => 'required',
            'dec_document' => 'required|mimes:pdf|max:10240',
            'dec_remarks' => 'required',
        ]);

        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();

        if ($request->hasFile('dec_document')) {
            $filePath = $request->file('dec_document')->store('dec_document', 'public'); 
            $validatedData['dec_document'] = $filePath; // Add file path to data
        } 

        $logarray = [
            'action' => $validatedData['dec_action'],
            'remark' => $validatedData['dec_remarks'],
            'created_by' => auth()->user()->id,
        ];

        if($validatedData['dec_document']) {
            $logarray['attachment'] = $validatedData['dec_document'];
        }

        $check = WorkFlowHistory::where(['hospital_id' => $hospital->id])->orderBy('id', 'DESC')->first();
        if($check && $check->action == $validatedData['dec_action'] && $check->created_by == auth()->user()->id) {
            $check->remark = $validatedData['dec_remarks'];
            $check->attachment = $logarray['attachment'] ? $logarray['attachment'] : $check->attachment;
            $check->save();
            $id = $check->id;
        } else {
            $id = Helpers::addWorkflowForHospital($hospital, $logarray);
        }

        $hospital->status =  $validatedData['dec_action'];

        $hospital->save();

        if($hospital->is_upgrade_application == 1) {
            $UHospitals = UHospitals::where('main_hospitalid', $hospital->id)->first();
            $UHospitals->status = $validatedData['dec_action'];
            $UHospitals->save();
        }

        $userdata = User::find($hospital->dec_qry_id);
        $data['hospital'] = $hospital;
        $data['remark'] = $logarray['remark'];
        $data['message'] = "The hospital ".$hospital->facility_name."(".$hospital->hospital_id.") has reviewed and corrected all information, updated the remarks, and successfully submitted the document.";
        $data['userdata'] = $userdata;
        $filePath = asset('public/storage/'.$logarray['attachment'] ); // Path to your document
        $data['filePath'] = $filePath;

        try {
            Mail::to($userdata->email)->send(new StatusMail($data));
        } catch (\Exception $e) {
            
        }
        $route = route('hospital.dashboard');
        return response()->json(['success' => true, 'message' => $validatedData['dec_action'].' !!', 'url' => $route]);
    }

    public function getWorkFlowData(Request $request, $hospitalId, $uuid) {
        if ($request->ajax()) {
            $data = WorkFlowHistory::with('hospital')->where('hospital_id', base64_decode($hospitalId))->orderBy('id', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn() // Adds a serial number column
                ->addColumn('facility_name', function ($row) {
                    // Access the related facilityType name
                    return $row->hospital ? $row->hospital->facility_name : '-';
                })
                ->addColumn('attachment', function ($row) {                    
                    if($row->attachment) {
                        $link = asset('public/storage/'.@$row->attachment);
                        return '<p><a href="'.$link.'" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></p>';
                    } else {
                        return '--';
                    }                   
                })
                ->addColumn('created_at', function ($row) {
                    return date('d-m-Y', strtotime($row->created_at)) . ' (' . date('h:i:sA', strtotime($row->created_at)) . ')';
                })
                ->rawColumns(['attachment']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }

    public function empanelmentDashboard(Request $request, $uuid) {
        $hospital = Hospitals::where('uuid', base64_decode($uuid))->first();

        return view('hospital.upgrade.dashboard', compact('hospital'));
    }  
    
}
