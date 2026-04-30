<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Habit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;

class HabitController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-habits', ['only' => ['store']]);
        $this->middleware('permission:edit-habits', ['only' => ['update']]);
        $this->middleware('permission:delete-habits', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.masters.habits.destroy', ['habit' => '__HABIT__']),            
            'store'   => route('hospital.masters.habits.store'),   
            'loadtable'   => route('hospital.masters.habits-load'),
            'showform'   => route('hospital.masters.habits.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.masters.habit.index', ['pathurl' => 'habit', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = Habit::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.masters.habit.partials.actions', compact('row'))->render();
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
        Habit::updateOrCreate(['id' => $request->id], ['hospital_id' => $this->hospital_id,'name' => $request->name]);

        $msg = $request->id ?'Habit updated successfully.' : 'Habit created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = Habit::where('id', $id)->first();
        }
        return view('hospital.masters.habit.form', compact('data', 'id'));
    }

    public function destroy(Habit $Habit)
    {
        $Habit->delete();

        return response()->json(['status' => true, 'message' => 'Habit Deleted Successfully.']);
    }
    
}
