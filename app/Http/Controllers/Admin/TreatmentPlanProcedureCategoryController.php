<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanProcedureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanProcedureCategoryController extends Controller
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
            'destroy' => route('admin.procedure-categories.destroy', ['procedure_category' => '__PROCEDURE_CATEGORY__']),
            'store' => route('admin.procedure-categories.store'),
            'loadtable' => route('admin.procedure-categories-load'),
            'showform' => route('admin.procedure-categories.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-procedure-category.index', ['pathurl' => 'treatment-plan-procedure-category', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanProcedureCategory::query()->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-procedure-category.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255|unique:procedure_categories,code,'.$request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        TreatmentPlanProcedureCategory::updateOrCreate(
            ['id' => $request->id],
            ['name' => $request->name, 'code' => $request->code]
        );

        $msg = $request->id ? 'Procedure category updated successfully.' : 'Procedure category created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanProcedureCategory::where('id', $id)->first();
        }

        return view('admin-views.treatment-plan-procedure-category.form', compact('data', 'id'));
    }

    public function destroy(TreatmentPlanProcedureCategory $procedure_category)
    {
        $procedure_category->delete();

        return response()->json(['status' => true, 'message' => 'Procedure category deleted successfully.']);
    }
}
