<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MedicineRoutesController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-frequency', ['only' => ['store']]);
        $this->middleware('permission:edit-frequency', ['only' => ['update']]);
        $this->middleware('permission:delete-frequency', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.pharmacy.medicine-route.destroy', ['medicine_route' => '__MEDICINE_ROUTE__']),
            'store' => route('hospital.settings.pharmacy.medicine-route.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-route-load'),
            'showform' => route('hospital.settings.pharmacy.medicine-route.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine-route.index', [
            'pathurl' => 'medicine-route',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineRoute::select('*');

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine-route.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineRoute::where('id', $id)->first();
        }

        return view('hospital.settings.pharmacy.medicine-route.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route' => 'required|string|max:255|unique:medicine_routes,route,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        MedicineRoute::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'route' => $request->route,
            ]
        );

        $msg = $request->id ? 'Medicine Route updated successfully.' : 'Medicine Route created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineRoute $medicine_route)
    {
        if ($medicine_route->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $medicine_route->delete();

        return response()->json(['status' => true, 'message' => 'Medicine Route deleted successfully.']);
    }
}
