<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanAddonLink;
use App\Models\TreatmentPlanProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanAddonLinkController extends Controller
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
            'destroy' => route('admin.addon-links.destroy', ['addon_link' => '__ADDON_LINK__']),
            'store' => route('admin.addon-links.store'),
            'loadtable' => route('admin.addon-links-load'),
            'showform' => route('admin.addon-links.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-addon-link.index', ['pathurl' => 'treatment-plan-addon-link', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanAddonLink::query()->with(['procedure', 'addOnProcedure'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('base_name', fn ($row) => $row->procedure?->name ?? '—')
            ->addColumn('addon_name', fn ($row) => $row->addOnProcedure?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-addon-link.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'procedure_id' => 'required|exists:procedures,id',
            'add_on_id' => 'required|exists:procedures,id|different:procedure_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $exists = TreatmentPlanAddonLink::where('procedure_id', $request->procedure_id)
            ->where('add_on_id', $request->add_on_id)
            ->when($request->id, fn ($q) => $q->where('id', '!=', $request->id))
            ->exists();
        if ($exists) {
            return response()->json([
                'errors' => [
                    ['code' => 'add_on_id', 'message' => 'This base and add-on pair already exists.'],
                ],
            ], 422);
        }

        TreatmentPlanAddonLink::updateOrCreate(
            ['id' => $request->id],
            [
                'procedure_id' => $request->procedure_id,
                'add_on_id' => $request->add_on_id,
            ]
        );

        $msg = $request->id ? 'Add-on link updated successfully.' : 'Add-on link created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanAddonLink::where('id', $id)->first();
        }
        $procedures = TreatmentPlanProcedure::orderBy('name')->pluck('name', 'id');

        return view('admin-views.treatment-plan-addon-link.form', compact('data', 'id', 'procedures'));
    }

    public function destroy(TreatmentPlanAddonLink $addon_link)
    {
        $addon_link->delete();

        return response()->json(['status' => true, 'message' => 'Add-on link deleted successfully.']);
    }
}
