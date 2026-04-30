<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\ChargeMaster;
use App\Models\RadiologyCategory;
use App\Models\RadiologyParameter;
use App\Models\RadiologyTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RadiologyTestController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-radiology-test', ['only' => ['store']]);
        $this->middleware('permission:edit-radiology-test', ['only' => ['update']]);
        $this->middleware('permission:delete-radiology-test', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.radiology.test.destroy', ['test' => '__TEST__']),
            'store' => route('hospital.settings.radiology.test.store'),
            'loadtable' => route('hospital.settings.radiology.test-load'),
            'showform' => route('hospital.settings.radiology.test.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.radiology.test.index', [
            'pathurl' => 'radiology-test',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = RadiologyTest::with([
            'category:id,name',
            'parameters:id,name',
            'chargeMaster:id,code,name',
        ])->select('*');

        return DataTables::of($data)
            ->addColumn('parameters_list', function ($row) {
                return $row->parameters->pluck('name')->implode(', ');
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.radiology.test.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if ($id) {
            $data = RadiologyTest::with(['parameters:id,name,radiology_unit_id,range'])->where('id', $id)->first();
        }

        $categories = RadiologyCategory::select('id', 'name')->orderBy('name')->get();
        $parameters = RadiologyParameter::with('unit:id,name')
            ->select('id', 'name', 'radiology_unit_id', 'range')
            ->orderBy('name')
            ->get();

        $selectedChargeMasterId = $data?->charge_master_id;
        $chargeMasters = ChargeMaster::query()
            ->where(function ($query) use ($selectedChargeMasterId) {
                $query->where('is_active', true);
                if ($selectedChargeMasterId) {
                    $query->orWhere('id', $selectedChargeMasterId);
                }
            })
            ->whereIn('category', ['radiology', 'general'])
            ->withCount('tpaRates')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'standard_rate', 'category']);

        return view('hospital.settings.radiology.test.form', compact('data', 'id', 'categories', 'parameters', 'chargeMasters'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'radiology_category_id' => 'nullable|integer|exists:radiology_categories,id',
            'test_name' => 'required|string|max:255',
            'test_code' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:255',
            'report_days' => 'nullable|string',
            'description' => 'nullable|string',
            'charge_master_id' => 'required|integer|exists:charge_masters,id',
            'radiology_parameter_ids' => 'nullable|array',
            'radiology_parameter_ids.*' => 'integer|exists:radiology_parameters,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            $testName = trim((string) $request->test_name);
            if ($testName !== '') {
                $exists = RadiologyTest::where('hospital_id', $this->hospital_id)
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
                $exists = RadiologyTest::where('hospital_id', $this->hospital_id)
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
                $ownedTest = RadiologyTest::where('id', $request->id)->exists();
                if (!$ownedTest) {
                    $validator->errors()->add('id', 'Invalid radiology test selected.');
                }
            }

            if ($request->radiology_category_id) {
                $validCategory = RadiologyCategory::where('id', $request->radiology_category_id)->exists();
                if (!$validCategory) {
                    $validator->errors()->add('radiology_category_id', 'Selected category is invalid.');
                }
            }

            if ($request->charge_master_id) {
                $chargeMaster = ChargeMaster::where('id', $request->charge_master_id)->first();
                if (!$chargeMaster) {
                    $validator->errors()->add('charge_master_id', 'Selected charge master is invalid.');
                } elseif (!in_array($chargeMaster->category, ['radiology', 'general'], true)) {
                    $validator->errors()->add('charge_master_id', 'Selected charge master category is not valid for radiology.');
                }
            }

            $parameterIds = collect($request->radiology_parameter_ids ?? [])->filter()->map(function ($id) {
                return (int) $id;
            })->unique()->values();

            if ($parameterIds->isNotEmpty()) {
                $validCount = RadiologyParameter::whereIn('id', $parameterIds)->count();
                if ($validCount !== $parameterIds->count()) {
                    $validator->errors()->add('radiology_parameter_ids', 'One or more selected parameters are invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $parameterIds = collect($request->radiology_parameter_ids ?? [])
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($request, $parameterIds) {
            $chargeMaster = ChargeMaster::where('id', $request->charge_master_id)->first();
            $standardCharge = (float) ($chargeMaster?->standard_rate ?? 0);

            $test = RadiologyTest::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id' => $this->hospital_id,
                    'radiology_category_id' => $request->radiology_category_id,
                    'charge_master_id' => $chargeMaster?->id,
                    'test_name' => trim((string) $request->test_name),
                    'test_code' => trim((string) $request->test_code) ?: null,
                    'method' => $request->method,
                    'report_days' => $request->report_days,
                    'description' => $request->description,
                    'standard_charge' => $standardCharge,
                ]
            );

            $test->parameters()->sync($parameterIds);
        });

        $msg = $request->id ? 'Radiology Test updated successfully.' : 'Radiology Test created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(RadiologyTest $test)
    {
        if ($test->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $test->delete();
        return response()->json(['status' => true, 'message' => 'Radiology Test deleted successfully.']);
    }
}
