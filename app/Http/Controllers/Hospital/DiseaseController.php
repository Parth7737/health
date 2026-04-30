<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Disease;
use App\Models\DiseaseType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;

class DiseaseController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-diseases', ['only' => ['store']]);
        $this->middleware('permission:edit-diseases', ['only' => ['update']]);
        $this->middleware('permission:delete-diseases', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.masters.diseases.destroy', ['disease' => '__DISEASE__']),            
            'store'   => route('hospital.masters.diseases.store'),   
            'loadtable'   => route('hospital.masters.diseases-load'),
            'showform'   => route('hospital.masters.diseases.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.masters.disease.index', ['pathurl' => 'disease', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = Disease::select('*')->with('diseaseType');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.disease.partials.actions', compact('row'))->render();
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
            'name' => 'required|unique:diseases,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
            'disease_type_id' => 'nullable|exists:disease_types,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)],422);
        }
        Disease::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'disease_type_id' => $request->disease_type_id,
                'name' => $request->name,
            ]
        );

        $msg = $request->id ?'Disease updated successfully.' : 'Disease created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = Disease::where('id', $id)->first();
        }
        $diseaseTypes = DiseaseType::select('id', 'name')->get();
        return view('hospital.masters.disease.form', compact('data', 'id', 'diseaseTypes'));
    }

    public function destroy(Disease $Disease)
    {
        $Disease->delete();

        return response()->json(['status' => true, 'message' => 'Disease Deleted Successfully.']);
    }
    
}
