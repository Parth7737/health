<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Symptoms;
use App\Models\SymptomsType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class SymptomsHeadController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-symptoms', ['only' => ['store']]);
        $this->middleware('permission:edit-symptoms', ['only' => ['update']]);
        $this->middleware('permission:delete-symptoms', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.masters.symptoms.symptoms-head.destroy', ['symptoms_head' => '__SYMPTOMS_HEAD__']),
            'store'     => route('hospital.masters.symptoms.symptoms-head.store'),
            'loadtable' => route('hospital.masters.symptoms.symptoms-head-load'),
            'showform'  => route('hospital.masters.symptoms.symptoms-head.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.masters.symptoms.symptoms-head.index', [
            'pathurl' => 'symptoms-head',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Symptoms::select('*')->with('type');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.symptoms.symptoms-head.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = Symptoms::where('id', $id)->first();
        }
        $types = SymptomsType::where('hospital_id', $this->hospital_id)->get();
        return view('hospital.masters.symptoms.symptoms-head.form', compact('data', 'id', 'types'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symptoms_type_id' => 'nullable|exists:symptoms_types,id',
            'name' => 'required|string|max:255',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->name) {
                $exists = Symptoms::where('hospital_id', $this->hospital_id)
                    ->where('name', $request->name)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })->exists();
                if ($exists) {
                    $validator->errors()->add('name', 'Symptoms with this name already exists.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Symptoms::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'symptoms_type_id' => $request->symptoms_type_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Symptoms Head updated successfully.' : 'Symptoms Head created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Symptoms $symptoms_head)
    {
        if ($symptoms_head->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $symptoms_head->delete();
        return response()->json(['status' => true, 'message' => 'Symptoms Head deleted successfully.']);
    }
    
    /**
     * Return symptoms matching any of the given type ids (AJAX)
     */
    public function loadSymptoms(Request $request)
    {
        $types = $request->input('types', []);
        $symptoms = [];
        if (!empty($types)) {
            $symptoms = Symptoms::whereIn('symptoms_type_id', $types)
                        ->get(['id','name']);
        }
        return response()->json($symptoms);
    }
}
