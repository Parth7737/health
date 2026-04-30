<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\VisitorPurpose;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class VisitorPurposeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-visitor-purposes', ['only' => ['store']]);
        $this->middleware('permission:edit-visitor-purposes', ['only' => ['update']]);
        $this->middleware('permission:delete-visitor-purposes', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.front-office.visitor-purposes.destroy', ['visitor_purpose' => '__VISITOR_PURPOSE__']),            
            'store'   => route('hospital.settings.front-office.visitor-purposes.store'),   
            'loadtable'   => route('hospital.settings.front-office.visitor-purposes-load'),
            'showform'   => route('hospital.settings.front-office.visitor-purposes.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.settings.front-office.visitor-purpose.index', ['pathurl' => 'visitor-purpose', 'routes' => $this->routes]);
    }

    /**
     * Load data for DataTable
     */
    public function loaddata(Request $request)
    {
        $data = VisitorPurpose::select('*');
        
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.front-office.visitor-purpose.partials.actions', compact('row'))->render();
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
            $data = VisitorPurpose::where('id', $id)->first();
        }
        return view('hospital.settings.front-office.visitor-purpose.form', compact('data', 'id'));
    }

    /**
     * Store a newly created or update resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:visitor_purposes,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
            'status' => 'required|in:active,inactive',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            VisitorPurpose::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id' => $this->hospital_id,
                    'name' => $request->name,
                    'status' => $request->status,
                ]
            );

            $msg = $request->id ? 'Visitor Purpose updated successfully.' : 'Visitor Purpose created successfully.';
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while saving the data.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisitorPurpose $visitor_purpose)
    {
        try {
            if ($visitor_purpose->hospital_id != $this->hospital_id) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }
            
            $visitor_purpose->delete();
            return response()->json(['status' => true, 'message' => 'Visitor Purpose deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the data.'], 500);
        }
    }
}
