<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\License;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class LicensesController extends Controller
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
            'destroy' => route('admin.licenses.destroy', ['license' => '__LICENSE__']),            
            'store'   => route('admin.licenses.store'),   
            'loadtable'   => route('admin.load-licenses'),
            'showform'   => route('admin.licenses.showform'),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin-views.license.index', ['pathurl' => 'licenses', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = License::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.license.partials.actions', compact('row'))->render();
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
            'name' => 'required|unique:licenses,name,' . $request->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        License::updateOrCreate(['id' => $request->id], ['name' => $request->name]);

        $msg = $request->id ?'License updated successfully.' : 'License created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = License::where('id', $id)->first();
        }
        return view('admin-views.license.form', compact('data', 'id'));
    }

    public function destroy(License $License)
    {
        $License->delete();

        return response()->json(['status' => true, 'message' => 'License Deleted Successfully.']);
    }
}
