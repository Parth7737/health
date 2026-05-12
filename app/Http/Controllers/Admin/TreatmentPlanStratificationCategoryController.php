<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanStratificationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanStratificationCategoryController extends Controller
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
            'destroy' => route('admin.stratification-categories.destroy', ['stratification_category' => '__STRATIFICATION_CATEGORY__']),
            'store' => route('admin.stratification-categories.store'),
            'loadtable' => route('admin.stratification-categories-load'),
            'showform' => route('admin.stratification-categories.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-stratification-category.index', ['pathurl' => 'treatment-plan-stratification-category', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanStratificationCategory::query()->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-stratification-category.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:stratification_categories,name,'.$request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        TreatmentPlanStratificationCategory::updateOrCreate(
            ['id' => $request->id],
            ['name' => $request->name]
        );

        $msg = $request->id ? 'Stratification category updated successfully.' : 'Stratification category created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanStratificationCategory::where('id', $id)->first();
        }

        return view('admin-views.treatment-plan-stratification-category.form', compact('data', 'id'));
    }

    public function destroy(TreatmentPlanStratificationCategory $stratification_category)
    {
        $stratification_category->delete();

        return response()->json(['status' => true, 'message' => 'Stratification category deleted successfully.']);
    }
}
