<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Speciality;
use App\Models\TreatmentPlanAddonSpeciality;
use App\Models\TreatmentPlanProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanAddonSpecialityController extends Controller
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
            'destroy' => route('admin.addon-specialities.destroy', ['addon_speciality' => '__ADDON_SPECIALITY__']),
            'store' => route('admin.addon-specialities.store'),
            'loadtable' => route('admin.addon-specialities-load'),
            'showform' => route('admin.addon-specialities.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-addon-speciality.index', ['pathurl' => 'treatment-plan-addon-speciality', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanAddonSpeciality::query()->with(['addonProcedure', 'speciality'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('procedure_name', fn ($row) => $row->addonProcedure?->name ?? '—')
            ->addColumn('speciality_name', fn ($row) => $row->speciality?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-addon-speciality.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'add_on_id' => 'required|exists:procedures,id',
            'speciality_id' => 'required|exists:specialities,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $exists = TreatmentPlanAddonSpeciality::where('add_on_id', $request->add_on_id)
            ->where('speciality_id', $request->speciality_id)
            ->when($request->id, fn ($q) => $q->where('id', '!=', $request->id))
            ->exists();
        if ($exists) {
            return response()->json([
                'errors' => [
                    ['code' => 'speciality_id', 'message' => 'This add-on procedure is already mapped to this speciality.'],
                ],
            ], 422);
        }

        TreatmentPlanAddonSpeciality::updateOrCreate(
            ['id' => $request->id],
            [
                'add_on_id' => $request->add_on_id,
                'speciality_id' => $request->speciality_id,
            ]
        );

        $msg = $request->id ? 'Add-on to speciality updated successfully.' : 'Add-on to speciality created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanAddonSpeciality::where('id', $id)->first();
        }
        $procedures = TreatmentPlanProcedure::orderBy('name')->pluck('name', 'id');
        $specialities = Speciality::orderBy('name')->pluck('name', 'id');

        return view('admin-views.treatment-plan-addon-speciality.form', compact('data', 'id', 'procedures', 'specialities'));
    }

    public function destroy(TreatmentPlanAddonSpeciality $addon_speciality)
    {
        $addon_speciality->delete();

        return response()->json(['status' => true, 'message' => 'Mapping deleted successfully.']);
    }
}
