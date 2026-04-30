<?php

namespace App\Http\Controllers\Admin;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
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
            'destroy' => route('admin.modules.destroy', ['module' => '__MODULE__']),            
            'store'   => route('admin.modules.store'),   
            'loadtable'   => route('admin.modulesload'),
            'showform'   => route('admin.modules.showform'),
        ];
    }

    public function moduleload(Request $request)
    {
        $data = Module::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.module.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function index()
    {
        $pathurl = 'modules';
        $routes = $this->routes;
        return view('admin-views.module.index', compact('pathurl', 'routes'));
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $module = '';
        if($id) {
            $module = Module::where('id', $id)->first();
        }
        return view('admin-views.module.form', compact('module', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:modules,name,' . $request->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Module::updateOrCreate(['id' => $request->id], [
            'name' => $request->name,
        ]);

        $msg = $request->id ?'Module updated successfully.' : 'Module created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(['status' => true, 'message' => 'Module deleted successfully!!']);
    }
}
