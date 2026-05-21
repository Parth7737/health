<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmpanelmentEligibility;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;

class EmpanelmentEligibilityController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.empanelment-eligibilities.destroy', ['empanelment_eligibility' => '__ELIGIBILITY__']),
            'store'   => route('admin.empanelment-eligibilities.store'),
            'loadtable'   => route('admin.load-empanelment-eligibilities'),
            'showform'   => route('admin.empanelment-eligibilities.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.empanelment-eligibility.index', ['pathurl' => 'empanelment-eligibilities', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = EmpanelmentEligibility::select(['id','title','subtitle','is_required']);
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.empanelment-eligibility.partials.actions', compact('row'))->render();
            })
            ->editColumn('is_required', function ($row) {
                return $row->is_required ? 'Yes' : 'No';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:empanelment_eligibilities,title,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $is_required = $request->has('is_required') ? 1 : 0;

        EmpanelmentEligibility::updateOrCreate(['id' => $request->id], ['title' => $request->title, 'subtitle' => $request->subtitle, 'is_required' => $is_required]);

        $msg = $request->id ? 'Eligibility item updated successfully.' : 'Eligibility item created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = EmpanelmentEligibility::where('id', $id)->first();
        }
        return view('admin-views.empanelment-eligibility.form', compact('data', 'id'));
    }

    public function destroy(EmpanelmentEligibility $empanelmentEligibility)
    {
        $empanelmentEligibility->delete();
        return response()->json(['status' => true, 'message' => 'Eligibility item Deleted Successfully.']);
    }
}
