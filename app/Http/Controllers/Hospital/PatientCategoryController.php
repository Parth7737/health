<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\PatientCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class PatientCategoryController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-patient-category', ['only' => ['store']]);
        $this->middleware('permission:edit-patient-category', ['only' => ['update']]);
        $this->middleware('permission:delete-patient-category', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.masters.patient-category.destroy', ['patient_category' => '__PATIENT_CATEGORY__']),
            'store'     => route('hospital.masters.patient-category.store'),
            'loadtable' => route('hospital.masters.patient-category-load'),
            'showform'  => route('hospital.masters.patient-category.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.masters.patient-category.index', [
            'pathurl' => 'patient-category',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PatientCategory::select('*')->where('hospital_id', $this->hospital_id);
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.patient-category.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = PatientCategory::where('id', $id)->where('hospital_id', $this->hospital_id)->first();
        }
        return view('hospital.masters.patient-category.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'waiver_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->name) {
                $exists = PatientCategory::where('hospital_id', $this->hospital_id)
                    ->where('name', $request->name)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })->exists();
                if ($exists) {
                    $validator->errors()->add('name', 'Patient Category with this name already exists.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        PatientCategory::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
                'waiver_percentage' => $request->waiver_percentage,
            ]
        );

        $msg = $request->id ? 'Patient Category updated successfully.' : 'Patient Category created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PatientCategory $patient_category)
    {
        if ($patient_category->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $patient_category->delete();
        return response()->json(['status' => true, 'message' => 'Patient Category deleted successfully.']);
    }
}