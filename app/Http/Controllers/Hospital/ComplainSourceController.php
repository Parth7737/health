<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\ComplainSource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class ComplainSourceController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-complain-sources', ['only' => ['store']]);
        $this->middleware('permission:edit-complain-sources', ['only' => ['update']]);
        $this->middleware('permission:delete-complain-sources', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.front-office.complain-sources.destroy', ['complain_source' => '__COMPLAIN_SOURCE__']),            
            'store'   => route('hospital.settings.front-office.complain-sources.store'),   
            'loadtable'   => route('hospital.settings.front-office.complain-sources-load'),
            'showform'   => route('hospital.settings.front-office.complain-sources.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.settings.front-office.complain-source.index', ['pathurl' => 'complain-source', 'routes' => $this->routes]);
    }

    /**
     * Load data for DataTable
     */
    public function loaddata(Request $request)
    {
        $data = ComplainSource::select('*');
        
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.front-office.complain-source.partials.actions', compact('row'))->render();
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
            $data = ComplainSource::where('id', $id)->first();
        }
        return view('hospital.settings.front-office.complain-source.form', compact('data', 'id'));
    }

    /**
     * Store a newly created or update resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:complain_sources,name,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            ComplainSource::updateOrCreate(
                ['id' => $request->id],
                [
                    'hospital_id' => $this->hospital_id,
                    'name' => $request->name,
                ]
            );

            $msg = $request->id ? 'Complain Source updated successfully.' : 'Complain Source created successfully.';
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while saving the data.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComplainSource $complain_source)
    {
        try {
            if ($complain_source->hospital_id != $this->hospital_id) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }
            
            $complain_source->delete();
            return response()->json(['status' => true, 'message' => 'Complain Source deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the data.'], 500);
        }
    }
}
