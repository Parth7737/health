<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineAllergyMapping;
use App\Models\Medicine;
use App\Models\Allergy;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class MedicineAllergyMappingController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-medicine-allergy-mapping', ['only' => ['store']]);
        $this->middleware('permission:delete-medicine-allergy-mapping', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.medicine-allergy-mapping.destroy', ['medicine_allergy_mapping' => '__MEDICINE_ALLERGY_MAPPING__']),
            'store'     => route('hospital.settings.pharmacy.medicine-allergy-mapping.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-allergy-mapping-load'),
            'showform'  => route('hospital.settings.pharmacy.medicine-allergy-mapping.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine-allergy-mapping.index', [
            'pathurl' => 'medicine-allergy-mapping',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineAllergyMapping::with(['medicine', 'allergy']);
        return DataTables::of($data)
            ->addColumn('medicine_name', function ($row) {
                return $row->medicine ? $row->medicine->name : 'N/A';
            })
            ->addColumn('allergy_name', function ($row) {
                return $row->allergy ? $row->allergy->name : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine-allergy-mapping.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineAllergyMapping::where('id', $id)->first();
        }
        $medicines = Medicine::orderBy('name')->get();
        $allergies = Allergy::orderBy('name')->get();
        return view('hospital.settings.pharmacy.medicine-allergy-mapping.form', compact('data', 'id', 'medicines', 'allergies'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|exists:medicines,id',
            'allergy_id' => 'required|exists:allergies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        // Check if mapping already exists
        $exists = MedicineAllergyMapping::where('medicine_id', $request->medicine_id)
            ->where('allergy_id', $request->allergy_id)
            ->when($request->id, function ($query) use ($request) {
                $query->where('id', '!=', $request->id);
            })
            ->exists();

        if ($exists) {
            return response()->json(['errors' => [
                ['code' => 'allergy_id', 'message' => 'This medicine allergy mapping already exists.']
            ]], 422);
        }

        MedicineAllergyMapping::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'medicine_id' => $request->medicine_id,
                'allergy_id' => $request->allergy_id,
            ]
        );

        $msg = $request->id ? 'Medicine Allergy Mapping updated successfully.' : 'Medicine Allergy Mapping created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineAllergyMapping $medicineAllergyMapping)
    {
        if ($medicineAllergyMapping->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $medicineAllergyMapping->delete();
        return response()->json(['status' => true, 'message' => 'Medicine Allergy Mapping deleted successfully.']);
    }
}
