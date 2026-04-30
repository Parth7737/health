<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class MedicineCategoryController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-medicine-category', ['only' => ['store']]);
        $this->middleware('permission:edit-medicine-category', ['only' => ['update']]);
        $this->middleware('permission:delete-medicine-category', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.medicine-category.destroy', ['medicine_category' => '__MEDICINE_CATEGORY__']),
            'store'     => route('hospital.settings.pharmacy.medicine-category.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-category-load'),
            'showform'  => route('hospital.settings.pharmacy.medicine-category.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine-category.index', [
            'pathurl' => 'medicine-category',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineCategory::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine-category.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineCategory::where('id', $id)->first();
        }
        return view('hospital.settings.pharmacy.medicine-category.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:medicine_categories,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        MedicineCategory::updateOrCreate(
            ['id' => $request->id],
            ['hospital_id' => $this->hospital_id, 'name' => $request->name]
        );

        $msg = $request->id ? 'Medicine Category updated successfully.' : 'Medicine Category created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineCategory $medicine_category)
    {
        if ($medicine_category->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $medicine_category->delete();
        return response()->json(['status' => true, 'message' => 'Medicine Category deleted successfully.']);
    }
}
