<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrDepartmentUnit;
use App\Models\HrDepartment;
use App\Models\Floor;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class HrDepartmentUnitController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-hr-department-unit', ['only' => ['store']]);
        $this->middleware('permission:edit-hr-department-unit', ['only' => ['update']]);
        $this->middleware('permission:delete-hr-department-unit', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.hr.department-unit.destroy', ['department_unit' => '__DEPARTMENT_UNIT__']),
            'store'     => route('hospital.settings.hr.department-unit.store'),
            'loadtable' => route('hospital.settings.hr.department-unit-load'),
            'showform'  => route('hospital.settings.hr.department-unit.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.hr.department-unit.index', [
            'pathurl' => 'hr-department-unit',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = HrDepartmentUnit::select('*')->with('department', 'floor', 'unitIncharge');
        return DataTables::of($data)
            ->addColumn('department_name', function ($row) {
                return $row->department?->name ?? '';
            })
            ->addColumn('floor_name', function ($row) {
                return $row->floor?->building?->building_name . ' - ' . $row->floor?->name ?? '';
            })
            ->addColumn('unit_incharge_name', function ($row) {
                return $row->unitIncharge?->full_name ?? '';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.hr.department-unit.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = HrDepartmentUnit::where('id', $id)->where('hospital_id', $this->hospital_id)->first();
        }
        $departments = HrDepartment::where('hospital_id', $this->hospital_id)->get();
        $floors = Floor::where('hospital_id', $this->hospital_id)->get();
        $staff = Staff::where('hospital_id', $this->hospital_id)->get();
        return view('hospital.settings.hr.department-unit.form', compact('data', 'id', 'departments', 'floors', 'staff'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|exists:hr_departments,id',
            'floor_id' => 'nullable|exists:floors,id',
            'unit_incharge_id' => 'nullable|exists:staff,id',
            'name' => 'required|string|max:255',
            'is_video_consultation' => 'required|in:Yes,No',
            'daily_capacity' => 'nullable|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->name && $request->department_id) {
                $exists = HrDepartmentUnit::where('hospital_id', $this->hospital_id)
                    ->where('hr_department_id', $request->department_id)
                    ->where('name', $request->name)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })->exists();
                if ($exists) {
                    $validator->errors()->add('name', 'Department Unit with this name already exists for the selected department.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        HrDepartmentUnit::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'hr_department_id' => $request->department_id,
                'floor_id' => $request->floor_id,
                'unit_incharge_id' => $request->unit_incharge_id,
                'name' => $request->name,
                'is_video_consultation' => $request->is_video_consultation,
                'daily_capacity' => $request->daily_capacity,
            ]
        );

        $msg = $request->id ? 'Department Unit updated successfully.' : 'Department Unit created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(HrDepartmentUnit $department_unit)
    {
        if ($department_unit->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $department_unit->delete();
        return response()->json(['status' => true, 'message' => 'Department Unit deleted successfully.']);
    }
    
    /**
     * Return units for a given department (AJAX)
     */
    public function loadUnits(Request $request)
    {
        $units = HrDepartmentUnit::where('hr_department_id', $request->hr_department_id)
                ->get(['id','name']);
        return response()->json($units);
    }

}