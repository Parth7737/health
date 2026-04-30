<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Allergy;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;

class AllergyController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-allergy', ['only' => ['store']]);
        $this->middleware('permission:edit-allergy', ['only' => ['update']]);
        $this->middleware('permission:delete-allergy', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.masters.allergy.destroy', ['allergy' => '__ALLERGY__']),            
            'store'   => route('hospital.masters.allergy.store'),   
            'loadtable'   => route('hospital.masters.allergy-load'),
            'showform'   => route('hospital.masters.allergy.showform'),
        ];
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.masters.allergy.index', ['pathurl' => 'allergy', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = Allergy::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.allergy.partials.actions', compact('row'))->render();
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
            'name' => 'required|unique:allergies,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)],422);
        }
        Allergy::updateOrCreate(['id' => $request->id], ['hospital_id' => $this->hospital_id,'name' => $request->name]);

        $msg = $request->id ?'Allergy updated successfully.' : 'Allergy created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = Allergy::where('id', $id)->first();
        }
        return view('hospital.masters.allergy.form', compact('data', 'id'));
    }

    public function destroy(Allergy $Allergy)
    {
        $Allergy->allergyReaction()->delete();
        $Allergy->delete();

        return response()->json(['status' => true, 'message' => 'Allergy Deleted Successfully.']);
    }
    
}
