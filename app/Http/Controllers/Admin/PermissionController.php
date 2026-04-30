<?php

namespace App\Http\Controllers\Admin;
use Spatie\Permission\Models\Permission;
use App\Models\Module;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
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
            'destroy' => route('admin.permissions.destroy', ['permission' => '__PERMISSION__']),            
            'store'   => route('admin.permissions.store'),         
            'loadtable'   => route('admin.permissionsload'),
            'showform'   => route('admin.permissions.showform'),
        ];
    }

    public function permissionload(Request $request)
    {
        $data = Permission::select('*');
        return DataTables::of($data)
            ->addColumn('module_name', function ($row) {
                $module_data = Module::find($row->module);
                return @$module_data->name ?? '';
            })
            ->addColumn('actions', function ($row) {
                return view('admin-views.permission.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions', 'module_name'])
            ->make(true);
    }

    public function index()
    {
        $pathurl = 'permissions';
        $routes = $this->routes;
        return view('admin-views.permission.index', compact( 'pathurl', 'routes'));
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $permission = '';
        if($id) {
            $permission = Permission::where('id', $id)->first();
        }
        $modules = Module::all();
        return view('admin-views.permission.form', compact('modules', 'id', 'permission'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $request->id,
            'module' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        Permission::updateOrCreate(['id' => $request->id], [
            'name' => $request->name,
            'module' => $request->module
        ]);

        $msg = $request->id ?'Permission updated successfully.' : 'Permission created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['status' => true, 'message' => 'Permission deleted successfully!!']);
    }
}
