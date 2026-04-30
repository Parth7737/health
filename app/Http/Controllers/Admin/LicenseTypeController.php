<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\License;
use App\Models\LicenseType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;

class LicenseTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.license-types.destroy', ['license_type' => '__LICENSE_TYPE__']),            
            'store'   => route('admin.license-types.store'),   
            'loadtable'   => route('admin.load-license-types'),
            'showform'   => route('admin.license-types.showform'),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin-views.license-type.index', ['pathurl' => 'license-types', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = LicenseType::with('license:id,name');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.license-type.partials.actions', compact('row'))->render();
            })
            ->editColumn('is_required', function ($row) {
                return $row->is_required?'Yes':'No';
            })
            ->editColumn('document_required', function ($row) {
                return $row->document_required?'Yes':'No';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:license_types,name,' . $request->id,
            'license_id' => 'nullable|exists:licenses,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $is_required = $request->has('is_required') ? 1 : 0;
        $document_required = $request->has('document_required') ? 1 : 0;
        LicenseType::updateOrCreate(['id' => $request->id], ['license_id' => $request->license_id,'name' => $request->name,'is_required'=>$is_required,'document_required'=>$document_required]);

        $msg = $request->id ?'License Type updated successfully.' : 'License Type created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = LicenseType::where('id', $id)->first();
        }
        return view('admin-views.license-type.form', compact('data', 'id'));
    }

    public function destroy(LicenseType $LicenseType)
    {
        $LicenseType->delete();

        return response()->json(['status' => true, 'message' => 'License Type Deleted Successfully.']);
    }
}
