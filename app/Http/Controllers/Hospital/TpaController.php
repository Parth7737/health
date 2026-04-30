<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Tpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TpaController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-tpa', ['only' => ['store']]);
        $this->middleware('permission:edit-tpa', ['only' => ['update']]);
        $this->middleware('permission:delete-tpa', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.tpa-management.tpas.destroy', ['tpa' => '__TPA__']),
            'store' => route('hospital.tpa-management.tpas.store'),
            'loadtable' => route('hospital.tpa-management.tpas-load'),
            'showform' => route('hospital.tpa-management.tpas.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.tpa-management.tpas.index', [
            'pathurl' => 'tpa',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Tpa::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.tpa-management.tpas.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = Tpa::where('id', $id)->first();
        }

        return view('hospital.tpa-management.tpas.form', compact('data', 'id'));
    }

    public function loadTpas(Request $request)
    {
        $tpas = Tpa::orderBy('name')
            ->get(['id', 'name']);

        return response()->json($tpas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->name) {
                $exists = Tpa::where('name', $request->name)
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('name', 'TPA with this name already exists.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Tpa::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'contact_person' => $request->contact_person,
                'contact_person_phone' => $request->contact_person_phone,
                'address' => $request->address,
            ]
        );

        $msg = $request->id ? 'TPA updated successfully.' : 'TPA created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Tpa $tpa)
    {
        if ($tpa->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $tpa->delete();
        return response()->json(['status' => true, 'message' => 'TPA deleted successfully.']);
    }
}
