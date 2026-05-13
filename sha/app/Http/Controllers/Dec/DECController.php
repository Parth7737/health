<?php

namespace App\Http\Controllers\Dec;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\{ Hospitals, User, WorkFlowHistory, InitiateVerification, UHospitals, FacilityOwnershipType, FacilityOwnershipSubType, HospitalSpeciality, Speciality };
use DataTables;
use App\Mail\StatusMail;
use Mail;
use Carbon\Carbon;

class DECController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function checkallstepcompleteornot($hospitalid) {
        $hospital = Hospitals::where('id', $hospitalid)->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'dec');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'dec');
        $step2 = Helpers::stepCheck(2, $hospital->id, 'speciality', 'dec');
        $step3 = Helpers::stepCheck(3, $hospital->id, 'services', 'dec');
        $step4 = Helpers::stepCheck(4, $hospital->id, 'statutory_licences', 'dec');
        $step5 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'dec');
        $step51 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'dec');
        $step52 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'dec');
        $step53 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'dec');
        $step6 = Helpers::stepCheck(6, $hospital->id, 'quality_accreditation', 'dec');
        $step7 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'dec');
        $step71 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'dec');
        if($step12 && $step1 && $step2 && $step3 && $step4 && $step5 && $step51 && $step52 && $step53 && $step6 && $step7 && $step71){
            return true;
        } else {
            return false;
        }
    }

    public function getHospital(Request $request, $hospitalId, $uuid)
    {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        $is_upgrade = 0;
        if($hospital->is_upgrade_application == 1) {
            $is_upgrade = 1;
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
            $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('assigned_by', auth()->user()->id)->orderby('id', 'DESC')->first();
        } else {
            $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('assigned_by', auth()->user()->id)->orderby('id', 'DESC')->first();
        }
        
        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'dec');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'dec');
        $step2 = Helpers::stepCheck(2, $hospital->id, 'speciality', 'dec');
        $step3 = Helpers::stepCheck(3, $hospital->id, 'services', 'dec');
        $step4 = Helpers::stepCheck(4, $hospital->id, 'statutory_licences', 'dec');
        $step5 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'dec');
        $step51 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'dec');
        $step52 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'dec');
        $step53 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'dec');
        $step6 = Helpers::stepCheck(6, $hospital->id, 'quality_accreditation', 'dec');
        $step7 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'dec');
        $step71 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'dec');

        $step = 1;
        if($step12 && $step1 && $step2 && $step3 && $step4 && $step5 && $step51 && $step52 && $step53 && $step6 && $step7 && $step71) {
            $step = 8;
        } else if($step12 && $step1 && $step2 && $step3 && $step4 && $step5 && $step51 && $step52 && $step53 && $step6) {
            $step = 7;
        } else if($step12 && $step1 && $step2 && $step3 && $step4 && $step5 && $step51 && $step52 && $step53) {
            $step = 6;
        } else if ($step12 && $step1 && $step2 && $step3 && $step4) {
            $step = 5;
        } else if ($step12 && $step1 && $step2 && $step3) {
            $step = 4;
        } else if ($step12 && $step1 && $step2) {
            $step = 3;
        } else if($step12 && $step1) {
            $step = 2;
        }

        return view('dec.submitworklist', compact('hospital', 'verification', 'step', 'is_upgrade'));
    }

    public function hospitalpreview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id',  base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        return view('dec.preview', compact('hospital'));
    }

    public function initiateVerification(Request $request, $hospitalId, $uuid) {
        $district = auth()->user()->district;
        $hospital = Hospitals::where('id',  base64_decode($hospitalId))->first();
        $users = User::where('role_id', 7)->where('district', $district)->get();
        return view('dec.initiateVerification', compact('hospital', 'users'));
    }

    public function initiateVerificationSubmit(Request $request, $hospitalId, $uuid) {
        $validatedData = $request->validate([
            'verification_authority' => 'required',
            'physical_verifier' => 'required',
            'verification_type' => 'required',
            'date_of_assignment' => 'required|date',
            'due_date_of_physical_verification' => 'required|date',
        ]);

        $hospital = Hospitals::where('id',  base64_decode($hospitalId))->first();
        $validatedData['assigned_by'] = auth()->user()->id;
        $validatedData['uuid'] = Helpers::generateUUID();
        if($hospital->is_upgrade_application == 1) {
            $hospital->initiateVerifications()->delete();
        }
        $hospital->initiateVerifications()->create($validatedData);
        
        $logarray = [
            'action' => 'PHYSICAL VERIFICATION INITIATE',
            'remark' => 'The hospital moved to the physical verification.',
            'created_by' => auth()->user()->id
        ];
        Helpers::addWorkflowForHospital($hospital, $logarray);
        return response()->json(['success' => true, 'message' => 'Physical Verification initiated for hospital ID'.$hospital->hospital_id, 'data' => auth()->user()]); 
    }

    public function getWorkFlowData(Request $request, $hospitalId, $uuid) {
        $district = auth()->user()->district;
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

    public function loadStep(Request $request, $hospitalId, $uuid) {
        $validatedData = $request->validate([
            'step' => 'required',
        ]);

       

        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        $is_upgrade = 0;
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
            $is_upgrade = 1;
            $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('assigned_by', auth()->user()->id)->orderby('id', 'DESC')->first();
        } else {
            $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('assigned_by', auth()->user()->id)->orderby('id', 'DESC')->first();
        }
        $allstepcomplete = $this->checkallstepcompleteornot(base64_decode($hospitalId));

        if($request->step == 1) {
            return view('dec._partials.basicinfo', compact('hospital', 'verification', 'allstepcomplete'));
        } else if($request->step == 2) {
            return view('dec._partials.speciality', compact('hospital', 'verification'));
        } else if($request->step == 3) {
            $services =  Helpers::getCommanData('Service');
            return view('dec._partials.services', compact('hospital', 'verification', 'services'));
        } else if($request->step == 4) {
            $licenses =  Helpers::getCommanData('Licenses');
            return view('dec._partials.licenses', compact('hospital', 'verification', 'licenses'));
        } else if($request->step == 5) {
            return view('dec._partials.humanresource', compact('hospital', 'verification'));
        } else if($request->step == 6) {
            return view('dec._partials.accreditation', compact('hospital', 'verification'));
        } else if($request->step == 7) {
            return view('dec._partials.finance', compact('hospital', 'verification'));
        } else if($request->step == 8) {
            return view('dec._partials.document', compact('hospital', 'verification', 'allstepcomplete', 'is_upgrade'));
        }
    }

    public function saveEstablishmentReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();

        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }

        if($hospital->dec_remark == "" && $hospital->dec_status == "") {
            $validatedData = $request->validate([
                'dec_remark' => 'required',
                'dec_status' => 'required'
            ]);
        }

        $hospital->dec_remark = $request->dec_remark;
        $hospital->dec_status = $request->dec_status;
        $hospital->dec_id = auth()->user()->id;
        $hospital->save();

        Helpers::saveTabStatus(1, 'establishment_details', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'dec');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'dec');
        if($step1 && $step12) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Establishment Details Verified Successfully By Dec!!', 'isComplete' => $isComplete]); 

    }

    public function saveAddressReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $address = $hospital->hospitalAddress;
        if($address->dec_status == "" && $address->dec_remark == "") {
            $validatedData = $request->validate([
                'dec_status' => 'required',
                'dec_remark' => 'required'
            ]);
        }

        $address->dec_status = $request->dec_status;
        $address->dec_remark = $request->dec_remark;
        $address->dec_id = auth()->user()->id;
        $address->save();

        Helpers::saveTabStatus(1, 'address', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'dec');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'dec');
        if($step1 && $step12) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Address Details Verified Successfully By DEC.!!', 'isComplete' => $isComplete]); 

    }

    public function saveSpecialityReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $specialities = $hospital->specialities;
        $rules = [];
        $messages = [];
        foreach ($specialities as $key => $value) {
            if($value->available) {
                $rules['dec_status_'.$value->id] = 'required';
                $rules['dec_remark_'.$value->id] = 'nullable';
                $messages['dec_status_'.$value->id] = 'The Status is required.';
            }
        }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Format the errors for AJAX response
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }

        foreach ($specialities as $key => $value) {
            $value->dec_status = $request->{'dec_status_'.$value->id};
            $value->dec_remark = $request->{'dec_remark_'.$value->id};
            $value->dec_id = auth()->user()->id;
            $value->save();
        }
        Helpers::saveTabStatus(2, 'speciality', $hospital->id, 'dec');

        return response()->json(['success' => true, 'message' => 'Specialities Verified Successfully BY DEC!!']); 
    }

    public function saveServicesReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $services = $hospital->services;
        if(sizeof($services) > 0) {
            // $services =  Helpers::getCommanData('Service');

            // foreach ($services as $key => $value) {
            //     if(sizeof($value->subServices) > 0) {
            //         foreach ($value->subServices as $k => $v) {
            //             if($v->is_required) {
            //                 $name = str_replace(' ', '-', strtolower($v->name));
            //                 $checklicences = $check->services()->where(['service_id' => $value->id, 'sub_service_id' => $v->id])->first();
            //                 $rules[$value->id.'_'.$v->id.'_'.$name] = 'required';
            //                 $rules[$value->id.'_'.$v->id.'_'.$name.'_text'] = 'required';
            //                 $rules[$value->id.'_'.$v->id.'_'.$name.'_image'] = $checklicences ? 'nullable' : 'required|mimes:jpg,png,jpeg';
                
            //                 $messages[$value->id.'_'.$v->id.'_'.$name] = 'This field is Required';
            //                 $messages[$value->id.'_'.$v->id.'_'.$name.'_text'] = 'This field is Required';
            //                 $messages[$value->id.'_'.$v->id.'_'.$name.'_image'] = 'This field is Required';
            //             }                   
            //         }
            //     }
            // }
            // // $validatedData = $request->validate($rules);
            // $validator = \Validator::make($request->all(), $rules, $messages);

            // if ($validator->fails()) {
            //     // Format the errors for AJAX response
            //     $errors = $validator->errors();
    
            //     return response()->json([
            //         'message' => $errors->first(), // Get the first error message
            //         'errors' => $errors->messages() // Get all errors keyed by input field
            //     ], 422);
            // }
            $isValid = 0;

            $rules = [];
            $messages = [];
            foreach ($services as $key => $value) {
                $rules['dec_status_'.$value->id] = 'required';
                $rules['dec_remark_'.$value->id] = 'nullable';
                $messages['dec_status_'.$value->id] = 'The Status is required.';
            }
    
            $validator = \Validator::make($request->all(), $rules, $messages);
    
            if ($validator->fails()) {
                // Format the errors for AJAX response
                $errors = $validator->errors();
    
                return response()->json([
                    'message' => $errors->first(), // Get the first error message
                    'errors' => $errors->messages() // Get all errors keyed by input field
                ], 422);
            }

            // $check->services()->delete();
            foreach ($services as $key => $value) {
                $value->dec_status = $request->{'dec_status_'.$value->id};
                $value->dec_remark = $request->{'dec_remark_'.$value->id};
                $value->dec_id = auth()->user()->id;
                $value->save();

            }
            Helpers::saveTabStatus(3, 'services', $hospital->id, 'dec');

            return response()->json(['success' => true, 'message' => 'Service Verified Successfully By DEC!!']);    
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveLicensesReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();

        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }

        if($hospital) {
            $licenses = $hospital->licenses;
            $rules = [];
            $messages = [];
            foreach ($licenses as $key => $value) {
                $rules['dec_status_'.$value->id] = 'required';
                $rules['dec_remark_'.$value->id] = 'nullable';
                $messages['dec_status_'.$value->id] = 'The Status is required.';
            }
    
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
                $value->dec_status = $request->{'dec_status_'.$value->id};
                $value->dec_remark = $request->{'dec_remark_'.$value->id};
                $value->dec_id = auth()->user()->id;
                $value->save();
            }
    
            Helpers::saveTabStatus(4, 'statutory_licences', $hospital->id, 'dec');

            return response()->json(['success' => true, 'message' => 'Licenses Verified Successfully BY DEC!!']);  
        } else {
            return response()->json(['success' => false, 'message' => 'Something Wrong!!']);
        }
    }

    public function saveCEOReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $ceo = $hospital->ceo;
        if($ceo->dec_status == "" && $ceo->dec_remark == "") {
            $validatedData = $request->validate([
                'dec_status' => 'required',
                'dec_remark' => 'nullable'
            ]);
        }

        $ceo->dec_status = $request->dec_status;
        $ceo->dec_remark = $request->dec_remark;
        $ceo->dec_id = auth()->user()->id;
        $ceo->save();

        Helpers::saveTabStatus(5, 'ceo', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'dec');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'dec');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'dec');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'dec');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'CEO Details Verified Successfully By DEC!!', 'isComplete' => $isComplete]); 

    }

    public function saveMHRReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $mhrresource = $hospital->humanResources()->where('type', 'mhr')->get();

        $rules = [];
        $messages = [];
        foreach ($mhrresource as $key => $value) {
            $rules['dec_status_'.$value->id] = 'required';
            $rules['dec_remark_'.$value->id] = 'nullable';
            $messages['dec_status_'.$value->id] = 'The Status is required.';
        }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Format the errors for AJAX response
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }

        foreach ($mhrresource as $key => $value) {
            $value->dec_status = $request->{'dec_status_'.$value->id};
            $value->dec_remark = $request->{'dec_remark_'.$value->id};
            $value->dec_id = auth()->user()->id;
            $value->save();
        }

        Helpers::saveTabStatus(5, 'mhr', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'dec');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'dec');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'dec');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'dec');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Medical HumanResource Details Verified Successfully By DEC!!', 'isComplete' => $isComplete]); 

    }

    public function saveSSHRReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $mhrresource = $hospital->humanResources()->where('type', 'sshr')->get();

        $rules = [];
        $messages = [];
        foreach ($mhrresource as $key => $value) {
            $rules['dec_status_'.$value->id] = 'required';
            $rules['dec_remark_'.$value->id] = 'nullable';
            $messages['dec_status_'.$value->id] = 'The Status is required.';
        }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Format the errors for AJAX response
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }

        foreach ($mhrresource as $key => $value) {
            $value->dec_status = $request->{'dec_status_'.$value->id};
            $value->dec_remark = $request->{'dec_remark_'.$value->id};
            $value->dec_id = auth()->user()->id;
            $value->save();
        }

        Helpers::saveTabStatus(5, 'sshr', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'dec');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'dec');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'dec');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'dec');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Medical HumanResource Details Verified Successfully By DEC!!', 'isComplete' => $isComplete]); 
    }

    public function saveSPECReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $hospitalTeam = $hospital->hospitalTeam;

        $rules = [];
        $messages = [];
        foreach ($hospitalTeam as $key => $value) {
            $rules['dec_status_'.$value->id] = 'required';
            $rules['dec_remark_'.$value->id] = 'nullable';
            $messages['dec_status_'.$value->id] = 'The Status is required.';
        }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Format the errors for AJAX response
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }

        foreach ($hospitalTeam as $key => $value) {
            $value->dec_status = $request->{'dec_status_'.$value->id};
            $value->dec_remark = $request->{'dec_remark_'.$value->id};
            $value->dec_id = auth()->user()->id;
            $value->save();
        }

        Helpers::saveTabStatus(5, 'specialist', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'dec');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'dec');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'dec');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'dec');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }


        return response()->json(['success' => true, 'message' => 'Specialities Details Verified Successfully By DEC!!', 'isComplete' => $isComplete]); 
    }

    public function saveAccreditationReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $accreditation = $hospital->hospitalAccreditation;

        $rules = [];
        $messages = [];
        // foreach ($hospitalTeam as $key => $value) {
            $rules['dec_status_'.$accreditation->id] = 'required';
            $rules['dec_remark_'.$accreditation->id] = 'nullable';
            $messages['dec_status_'.$accreditation->id] = 'The Status is required.';
        // }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Format the errors for AJAX response
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }

        // foreach ($hospitalTeam as $key => $value) {
            $accreditation->dec_status = $request->{'dec_status_'.$accreditation->id};
            $accreditation->dec_remark = $request->{'dec_remark_'.$accreditation->id};
            $accreditation->dec_id = auth()->user()->id;
            $accreditation->save();
        // }

        Helpers::saveTabStatus(6, 'quality_accreditation', $hospital->id, 'dec');

        return response()->json(['success' => true, 'message' => 'Accreditation Details Verified Successfully By DEC!!']); 
    }

    public function saveFinancialReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $finance = $hospital->financialInformation;
        $validatedData = $request->validate([
            'dec_status' => 'required',
            'dec_remark' => 'nullable'
        ]);

        $finance->dec_status = $request->dec_status;
        $finance->dec_remark = $request->dec_remark;
        $finance->dec_id = auth()->user()->id;
        $finance->save();

        Helpers::saveTabStatus(7, 'finance_details', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'dec');
        $step2 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'dec');
        if($step1 && $step2) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Financial Information Verified Successfully By DEC!!', 'isComplete' => $isComplete]); 

    }

    public function saveTaxdetailsReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $taxDetails = $hospital->taxDetails;
        $validatedData = $request->validate([
            'dec_status' => 'required',
            'dec_remark' => 'nullable'
        ]);

        $taxDetails->dec_status = $request->dec_status;
        $taxDetails->dec_remark = $request->dec_remark;
        $taxDetails->dec_id = auth()->user()->id;
        $taxDetails->save();

        Helpers::saveTabStatus(7, 'tax_details', $hospital->id, 'dec');

        $step1 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'dec');
        $step2 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'dec');
        if($step1 && $step2) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Tax Information Verified Successfully By DEC!!', 'isComplete' => $isComplete]); 

    }

    public function saveDocumentReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $documents = $hospital->documents;

        $rules = [];
        $messages = [];
        foreach ($documents as $key => $value) {
            $rules['dec_status_'.$value->id] = 'required';
            $rules['dec_remark_'.$value->id] = 'nullable';
            $messages['dec_status_'.$value->id] = 'The Status is required.';
        }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Format the errors for AJAX response
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }

        foreach ($documents as $key => $value) {
            $value->dec_status = $request->{'dec_status_'.$value->id};
            $value->dec_remark = $request->{'dec_remark_'.$value->id};
            $value->dec_id = auth()->user()->id;
            $value->save();
        }

        return response()->json(['success' => true, 'message' => 'Documents Verified Successfully By DEC!!']); 
    }

    public function submitVerifierReport(Request $request, $hospitalId, $verifyId, $uuid) {
        $validatedData = $request->validate([
            'dec_action' => 'required',
            'dec_document' => 'required|mimes:pdf|max:10240',
            'dec_remarks' => 'required',
        ]);
        
        $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('id', base64_decode($verifyId))->first();

        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        $validatedData['dec_verifier_id'] = auth()->user()->id;

        if ($request->hasFile('dec_document')) {
            $filePath = $request->file('dec_document')->store('dec_document', 'public'); 
            $validatedData['dec_document'] = $filePath; // Add file path to data
        }  

        $hospital->hospitalReport()->updateOrCreate(
            ['hospital_id' => $hospital->id],
            $validatedData
        );

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

        // Helpers::addWorkflowForHospital($hospital, $logarray);
        
        

        if($hospital->status == "Query Raised by SEC") {
            $userdata = User::find($hospital->sec_qry_id);
        } else {
            $userdata = User::find($hospital->user_id);
        }
        $data['hospital'] = $hospital;
        $data['remark'] = $logarray['remark'];

        if($logarray['action'] == "Response Required From Facility") {
            $data['message'] = "The DEC officer (".auth()->user()->name.") has reviewed the submitted document for the hospital ".$hospital->facility_name."(".$hospital->hospital_id.") and has identified certain queries that require clarification. Please review the queries and provide the necessary updates at the earliest.";
        } else if($logarray['action'] == "Empanelment Not Recommended by DEC") {
            $data['message'] = "The DEC officer (".auth()->user()->name.") has reviewed the submitted document for the hospital ".$hospital->facility_name."(".$hospital->hospital_id.") and found that the responses to the queries are unsatisfactory. As a result, the submission has been rejected. Please address the concerns and resubmit the document for further review.";
        } else if($logarray['action'] == "Empanelment Recommended by DEC") {
            $data['message'] = "The DEC officer (".auth()->user()->name.") has reviewed the submitted document for the hospital ".$hospital->facility_name."(".$hospital->hospital_id.") and, as per the DEC's recommendation, has approved the submission. No further clarifications are required at this stage.";
        } else if($logarray['action'] == "Approved Upgradation Request") {
            $data['message'] = "The DEC officer (".auth()->user()->name.") has reviewed the submitted document for the upgrade hospital ".$hospital->facility_name."(".$hospital->hospital_id.") and, as per the DEC's recommendation, has approved the submission. No further clarifications are required at this stage.";
        } else if($logarray['action'] == "Query On Upgradation Request From Facility") {
            $data['message'] = "The DEC officer (".auth()->user()->name.") has reviewed the submitted document for the upgrade hospital ".$hospital->facility_name."(".$hospital->hospital_id.") and has identified certain queries that require clarification. Please review the queries and provide the necessary updates at the earliest.";
        }  else if($logarray['action'] == "Rejected Upgradation Request") {
            $data['message'] = "The DEC officer (".auth()->user()->name.") has reviewed the submitted document for the upgrade hospital ".$hospital->facility_name."(".$hospital->hospital_id.") and found that the responses to the queries are unsatisfactory. As a result, the submission has been rejected. Please address the concerns and resubmit the document for further review.";
        }

        $data['userdata'] = $userdata;
        $filePath = asset('public/storage/'.$logarray['attachment'] ); // Path to your document
        $data['filePath'] = $filePath;
        
        try {
            Mail::to($userdata->email)->send(new StatusMail($data));
        } catch (\Exception $e) {
            
        }
        
        $hospital->status = $validatedData['dec_action'];
        // $hospital->status_update_date = date('Y-m-d H:i:s');
        $hospital->dec_qry_id = auth()->user()->id;
        $hospital->qry_type = 'DEC';
        $hospital->dec_work_id = $id;
        $hospital->dec_change_date = date('Y-m-d');
        $hospital->save();

        if($hospital->is_upgrade_application == 1) {
            $UHospitals = UHospitals::where('main_hospitalid', $hospital->id)->first();
            $UHospitals->status = $validatedData['dec_action'];
            $UHospitals->dec_qry_id = auth()->user()->id;
            $UHospitals->qry_type = 'DEC';
            $UHospitals->dec_work_id = $id;
            $UHospitals->dec_change_date = date('Y-m-d');
            $UHospitals->save();
        }

        $route = route('dec.dashboard');
        return response()->json(['success' => true, 'message' => $validatedData['dec_action'].' !!', 'url' => $route]);
    }

    public function submitDecResponse(Request $request, $hospitalId, $uuid)  {
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
        $hospital->dec_qry_id = auth()->user()->id;
        $hospital->qry_type = 'DEC';
        $hospital->dec_work_id = $id;
        $hospital->dec_change_date = date('Y-m-d');
        $hospital->save();

        if($hospital->is_upgrade_application == 1) {
            $UHospitals = UHospitals::where('main_hospitalid', $hospital->id)->first();
            $UHospitals->status = $validatedData['dec_action'];
            $UHospitals->dec_qry_id = auth()->user()->id;
            $UHospitals->qry_type = 'DEC';
            $UHospitals->dec_work_id = $id;
            $UHospitals->dec_change_date = date('Y-m-d');
            $UHospitals->save();
        }

        $userdata = User::find($hospital->user_id);
        $data['hospital'] = $hospital;
        $data['remark'] = $logarray['remark'];
        $data['message'] = "The DEC officer (".auth()->user()->name.") has reviewed the submitted document for the hospital <strong>.$hospital->facility_name.</strong> (".$hospital->hospital_id.") and has identified certain queries that require clarification. Please review the queries and provide the necessary updates at the earliest.";
        $data['userdata'] = $userdata;
        $filePath = asset('public/storage/'.$logarray['attachment'] ); // Path to your document
        $data['filePath'] = $filePath;

        try {
            Mail::to($userdata->email)->send(new StatusMail($data));
        } catch (\Exception $e) {
            
        }
        $route = route('dec.dashboard');
        return response()->json(['success' => true, 'message' => $validatedData['dec_action'].' !!', 'url' => $route]);
    }

    public function hospitaltypechart(Request $request) {

        $type = $request->type;
        $baseQuery = Hospitals::query();
    
        if ($request->scheme_id) {
            $baseQuery->where('scheme', $request->scheme_id);
        }
    
        if ($request->facility_type) {
            $baseQuery->where('facility_type', $request->facility_type);
        }

        $privateID = FacilityOwnershipType::where('name', 'Private')->first();
        $publicID = FacilityOwnershipType::where('name', 'Public')->first();

        $stateid = $publicID
            ? FacilityOwnershipSubType::where('facility_ownership_type_id', $publicID->id)->where('name', 'State')->first()
            : null;

        $centralid = $publicID
            ? FacilityOwnershipSubType::where('facility_ownership_type_id', $publicID->id)->where('name', 'Central')->first()
            : null;

        $isEmpanelled = $type === "empanelled" ? 1 : 0;
    
        $statecount = $stateid
        ? (clone $baseQuery)
            ->where('is_empanelled', $isEmpanelled)
            ->where('facility_ownership_sub_type1', $stateid->id)
            ->count()
        : 0;

        $centralcount = $centralid
            ? (clone $baseQuery)
                ->where('is_empanelled', $isEmpanelled)
                ->where('facility_ownership_sub_type1', $centralid->id)
                ->count()
            : 0;

        $privatecount = $privateID
            ? (clone $baseQuery)
                ->where('is_empanelled', $isEmpanelled)
                ->where('facility_ownership_type', $privateID->id)
                ->count()
            : 0;

        return response()->json([
            'labels' => ['Goverment State', 'Goverment Central', 'Private'],
            'data' => [$statecount, $centralcount, $privatecount],
            'colors' => ['#0e753b', '#2039e9', '#bdbd61']
        ]);
    }

    public function bedsizechart(Request $request) {
     
        $ranges = [
            [1, 10],
            [11, 30],
            [31, 50],
            [51, 100],
            [101, 200],
            [201, 300],
            [301, 400],
            [401, 500],
        ];
    
        $data = [];
    
        foreach ($ranges as $range) {
            $query = Hospitals::query();
            if ($request->scheme_id) {
                $query->where('scheme', $request->scheme_id);
            }
    
            if ($request->facility_type) {
                $query->where('facility_type', $request->facility_type);
            }
    
            $query->whereBetween('total_no_of_beds', $range);
    
            $data[] = $query->count();
        } 

        $query = Hospitals::query();

        if ($request->scheme_id) {
            $query->where('scheme', $request->scheme_id);
        }
    
        if ($request->facility_type) {
            $query->where('facility_type', $request->facility_type);
        }
    
        $query->where('total_no_of_beds', '>', 500);
    
        $data[] = $query->count();

        return response()->json([
            'labels' => ['1-10', '11-30', '31-50', '51-100', '101-200', '201-300', '301-400', '401-500', '500+'],
            'data' => $data,
            'colors' => [
                '#7c4dff', '#b39ddb', '#ce93d8', '#f48fb1',
                '#ffccbc', '#ffe082', '#c5e1a5', '#81d4fa', '#b0bec5'
            ]
        ]);
    }

    public function statusChart(Request $request){
        $district = auth()->user()->district;
        $status = $request->status;
        $hospitalCount = 0;
        if ($status !== 'Suspended') {
            $query = Hospitals::query();
    
            // Optional: Apply district filter if needed
            // $query->whereHas('hospitalAddress', function ($q) use ($district) {
            //     $q->where('district', $district);
            // });
    
            if ($request->scheme_id) {
                $query->where('scheme', $request->scheme_id);
            }
    
            if ($request->facility_type) {
                $query->where('facility_type', $request->facility_type);
            }
    
            $query->where('status', $status);
    
            $hospitalCount = $query->count();
        }

        return response()->json([
            'labels' => [$status],
            'data' => [$hospitalCount],
            'colors' => ['#0ecfd8']
        ]);
    }

    public function trandsChart(Request $request)
    {
        $district = auth()->user()->district;

        $labels = [];
        $empanelled = [];
        $pendingDec = [];
        $pendingSec = [];

        $startDate = Carbon::now()->subMonths(11)->startOfMonth(); // 12 months ago
        $endDate = Carbon::now(); // Current month

        while ($startDate <= $endDate) {
            $monthLabel = $startDate->format('M Y');
            $labels[] = $monthLabel;

            // Common filters
            $filters = function ($query) use ($request) {
                if ($request->scheme_id) {
                    $query->where('scheme', $request->scheme_id);
                }
                if ($request->facility_type) {
                    $query->where('facility_type', $request->facility_type);
                }
            };

            // Empanelled
            $empanelled[] = Hospitals::where('status', 'Empanelled')
                ->whereYear('sec_change_date', $startDate->year)
                ->whereMonth('sec_change_date', $startDate->month)
                ->when(true, $filters)
                ->count();

            // Pending DEC
            $pendingDec[] = Hospitals::whereIn('status', ['Submitted', 'Re-Submitted'])
                ->whereYear('status_update_date', $startDate->year)
                ->whereMonth('status_update_date', $startDate->month)
                ->when(true, $filters)
                ->count();

            // Pending SEC
            $pendingSec[] = Hospitals::where('status', 'Empanelment Recommended by DEC')
                ->whereYear('dec_change_date', $startDate->year)
                ->whereMonth('dec_change_date', $startDate->month)
                ->when(true, $filters)
                ->count();

            $startDate->addMonth();
        }

        return response()->json([
            'labels' => $labels,
            'empanelled' => $empanelled,
            'pending_dec' => $pendingDec,
            'pending_sec' => $pendingSec,
        ]);
    }


    public function specialiitieschart(Request $request)
    {
        $hospitalQuery = Hospitals::where('is_empanelled', 1);
    
        if ($request->scheme_id) {
            $hospitalQuery->where('scheme', $request->scheme_id);
        }
    
        if ($request->facility_type) {
            $hospitalQuery->where('facility_type', $request->facility_type);
        }
    
        $hospitalIds = $hospitalQuery->pluck('id')->toArray();
    
        $specialityIds = HospitalSpeciality::whereIn('hospital_id', $hospitalIds)
            ->where('available', 1)
            ->pluck('speciality_id')
            ->unique();
    
        $labels = [];
        $data = [];
        $colors = [];
    
        $colorPalette = [
            '#7c4dff', '#b39ddb', '#ce93d8', '#f48fb1',
            '#ffccbc', '#ffe082', '#c5e1a5', '#81d4fa', '#b0bec5',
            '#ffab91', '#ff7043', '#ffcc80', '#aed581'
        ];
    
        $index = 0;
    
        foreach ($specialityIds as $specialityId) {
            $speciality = Speciality::find($specialityId);
            if ($speciality) {
                $labels[] = $speciality->name;
    
                $count = HospitalSpeciality::where('speciality_id', $specialityId)
                    ->where('available', 1)
                    ->whereIn('hospital_id', $hospitalIds)
                    ->count();
    
                $data[] = $count;
                $colors[] = $colorPalette[$index % count($colorPalette)];
                $index++;
            }
        }
    
        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors
        ]);
    }
    
}
