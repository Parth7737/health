<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\SchemeType;
use App\Models\TreatmentPlanInvestigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanInvestigationController extends Controller
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
            'destroy' => route('admin.investigations.destroy', ['investigation' => '__INVESTIGATION__']),
            'store' => route('admin.investigations.store'),
            'loadtable' => route('admin.investigations-load'),
            'showform' => route('admin.investigations.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-investigation.index', ['pathurl' => 'treatment-plan-investigation', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanInvestigation::query()
            ->with('schemeType')
            ->select('*')
            ->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('scheme_type_name', fn ($row) => $row->schemeType?->name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-investigation.partials.actions', compact('row'))->render();
            })
            ->editColumn('is_required', fn ($row) => $row->is_required == 1 ? 'Yes' : 'No')
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'code' => 'nullable|string|max:255|unique:investigations,code,'.$request->id,
            'scheme_type_id' => 'nullable|exists:scheme_types,id',
            'type' => 'nullable|string|max:255',
            'is_required' => 'nullable|string|max:32',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        TreatmentPlanInvestigation::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'code' => $request->code,
                'scheme_type_id' => $request->scheme_type_id ?: null,
                'type' => $request->type,
                'is_required' => $request->is_required,
            ]
        );

        $msg = $request->id ? 'Investigation updated successfully.' : 'Investigation created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanInvestigation::where('id', $id)->first();
        }

        $schemeTypes = SchemeType::orderBy('name')->pluck('name', 'id');

        return view('admin-views.treatment-plan-investigation.form', compact('data', 'id', 'schemeTypes'));
    }

    public function destroy(TreatmentPlanInvestigation $investigation)
    {
        $investigation->delete();

        return response()->json(['status' => true, 'message' => 'Investigation deleted successfully.']);
    }
}
