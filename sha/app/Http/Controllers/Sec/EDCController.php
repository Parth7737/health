<?php

namespace App\Http\Controllers\Sec;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\{ Hospitals, HospitalState, EDCAction, EDCWorkFlow, EDCWorkDocument, User};
use DataTables;
use App\Mail\EDCActionMail;
use Mail;

class EDCController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        $state_name = auth()->user()->parent_entity;
        $generalcommunication = EDCAction::whereIn('last_action', ['Initiate General Communication', "Responded on General Communication", "SEC Responded On General Communication"])->count();
        $shaucause = EDCAction::whereIn('last_action', ['Initiate Show Cause Notice', "Responded on Show Cause Notice"])->count();
        $blacklist = EDCAction::whereIn('last_action', ['Initiate Blacklist'])->count();
        $revoked = EDCAction::whereIn('last_action', ["Revoke Blacklist", "Revoke Suspension"])->count();
        $stopPayment = EDCAction::where('is_stop_payment', 1)->count();
        $stopPreauth= EDCAction::where('is_stop_preauth', 1)->count();
        $suspendfacility = EDCAction::whereIn('last_action', ["Initiate Immediate Suspension", "Responded on Immediate Suspension"])->count();
        $deempanelled = EDCAction::where('last_action', "De-Empanelled")->count();
        $fir = EDCAction::where('last_action', "FIR")->count();
        $penalty = EDCAction::where('last_action', "Initiate Penalty")->count();
        return view('sec.edc.index', compact('generalcommunication', 'shaucause', 'blacklist', 'revoked', 'stopPayment', 'stopPreauth', 'suspendfacility', 'deempanelled', 'fir', 'penalty'));
    }

    public function loadedcactiondata(Request $request) {
        if ($request->ajax()) {
            $data = EDCAction::with('hospital', 'workflow')->orderBy('id', 'DESC');

            $data->where('added_by' , auth()->user()->id);

            if($request->status) {
                $status = $request->status;
                if($status == "Initiate General Communication") {
                    $data->whereIn('last_action', [$request->status, "Responded on General Communication", "SEC Responded On General Communication"]);
                } else if($status == "Initiate Show Cause Notice") {
                    $data->whereIn('last_action', [$request->status, "Responded on Show Cause Notice"]);
                } else if($status == "Revoked") {
                    $data->whereIn('last_action', ["Revoke Blacklist", "Revoke Suspension"]);
                } else if($status == "Stop Payment") { 
                    $data->where('is_stop_payment', 1);
                } else if($status == "Stop Preauth") { 
                    $data->where('is_stop_preauth', 1);
                } else if($status == "Suspended Facility") {
                    $data->whereIn('last_action', ["Initiate Immediate Suspension", "Responded on Immediate Suspension"]);
                } else {
                    $data->where('last_action', $request->status);
                }
            }

            return DataTables::of($data)
                ->addIndexColumn() // Adds a serial number column
                ->addColumn('facility_ownership_type', function ($row) {
                    // Access the related facilityType name
                    return $row->hospital->facilityOwnershipType->name ?? 'N/A';
                })
                ->addColumn('district', function ($row) {
                    // Access the related facilityType name
                    return $row->hospital->hospitalAddress->districts->name ?? 'N/A';
                })
                ->addColumn('date_of_issuance', function ($row) {
                    // Access the related facilityType name
                    return $row->workflow()->orderBy('id', 'DESC')->limit(1)->first()->date_of_issuance ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $route = route('sec.viewAction', [base64_encode($row->id)]);
                    return '<a href="'.$route.'" >Details</a>';
                })
                ->rawColumns(['action']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }

    public function viewAction(Request $request, $actionid) {
        $action = EDCAction::where('id', base64_decode($actionid))->first();
        $hospital = Hospitals::where('id', $action->hospital_id)->first();
        return view('sec.edc.viewaction', compact('hospital', 'action'));
    }

    public function hospitallist(Request $request)
    {
        $state_name = auth()->user()->parent_entity;
        $state = HospitalState::where('name', $state_name)->first();
        $state_id = '';
        if($state) {
            $state_id = $state->id;
        } else {
            $state_id = 1;
        }

        if ($request->ajax()) {
            $data = Hospitals::with('hospitalAddress')->whereHas('hospitalAddress', function ($query) use ($state_id, $request) {
                $query->where('state', $state_id);

                if ($request->district) {
                    $query->where('district', $request->district);
                }

            })->select([
                'id',
                'uuid',
                'hospital_id',
                'facility_name',
                'facility_ownership_type',
                'scheme',
                'status',
            ])->where('is_empanelled', 1)->orderBy('id', 'DESC');

            if(auth()->user()->role_id == 2) {
                $data->where('user_id' , auth()->user()->id);
            }

            if($request->ownership_type) {
                $data->where('facility_ownership_type' , $request->ownership_type);
            }

            return DataTables::of($data)
                ->addIndexColumn() // Adds a serial number column
                ->addColumn('ownership_type', function ($row) {
                    // Access the related facilityType name
                    return $row->facilityOwnershipType->name ?? 'N/A';
                })
                ->addColumn('district_name', function ($row) {
                    // Access the related facilityType name
                    return $row->hospitalAddress->districts->name ?? 'N/A';
                })
                ->addColumn('mobile_no', function ($row) {
                    // Access the related facilityType name
                    return $row->hospitalAddress->mobile_no ?? 'N/A';
                })
                ->addColumn('specialities', function ($row) {
                    $spec = '';
                    $specialities = $row->specialities()->where('available', 1)->get();
                    foreach ($specialities as $key => $value) {
                        if($spec == '') {
                            $spec .= $value->speciality->code;
                        } else {
                            $spec .= ', '.$value->speciality->code;
                        }
                    }
                    return $spec;
                })
                ->addColumn('hospital_id', function ($row) {
                    $route = route('sec.initiate.action', [base64_encode($row->id), base64_encode($row->uuid)]);
                    return '<a href="'.$route.'" >'.$row->hospital_id.'</a>';
                })
                ->rawColumns(['hospital_id']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }

    public function initiateEDC(Request $request) {

        $state_name = auth()->user()->parent_entity;
        $state = HospitalState::where('name', $state_name)->first();
        $districts = $state->districts;
        $ownershiptype = Helpers::getCommanData('FacilityOwnershipType');
        return view('sec.edc.initiatelist', compact('districts', 'ownershiptype'));                 
    }

    public function initiateAction(Request $request, $id, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($id))->first();
        return view('sec.edc.actionform', compact('hospital'));
    }

    public function saveinitiateAction(Request $request, $id, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($id))->first();
        if($hospital) {
            if(EDCAction::where(["hospital_id" => $hospital->id, "is_close_action" => 0])->exists()) {
                return response()->json(['success' => false, "message" => "Already one action is open"]);
            }
            $validatedData = $request->validate([
                'edc_action' => 'required',
                'days' => 'nullable|integer',
                'action_start_date' => 'nullable|date',
                'action_end_date' => 'nullable|date',
                "date_of_issuance" => 'nullable|required_if:edc_action,FIR|date',
                "fir_case_number" => 'nullable|required_if:edc_action,FIR',
                "penalty_imposed" => 'nullable|required_if:edc_action,Initiate Penalty',
                "penalty_recovered" => 'nullable|required_if:edc_action,Initiate Penalty',
                "remark" => "required",
                "document_type" => "required",
                "document" => "required|mimes:pdf|max:10240",
                "description" => "required"
            ]);

            $edcdata = EDCAction::updateOrCreate([
                'hospital_id' => $hospital->id,
                'is_close_action' => 0,
            ],[
                'hospital_id' => $hospital->id,
                'hospital_user' => $hospital->user_id,
                'is_close_action' => 0,
                "order_id" => rand(00000, 99999),
                "last_action" => $request->edc_action,
                "status" => "Initiate",
                "main_status" => $request->edc_action,
                "submission_date" => date('Y-m-d'),
                "added_by" => auth()->user()->id,
                "is_stop_payment" => $request->is_stop_payment ? $request->is_stop_payment : 0,
                "is_stop_preauth" => $request->is_stop_preauth ? $request->is_stop_preauth : 0 
            ]);

            $workflowdetails = [
                "action_id" => $edcdata['id'],
                "action" => $request->edc_action,
                "remark" => $request->remark,
                "date_of_issuance" => $request->date_of_issuance ? $request->date_of_issuance : null,
                "action_start_date" => $request->action_start_date ? $request->action_start_date : null,
                "action_end_date" => $request->action_end_date ? $request->action_end_date : null,
                "days" => $request->days,
                "fir_case_number" => $request->fir_case_number,
                "penalty_imposed" => $request->penalty_imposed,
                "penalty_recovered" => $request->penalty_recovered,
                "added_by" => auth()->user()->id,
                "authority" => "SEC Officer",
                "submission_date" => $edcdata->submission_date,
            ];

            $workdata = EDCWorkFlow::create($workflowdetails);

            if($request->document) {
                if ($request->hasFile('document')) {
                    $filePath = $request->file('document')->store('edcdocument', 'public'); 
                    $documentdetails['document'] = $filePath;
                    $documentdetails['description'] = $request->description;
                    $documentdetails['document_type'] = $request->document_type;
                    $documentdetails['action_id'] = $workflowdetails['action_id'];
                    $documentdetails['work_flow_id'] = $workdata->id;

                    EDCWorkDocument::create($documentdetails);
                } 
            }

            if($request->is_stop_preauth || $request->is_stop_payment) {
                $hospital->is_preauth_stop = $request->is_stop_preauth ? $request->is_stop_preauth : 0;;
                $hospital->is_payment_stop = $request->is_stop_payment ? $request->is_stop_payment : 0;;
                $hospital->save();
            }

            if($request->edc_action == "De-Empanelled" || $request->edc_action == "FIR") {
                $hospital->status = "De-Empanelled";
                $hospital->is_empanelled = 4;
                $hospital->is_preauth_stop = 1;
                $hospital->is_payment_stop = 1;
                $hospital->save();

                $action = EDCAction::where('id', $workflowdetails['action_id'])->first();
                $action->is_close_action = 1;
                $action->status = "Approved";
                $action->is_stop_payment = 1;
                $action->is_stop_preauth = 1;
                $action->save();
            }

            if($request->edc_action == "Initiate Immediate Suspension") {
                $hospital->status = "In-Active";
                $hospital->is_empanelled = 5;
                $hospital->save();
            }

            if($request->edc_action == "Initiate Penalty") {
                // $hospital->status = "In-Active";
                // $hospital->is_empanelled = 5;
                // $hospital->save();

                $action = EDCAction::where('id', $workflowdetails['action_id'])->first();
                $action->is_close_action = 1;
                $action->status = "Approved";
                $action->save();
            }

            if($request->edc_action == "Watch List") {
                $action = EDCAction::where('id', $workflowdetails['action_id'])->first();
                $action->is_close_action = 1;
                $action->status = "Approved";
                $action->save();
            }

            // Mail Code
            $getdocument = EDCWorkDocument::where('action_id', $workflowdetails['action_id'])->where('work_flow_id', $workdata->id)->orderBy('id', 'DESC')->first();
       
            $userdata = User::find($hospital->user_id);
            $data = new \stdClass();
            $data->userdata = $userdata;
            $data->message = "The SEC officer (".auth()->user()->name.") has actioned for the hospital ".$hospital->facility_name."(".$hospital->hospital_id."). Please review the action.";
    
            if($getdocument) {
                $filePath = asset('public/storage/'.$getdocument->document ); // Path to your document
                $data->filePath = $filePath;
            } else {
                $data->filePath = "";
            }
    
            $data->hospital = $hospital;
            $data->remark = $request->remark;
            $data->action = $request->edc_action;
            $data->actionData = EDCAction::where('id', $workflowdetails['action_id'])->first();
            try {
                Mail::to($userdata->email)->send(new EDCActionMail($data));
            } catch (\Exception $e) {
                
            }
            return response()->json(['success' => true, "message" => "Action Performed Successfully!!"]);
        } else {
            return response()->json(['success' => false, "message" => "Hospital Not Found!!"]);
        }
    }

    public function updateinitiateAction(Request $request, $actionid, $uuid) {

        $validatedData = $request->validate([
            'edc_action' => 'required',
            // 'action_start_date' => 'nullable|date',
            // 'action_end_date' => 'nullable|date',
            // "date_of_issuance" => 'nullable|required_if:edc_action,FIR|date',
            // "fir_case_number" => 'nullable|required_if:edc_action,FIR',
            // "penalty_imposed" => 'nullable|required_if:edc_action,Initiate Penalty',
            // "penalty_recovered" => 'nullable|required_if:edc_action,Initiate Penalty',
            "remark" => "required",
            "document_type" => $request->edc_action == "Close The Matter" || $request->edc_action == "Revoke Blacklist" ? "nullable" : "required",
            "document" => $request->edc_action == "Close The Matter" || $request->edc_action == "Revoke Blacklist" ? "nullable|mimes:pdf|max:10240" : "required|mimes:pdf|max:10240",
            "description" => $request->edc_action == "Close The Matter" || $request->edc_action == "Revoke Blacklist" ? "nullable" : "required"
        ]);


        $actionId = base64_decode($actionid);

        $action = EDCAction::where('id', $actionId)->first();
        $action->last_action = $request->edc_action;

        $action->is_stop_preauth = $request->is_stop_preauth ? $request->is_stop_preauth : 0;
        $action->is_stop_payment = $request->is_stop_payment ? $request->is_stop_payment : 0;

        $action->is_close_action = $request->is_close_action ? $request->is_close_action : 0;
        if($request->is_close_action) {
            $action->status = 'Approved';
        }
        $action->save();

        
        $workflowdetails = [
            "action_id" => $action->id,
            "action" => $request->edc_action,
            "remark" => $request->remark,
            "date_of_issuance" => $request->date_of_issuance ? $request->date_of_issuance : null,
            "action_start_date" => $request->action_start_date ? $request->action_start_date : null,
            "action_end_date" => $request->action_end_date ? $request->action_end_date : null,
            "days" => $request->days,
            "fir_case_number" => $request->fir_case_number,
            "penalty_imposed" => $request->penalty_imposed,
            "penalty_recovered" => $request->penalty_recovered,
            "added_by" => auth()->user()->id,
            "authority" => "SEC Officer",
            "submission_date" => date('Y-m-d'),
        ];

        $workdata = EDCWorkFlow::create($workflowdetails);

        if($request->document) {
            if ($request->hasFile('document')) {
                $filePath = $request->file('document')->store('edcdocument', 'public'); 
                $documentdetails['document'] = $filePath;
                $documentdetails['description'] = $request->description;
                $documentdetails['document_type'] = $request->document_type;
                $documentdetails['action_id'] = $action->id;
                $documentdetails['work_flow_id'] = $workdata->id;

                EDCWorkDocument::create($documentdetails);
            } 
        }

        $hospital = Hospitals::where('id', $action->hospital_id)->first();


        $hospital->is_preauth_stop = $request->is_stop_preauth ? $request->is_stop_preauth : 0;
        $hospital->is_payment_stop = $request->is_stop_payment ? $request->is_stop_payment : 0;
        $hospital->save();
        
        if($request->edc_action == "Initiate Blacklist") {
            // $hospital->status = "In-Active";
            // $hospital->is_empanelled = 5;
            // $hospital->save();
        }

        if($request->edc_action == "Revoke Suspension") {
            $hospital->status = "Empanelled";
            $hospital->is_empanelled = 1;
            $action->is_stop_payment = 0;
            $action->is_stop_preauth = 0;
            $hospital->save();
        }

        if($request->edc_action == "De-Empanelled") {
            $hospital->status = "De-Empanelled";
            $hospital->is_empanelled = 4;
            $hospital->is_preauth_stop = 1;
            $hospital->is_payment_stop = 1;
            $hospital->save();

            $action->is_close_action = 1;
            $action->status = "Approved";
            $action->is_stop_payment = 1;
            $action->is_stop_preauth = 1;
            $action->save();
        }

        if($request->edc_action == "Close The Matter" || $request->edc_action == 'Revoke Blacklist' || $request->edc_action == "Revoke Suspension") {
            $action->is_close_action = 1;
            $action->status = 'Approved';
            $action->save();
        }

        if($request->edc_action == 'Revoke Blacklist') {
            // $hospital->status = "Empanelled";
            // $hospital->is_empanelled = 1;
            // $hospital->save();
        }
        
        $getdocument = EDCWorkDocument::where('action_id', $action->id)->where('work_flow_id', $workdata->id)->orderBy('id', 'DESC')->first();
       
        $userdata = User::find($hospital->user_id);
        $data = new \stdClass();
        $data->userdata = $userdata;
        $data->message = "The SEC officer (".auth()->user()->name.") has actioned for the hospital ".$hospital->facility_name."(".$hospital->hospital_id."). Please review the action.";

        if($getdocument) {
            $filePath = asset('public/storage/'.$getdocument->document ); // Path to your document
            $data->filePath = $filePath;
        } else {
            $data->filePath = "";
        }

        $data->hospital = $hospital;
        $data->remark = $request->remark;
        $data->action = $request->edc_action;
        $data->actionData = EDCAction::where('id', $actionId)->first();
        
        try {
            Mail::to($userdata->email)->send(new EDCActionMail($data));
        } catch (\Exception $e) {
            
        }
        //     $hospital->is_preauth_stop = 0;
        //     $hospital->is_payment_stop = 0;
        //     $hospital->save();
            
        //     $action->is_stop_payment = 0;
        //     $action->is_stop_preauth = 0;
        //     $action->save();
        // }

        return response()->json(['success' => true, "message" => "Responded Successfully!!"]);
    }
}
