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
            'applicable_gender' => 'required|in:all,male,female',
            'value_type' => 'required|in:numeric,ordinal,boolean',
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
            'normal_values' => 'nullable|string|max:2000',
            'abnormal_values' => 'nullable|string|max:2000',
            'low_values' => 'nullable|string|max:2000',
            'high_values' => 'nullable|string|max:2000',
            'critical_low_values' => 'nullable|string|max:2000',
            'critical_high_values' => 'nullable|string|max:2000',
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

            $valueType = strtolower((string) $request->value_type);

            if ($valueType === 'numeric') {
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
            }

            if ($valueType === 'ordinal') {
                $normal = $this->parseFlagValues($request->normal_values);
                $abnormal = array_merge(
                    $this->parseFlagValues($request->low_values),
                    $this->parseFlagValues($request->high_values),
                    $this->parseFlagValues($request->critical_low_values),
                    $this->parseFlagValues($request->critical_high_values)
                );

                if (count($normal) === 0) {
                    $validator->errors()->add('normal_values', 'Add at least one normal value for non-numeric parameters.');
                }

                if (count($abnormal) === 0) {
                    $validator->errors()->add('low_values', 'Add at least one abnormal or critical value for non-numeric parameters.');
                }
            }

            if ($valueType === 'boolean') {
                $normal = $this->parseFlagValues($request->normal_values);
                $abnormal = $this->parseFlagValues($request->abnormal_values);

                if (count($normal) === 0) {
                    $validator->errors()->add('normal_values', 'For boolean type, add at least one Normal value (Yes or No).');
                }

                if (count($abnormal) === 0) {
                    $validator->errors()->add('abnormal_values', 'For boolean type, add at least one Abnormal value (Yes or No).');
                }

                $allowed = ['yes', 'no'];

                $invalidNormal = array_values(array_filter($normal, function ($item) use ($allowed) {
                    return !in_array(strtolower((string) $item), $allowed, true);
                }));

                $invalidAbnormal = array_values(array_filter($abnormal, function ($item) use ($allowed) {
                    return !in_array(strtolower((string) $item), $allowed, true);
                }));

                if (!empty($invalidNormal)) {
                    $validator->errors()->add('normal_values', 'Boolean Normal values can only be Yes or No.');
                }

                if (!empty($invalidAbnormal)) {
                    $validator->errors()->add('abnormal_values', 'Boolean Abnormal values can only be Yes or No.');
                }

                $normalizedNormal = array_values(array_unique(array_map('strtolower', $normal)));
                $normalizedAbnormal = array_values(array_unique(array_map('strtolower', $abnormal)));

                if (!empty(array_intersect($normalizedNormal, $normalizedAbnormal))) {
                    $validator->errors()->add('abnormal_values', 'Yes/No cannot exist in both Normal and Abnormal values.');
                }

                $combined = array_values(array_unique(array_merge($normalizedNormal, $normalizedAbnormal)));
                if (!empty($combined) && count($combined) < 2) {
                    $validator->errors()->add('abnormal_values', 'Boolean mapping should cover both Yes and No values.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $valueType = strtolower((string) $request->value_type);
        $flagRules = null;

        if ($valueType === 'ordinal') {
            $flagRules = [
                'normal' => $this->parseFlagValues($request->normal_values),
                'low' => $this->parseFlagValues($request->low_values),
                'high' => $this->parseFlagValues($request->high_values),
                'critical_low' => $this->parseFlagValues($request->critical_low_values),
                'critical_high' => $this->parseFlagValues($request->critical_high_values),
            ];
        }

        if ($valueType === 'boolean') {
            $flagRules = [
                'normal' => $this->parseFlagValues($request->normal_values),
                'abnormal' => $this->parseFlagValues($request->abnormal_values),
            ];
        }

        RadiologyParameter::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'radiology_unit_id' => $request->radiology_unit_id,
                'name' => $request->name,
                'range' => $request->range,
                'description' => $request->description,
                'applicable_gender' => $request->applicable_gender,
                'value_type' => $valueType,
                'flag_rules' => $flagRules,
                'min_value' => $valueType === 'numeric' ? $request->min_value : null,
                'max_value' => $valueType === 'numeric' ? $request->max_value : null,
                'critical_low' => $valueType === 'numeric' ? $request->critical_low : null,
                'critical_high' => $valueType === 'numeric' ? $request->critical_high : null,
                'min_value_male' => $valueType === 'numeric' ? $request->min_value_male : null,
                'max_value_male' => $valueType === 'numeric' ? $request->max_value_male : null,
                'critical_low_male' => $valueType === 'numeric' ? $request->critical_low_male : null,
                'critical_high_male' => $valueType === 'numeric' ? $request->critical_high_male : null,
                'min_value_female' => $valueType === 'numeric' ? $request->min_value_female : null,
                'max_value_female' => $valueType === 'numeric' ? $request->max_value_female : null,
                'critical_low_female' => $valueType === 'numeric' ? $request->critical_low_female : null,
                'critical_high_female' => $valueType === 'numeric' ? $request->critical_high_female : null,
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

    protected function parseFlagValues($raw): array
    {
        return collect(explode(',', (string) $raw))
            ->map(function ($item) {
                return trim((string) $item);
            })
            ->filter(function ($item) {
                return $item !== '';
            })
            ->unique()
            ->values()
            ->all();
    }
}
