<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Speciality;
use App\Models\SchemeType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Yajra\DataTables\Facades\DataTables;

class SpecialityController extends Controller
{
    public $routes = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.specialities.destroy', ['speciality' => '__SPECIALITY__']),            
            'store'   => route('admin.specialities.store'),   
            'loadtable'   => route('admin.specialitiesload'),
            'showform'   => route('admin.specialities.showform'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin-views.speciality.index', ['pathurl' => 'speciality', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = Speciality::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.speciality.partials.actions', compact('row'))->render();
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
            'name' => 'required|unique:specialities,name,' . $request->id,
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Speciality::updateOrCreate(['id' => $request->id], ['name' => $request->name, 'code' => $request->code]);

        $msg = $request->id ?'Speciality updated successfully.' : 'Speciality created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = Speciality::where('id', $id)->first();
        }
        return view('admin-views.speciality.form', compact('data', 'id'));
    }

    public function destroy(Speciality $Speciality)
    {
        $Speciality->delete();

        return response()->json(['status' => true, 'message' => 'Speciality Deleted Successfully.']);
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $file = $request->file('file');

        // Read CSV file
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            $name = mb_convert_encoding($row['Name'], 'UTF-8', 'ISO-8859-1');
            $scheme_type_id = SchemeType::where('name',$row['Scheme Type'])->value('id');
            if (!empty($name) && !empty($row['Code']) && !empty($scheme_type_id)) {
                Speciality::updateOrInsert(
                    ['name' => $name, 'scheme_type_id' => $scheme_type_id],
                    ['name' => $name, 'code' => $row['Code'], 'scheme_type_id' => $scheme_type_id]
                );
            }
        }

        return back()->with('success', 'Specialities imported successfully!');
    }
}
