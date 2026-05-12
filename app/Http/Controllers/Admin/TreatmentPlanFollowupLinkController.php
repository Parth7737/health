<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanFollowupLink;
use App\Models\TreatmentPlanProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanFollowupLinkController extends Controller
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
            'destroy' => route('admin.followup-links.destroy', ['followup_link' => '__FOLLOWUP_LINK__']),
            'store' => route('admin.followup-links.store'),
            'loadtable' => route('admin.followup-links-load'),
            'showform' => route('admin.followup-links.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-followup-link.index', ['pathurl' => 'treatment-plan-followup-link', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanFollowupLink::query()->with(['procedure', 'followUpProcedure'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('parent_name', fn ($row) => $row->procedure?->name ?? '—')
            ->addColumn('followup_name', fn ($row) => $row->followUpProcedure?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-followup-link.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'procedure_id' => 'required|exists:procedures,id',
            'follow_up_id' => 'required|exists:procedures,id|different:procedure_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $exists = TreatmentPlanFollowupLink::where('procedure_id', $request->procedure_id)
            ->where('follow_up_id', $request->follow_up_id)
            ->when($request->id, fn ($q) => $q->where('id', '!=', $request->id))
            ->exists();
        if ($exists) {
            return response()->json([
                'errors' => [
                    ['code' => 'follow_up_id', 'message' => 'This procedure and follow-up pair already exists.'],
                ],
            ], 422);
        }

        TreatmentPlanFollowupLink::updateOrCreate(
            ['id' => $request->id],
            [
                'procedure_id' => $request->procedure_id,
                'follow_up_id' => $request->follow_up_id,
            ]
        );

        $msg = $request->id ? 'Follow-up link updated successfully.' : 'Follow-up link created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanFollowupLink::where('id', $id)->first();
        }
        $procedures = TreatmentPlanProcedure::orderBy('name')->pluck('name', 'id');

        return view('admin-views.treatment-plan-followup-link.form', compact('data', 'id', 'procedures'));
    }

    public function destroy(TreatmentPlanFollowupLink $followup_link)
    {
        $followup_link->delete();

        return response()->json(['status' => true, 'message' => 'Follow-up link deleted successfully.']);
    }
}
