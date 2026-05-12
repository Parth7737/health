<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\SchemeType;
use App\Models\Speciality;
use App\Models\TreatmentPlanInvestigation;
use App\Models\TreatmentPlanPackage;
use App\Models\TreatmentPlanProcedure;
use App\Models\TreatmentPlanProcedureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanProcedureController extends Controller
{
    public $routes = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->user() || ! auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }

            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.procedures.destroy', ['procedure' => '__PROCEDURE__']),
            'store' => route('admin.procedures.store'),
            'loadtable' => route('admin.procedures-load'),
            'showform' => route('admin.procedures.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-procedure.index', ['pathurl' => 'treatment-plan-procedure', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanProcedure::query()
            ->with(['package', 'category', 'speciality', 'schemeType'])
            ->select('procedures.*')
            ->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('display_name', fn ($row) => $row->procedure_name ?: $row->name ?: '—')
            ->addColumn('scheme_type_name', fn ($row) => $row->schemeType?->name ?? '—')
            ->addColumn('package_name', fn ($row) => $row->package?->name ?? '—')
            ->addColumn('category_name', fn ($row) => $row->category?->name ?? '—')
            ->addColumn('speciality_name', fn ($row) => $row->speciality?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-procedure.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scheme_type_id' => 'nullable|exists:scheme_types,id',
            'name' => 'nullable|string',
            'package_id' => 'nullable|exists:packages,id',
            'procedure_category_id' => 'nullable|exists:procedure_categories,id',
            'speciality_id' => 'nullable|exists:specialities,id',
            'procedure_code_1' => 'nullable|string|max:128',
            'procedure_code_2' => 'nullable|string|max:128',
            'is_multiple_procedure' => 'nullable|string|max:32',
            'procedure_name' => 'nullable|string',
            'icd_code' => 'nullable|string|max:255',
            'procedure_type' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'non_nabh_price' => 'nullable|numeric|min:0',
            'stratification_criteria' => 'nullable|string|max:32',
            'no_of_stratification' => 'nullable|string|max:64',
            'implants_high_end_consumables' => 'nullable|string|max:32',
            'more_than_one_implant' => 'nullable|string|max:64',
            'special_conditions' => 'nullable|string|max:32',
            'reservation_public_hospitals' => 'nullable|string|max:32',
            'reservation_tertiary_hospitals' => 'nullable|string|max:32',
            'level_of_care' => 'nullable|string|max:64',
            'los' => 'nullable|string|max:64',
            'auto_approved' => 'nullable|string|max:32',
            'procedure_label' => 'nullable|string|max:128',
            'special_condition_pop_up' => 'nullable|string|max:32',
            'special_condition_pop_up_message' => 'nullable|string',
            'special_conditions_rule' => 'nullable|string|max:32',
            'special_conditions_rule_message' => 'nullable|string',
            'enhancement_applicable' => 'nullable|string|max:32',
            'medical_or_surgical' => 'nullable|string|max:64',
            'day_care_procedure' => 'nullable|string|max:32',
            'status' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $preAuth = $this->normalizeDocList($request->input('mandatory_documents_pre_auth'));
        $claim = $this->normalizeDocList($request->input('mandatory_documents_claim_processing'));

        $payload = [
            'scheme_type_id' => $request->scheme_type_id ?: null,
            'name' => $request->name,
            'package_id' => $request->package_id ?: null,
            'procedure_category_id' => $request->procedure_category_id ?: null,
            'speciality_id' => $request->speciality_id ?: null,
            'procedure_code_1' => $request->procedure_code_1,
            'procedure_code_2' => $request->procedure_code_2,
            'is_multiple_procedure' => $request->is_multiple_procedure,
            'procedure_name' => $request->procedure_name,
            'icd_code' => $request->icd_code,
            'procedure_type' => $request->procedure_type,
            'price' => $request->price ?? 0,
            'non_nabh_price' => $request->non_nabh_price,
            'stratification_criteria' => $request->stratification_criteria,
            'no_of_stratification' => $request->no_of_stratification,
            'implants_high_end_consumables' => $request->implants_high_end_consumables,
            'more_than_one_implant' => $request->more_than_one_implant,
            'special_conditions' => $request->special_conditions,
            'reservation_public_hospitals' => $request->reservation_public_hospitals,
            'reservation_tertiary_hospitals' => $request->reservation_tertiary_hospitals,
            'level_of_care' => $request->level_of_care,
            'los' => $request->los !== null && $request->los !== '' ? (string) $request->los : null,
            'auto_approved' => $request->auto_approved,
            'mandatory_documents_pre_auth' => $preAuth,
            'mandatory_documents_claim_processing' => $claim,
            'procedure_label' => $request->procedure_label,
            'special_condition_pop_up' => $request->special_condition_pop_up,
            'special_condition_pop_up_message' => $request->special_condition_pop_up_message,
            'special_conditions_rule' => $request->special_conditions_rule,
            'special_conditions_rule_message' => $request->special_conditions_rule_message,
            'enhancement_applicable' => $request->enhancement_applicable,
            'medical_or_surgical' => $request->medical_or_surgical,
            'day_care_procedure' => $request->day_care_procedure,
            'status' => $request->status ?: 'active',
        ];

        TreatmentPlanProcedure::updateOrCreate(['id' => $request->id], $payload);

        $msg = $request->id ? 'Procedure updated successfully.' : 'Procedure created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanProcedure::where('id', $id)->first();
        }
        $packages = TreatmentPlanPackage::orderBy('name')->pluck('name', 'id');
        $categories = TreatmentPlanProcedureCategory::orderBy('name')->pluck('name', 'id');
        $investigations = TreatmentPlanInvestigation::orderBy('name')->get();
        $schemeTypes = SchemeType::orderBy('name')->pluck('name', 'id');
        /** @var int Same as SHA admin procedure UI (scheme_types.id = 1 shows category + non-NABH). */
        $sghsSchemeTypeId = 1;

        return view('admin-views.treatment-plan-procedure.form', compact('data', 'id', 'packages', 'categories', 'investigations', 'schemeTypes', 'sghsSchemeTypeId'));
    }

    public function getSpecialitiesByScheme(Request $request)
    {
        $query = Speciality::query()->orderBy('name');

        if (Schema::hasColumn('specialities', 'scheme_type_id')) {
            $schemeTypeId = $request->input('scheme_type_id');
            if ($schemeTypeId !== null && $schemeTypeId !== '') {
                $query->where('scheme_type_id', (int) $schemeTypeId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return response()->json([
            'specialities' => $query->get(['id', 'name']),
        ]);
    }

    public function destroy(TreatmentPlanProcedure $procedure)
    {
        $procedure->delete();

        return response()->json(['status' => true, 'message' => 'Procedure deleted successfully.']);
    }

    private function normalizeDocList($value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_array($value)) {
            return implode(',', array_filter(array_map('strval', $value)));
        }
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }
}
