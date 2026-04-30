<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\ComplainType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class ComplainTypeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-complain-types', ['only' => ['store']]);
        $this->middleware('permission:edit-complain-types', ['only' => ['update']]);
        $this->middleware('permission:delete-complain-types', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.front-office.complain-types.destroy', ['complain_type' => '__COMPLAIN_TYPE__']),            
            'store'   => route('hospital.settings.front-office.complain-types.store'),   
            'loadtable'   => route('hospital.settings.front-office.complain-types-load'),
            'showform'   => route('hospital.settings.front-office.complain-types.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.settings.front-office.complain-type.index', ['pathurl' => 'complain-type', 'routes' => $this->routes]);
    }

    /**
     * Load data for DataTable
     */
    public function loaddata(Request $request)
    {
        $data = ComplainType::select('*');
        
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.front-office.complain-type.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Show the form for creating or editing a resource.
     */
    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = ComplainType::where('id', $id)->first();
        }
        return view('hospital.settings.front-office.complain-type.form', compact('data', 'id'));
    }

    /**
     * Store a newly created or update resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:complain_types,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            ComplainType::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id' => $this->hospital_id,
                    'name' => $request->name,
                ]
            );

            $msg = $request->id ? 'Complain Type updated successfully.' : 'Complain Type created successfully.';
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while saving the data.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComplainType $complain_type)
    {
        try {
            if ($complain_type->hospital_id != $this->hospital_id) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }
            
            $complain_type->delete();
            return response()->json(['status' => true, 'message' => 'Complain Type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the data.'], 500);
        }
    }
}
