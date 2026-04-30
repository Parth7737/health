<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WardController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-ward', ['only' => ['store']]);
        $this->middleware('permission:edit-ward', ['only' => ['store']]);
        $this->middleware('permission:delete-ward', ['only' => ['destroy']]);

        $this->routes = [
            'destroy' => route('hospital.settings.beds.ward.destroy', ['ward' => '__WARD__']),
            'store' => route('hospital.settings.beds.ward.store'),
            'loadtable' => route('hospital.settings.beds.ward-load'),
            'showform' => route('hospital.settings.beds.ward.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.beds.ward.index', [
            'pathurl' => 'ward',
            'pathfloorurl' => 'ward',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Ward::query()
            ->where('hospital_id', $this->hospital_id)
            ->with(['floor:id,name,building_id', 'floor.building:id,building_name']);

        return DataTables::of($data)
            ->addColumn('floor_name', function ($row) {
                return $row->floor?->building?->building_name . ' - ' . $row->floor?->name ?? '';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.beds.ward.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['is_active', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if ($id) {
            $data = Ward::where('hospital_id', $this->hospital_id)
                ->where('id', $id)
                ->first();
        }

        $floors = Floor::where('hospital_id', $this->hospital_id)
            ->with(['building:id,building_name'])
            ->orderBy('name')
            ->get(['id', 'name', 'building_id']);

        return view('hospital.settings.beds.ward.form', compact('id', 'data', 'floors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'floor_id' => 'required|integer|exists:floors,id',
            'ward_name' => 'required|string|max:255',
            'ward_code' => 'required|string|max:100',
            'total_beds' => 'required|integer|min:0|max:2000',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validator->after(function ($validator) use ($request) {
            $exists = Ward::where('hospital_id', $this->hospital_id)
                ->where('ward_code', trim((string) $request->ward_code))
                ->when($request->id, function ($q) use ($request) {
                    return $q->where('id', '!=', $request->id);
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add('ward_code', 'Ward code already exists.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Ward::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'floor_id' => (int) $request->floor_id,
                'ward_name' => trim($request->ward_name),
                'ward_code' => strtoupper(trim($request->ward_code)),
                'total_beds' => (int) $request->total_beds,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        $msg = $request->id ? 'Ward updated successfully.' : 'Ward created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Ward $ward)
    {
        if ($ward->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $ward->delete();

        return response()->json(['status' => true, 'message' => 'Ward deleted successfully.']);
    }
}
