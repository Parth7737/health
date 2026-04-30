<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\PathologyParameter;
use App\Models\PathologyUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class PathologyParameterController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pathology-parameter', ['only' => ['store']]);
        $this->middleware('permission:edit-pathology-parameter', ['only' => ['update']]);
        $this->middleware('permission:delete-pathology-parameter', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pathology.parameter.destroy', ['parameter' => '__PARAMETER__']),
            'store'     => route('hospital.settings.pathology.parameter.store'),
            'loadtable' => route('hospital.settings.pathology.parameter-load'),
            'showform'  => route('hospital.settings.pathology.parameter.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pathology.parameter.index', [
            'pathurl' => 'pathology-parameter',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PathologyParameter::select('*')->with('unit');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pathology.parameter.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = PathologyParameter::where('id', $id)->first();
        }
        $units = PathologyUnit::where('hospital_id', $this->hospital_id)->get();
        return view('hospital.settings.pathology.parameter.form', compact('data', 'id', 'units'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pathology_unit_id' => 'nullable|exists:pathology_units,id',
            'name' => 'required|string|max:255',
            'range' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'applicable_gender' => 'required|in:all,male,female',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'critical_low' => 'nullable|numeric',
            'critical_high' => 'nullable|numeric',
            'min_value_male' => 'nullable|numeric',
            'max_value_male' => 'nullable|numeric',
            'critical_low_male' => 'nullable|numeric',
            'critical_high_male' => 'nullable|numeric',
            'min_value_female' => 'nullable|numeric',
            'max_value_female' => 'nullable|numeric',
            'critical_low_female' => 'nullable|numeric',
            'critical_high_female' => 'nullable|numeric',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->name) {
                $exists = PathologyParameter::where('hospital_id', $this->hospital_id)
                    ->where('name', $request->name)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })->exists();
                if ($exists) {
                    $validator->errors()->add('name', 'Parameter with this name already exists.');
                }
            }

            // Validate min/max ranges
            if ($request->min_value !== null && $request->max_value !== null) {
                if ((float) $request->min_value > (float) $request->max_value) {
                    $validator->errors()->add('min_value', 'Minimum value must be less than or equal to maximum value.');
                }
            }

            if ($request->critical_low !== null && $request->min_value !== null) {
                if ((float) $request->critical_low >= (float) $request->min_value) {
                    $validator->errors()->add('critical_low', 'Critical low must be less than minimum value.');
                }
            }

            if ($request->critical_high !== null && $request->max_value !== null) {
                if ((float) $request->critical_high <= (float) $request->max_value) {
                    $validator->errors()->add('critical_high', 'Critical high must be greater than maximum value.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        PathologyParameter::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'pathology_unit_id' => $request->pathology_unit_id,
                'name' => $request->name,
                'range' => $request->range,
                'description' => $request->description,
                'applicable_gender' => $request->applicable_gender,
                'min_value' => $request->min_value,
                'max_value' => $request->max_value,
                'critical_low' => $request->critical_low,
                'critical_high' => $request->critical_high,
                'min_value_male' => $request->min_value_male,
                'max_value_male' => $request->max_value_male,
                'critical_low_male' => $request->critical_low_male,
                'critical_high_male' => $request->critical_high_male,
                'min_value_female' => $request->min_value_female,
                'max_value_female' => $request->max_value_female,
                'critical_low_female' => $request->critical_low_female,
                'critical_high_female' => $request->critical_high_female,
            ]
        );

        $msg = $request->id ? 'Pathology Parameter updated successfully.' : 'Pathology Parameter created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PathologyParameter $parameter)
    {
        if ($parameter->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $parameter->delete();
        return response()->json(['status' => true, 'message' => 'Pathology Parameter deleted successfully.']);
    }
}
