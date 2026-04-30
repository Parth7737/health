<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineDosage;
use App\Models\MedicineCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class MedicineDosageController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-medicine-dosage', ['only' => ['store']]);
        $this->middleware('permission:edit-medicine-dosage', ['only' => ['update']]);
        $this->middleware('permission:delete-medicine-dosage', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.medicine-dosage.destroy', ['medicine_dosage' => '__MEDICINE_DOSAGE__']),
            'store'     => route('hospital.settings.pharmacy.medicine-dosage.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-dosage-load'),
            'showform'  => route('hospital.settings.pharmacy.medicine-dosage.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine-dosage.index', [
            'pathurl' => 'medicine-dosage',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineDosage::select('*')->with('category');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine-dosage.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineDosage::where('id', $id)->first();
        }
        $categories = MedicineCategory::where('hospital_id', $this->hospital_id)->get();
        return view('hospital.settings.pharmacy.medicine-dosage.form', compact('data', 'id', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_category_id' => 'required|exists:medicine_categories,id',
            'dosage' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = MedicineDosage::where('medicine_category_id', $request->medicine_category_id)
                        ->where('dosage', $value)
                        ->when($request->id, function ($q) use ($request) {
                            return $q->where('id', '!=', $request->id);
                        })->exists();
                    if ($exists) {
                        $fail('The dosage for this category already exists.');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        MedicineDosage::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'medicine_category_id' => $request->medicine_category_id,
                'dosage' => $request->dosage,
            ]
        );

        $msg = $request->id ? 'Medicine Dosage updated successfully.' : 'Medicine Dosage created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineDosage $medicineDosage)
    {
        if ($medicineDosage->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $medicineDosage->delete();
        return response()->json(['status' => true, 'message' => 'Medicine Dosage deleted successfully.']);
    }
}
