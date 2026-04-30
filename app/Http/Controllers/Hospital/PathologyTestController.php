<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\ChargeMaster;
use App\Models\PathologyCategory;
use App\Models\PathologyParameter;
use App\Models\PathologySampleType;
use App\Models\PathologyTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PathologyTestController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pathology-test', ['only' => ['store']]);
        $this->middleware('permission:edit-pathology-test', ['only' => ['update']]);
        $this->middleware('permission:delete-pathology-test', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.pathology.test.destroy', ['test' => '__TEST__']),
            'store' => route('hospital.settings.pathology.test.store'),
            'loadtable' => route('hospital.settings.pathology.test-load'),
            'showform' => route('hospital.settings.pathology.test.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pathology.test.index', [
            'pathurl' => 'pathology-test',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PathologyTest::with([
            'category:id,name',
            'parameters:id,name',
            'sampleTypes:id,name',
            'chargeMaster:id,code,name',
        ])->select('*');

        return DataTables::of($data)
            ->addColumn('parameters_list', function ($row) {
                return $row->parameters->pluck('name')->implode(', ');
            })
            ->addColumn('sample_types_list', function ($row) {
                return $row->sampleTypes->pluck('name')->implode(', ');
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pathology.test.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if ($id) {
            $data = PathologyTest::with(['parameters:id,name,pathology_unit_id,range', 'sampleTypes:id,name'])->where('id', $id)->first();
        }

        $categories = PathologyCategory::select('id', 'name')->orderBy('name')->get();
        $parameters = PathologyParameter::with('unit:id,name')
            ->select('id', 'name', 'pathology_unit_id', 'range')
            ->orderBy('name')
            ->get();
        $sampleTypes = PathologySampleType::select('id', 'name')->orderBy('name')->get();

        $selectedChargeMasterId = $data?->charge_master_id;
        $chargeMasters = ChargeMaster::query()
            ->where(function ($query) use ($selectedChargeMasterId) {
                $query->where('is_active', true);
                if ($selectedChargeMasterId) {
                    $query->orWhere('id', $selectedChargeMasterId);
                }
            })
            ->whereIn('category', ['pathology', 'general'])
            ->withCount('tpaRates')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'standard_rate', 'category']);

        return view('hospital.settings.pathology.test.form', compact('data', 'id', 'categories', 'parameters', 'chargeMasters', 'sampleTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pathology_category_id' => 'nullable|integer|exists:pathology_categories,id',
            'test_name' => 'required|string|max:255',
            'test_code' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:255',
            'report_days' => 'nullable|string',
            'description' => 'nullable|string',
            'charge_master_id' => 'required|integer|exists:charge_masters,id',
            'sample_type_ids' => 'nullable|array',
            'sample_type_ids.*' => 'required|integer|exists:pathology_sample_types,id',
            'pathology_parameter_ids' => 'required|array|min:1',
            'pathology_parameter_ids.*' => 'required|integer|exists:pathology_parameters,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            $testName = trim((string) $request->test_name);
            if ($testName !== '') {
                $exists = PathologyTest::where('hospital_id', $this->hospital_id)
                    ->where('test_name', $testName)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('test_name', 'Test with this name already exists.');
                }
            }

            $testCode = trim((string) $request->test_code);
            if ($testCode !== '') {
                $exists = PathologyTest::where('hospital_id', $this->hospital_id)
                    ->where('test_code', $testCode)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('test_code', 'Test code already exists.');
                }
            }

            if ($request->id) {
                $ownedTest = PathologyTest::where('id', $request->id)->exists();
                if (!$ownedTest) {
                    $validator->errors()->add('id', 'Invalid pathology test selected.');
                }
            }

            if ($request->pathology_category_id) {
                $validCategory = PathologyCategory::where('id', $request->pathology_category_id)->exists();
                if (!$validCategory) {
                    $validator->errors()->add('pathology_category_id', 'Selected category is invalid.');
                }
            }

            if ($request->charge_master_id) {
                $chargeMaster = ChargeMaster::where('id', $request->charge_master_id)->first();
                if (!$chargeMaster) {
                    $validator->errors()->add('charge_master_id', 'Selected charge master is invalid.');
                } elseif (!in_array($chargeMaster->category, ['pathology', 'general'], true)) {
                    $validator->errors()->add('charge_master_id', 'Selected charge master category is not valid for pathology.');
                }
            }

            $parameterIds = collect($request->pathology_parameter_ids ?? [])->filter()->map(function ($id) {
                return (int) $id;
            })->unique()->values();

            if ($parameterIds->isEmpty()) {
                $validator->errors()->add('pathology_parameter_ids', 'Please select at least one parameter.');
                return;
            }

            $validCount = PathologyParameter::whereIn('id', $parameterIds)->count();
            if ($validCount !== $parameterIds->count()) {
                $validator->errors()->add('pathology_parameter_ids', 'One or more selected parameters are invalid.');
            }

            $sampleTypeIds = collect($request->sample_type_ids ?? [])->filter()->map(function ($id) {
                return (int) $id;
            })->unique()->values();
            if ($sampleTypeIds->isNotEmpty()) {
                $sampleTypeCount = PathologySampleType::whereIn('id', $sampleTypeIds)->count();
                if ($sampleTypeCount !== $sampleTypeIds->count()) {
                    $validator->errors()->add('sample_type_ids', 'One or more selected sample types are invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $parameterIds = collect($request->pathology_parameter_ids)
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();
        $sampleTypeIds = collect($request->sample_type_ids ?? [])
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($request, $parameterIds, $sampleTypeIds) {
            $chargeMaster = ChargeMaster::where('id', $request->charge_master_id)->first();
            $standardCharge = (float) ($chargeMaster?->standard_rate ?? 0);
            $sampleTypeNames = PathologySampleType::whereIn('id', $sampleTypeIds)
                ->orderBy('name')
                ->pluck('name')
                ->all();
            $sampleTypeText = !empty($sampleTypeNames) ? implode(' | ', $sampleTypeNames) : null;

            $test = PathologyTest::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id' => $this->hospital_id,
                    'pathology_category_id' => $request->pathology_category_id,
                    'charge_master_id' => $chargeMaster?->id,
                    'test_name' => trim((string) $request->test_name),
                    'test_code' => trim((string) $request->test_code) ?: null,
                    'sample_type' => $sampleTypeText,
                    'method' => $request->method,
                    'report_days' => $request->report_days,
                    'description' => $request->description,
                    'standard_charge' => $standardCharge,
                ]
            );

            $test->parameters()->sync($parameterIds);
            $test->sampleTypes()->sync($sampleTypeIds);
        });

        $msg = $request->id ? 'Pathology Test updated successfully.' : 'Pathology Test created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PathologyTest $test)
    {
        if ($test->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $test->delete();
        return response()->json(['status' => true, 'message' => 'Pathology Test deleted successfully.']);
    }
}