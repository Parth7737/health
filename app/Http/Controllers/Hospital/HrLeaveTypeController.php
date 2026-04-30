<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrLeaveType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class HrLeaveTypeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-hr-leave-type', ['only' => ['store']]);
        $this->middleware('permission:edit-hr-leave-type', ['only' => ['update']]);
        $this->middleware('permission:delete-hr-leave-type', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.hr.leave-type.destroy', ['leave_type' => '__TYPE__']),
            'store'     => route('hospital.settings.hr.leave-type.store'),
            'loadtable' => route('hospital.settings.hr.leave-type-load'),
            'showform'  => route('hospital.settings.hr.leave-type.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.hr.leave-type.index', [
            'pathurl' => 'hr-leave-type',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = HrLeaveType::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.hr.leave-type.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = HrLeaveType::where('id', $id)->first();
        }
        return view('hospital.settings.hr.leave-type.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:hr_leave_types,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        HrLeaveType::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Leave Type updated successfully.' : 'Leave Type created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(HrLeaveType $leave_type)
    {
        if ($leave_type->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $leave_type->delete();
        return response()->json(['status' => true, 'message' => 'Leave Type deleted successfully.']);
    }
}
