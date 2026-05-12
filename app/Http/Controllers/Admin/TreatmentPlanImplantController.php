<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Speciality;
use App\Models\TreatmentPlanProcedure;
use Illuminate\Http\Request;
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
        $data = TreatmentPlanImplant::query()->with(['procedure', 'speciality'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('procedure_name', fn ($row) => $row->procedure?->procedure_name ?: $row->procedure?->name ?? '—')
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        TreatmentPlanImplant::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'code' => $request->code,
                'no_of_multiplier' => $request->no_of_multiplier ?? 1,
                'price' => $request->price ?? 0,
                'procedure_id' => $request->procedure_id ?: null,
                'speciality_id' => $request->speciality_id ?: null,
            ]
        );

        $msg = $request->id ? 'Implant updated successfully.' : 'Implant created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanImplant::where('id', $id)->first();
        }
        $procedures = TreatmentPlanProcedure::query()
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn ($p) => [$p->id => ($p->procedure_name ?: $p->name ?: 'Procedure #'.$p->id)]);

        $specialities = Speciality::orderBy('name')->pluck('name', 'id');

        return view('admin-views.treatment-plan-implant.form', compact('data', 'id', 'procedures', 'specialities'));
    }

    public function destroy(TreatmentPlanImplant $implant)
    {
        $implant->delete();

        return response()->json(['status' => true, 'message' => 'Implant deleted successfully.']);
    }
}
