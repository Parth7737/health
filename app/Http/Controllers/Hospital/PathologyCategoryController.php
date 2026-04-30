<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\PathologyCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class PathologyCategoryController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pathology-category', ['only' => ['store']]);
        $this->middleware('permission:edit-pathology-category', ['only' => ['update']]);
        $this->middleware('permission:delete-pathology-category', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pathology.category.destroy', ['category' => '__CATEGORY__']),
            'store'     => route('hospital.settings.pathology.category.store'),
            'loadtable' => route('hospital.settings.pathology.category-load'),
            'showform'  => route('hospital.settings.pathology.category.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pathology.category.index', [
            'pathurl' => 'pathology-category',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PathologyCategory::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pathology.category.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = PathologyCategory::where('id', $id)->first();
        }
        return view('hospital.settings.pathology.category.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:pathology_categories,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        PathologyCategory::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Pathology Category updated successfully.' : 'Pathology Category created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PathologyCategory $category)
    {
        if ($category->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $category->delete();
        return response()->json(['status' => true, 'message' => 'Pathology Category deleted successfully.']);
    }
}
