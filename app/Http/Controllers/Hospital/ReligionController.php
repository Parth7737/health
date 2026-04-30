<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Religion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;

class ReligionController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-religion', ['only' => ['store']]);
        $this->middleware('permission:edit-religion', ['only' => ['update']]);
        $this->middleware('permission:delete-religion', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.masters.religion.destroy', ['religion' => '__RELIGION__']),            
            'store'   => route('hospital.masters.religion.store'),   
            'loadtable'   => route('hospital.masters.religion-load'),
            'showform'   => route('hospital.masters.religion.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.masters.religion.index', ['pathurl' => 'religion', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = Religion::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.religion.partials.actions', compact('row'))->render();
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
            'name' => 'required|unique:religions,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)],422);
        }
        Religion::updateOrCreate(['id' => $request->id], ['hospital_id' => $this->hospital_id,'name' => $request->name]);

        $msg = $request->id ?'Religion updated successfully.' : 'Religion created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = Religion::where('id', $id)->first();
        }
        return view('hospital.masters.religion.form', compact('data', 'id'));
    }

    public function destroy(Religion $Religion)
    {
        $Religion->delete();

        return response()->json(['status' => true, 'message' => 'Religion Deleted Successfully.']);
    }
    
}
