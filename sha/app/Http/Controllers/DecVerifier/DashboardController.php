<?php

namespace App\Http\Controllers\DecVerifier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\{Hospitals, InitiateVerification, WorkFlowHistory, User, UHospitals};
use DataTables;
use App\Mail\StatusMail;
use Mail;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        $district = auth()->user()->district;
        return view('decverifier.index');
    }

    public function checkallstepcompleteornot($hospitalid) {
        $hospital = Hospitals::where('id', $hospitalid)->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'verifier');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'verifier');
        $step2 = Helpers::stepCheck(2, $hospital->id, 'speciality', 'verifier');
        $step3 = Helpers::stepCheck(3, $hospital->id, 'services', 'verifier');
        $step4 = Helpers::stepCheck(4, $hospital->id, 'statutory_licences', 'verifier');
        $step5 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'verifier');
        $step51 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'verifier');
        $step52 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'verifier');
        $step53 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'verifier');
        $step6 = Helpers::stepCheck(6, $hospital->id, 'quality_accreditation', 'verifier');
        $step7 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'verifier');
        $step71 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'verifier');
        if($step12 && $step1 && $step2 && $step3 && $step4 && $step5 && $step51 && $step52 && $step53 && $step6 && $step7 && $step71){
            return true;
        } else {
            return false;
        }
    }

    public function getHospital(Request $request, $hospitalId, $uuid)
    {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'verifier');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'verifier');
        $step2 = Helpers::stepCheck(2, $hospital->id, 'speciality', 'verifier');
        $step3 = Helpers::stepCheck(3, $hospital->id, 'services', 'verifier');
        $step4 = Helpers::stepCheck(4, $hospital->id, 'statutory_licences', 'verifier');
        $step5 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'verifier');
        $step51 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'verifier');
        $step52 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'verifier');
        $step53 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'verifier');
        $step6 = Helpers::stepCheck(6, $hospital->id, 'quality_accreditation', 'verifier');
        $step7 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'verifier');
        $step71 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'verifier');

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

        // ->whereIn('id', function ($query) {
        //     $query->selectRaw('MAX(id)')
        //         ->from('initiate_verifications')
        //         ->groupBy('hospital_id');
        // })
        $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('physical_verifier', auth()->user()->id)->orderby('id', 'DESC')->first();
        return view('decverifier.submitworklist', compact('hospital', 'verification', 'step'));
    }

    public function getFacilityData(Request $request)
    {
        $district = auth()->user()->district;
        if ($request->ajax()) {
            $data = InitiateVerification::with('hospital')
            ->where('physical_verifier', auth()->user()->id)->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('initiate_verifications')
                    ->groupBy('hospital_id');
            })->orderBy('id', 'DESC');
            if (!empty($request->facility_name)) {
                $data->whereHas('hospital', function ($query) use ($request) {
                    $query->where('facility_name', 'like', '%'.$request->facility_name.'%');
                });
            }
                
            if (!empty($request->status)) {
                $data->where('status', $request->status); // Apply filter if status is provided
            }

            if (!empty($request->due_date)) {
                $data->where('due_date_of_physical_verification', $request->due_date);
            }
            
            
            return DataTables::of($data)
                ->addIndexColumn() // Adds a serial number column
                ->addColumn('hospital_id', function ($row) {
                    // Access the related facilityType name
                    return $row->hospital->hospital_id ? $row->hospital->hospital_id : '-';
                })
                ->addColumn('facility_name', function ($row) {
                    // Access the related facilityType name
                    return $row->hospital->facility_name ? $row->hospital->facility_name : '-';
                })
                ->addColumn('updated_at', function ($row) {
                    // Access the related facilityType name
                    return date('d-m-Y', strtotime($row->updated_at));
                })
                // ->addColumn('specialities', function ($row) {
                //     // Access the related facilityType name
                //     return "";
                // })
                ->addColumn('action', function ($row) {
                    $route = route('decverifier.gethospital', [base64_encode($row->hospital_id), base64_encode($row->hospital->uuid)]);
                    return '<a href="'.$route.'" class="btn btn-primary btn-sm">></a>';
                })
                ->rawColumns(['action']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
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
                ->rawColumns(['attachment'])
                ->make(true);
        }
    }

    public function loadStep(Request $request, $hospitalId, $uuid) {
        $validatedData = $request->validate([
            'step' => 'required',
        ]);
        $allstepcomplete = $this->checkallstepcompleteornot(base64_decode($hospitalId));
        // ->whereIn('id', function ($query) {
        //     $query->selectRaw('MAX(id)')
        //         ->from('initiate_verifications')
        //         ->groupBy('hospital_id');
        // })
        $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('physical_verifier', auth()->user()->id)->orderBy('id', 'DESC')->first();

        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        if($request->step == 1) {
            return view('decverifier._partials.basicinfo', compact('hospital', 'verification', 'allstepcomplete'));
        }  else if($request->step == 2) {
            return view('decverifier._partials.speciality', compact('hospital', 'verification', 'allstepcomplete'));
        } else if($request->step == 3) {
            $services =  Helpers::getCommanData('Service');
            return view('decverifier._partials.services', compact('hospital', 'verification', 'services'));
        } else if($request->step == 4) {
            $licenses =  Helpers::getCommanData('Licenses');
            return view('decverifier._partials.licenses', compact('hospital', 'verification', 'licenses'));
        } else if($request->step == 5) {
            return view('decverifier._partials.humanresource', compact('hospital', 'verification'));
        } else if($request->step == 6) {
            return view('decverifier._partials.accreditation', compact('hospital', 'verification'));
        } else if($request->step == 7) {
            return view('decverifier._partials.finance', compact('hospital', 'verification'));
        } else if($request->step == 8) {
            return view('decverifier._partials.document', compact('hospital', 'verification', 'allstepcomplete'));
        }
    }

    public function saveEstablishmentReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        if($hospital->dec_verify_status == "" && $hospital->dec_verify_remark == "") {
            $validatedData = $request->validate([
                'dec_verify_status' => 'required',
                'dec_verify_remark' => 'required'
            ]);
        }

        $hospital->dec_verify_status = $request->dec_verify_status;
        $hospital->dec_verify_remark = $request->dec_verify_remark;
        $hospital->dec_verify_id = auth()->user()->id;
        $hospital->save();
        Helpers::saveTabStatus(1, 'establishment_details', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'verifier');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'verifier');
        if($step1 && $step12) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }
        return response()->json(['success' => true, 'message' => 'Establishment Details Verified Successfully!!', 'isComplete' => $isComplete]); 

    }

    public function saveAddressReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $address = $hospital->hospitalAddress;
        if($address->dec_verify_status == "" && $address->dec_verify_remark == "") {
            $validatedData = $request->validate([
                'dec_verify_status' => 'required',
                'dec_verify_remark' => 'required'
            ]);
        }

        $address->dec_verify_status = $request->dec_verify_status;
        $address->dec_verify_remark = $request->dec_verify_remark;
        $address->dec_verify_id = auth()->user()->id;
        $address->save();

        Helpers::saveTabStatus(1, 'address', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'verifier');
        $step12 = Helpers::stepCheck(1, $hospital->id, 'address', 'verifier');
        if($step1 && $step12) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Address Details Verified Successfully!!', 'isComplete' => $isComplete]); 

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
                $rules['dec_verify_status_'.$value->id] = 'required';
                $rules['dec_verify_remark_'.$value->id] = 'nullable';
                $messages['dec_verify_status_'.$value->id] = 'The Status is required.';
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
            $value->dec_verify_status = $request->{'dec_verify_status_'.$value->id};
            $value->dec_verify_remark = $request->{'dec_verify_remark_'.$value->id};
            $value->dec_verify_id = auth()->user()->id;
            $value->save();
        }

        Helpers::saveTabStatus(2, 'speciality', $hospital->id, 'verifier');

        return response()->json(['success' => true, 'message' => 'Specialities Verified Successfully!!']); 
    }

    public function saveServicesReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        if($hospital) {
            $services =  Helpers::getCommanData('Service');

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
            // $check->services()->delete();
            foreach ($services as $key => $value) {
                if(sizeof($value->subServices) > 0) {
                    foreach ($value->subServices as $k => $v) {
                        $name = str_replace(' ', '-', strtolower($v->name));
                        if($request->{$value->id.'_'.$v->id.'_'.$name} == 0 || $request->{$value->id.'_'.$v->id.'_'.$name} == 1 || $request->{$value->id.'_'.$v->id.'_'.$name} != '') {
                            $isValid = 1;
                            $array = [                               
                                'dec_verify_service_value' => $request->{$value->id.'_'.$v->id.'_'.$name},
                                'dec_verify_text_value' => $request->{$value->id.'_'.$v->id.'_'.$name.'_text'},
                                'dec_verify_remark' => $request->{$value->id.'_'.$v->id.'_remark'}
                            ];

                            if ($request->hasFile($value->id.'_'.$v->id.'_'.$name.'_image')) {
                                $filePath = $request->file($value->id.'_'.$v->id.'_'.$name.'_image')->store('serviceimage', 'public'); 
                                $array['dec_verify_image'] = $filePath; // Add file path to data
                            }                 
                            
                            $array['dec_verify_id'] = auth()->user()->id;
    
                            $hospital->services()->updateOrCreate(['service_id' => $value->id, 'sub_service_id' => $v->id], $array);
                        }                        
                    }
                }
            }

            Helpers::saveTabStatus(3, 'services', $hospital->id, 'verifier');

            return response()->json(['success' => true, 'message' => 'Service Verified Successfully!!']);    
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
                $rules['dec_verify_status_'.$value->id] = 'required';
                $rules['dec_verify_remark_'.$value->id] = 'nullable';
                $messages['dec_verify_status_'.$value->id] = 'The Status is required.';
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
                $value->dec_verify_status = $request->{'dec_verify_status_'.$value->id};
                $value->dec_verify_remark = $request->{'dec_verify_remark_'.$value->id};
                $value->dec_verify_id = auth()->user()->id;
                $value->save();
            }
    
            Helpers::saveTabStatus(4, 'statutory_licences', $hospital->id, 'verifier');

            return response()->json(['success' => true, 'message' => 'Licenses Verified Successfully!!']);  
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
        if($ceo->dec_verify_status == "" && $ceo->dec_verify_remark == "") {
            $validatedData = $request->validate([
                'dec_verify_status' => 'required',
                'dec_verify_remark' => 'nullable'
            ]);
        }

        $ceo->dec_verify_status = $request->dec_verify_status;
        $ceo->dec_verify_remark = $request->dec_verify_remark;
        $ceo->dec_verify_id = auth()->user()->id;
        $ceo->save();
        Helpers::saveTabStatus(5, 'ceo', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'verifier');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'verifier');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'verifier');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'verifier');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'CEO Details Verified Successfully!!', 'isComplete' => $isComplete]); 

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
            $rules['dec_verify_status_'.$value->id] = 'required';
            $rules['dec_verify_remark_'.$value->id] = 'nullable';
            $messages['dec_verify_status_'.$value->id] = 'The Status is required.';
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
            $value->dec_verify_status = $request->{'dec_verify_status_'.$value->id};
            $value->dec_verify_remark = $request->{'dec_verify_remark_'.$value->id};
            $value->dec_verify_id = auth()->user()->id;
            $value->save();
        }

        Helpers::saveTabStatus(5, 'mhr', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'verifier');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'verifier');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'verifier');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'verifier');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Medical HumanResource Details Verified Successfully!!', 'isComplete' => $isComplete]); 

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
            $rules['dec_verify_status_'.$value->id] = 'required';
            $rules['dec_verify_remark_'.$value->id] = 'nullable';
            $messages['dec_verify_status_'.$value->id] = 'The Status is required.';
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
            $value->dec_verify_status = $request->{'dec_verify_status_'.$value->id};
            $value->dec_verify_remark = $request->{'dec_verify_remark_'.$value->id};
            $value->dec_verify_id = auth()->user()->id;
            $value->save();
        }

        Helpers::saveTabStatus(5, 'sshr', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'verifier');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'verifier');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'verifier');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'verifier');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Medical HumanResource Details Verified Successfully!!', 'isComplete' => $isComplete]); 
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
            $rules['dec_verify_status_'.$value->id] = 'required';
            $rules['dec_verify_remark_'.$value->id] = 'nullable';
            $messages['dec_verify_status_'.$value->id] = 'The Status is required.';
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
            $value->dec_verify_status = $request->{'dec_verify_status_'.$value->id};
            $value->dec_verify_remark = $request->{'dec_verify_remark_'.$value->id};
            $value->dec_verify_id = auth()->user()->id;
            $value->save();
        }

        Helpers::saveTabStatus(5, 'specialist', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(5, $hospital->id, 'ceo', 'verifier');
        $step12 = Helpers::stepCheck(5, $hospital->id, 'mhr', 'verifier');
        $step13 = Helpers::stepCheck(5, $hospital->id, 'sshr', 'verifier');
        $step14 = Helpers::stepCheck(5, $hospital->id, 'specialist', 'verifier');
        if($step1 && $step12 && $step13 && $step14) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }

        return response()->json(['success' => true, 'message' => 'Specialities Details Verified Successfully!!', 'isComplete' => $isComplete]); 
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
            $rules['dec_verify_status_'.$accreditation->id] = 'required';
            $rules['dec_verify_remark_'.$accreditation->id] = 'nullable';
            $messages['dec_verify_status_'.$accreditation->id] = 'The Status is required.';
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
            $accreditation->dec_verify_status = $request->{'dec_verify_status_'.$accreditation->id};
            $accreditation->dec_verify_remark = $request->{'dec_verify_remark_'.$accreditation->id};
            $accreditation->dec_verify_id = auth()->user()->id;
            $accreditation->save();
        // }

        Helpers::saveTabStatus(6, 'quality_accreditation', $hospital->id, 'verifier');

        return response()->json(['success' => true, 'message' => 'Accreditation Details Verified Successfully!!']); 
    }

    public function saveFinancialReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $finance = $hospital->financialInformation;
        $validatedData = $request->validate([
            'dec_verify_status' => 'required',
            'dec_verify_remark' => 'nullable'
        ]);

        $finance->dec_verify_status = $request->dec_verify_status;
        $finance->dec_verify_remark = $request->dec_verify_remark;
        $finance->dec_verify_id = auth()->user()->id;
        $finance->save();

        Helpers::saveTabStatus(7, 'finance_details', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'verifier');
        $step2 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'verifier');
        if($step1 && $step2) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }
        return response()->json(['success' => true, 'message' => 'Financial Information Verified Successfully!!', 'isComplete' => $isComplete]); 

    }

    public function saveTaxdetailsReview(Request $request, $hospitalId, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        if($hospital->is_upgrade_application == 1) {
            $hospital = UHospitals::where('main_hospitalid', $hospital->id)->first();
        }
        $taxDetails = $hospital->taxDetails;
        $validatedData = $request->validate([
            'dec_verify_status' => 'required',
            'dec_verify_remark' => 'nullable'
        ]);

        $taxDetails->dec_verify_status = $request->dec_verify_status;
        $taxDetails->dec_verify_remark = $request->dec_verify_remark;
        $taxDetails->dec_verify_id = auth()->user()->id;
        $taxDetails->save();

        Helpers::saveTabStatus(7, 'tax_details', $hospital->id, 'verifier');

        $step1 = Helpers::stepCheck(7, $hospital->id, 'finance_details', 'verifier');
        $step2 = Helpers::stepCheck(7, $hospital->id, 'tax_details', 'verifier');
        if($step1 && $step2) {
            $isComplete = true;
        } else {
            $isComplete = false;
        }
        return response()->json(['success' => true, 'message' => 'Tax Information Verified Successfully!!', 'isComplete' => $isComplete]); 

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
            $rules['dec_verify_status_'.$value->id] = 'required';
            $rules['dec_verify_remark_'.$value->id] = 'nullable';
            $messages['dec_verify_status_'.$value->id] = 'The Status is required.';
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
            $value->dec_verify_status = $request->{'dec_verify_status_'.$value->id};
            $value->dec_verify_remark = $request->{'dec_verify_remark_'.$value->id};
            $value->dec_verify_id = auth()->user()->id;
            $value->save();
        }

        return response()->json(['success' => true, 'message' => 'Documents Verified Successfully!!']); 
    }

    public function submitVerifierReport(Request $request, $hospitalId, $verifyId, $uuid) {
        $validatedData = $request->validate([
            'document_type' => 'required',
            'document' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->document_type === 'Audio') {
                        $mimes = ['mp3', 'mp4'];
                    } elseif ($request->document_type === 'Document') {
                        $mimes = ['pdf', 'docx'];
                    } elseif ($request->document_type === 'Video') {
                        $mimes = ['mp4'];
                    } else {
                        $mimes = [];
                    }
        
                    if (!in_array($value->getClientOriginalExtension(), $mimes)) {
                        $fail("The {$attribute} must be a file of type: " . implode(', ', $mimes));
                    }
                }
            ],
            'description' => 'nullable',
            'remark' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);
        
        $verification = InitiateVerification::where('hospital_id', base64_decode($hospitalId))->where('id', base64_decode($verifyId))->orderBy('id', 'DESC')->first();

        $hospital = Hospitals::where('id', base64_decode($hospitalId))->first();
        $validatedData['verifier_id'] = auth()->user()->id;

        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('document', 'public'); 
            $validatedData['document'] = $filePath; // Add file path to data
        }  
        
        if($hospital->is_upgrade_application == 1) {
            $hospital->hospitalReport()->where('hospital_id', $hospital->id)->delete();
        }
        $hospital->hospitalReport()->updateOrCreate(
            ['hospital_id' => $hospital->id, 'verifier_id' => auth()->user()->id],
            $validatedData
        );

        $verification->status = "Physical Verification Completed";
        $verification->is_approve = 1;
        $verification->save();

        $logarray = [
            'action' => 'Physical Verification Completed',
            'remark' => $validatedData['remark'],
            'created_by' => auth()->user()->id,
        ];

        if($validatedData['document']) {
            $logarray['attachment'] = $validatedData['document'];
        }

        $check = WorkFlowHistory::where(['hospital_id' => $hospital->id, 'action' => 'Physical Verification Completed', 'created_by' => auth()->user()->id])->orderBy('id', 'DESC')->first();

        if($check) {
            $check->remark = $validatedData['remark'];
            $check->attachment = $logarray['attachment'] ? $logarray['attachment'] : $check->attachment;
            $check->save();
            $id = $check->id;
        } else {
            $id = Helpers::addWorkflowForHospital($hospital, $logarray);
        }

        $userdata = User::find($verification->assigned_by);
        $data['hospital'] = $hospital;
        $data['remark'] = $logarray['remark'];        
        $data['message'] = "The Physical Officer (".auth()->user()->name.") has reviewed the submitted document for the hospital ".$hospital->facility_name."(".$hospital->hospital_id.") and, physical verification approved the submission. No further clarifications are required at this stage.";      
        $data['userdata'] = $userdata;
        $filePath = asset('public/storage/'.$logarray['attachment'] ); // Path to your document
        $data['filePath'] = $filePath;

        try {
            Mail::to($userdata->email)->send(new StatusMail($data));
        } catch (\Exception $e) {
            
        }
        $route = route('decverifier.dashboard');
        return response()->json(['success' => true, 'message' => 'Report submitted successfully by physical verifier!!', 'url' => $route]);
    }
}
