<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class MedicineController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-medicine', ['only' => ['store']]);
        $this->middleware('permission:edit-medicine', ['only' => ['update']]);
        $this->middleware('permission:delete-medicine', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.medicine.destroy', ['medicine' => '__MEDICINE__']),
            'store'     => route('hospital.settings.pharmacy.medicine.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-load'),
            'showform'  => route('hospital.settings.pharmacy.medicine.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine.index', [
            'pathurl' => 'medicine',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Medicine::with('category');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = Medicine::where('id', $id)->first();
        }
        $categories = MedicineCategory::where('hospital_id', $this->hospital_id)->get();
        return view('hospital.settings.pharmacy.medicine.form', compact('data', 'id', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'medicine_category_id' => 'nullable|exists:medicine_categories,id',
            'generic_name' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'composition' => 'nullable|string',
            'min_level' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'vat' => 'nullable|integer',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Medicine::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'medicine_category_id' => $request->medicine_category_id,
                'name' => $request->name,
                'generic_name' => $request->generic_name,
                'company' => $request->company,
                'unit' => $request->unit,
                'composition' => $request->composition,
                'min_level' => $request->min_level,
                'reorder_level' => $request->reorder_level,
                'vat' => $request->vat,
                'image' => $request->image,
                'description' => $request->description,
            ]
        );

        $msg = $request->id ? 'Medicine updated successfully.' : 'Medicine created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $medicine->delete();
        return response()->json(['status' => true, 'message' => 'Medicine deleted successfully.']);
    }
}
