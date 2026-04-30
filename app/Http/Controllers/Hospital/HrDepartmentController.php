<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrDepartment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class HrDepartmentController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-hr-department', ['only' => ['store']]);
        $this->middleware('permission:edit-hr-department', ['only' => ['update']]);
        $this->middleware('permission:delete-hr-department', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.hr.department.destroy', ['department' => '__DEPARTMENT__']),
            'store'     => route('hospital.settings.hr.department.store'),
            'loadtable' => route('hospital.settings.hr.department-load'),
            'showform'  => route('hospital.settings.hr.department.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.hr.department.index', [
            'pathurl' => 'hr-department',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = HrDepartment::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.hr.department.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = HrDepartment::where('id', $id)->first();
        }
        return view('hospital.settings.hr.department.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:hr_departments,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        HrDepartment::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
                'charge' => $request->charge ?? 0,
            ]
        );

        $msg = $request->id ? 'Department updated successfully.' : 'Department created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(HrDepartment $department)
    {
        if ($department->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $department->delete();
        return response()->json(['status' => true, 'message' => 'Department deleted successfully.']);
    }
}
