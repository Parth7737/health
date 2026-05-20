<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffStrength;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StaffStrengthController extends Controller
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
            'destroy'   => route('admin.staff-strengths.destroy', ['staff_strength' => '__STAFFSTRENGTH__']),
            'store'     => route('admin.staff-strengths.store'),
            'loadtable' => route('admin.staff-strengths-load'),
            'showform'  => route('admin.staff-strengths.showform'),
        ];
    }

    public function loaddata(Request $request)
    {
        $data = StaffStrength::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.staff_strengths.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function index()
    {
        return view('admin-views.staff_strengths.index', [
            'pathurl' => 'staffstrengths',
            'routes'  => $this->routes,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:staff_strengths,name,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        StaffStrength::updateOrCreate(['id' => $request->id], ['name' => $request->name]);

        $msg = $request->id ? 'Staff strength updated successfully.' : 'Staff strength created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $staffStrength = '';
        if ($id) {
            $staffStrength = StaffStrength::where('id', $id)->first();
        }
        return view('admin-views.staff_strengths.form', compact('staffStrength', 'id'));
    }

    public function destroy(Request $request, $staff_strength)
    {
        StaffStrength::where('id', $request->id)->delete();
        return response()->json(['status' => true, 'message' => 'Staff strength deleted successfully.']);
    }
}
