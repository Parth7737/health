<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TreatmentPlanPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TreatmentPlanPackageController extends Controller
{
    public $routes = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->user() || ! auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }

            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.packages.destroy', ['package' => '__PACKAGE__']),
            'store' => route('admin.packages.store'),
            'loadtable' => route('admin.packages-load'),
            'showform' => route('admin.packages.showform'),
        ];
    }

    public function index()
    {
        return view('admin-views.treatment-plan-package.index', ['pathurl' => 'treatment-plan-package', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = TreatmentPlanPackage::query()->select('*')->orderByDesc('id');

        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.treatment-plan-package.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'code' => 'nullable|string|max:255|unique:packages,code,'.$request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        TreatmentPlanPackage::updateOrCreate(
            ['id' => $request->id],
            ['name' => $request->name, 'code' => $request->code]
        );

        $msg = $request->id ? 'Package updated successfully.' : 'Package created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;
        if ($id) {
            $data = TreatmentPlanPackage::where('id', $id)->first();
        }

        return view('admin-views.treatment-plan-package.form', compact('data', 'id'));
    }

    public function destroy(TreatmentPlanPackage $package)
    {
        $package->delete();

        return response()->json(['status' => true, 'message' => 'Package deleted successfully.']);
    }
}
