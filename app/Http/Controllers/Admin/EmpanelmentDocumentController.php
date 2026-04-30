<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmpanelmentDocument;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EmpanelmentDocumentController extends Controller
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
            'destroy' => route('admin.hospital-documents.destroy', ['hospital_document' => '__HOSPITAL_DOCUMENT__']),            
            'store'   => route('admin.hospital-documents.store'),   
            'loadtable'   => route('admin.hospital-documentsload'),
            'showform'   => route('admin.hospital-documents.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.empanelment-documents.index', ['pathurl' => 'empaneldocuments', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = EmpanelmentDocument::select('*');
        return DataTables::of($data)
            ->addColumn('is_required', function ($row) {
                return $row->is_required ? '<span class="badge badge-danger">Yes</span>' : '<span class="badge badge-default text-black">No</span>' ;
            })
            ->addColumn('actions', function ($row) {
                return view('admin-views.empanelment-documents.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions','is_required'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:empanelment_documents,name,' . $request->id,
            // 'is_required' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        EmpanelmentDocument::updateOrCreate(['id' => $request->id], ['name' => $request->name, 'is_required' => ($request->is_required && $request->is_required == 1 ? 1 : 0)]);
        $msg = $request->id ? 'Document updated successfully.' : 'Document Added Successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = EmpanelmentDocument::where('id', $id)->first();
        }

        return view('admin-views.empanelment-documents.form', compact('data', 'id'));
    }

    public function destroy(Request $request)
    {
        EmpanelmentDocument::where('id', $request->id)->delete();
        return response()->json(['status' => true, 'message' => 'Document Deleted Successfully.']);
    }
}
