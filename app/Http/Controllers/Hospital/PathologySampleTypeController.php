<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\PathologySampleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PathologySampleTypeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pathology-test', ['only' => ['store']]);
        $this->middleware('permission:edit-pathology-test', ['only' => ['update']]);
        $this->middleware('permission:delete-pathology-test', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.pathology.sample-type.destroy', ['sample_type' => '__SAMPLE_TYPE__']),
            'store' => route('hospital.settings.pathology.sample-type.store'),
            'loadtable' => route('hospital.settings.pathology.sample-type-load'),
            'showform' => route('hospital.settings.pathology.sample-type.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pathology.sample-type.index', [
            'pathurl' => 'pathology-sample-type',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PathologySampleType::select('*');

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pathology.sample-type.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = PathologySampleType::where('id', $id)->first();
        }

        return view('hospital.settings.pathology.sample-type.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:pathology_sample_types,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        PathologySampleType::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => trim((string) $request->name),
            ]
        );

        $msg = $request->id ? 'Pathology sample type updated successfully.' : 'Pathology sample type created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PathologySampleType $sample_type)
    {
        if ($sample_type->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $sample_type->delete();
        return response()->json(['status' => true, 'message' => 'Pathology sample type deleted successfully.']);
    }
}
