<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrSpecialist;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class HrSpecialistController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-hr-specialist', ['only' => ['store']]);
        $this->middleware('permission:edit-hr-specialist', ['only' => ['update']]);
        $this->middleware('permission:delete-hr-specialist', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.hr.specialist.destroy', ['specialist' => '__SPECIALIST__']),
            'store'     => route('hospital.settings.hr.specialist.store'),
            'loadtable' => route('hospital.settings.hr.specialist-load'),
            'showform'  => route('hospital.settings.hr.specialist.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.hr.specialist.index', [
            'pathurl' => 'hr-specialist',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = HrSpecialist::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.hr.specialist.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = HrSpecialist::where('id', $id)->first();
        }
        return view('hospital.settings.hr.specialist.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:hr_specialists,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        HrSpecialist::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Specialist updated successfully.' : 'Specialist created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(HrSpecialist $specialist)
    {
        if ($specialist->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $specialist->delete();
        return response()->json(['status' => true, 'message' => 'Specialist deleted successfully.']);
    }
}
