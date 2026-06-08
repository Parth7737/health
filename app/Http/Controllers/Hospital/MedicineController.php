<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineUnit;
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
        $data = Medicine::with(['category','unit']);
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
        $units = MedicineUnit::where('hospital_id', $this->hospital_id)->get();
        return view('hospital.settings.pharmacy.medicine.form', compact('data', 'id', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'medicine_category_id' => 'nullable|exists:medicine_categories,id',
            'generic_name' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'medicine_unit_id' => 'nullable|exists:medicine_units,id',
            'composition' => 'nullable|string',
            'min_level' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'vat' => 'nullable|integer',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'is_high_risk' => 'nullable|boolean',
            'requires_rx' => 'nullable|boolean',
            'min_dose' => 'nullable|numeric|min:0',
            'max_dose' => 'nullable|numeric|min:0',
            'max_daily_dose' => 'nullable|numeric|min:0',
            'dose_unit' => 'nullable|string|max:50',
            'weight_based_dose' => 'nullable|boolean',
            'dose_per_kg' => 'nullable|numeric|min:0',
            'pregnancy_risk' => 'nullable|in:safe,caution,moderate,high_risk,contraindicated',
            'renal_adjustment_required' => 'nullable|boolean',
            'liver_adjustment_required' => 'nullable|boolean',
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
                'medicine_unit_id' => $request->medicine_unit_id,
                'composition' => $request->composition,
                'min_level' => $request->min_level,
                'reorder_level' => $request->reorder_level,
                'vat' => $request->vat,
                'image' => $request->image,
                'description' => $request->description,
                'is_high_risk' => $request->has('is_high_risk') ? (bool) $request->is_high_risk : false,
                'requires_rx' => $request->has('requires_rx') ? (bool) $request->requires_rx : false,
                'min_dose' => $request->min_dose,
                'max_dose' => $request->max_dose,
                'max_daily_dose' => $request->max_daily_dose,
                'dose_unit' => $request->dose_unit,
                'weight_based_dose' => $request->has('weight_based_dose') ? (bool) $request->weight_based_dose : false,
                'dose_per_kg' => $request->dose_per_kg,
                'pregnancy_risk' => $request->pregnancy_risk,
                'renal_adjustment_required' => $request->has('renal_adjustment_required') ? (bool) $request->renal_adjustment_required : false,
                'liver_adjustment_required' => $request->has('liver_adjustment_required') ? (bool) $request->liver_adjustment_required : false,
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
