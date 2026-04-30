<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\PathologyUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class PathologyUnitController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pathology-unit', ['only' => ['store']]);
        $this->middleware('permission:edit-pathology-unit', ['only' => ['update']]);
        $this->middleware('permission:delete-pathology-unit', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pathology.unit.destroy', ['unit' => '__UNIT__']),
            'store'     => route('hospital.settings.pathology.unit.store'),
            'loadtable' => route('hospital.settings.pathology.unit-load'),
            'showform'  => route('hospital.settings.pathology.unit.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pathology.unit.index', [
            'pathurl' => 'pathology-unit',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PathologyUnit::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pathology.unit.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = PathologyUnit::where('id', $id)->first();
        }
        return view('hospital.settings.pathology.unit.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:pathology_units,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        PathologyUnit::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Pathology Unit updated successfully.' : 'Pathology Unit created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PathologyUnit $unit)
    {
        if ($unit->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $unit->delete();
        return response()->json(['status' => true, 'message' => 'Pathology Unit deleted successfully.']);
    }
}
