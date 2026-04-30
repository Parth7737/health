<?php
namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Floor;
use App\Models\Building;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class FloorController extends BaseHospitalController
{

    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-floor', ['only' => ['store']]);
        $this->middleware('permission:edit-floor', ['only' => ['store']]);
        $this->middleware('permission:delete-floor', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.beds.floor.destroy', ['floor' => '__FLOOR__']),
            'store'     => route('hospital.settings.beds.floor.store'),
            'loadtable' => route('hospital.settings.beds.floor-load'),
            'showform'  => route('hospital.settings.beds.floor.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.beds.floor.index', [
            'pathurl' => 'floor',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Floor::with(['building:id,building_name'])
            ->where('hospital_id', $this->hospital_id)
            ->select('floors.*');

        return DataTables::of($data)
            ->addColumn('building_name', function ($row) {
                return $row->building?->building_name ?? '-';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.beds.floor.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = Floor::where('id', $id)->where('hospital_id', $this->hospital_id)->first();
        }
        $buildings = Building::where('hospital_id', $this->hospital_id)
            ->orderBy('building_name')
            ->get(['id', 'building_name']);
        return view('hospital.settings.beds.floor.form', compact('data', 'id', 'buildings'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'name' => 'required|string|max:20',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->building_id && $request->name) {
                $exists = Floor::where('hospital_id', $this->hospital_id)
                    ->where('building_id', $request->building_id)
                    ->where('name', trim($request->name))
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })->exists();
                if ($exists) {
                    $validator->errors()->add('name', 'Floor name already exists for this building.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Floor::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id'  => $this->hospital_id,
                'building_id'  => (int) $request->building_id,
                'name' => trim($request->name),
            ]
        );

        $msg = $request->id ? 'Floor updated successfully.' : 'Floor created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Floor $floor)
    {
        if ($floor->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $floor->delete();
        return response()->json(['status' => true, 'message' => 'Floor deleted successfully.']);
    }
}
