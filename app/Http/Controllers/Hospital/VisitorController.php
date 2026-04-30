<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\Visitor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VisitorController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-visitor', ['only' => ['store']]);
        $this->middleware('permission:edit-visitor', ['only' => ['update']]);
        $this->middleware('permission:delete-visitor', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.front-office.visitors.destroy', ['visitor' => '__VISITOR__']),            
            'store'   => route('hospital.front-office.visitors.store'),   
            'loadtable'   => route('hospital.front-office.visitors-load'),
            'showform'   => route('hospital.front-office.visitor.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hospital.front-office.visitors.index', ['pathurl' => 'visitors', 'routes' => $this->routes]);
    }

    /**
     * Load data for DataTable
     */
    public function loaddata(Request $request)
    {
        $data = Visitor::with('visitor_purpose:id,name');
        
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.front-office.visitors.partials.actions', compact('row'))->render();
            })->editcolumn('visit_date', function($row) {
                return Carbon::parse($row->visit_date)->format('d-m-Y');
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
            $data = Visitor::where('id', $id)->first();
        }
        return view('hospital.front-office.visitors.form', compact('data', 'id'));
    }

    /**
     * Store a newly created or update resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'visitor_purpose_id' => 'required',
            'document' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        if($request->hasFile('document')) {
            if($request->id) {
                $existingVisitor = Visitor::find($request->id);
                if ($existingVisitor && $existingVisitor->document) {
                    Storage::disk('public')->delete($existingVisitor->document);
                }
            }
            $filePath = Storage::disk('public')->put('visitor_documents', $request->file('document')); 
            $request->document = $filePath;
        }
        Visitor::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->name,
                'visitor_purpose_id' => $request->visitor_purpose_id,
                'phone' => $request->phone,
                'id_card' => $request->id_card,
                'number_of_persons' => $request->number_of_persons,
                'visit_date' => Carbon::parse($request->visit_date)->format('Y-m-d'),
                'in_time' => $request->in_time,
                'out_time' => $request->out_time,
                'document' => $request->document ?? null,
            ]
        );

        $msg = $request->id ? 'Visitor updated successfully.' : 'Visitor created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visitor $visitor)
    {
        try {
            
            if($visitor->document) {
                Storage::disk('public')->delete($visitor->document);
            }
            $visitor->delete();
            return response()->json(['status' => true, 'message' => 'Visitor deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the data.'], 500);
        }
    }
}
