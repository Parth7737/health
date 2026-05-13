<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanProcedure;
use App\Models\TreatmentPlanStratification;
use App\Models\TreatmentPlanStratificationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanStratificationController extends Controller
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
            'destroy' => route('admin.stratifications.destroy', ['stratification' => '__STRATIFICATION__']),
            'store' => route('admin.stratifications.store'),
            'loadtable' => route('admin.stratifications-load'),
            'showform' => route('admin.stratifications.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-stratification.index', ['pathurl' => 'treatment-plan-stratification', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanStratification::query()->with(['category'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('category_name', fn ($row) => $row->category?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-stratification.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stratification_category_id' => 'required|exists:stratification_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:64',
            'code2' => 'nullable|string|max:64',
            /** SHA-style: single letter a–z */
            'rule' => 'required|string|size:1|regex:/^[a-z]$/',
            'price' => 'nullable|numeric|min:0',
            /** SHA-style: procedure_id[] — at least one procedure */
            'procedure_id' => 'required|array|min:1',
            'procedure_id.*' => 'integer|exists:procedures,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $procedureIds = array_values(array_unique(array_map('intval', (array) $request->input('procedure_id', []))));

        DB::transaction(function () use ($request, $procedureIds) {
            $payload = [
                'stratification_category_id' => $request->stratification_category_id,
                /** Legacy column: first selected procedure (Patient 360 / single FK consumers) */
                'procedure_id' => $procedureIds[0] ?? null,
                'name' => $request->name,
                'code' => $request->code,
                'code2' => $request->code2,
                'rule' => $request->rule,
                'price' => $request->price,
            ];

            if ($request->filled('id')) {
                $model = TreatmentPlanStratification::query()->findOrFail((int) $request->id);
                $model->update($payload);
            } else {
                $model = TreatmentPlanStratification::query()->create($payload);
            }

            if (Schema::hasTable('stratification_procedures')) {
                $model->procedures()->sync($procedureIds);
            }
        });

        $msg = filled($request->id) ? 'Stratification updated successfully.' : 'Stratification created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanStratification::query()->where('id', $id)->first();
            if ($data && Schema::hasTable('stratification_procedures')) {
                $data->load('procedures');
            }
        }
        $categories = TreatmentPlanStratificationCategory::orderBy('name')->pluck('name', 'id');
        /** SHA create: options keyed by procedure_code_2 label */
        $procedureOptions = TreatmentPlanProcedure::query()
            ->orderBy('procedure_code_2')
            ->orderBy('procedure_name')
            ->orderBy('name')
            ->get();

        $selectedProcedureIds = [];
        if (old('procedure_id')) {
            $selectedProcedureIds = array_map('intval', (array) old('procedure_id'));
        } elseif ($data) {
            if (Schema::hasTable('stratification_procedures') && $data->relationLoaded('procedures')) {
                $selectedProcedureIds = $data->procedures->pluck('id')->map(fn ($x) => (int) $x)->all();
            }
            if ($selectedProcedureIds === [] && $data->procedure_id) {
                $selectedProcedureIds = [(int) $data->procedure_id];
            }
        }

        return view('admin-views.treatment-plan-stratification.form', compact(
            'data',
            'id',
            'categories',
            'procedureOptions',
            'selectedProcedureIds'
        ));
    }

    public function destroy(TreatmentPlanStratification $stratification)
    {
        $stratification->delete();

        return response()->json(['status' => true, 'message' => 'Stratification deleted successfully.']);
    }
}
