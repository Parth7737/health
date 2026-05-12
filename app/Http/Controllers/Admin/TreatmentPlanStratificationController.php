<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanProcedure;
use App\Models\TreatmentPlanStratification;
use App\Models\TreatmentPlanStratificationCategory;
use Illuminate\Http\Request;
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
        $data = TreatmentPlanStratification::query()->with(['category', 'procedure'])->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('category_name', fn ($row) => $row->category?->name ?? '—')
            ->addColumn('procedure_name', fn ($row) => $row->procedure?->name ?? '—')
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
            'rule' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'procedure_id' => 'nullable|exists:procedures,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        TreatmentPlanStratification::updateOrCreate(
            ['id' => $request->id],
            [
                'stratification_category_id' => $request->stratification_category_id,
                'procedure_id' => $request->procedure_id ?: null,
                'name' => $request->name,
                'code' => $request->code,
                'code2' => $request->code2,
                'rule' => $request->rule,
                'price' => $request->price,
            ]
        );

        $msg = $request->id ? 'Stratification updated successfully.' : 'Stratification created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanStratification::where('id', $id)->first();
        }
        $categories = TreatmentPlanStratificationCategory::orderBy('name')->pluck('name', 'id');
        $procedures = TreatmentPlanProcedure::orderBy('name')->pluck('name', 'id');

        return view('admin-views.treatment-plan-stratification.form', compact('data', 'id', 'categories', 'procedures'));
    }

    public function destroy(TreatmentPlanStratification $stratification)
    {
        $stratification->delete();

        return response()->json(['status' => true, 'message' => 'Stratification deleted successfully.']);
    }
}
