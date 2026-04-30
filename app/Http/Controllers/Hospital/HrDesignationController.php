<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrDesignation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class HrDesignationController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-hr-designation', ['only' => ['store']]);
        $this->middleware('permission:edit-hr-designation', ['only' => ['update']]);
        $this->middleware('permission:delete-hr-designation', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.hr.designation.destroy', ['designation' => '__DESIGNATION__']),
            'store'     => route('hospital.settings.hr.designation.store'),
            'loadtable' => route('hospital.settings.hr.designation-load'),
            'showform'  => route('hospital.settings.hr.designation.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.hr.designation.index', [
            'pathurl' => 'hr-designation',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = HrDesignation::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.hr.designation.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = HrDesignation::where('id', $id)->first();
        }
        return view('hospital.settings.hr.designation.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:hr_designations,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        HrDesignation::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Designation updated successfully.' : 'Designation created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(HrDesignation $designation)
    {
        if ($designation->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $designation->delete();
        return response()->json(['status' => true, 'message' => 'Designation deleted successfully.']);
    }
}
