<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;

class ServiceController extends Controller
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
            'destroy' => route('admin.services.destroy', ['service' => '__SERVICE__']),            
            'store'   => route('admin.services.store'),   
            'loadtable'   => route('admin.load-services'),
            'showform'   => route('admin.services.showform'),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin-views.service.index', ['pathurl' => 'services', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = Service::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.service.partials.actions', compact('row'))->render();
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
            'name' => 'required|unique:services,name,' . $request->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Service::updateOrCreate(['id' => $request->id], ['name' => $request->name]);

        $msg = $request->id ?'Service updated successfully.' : 'Service created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = Service::where('id', $id)->first();
        }
        return view('admin-views.service.form', compact('data', 'id'));
    }

    public function destroy(Service $Service)
    {
        $Service->delete();

        return response()->json(['status' => true, 'message' => 'Service Deleted Successfully.']);
    }
    
}
