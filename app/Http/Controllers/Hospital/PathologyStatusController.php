<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\PathologyStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class PathologyStatusController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pathology-status', ['only' => ['store']]);
        $this->middleware('permission:edit-pathology-status', ['only' => ['update']]);
        $this->middleware('permission:delete-pathology-status', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pathology.status.destroy', ['status' => '__STATUS__']),
            'store'     => route('hospital.settings.pathology.status.store'),
            'loadtable' => route('hospital.settings.pathology.status-load'),
            'showform'  => route('hospital.settings.pathology.status.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pathology.status.index', [
            'pathurl' => 'pathology-status',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PathologyStatus::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pathology.status.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = PathologyStatus::where('id', $id)->first();
        }
        return view('hospital.settings.pathology.status.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:pathology_statuses,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        PathologyStatus::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ? 'Pathology Status updated successfully.' : 'Pathology Status created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PathologyStatus $status)
    {
        if ($status->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $status->delete();
        return response()->json(['status' => true, 'message' => 'Pathology Status deleted successfully.']);
    }
}
