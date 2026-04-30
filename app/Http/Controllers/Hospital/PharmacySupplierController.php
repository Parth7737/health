<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\PharmacySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PharmacySupplierController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pharmacy-supplier', ['only' => ['store']]);
        $this->middleware('permission:delete-pharmacy-supplier', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.supplier.destroy', ['supplier' => '__ID__']),
            'store'     => route('hospital.settings.pharmacy.supplier.store'),
            'loadtable' => route('hospital.settings.pharmacy.supplier-load'),
            'showform'  => route('hospital.settings.pharmacy.supplier.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.supplier.index', [
            'pathurl' => 'pharmacy-supplier',
            'routes'  => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = PharmacySupplier::query()->latest('id');

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.supplier.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id   = $request->id;
        $data = $id ? PharmacySupplier::where('id', $id)->firstOrFail() : null;

        return view('hospital.settings.pharmacy.supplier.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'gstin'          => 'nullable|string|max:20',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        PharmacySupplier::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id'    => $this->hospital_id,
                'name'           => $request->name,
                'contact_person' => $request->contact_person,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'address'        => $request->address,
                'gstin'          => $request->gstin,
                'notes'          => $request->notes,
            ]
        );

        $msg = $request->id ? 'Supplier updated successfully.' : 'Supplier created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(PharmacySupplier $supplier)
    {
        if ($supplier->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $supplier->delete();
        return response()->json(['status' => true, 'message' => 'Supplier deleted successfully.']);
    }
}
