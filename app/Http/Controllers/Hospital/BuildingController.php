<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BuildingController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-building', ['only' => ['store']]);
        $this->middleware('permission:edit-building', ['only' => ['store']]);
        $this->middleware('permission:delete-building', ['only' => ['destroy']]);

        $this->routes = [
            'destroy' => route('hospital.settings.beds.building.destroy', ['building' => '__BUILDING__']),
            'store' => route('hospital.settings.beds.building.store'),
            'loadtable' => route('hospital.settings.beds.building-load'),
            'showform' => route('hospital.settings.beds.building.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.beds.building.index', [
            'pathurl' => 'building',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Building::query()->where('hospital_id', $this->hospital_id);

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.beds.building.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if ($id) {
            $data = Building::where('hospital_id', $this->hospital_id)
                ->where('id', $id)
                ->first();
        }

        return view('hospital.settings.beds.building.form', compact('id', 'data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_name' => 'required|string|max:255',
            'building_code' => 'required|string|max:100',
            'floors_count' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->building_code) {
                $exists = Building::where('hospital_id', $this->hospital_id)
                    ->where('building_code', trim($request->building_code))
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('building_code', 'Building code already exists.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Building::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'building_name' => trim($request->building_name),
                'building_code' => strtoupper(trim($request->building_code)),
                'floors_count' => (int) $request->floors_count,
                'description' => $request->description,
            ]
        );

        $msg = $request->id ? 'Building updated successfully.' : 'Building created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Building $building)
    {
        if ($building->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $building->delete();

        return response()->json(['status' => true, 'message' => 'Building deleted successfully.']);
    }
}
