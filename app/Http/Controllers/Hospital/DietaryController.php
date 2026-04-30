<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Dietary;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;

class DietaryController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-dietary', ['only' => ['store']]);
        $this->middleware('permission:edit-dietary', ['only' => ['update']]);
        $this->middleware('permission:delete-dietary', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.masters.dietary.destroy', ['dietary' => '__DIETARY__']),            
            'store'   => route('hospital.masters.dietary.store'),   
            'loadtable'   => route('hospital.masters.dietary-load'),
            'showform'   => route('hospital.masters.dietary.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.masters.dietary.index', ['pathurl' => 'dietary', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = Dietary::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.dietary.partials.actions', compact('row'))->render();
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
            'name' => 'required|unique:dietaries,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)],422);
        }
        Dietary::updateOrCreate(['id' => $request->id], ['hospital_id' => $this->hospital_id,'name' => $request->name]);

        $msg = $request->id ?'Dietary updated successfully.' : 'Dietary created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = Dietary::where('id', $id)->first();
        }
        return view('hospital.masters.dietary.form', compact('data', 'id'));
    }

    public function destroy(Dietary $Dietary)
    {
        $Dietary->delete();

        return response()->json(['status' => true, 'message' => 'Dietary Deleted Successfully.']);
    }
    
}
