<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Entity;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public $routes = [];
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.roles.destroy', ['role' => '__ROLE__']),            
            'store'   => route('admin.roles.store'),
            'loadtable'   => route('admin.rolesload'),
            'showform'   => route('admin.roles.showform'),
        ];
    }
    public function index()
    {
        $pathurl = 'roles';
        $routes = $this->routes;
        return view('admin-views.roles.index', compact('pathurl', 'routes'));
    }

    public function rolesload(Request $request)
    {
        $data = Role::query()->whereNull('hospital_id');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.roles.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $groupedPermissions = Permission::all()->groupBy('module');
        $role = '';
        $rolePermissions = [];
        if($id && $id != "") {
            $role = Role::where('id', $id)->whereNull('hospital_id')->first();
            if ($role) {
                $rolePermissions = $role->permissions->pluck('name')->toArray();
            }
        }
        return view('admin-views.roles.form', compact('groupedPermissions', 'rolePermissions', 'role', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $request->id,
            'permissions' => 'array'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }
        $id = $request->id;

        $role = Role::updateOrCreate(
            ['id' => $id],
            [
                'name' => $request->name,
                'hospital_id' => null,
            ]
        );

        $role->syncPermissions($request->permissions ?? []);

        $msg = $id ?'Role updated successfully.' : 'Role created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Role $role)
    {
        $roles = Role::where('id', $role->id)->whereNull('hospital_id')->firstOrFail();

        // Revoke role from users
        foreach ($roles->users as $user) {
            $user->removeRole($roles);
        }

        // Detach permissions from role
        $roles->permissions()->detach();

        // Optional: delete permissions if orphaned
        foreach ($roles->permissions as $permission) {
            if ($permission->roles()->count() === 1) {
                $permission->delete();
            }
        }

        $roles->delete();
        return response()->json(['status' => true, 'message' => 'Role deleted successfully!!']);
    }
}
