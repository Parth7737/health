<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Speciality;
use App\Models\TreatmentPlanProcedure;
use App\Models\TreatmentPlanImplant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanImplantController extends Controller
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
            'destroy' => route('admin.implants.destroy', ['implant' => '__IMPLANT__']),
            'store' => route('admin.implants.store'),
            'loadtable' => route('admin.implants-load'),
            'showform' => route('admin.implants.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-implant.index', ['pathurl' => 'treatment-plan-implant', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanImplant::query()->with(['speciality'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('speciality_name', fn ($row) => $row->speciality?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-implant.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:64',
            'no_of_multiplier' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'speciality_id' => 'nullable|exists:specialities,id',
            /** SHA implant/create: procedure_id[] */
            'procedure_id' => 'required|array|min:1',
            'procedure_id.*' => 'integer|exists:procedures,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $procedureIds = array_values(array_unique(array_map('intval', (array) $request->input('procedure_id', []))));

        DB::transaction(function () use ($request, $procedureIds) {
            $payload = [
                'name' => $request->name,
                'code' => $request->code,
                'no_of_multiplier' => $request->no_of_multiplier ?? 1,
                'price' => $request->price ?? 0,
                'procedure_id' => $procedureIds[0] ?? null,
                'speciality_id' => $request->speciality_id ?: null,
            ];

            if ($request->filled('id')) {
                $model = TreatmentPlanImplant::query()->findOrFail((int) $request->id);
                $model->update($payload);
            } else {
                $model = TreatmentPlanImplant::query()->create($payload);
            }

            if (Schema::hasTable('implant_procedures')) {
                $model->procedures()->sync($procedureIds);
            }
        });

        $msg = filled($request->id) ? 'Implant updated successfully.' : 'Implant created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanImplant::query()->where('id', $id)->first();
            if ($data && Schema::hasTable('implant_procedures')) {
                $data->load('procedures');
            }
        }

        $procedureOptions = TreatmentPlanProcedure::query()
            ->orderBy('procedure_code_2')
            ->orderBy('procedure_name')
            ->orderBy('name')
            ->get();

        $specialities = Speciality::orderBy('name')->pluck('name', 'id');

        $selectedProcedureIds = [];
        if (old('procedure_id')) {
            $selectedProcedureIds = array_map('intval', (array) old('procedure_id'));
        } elseif ($data) {
            if (Schema::hasTable('implant_procedures') && $data->relationLoaded('procedures')) {
                $selectedProcedureIds = $data->procedures->pluck('id')->map(fn ($x) => (int) $x)->all();
            }
            if ($selectedProcedureIds === [] && $data->procedure_id) {
                $selectedProcedureIds = [(int) $data->procedure_id];
            }
        }

        return view('admin-views.treatment-plan-implant.form', compact(
            'data',
            'id',
            'procedureOptions',
            'specialities',
            'selectedProcedureIds'
        ));
    }

    public function destroy(TreatmentPlanImplant $implant)
    {
        $implant->delete();

        return response()->json(['status' => true, 'message' => 'Implant deleted successfully.']);
    }
}
