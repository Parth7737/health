<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HospitalType;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class HospitalTypeController extends Controller
{
    public $routes = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.hospitaltypes.destroy', ['hospitaltype' => '__HOSPITALTYPE__']),            
            'store'   => route('admin.hospitaltypes.store'),   
            'loadtable'   => route('admin.hospitaltypesload'),
            'showform'   => route('admin.hospitaltypes.showform'),
        ];
    }

    public function loaddata(Request $request)
    {
        $data = HospitalType::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.hospital_types.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function index()
    {
        return view('admin-views.hospital_types.index', ['pathurl' => 'hospitaltypes', 'routes' => $this->routes]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:hospital_types,name,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
            
            
        }

        HospitalType::updateOrCreate(['id' => $request->id], ['name' => $request->name]);

        $msg = $request->id ?'Hospital type updated successfully.' : 'Hospital type created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $hospitalType = '';
        if($id) {
            $hospitalType = HospitalType::where('id', $id)->first();
        }
        return view('admin-views.hospital_types.form', compact('hospitalType', 'id'));
    }

    public function destroy(Request $request, $id)
    {
        HospitalType::where('id', $request->id)->delete();

        return response()->json(['status' => true, 'message' => 'Hospital Type Deleted Successfully.']);
    }
}
