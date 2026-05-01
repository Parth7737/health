<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\RadiologyParameter;
use App\Models\RadiologyUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class RadiologyParameterController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-radiology-parameter', ['only' => ['store']]);
        $this->middleware('permission:edit-radiology-parameter', ['only' => ['update']]);
        $this->middleware('permission:delete-radiology-parameter', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.radiology.parameter.destroy', ['parameter' => '__PARAMETER__']),
            'store'     => route('hospital.settings.radiology.parameter.store'),
            'loadtable' => route('hospital.settings.radiology.parameter-load'),
            'showform'  => route('hospital.settings.radiology.parameter.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.radiology.parameter.index', [
            'pathurl' => 'radiology-parameter',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = RadiologyParameter::select('*')->with('unit');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.radiology.parameter.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = RadiologyParameter::where('id', $id)->first();
        }
        $units = RadiologyUnit::where('hospital_id', $this->hospital_id)->get();
        return view('hospital.settings.radiology.parameter.form', compact('data', 'id', 'units'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'radiology_unit_id' => 'nullable|exists:radiology_units,id',
            'name' => 'required|string|max:255',
            'range' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'critical_low' => 'nullable|numeric',
            'critical_high' => 'nullable|numeric',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->name) {
                $exists = RadiologyParameter::where('hospital_id', $this->hospital_id)
                    ->where('name', $request->name)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })->exists();
                if ($exists) {
                    $validator->errors()->add('name', 'Parameter with this name already exists.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        RadiologyParameter::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'radiology_unit_id' => $request->radiology_unit_id,
                'name' => $request->name,
                'range' => $request->range,
                'description' => $request->description,
                'min_value' => $request->filled('min_value') ? $request->min_value : null,
                'max_value' => $request->filled('max_value') ? $request->max_value : null,
                'critical_low' => $request->filled('critical_low') ? $request->critical_low : null,
                'critical_high' => $request->filled('critical_high') ? $request->critical_high : null,
            ]
        );

        $msg = $request->id ? 'Radiology Parameter updated successfully.' : 'Radiology Parameter created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(RadiologyParameter $parameter)
    {
        if ($parameter->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $parameter->delete();
        return response()->json(['status' => true, 'message' => 'Radiology Parameter deleted successfully.']);
    }
}
