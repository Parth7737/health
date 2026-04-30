<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\AppointmentPriority;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class AppointmentPriorityController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-appointment-priorities', ['only' => ['store']]);
        $this->middleware('permission:edit-appointment-priorities', ['only' => ['update']]);
        $this->middleware('permission:delete-appointment-priorities', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.front-office.appointment-priorities.destroy', ['appointment_priority' => '__APPOINTMENT_PRIORITY__']),            
            'store'   => route('hospital.settings.front-office.appointment-priorities.store'),   
            'loadtable'   => route('hospital.settings.front-office.appointment-priorities-load'),
            'showform'   => route('hospital.settings.front-office.appointment-priorities.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.settings.front-office.appointment-priority.index', ['pathurl' => 'appointment-priority', 'routes' => $this->routes]);
    }

    /**
     * Load data for DataTable
     */
    public function loaddata(Request $request)
    {
        $data = AppointmentPriority::select('*');
        
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.front-office.appointment-priority.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Show the form for creating or editing a resource.
     */
    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = AppointmentPriority::where('id', $id)->first();
        }
        return view('hospital.settings.front-office.appointment-priority.form', compact('data', 'id'));
    }

    /**
     * Store a newly created or update resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:appointment_priorities,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            AppointmentPriority::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id' => $this->hospital_id,
                    'name' => $request->name,
                ]
            );

            $msg = $request->id ? 'Appointment Priority updated successfully.' : 'Appointment Priority created successfully.';
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while saving the data.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppointmentPriority $appointment_priority)
    {
        try {
            if ($appointment_priority->hospital_id != $this->hospital_id) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }
            
            $appointment_priority->delete();
            return response()->json(['status' => true, 'message' => 'Appointment Priority deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the data.'], 500);
        }
    }
}
