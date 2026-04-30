<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineFrequency;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class FrequencyController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-frequency', ['only' => ['store']]);
        $this->middleware('permission:edit-frequency', ['only' => ['update']]);
        $this->middleware('permission:delete-frequency', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.frequency.destroy', ['frequency' => '__FREQUENCY__']),
            'store'     => route('hospital.settings.pharmacy.frequency.store'),
            'loadtable' => route('hospital.settings.pharmacy.frequency-load'),
            'showform'  => route('hospital.settings.pharmacy.frequency.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.frequency.index', [
            'pathurl' => 'frequency',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineFrequency::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.frequency.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineFrequency::where('id', $id)->first();
        }
        return view('hospital.settings.pharmacy.frequency.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frequency' => 'required|string|max:255|unique:medicine_frequencies,frequency,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        MedicineFrequency::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'frequency' => $request->frequency,
            ]
        );

        $msg = $request->id ? 'Frequency updated successfully.' : 'Frequency created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineFrequency $frequency)
    {
        if ($frequency->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $frequency->delete();
        return response()->json(['status' => true, 'message' => 'Frequency deleted successfully.']);
    }
}
