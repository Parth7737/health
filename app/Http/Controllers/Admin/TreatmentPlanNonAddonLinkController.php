<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanNonAddonLink;
use App\Models\TreatmentPlanProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanNonAddonLinkController extends Controller
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
            'destroy' => route('admin.non-addon-links.destroy', ['non_addon_link' => '__NON_ADDON_LINK__']),
            'store' => route('admin.non-addon-links.store'),
            'loadtable' => route('admin.non-addon-links-load'),
            'showform' => route('admin.non-addon-links.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-non-addon-link.index', ['pathurl' => 'treatment-plan-non-addon-link', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanNonAddonLink::query()->with(['procedure', 'nonAddOnProcedure'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('procedure_name', fn ($row) => $row->procedure?->name ?? '—')
            ->addColumn('related_name', fn ($row) => $row->nonAddOnProcedure?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-non-addon-link.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'procedure_id' => 'required|exists:procedures,id',
            'non_add_on_id' => 'required|exists:procedures,id|different:procedure_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $exists = TreatmentPlanNonAddonLink::where('procedure_id', $request->procedure_id)
            ->where('non_add_on_id', $request->non_add_on_id)
            ->when($request->id, fn ($q) => $q->where('id', '!=', $request->id))
            ->exists();
        if ($exists) {
            return response()->json([
                'errors' => [
                    ['code' => 'non_add_on_id', 'message' => 'This pair already exists.'],
                ],
            ], 422);
        }

        TreatmentPlanNonAddonLink::updateOrCreate(
            ['id' => $request->id],
            [
                'procedure_id' => $request->procedure_id,
                'non_add_on_id' => $request->non_add_on_id,
            ]
        );

        $msg = $request->id ? 'Non–add-on link updated successfully.' : 'Non–add-on link created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanNonAddonLink::where('id', $id)->first();
        }
        $procedures = TreatmentPlanProcedure::orderBy('name')->pluck('name', 'id');

        return view('admin-views.treatment-plan-non-addon-link.form', compact('data', 'id', 'procedures'));
    }

    public function destroy(TreatmentPlanNonAddonLink $non_addon_link)
    {
        $non_addon_link->delete();

        return response()->json(['status' => true, 'message' => 'Non–add-on link deleted successfully.']);
    }
}
