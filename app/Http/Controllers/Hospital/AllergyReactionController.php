<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\AllergyReaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;

class AllergyReactionController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-allergy-reaction', ['only' => ['store']]);
        $this->middleware('permission:edit-allergy-reaction', ['only' => ['update']]);
        $this->middleware('permission:delete-allergy-reaction', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.masters.allergy-reaction.destroy', ['allergy_reaction' => '__ALLERGY_REACTION__']),            
            'store'   => route('hospital.masters.allergy-reaction.store'),   
            'loadtable'   => route('hospital.masters.allergy-reaction-load'),
            'showform'   => route('hospital.masters.allergy-reaction.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.masters.allergy-reaction.index', ['pathurl' => 'allergy-reaction', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = AllergyReaction::select('*')->with('allergy:id,name');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.allergy-reaction.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:allergy_reactions,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
            'allergy_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)],422);
        }
        AllergyReaction::updateOrCreate(['id' => $request->id], ['hospital_id' => $this->hospital_id,'allergy_id' => $request->allergy_id,'name' => $request->name]);

        $msg = $request->id ?'Allergy Reaction updated successfully.' : 'Allergy Reaction created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = AllergyReaction::where('id', $id)->first();
        }
        return view('hospital.masters.allergy-reaction.form', compact('data', 'id'));
    }

    public function destroy(AllergyReaction $AllergyReaction)
    {
        $AllergyReaction->delete();

        return response()->json(['status' => true, 'message' => 'Allergy Reaction Deleted Successfully.']);
    }
    
}
