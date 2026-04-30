<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\RadiologyCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class RadiologyCategoryController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-radiology-category', ['only' => ['store']]);
        $this->middleware('permission:edit-radiology-category', ['only' => ['update']]);
        $this->middleware('permission:delete-radiology-category', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.radiology.category.destroy', ['category' => '__CATEGORY__']),
            'store'     => route('hospital.settings.radiology.category.store'),
            'loadtable' => route('hospital.settings.radiology.category-load'),
            'showform'  => route('hospital.settings.radiology.category.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.radiology.category.index', [
            'pathurl' => 'radiology-category',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = RadiologyCategory::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.radiology.category.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = RadiologyCategory::where('id', $id)->first();
        }
        return view('hospital.settings.radiology.category.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:radiology_categories,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        RadiologyCategory::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Radiology Category updated successfully.' : 'Radiology Category created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(RadiologyCategory $category)
    {
        if ($category->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $category->delete();
        return response()->json(['status' => true, 'message' => 'Radiology Category deleted successfully.']);
    }
}
