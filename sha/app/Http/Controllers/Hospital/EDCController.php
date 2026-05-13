<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\{ Hospitals, HospitalState, EDCAction, EDCWorkFlow, EDCWorkDocument};
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
        $generalcommunication = EDCAction::whereIn('last_action', ['Initiate General Communication', "Responded on General Communication", "SEC Responded On General Communication"])->where('hospital_user', auth()->user()->id)->count();
        $shaucause = EDCAction::whereIn('last_action', ['Initiate Show Cause Notice', "Responded on Show Cause Notice"])->where('hospital_user', auth()->user()->id)->count();
        $blacklist = EDCAction::whereIn('last_action', ['Initiate Blacklist'])->where('hospital_user', auth()->user()->id)->count();
        $revoked = EDCAction::whereIn('last_action', ["Revoke Blacklist", "Revoke Suspension"])->where('hospital_user', auth()->user()->id)->count();
        $stopPayment = EDCAction::where('is_stop_payment', 1)->where('hospital_user', auth()->user()->id)->count();
        $stopPreauth = EDCAction::where('is_stop_preauth', 1)->where('hospital_user', auth()->user()->id)->count();
        $suspendfacility = EDCAction::whereIn('last_action', ["Initiate Immediate Suspension", "Responded on Immediate Suspension"])->where('hospital_user', auth()->user()->id)->count();
        $deempanelled = EDCAction::where('last_action', "De-Empanelled")->where('hospital_user', auth()->user()->id)->count();
        $fir = EDCAction::where('last_action', "FIR")->where('hospital_user', auth()->user()->id)->count();
        $penalty = EDCAction::where('last_action', "Initiate Penalty")->where('hospital_user', auth()->user()->id)->count();

        return view('hospital.edc.index', compact('generalcommunication', 'shaucause', 'blacklist', 'revoked', 'stopPayment', 'stopPreauth', 'suspendfacility', 'deempanelled', 'fir', 'penalty'));
    }

    public function loadedcactiondata(Request $request) {
        if ($request->ajax()) {
            $data = EDCAction::with('hospital', 'workflow')->orderBy('id', 'DESC');

            $data->where('hospital_user', auth()->user()->id);

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
                    $route = route('hospital.viewAction', [base64_encode($row->id)]);
                    return '<a href="'.$route.'" >Details</a>';
                })
                ->rawColumns(['action']) // Indicates that the 'action' column contains raw HTML
                ->make(true);
        }
    }

    public function viewAction(Request $request, $actionid) {
        $action = EDCAction::where('id', base64_decode($actionid))->first();
        $hospital = Hospitals::where('id', $action->hospital_id)->first();
        return view('hospital.edc.viewaction', compact('hospital', 'action'));
    }

    public function saveinitiateAction(Request $request, $id, $uuid) {
        $hospital = Hospitals::where('id', base64_decode($id))->first();
        if($hospital) {

            $validatedData = $request->validate([
                'edc_action' => 'required',
                'days' => 'nullable|integer',
                // 'action_start_date' => 'nullable|date',
                // 'action_end_date' => 'nullable|date',
                // "date_of_issuance" => 'nullable|required_if:edc_action,FIR|date',
                // "fir_case_number" => 'nullable|required_if:edc_action,FIR',
                // "penalty_imposed" => 'nullable|required_if:edc_action,Initiate Penalty',
                // "penalty_recovered" => 'nullable|required_if:edc_action,Initiate Penalty',
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
            
            return response()->json(['success' => true, "message" => "Action Performed Successfully!!"]);
        } else {
            return response()->json(['success' => false, "message" => "Hospital Not Found!!"]);
        }
    }

    public function updateinitiateAction(Request $request, $actionid, $uuid) {

        $validatedData = $request->validate([
            'edc_action' => 'required',
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


        $actionId = base64_decode($actionid);

        $action = EDCAction::where('id', $actionId)->first();
        $action->last_action = $request->edc_action;
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
            "authority" => "Hospital Admin",
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

        $getdocument = EDCWorkDocument::where('action_id', $action->id)->where('work_flow_id', $workdata->id)->orderBy('id', 'DESC')->first();
       
        $userdata = User::find($action->added_by);
        $data = new \stdClass();
        $data->userdata = $userdata;
        $data->message = "The hospital ".$hospital->facility_name."(".$hospital->hospital_id.") has replay to action. please check ".$userdata->name.".";

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
        
        return response()->json(['success' => true, "message" => "Responded Successfully!!"]);
    }
}
