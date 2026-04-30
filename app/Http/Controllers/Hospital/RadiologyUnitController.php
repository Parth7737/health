<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\RadiologyUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class RadiologyUnitController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-radiology-unit', ['only' => ['store']]);
        $this->middleware('permission:edit-radiology-unit', ['only' => ['update']]);
        $this->middleware('permission:delete-radiology-unit', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.radiology.unit.destroy', ['unit' => '__UNIT__']),
            'store'     => route('hospital.settings.radiology.unit.store'),
            'loadtable' => route('hospital.settings.radiology.unit-load'),
            'showform'  => route('hospital.settings.radiology.unit.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.radiology.unit.index', [
            'pathurl' => 'radiology-unit',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = RadiologyUnit::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.radiology.unit.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = RadiologyUnit::where('id', $id)->first();
        }
        return view('hospital.settings.radiology.unit.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:radiology_units,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        RadiologyUnit::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Radiology Unit updated successfully.' : 'Radiology Unit created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(RadiologyUnit $unit)
    {
        if ($unit->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $unit->delete();
        return response()->json(['status' => true, 'message' => 'Radiology Unit deleted successfully.']);
    }
}
