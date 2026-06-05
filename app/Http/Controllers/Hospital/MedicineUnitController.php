<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class MedicineUnitController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-medicine-unit', ['only' => ['store']]);
        $this->middleware('permission:edit-medicine-unit', ['only' => ['update']]);
        $this->middleware('permission:delete-medicine-unit', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.medicine-unit.destroy', ['medicine_unit' => '__MEDICINE_UNIT__']),
            'store'     => route('hospital.settings.pharmacy.medicine-unit.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-unit-load'),
            'showform'  => route('hospital.settings.pharmacy.medicine-unit.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine-unit.index', [
            'pathurl' => 'medicine-unit',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineUnit::select('*');
        return DataTables::of($data)
            ->addColumn('apply_frequency_badge', function ($row) {
                return $row->apply_frequency
                    ? '<span class="badge bg-success">Days × Freq</span>'
                    : '<span class="badge bg-warning text-dark">1 Pack</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine-unit.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['apply_frequency_badge', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineUnit::where('id', $id)->first();
        }
        return view('hospital.settings.pharmacy.medicine-unit.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:medicine_units,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        MedicineUnit::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id'      => $this->hospital_id,
                'name'             => $request->name,
                'apply_frequency'  => $request->has('apply_frequency'),
            ]
        );

        $msg = $request->id ? 'Medicine Unit updated successfully.' : 'Medicine Unit created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineUnit $medicineUnit)
    {
        if ($medicineUnit->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $medicineUnit->delete();
        return response()->json(['status' => true, 'message' => 'Medicine Unit deleted successfully.']);
    }
}
