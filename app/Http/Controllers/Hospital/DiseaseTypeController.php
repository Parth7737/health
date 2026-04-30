<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\DiseaseType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class DiseaseTypeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-disease-types', ['only' => ['store']]);
        $this->middleware('permission:edit-disease-types', ['only' => ['update']]);
        $this->middleware('permission:delete-disease-types', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.masters.disease-type.destroy', ['disease_type' => '__DISEASE_TYPE__']),
            'store' => route('hospital.masters.disease-type.store'),
            'loadtable' => route('hospital.masters.disease-type-load'),
            'showform' => route('hospital.masters.disease-type.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.masters.disease-type.index', ['pathurl' => 'disease-type', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = DiseaseType::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.disease-type.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:disease_types,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        DiseaseType::updateOrCreate(
            ['id' => $request->id],
            ['hospital_id' => $this->hospital_id, 'name' => $request->name]
        );

        $msg = $request->id ? 'Disease Type updated successfully.' : 'Disease Type created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = DiseaseType::where('id', $id)->first();
        }
        return view('hospital.masters.disease-type.form', compact('data', 'id'));
    }

    public function destroy(DiseaseType $disease_type)
    {
        $disease_type->delete();

        return response()->json(['status' => true, 'message' => 'Disease Type Deleted Successfully.']);
    }
}
