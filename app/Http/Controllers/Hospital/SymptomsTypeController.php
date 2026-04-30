<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\SymptomsType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class SymptomsTypeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-symptoms-type', ['only' => ['store']]);
        $this->middleware('permission:edit-symptoms-type', ['only' => ['update']]);
        $this->middleware('permission:delete-symptoms-type', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.masters.symptoms.type.destroy', ['type' => '__TYPE__']),
            'store'     => route('hospital.masters.symptoms.type.store'),
            'loadtable' => route('hospital.masters.symptoms.type-load'),
            'showform'  => route('hospital.masters.symptoms.type.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.masters.symptoms.type.index', [
            'pathurl' => 'symptoms-type',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = SymptomsType::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.symptoms.type.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = SymptomsType::where('id', $id)->first();
        }
        return view('hospital.masters.symptoms.type.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:symptoms_types,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        SymptomsType::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Symptoms Type updated successfully.' : 'Symptoms Type created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(SymptomsType $type)
    {
        if ($type->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $type->delete();
        return response()->json(['status' => true, 'message' => 'Symptoms Type deleted successfully.']);
    }
}
