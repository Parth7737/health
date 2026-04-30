<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\HospitalRolePermissionOverride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:manage-roles');

        $this->routes = [
            'destroy' => route('hospital.settings.role-management.destroy', ['role' => '__ROLE__']),
            'store' => route('hospital.settings.role-management.store'),
            'loadtable' => route('hospital.settings.role-management.load'),
            'showform' => route('hospital.settings.role-management.showform'),
        ];
    }

    public function index()
    {
        $pathurl = 'roles';
        $routes = $this->routes;

        return view('hospital.settings.role-management.index', compact('pathurl', 'routes'));
    }

    public function rolesload(Request $request)
    {
        $data = Role::query()
            ->where('name', '!=', 'Master Admin')
            ->where(function ($q) {
                $q->whereNull('hospital_id')
                    ->orWhere('hospital_id', $this->hospital_id);
            })
            ->orderBy('name');

        return DataTables::of($data)
            ->editColumn('name', function ($row) {
                if ($row->hospital_id === $this->hospital_id) {
                    return $row->name . ' <span class="badge bg-success">Hospital</span>';
                }

                return $row->name . ' <span class="badge bg-info">Admin</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.role-management.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['name', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $role = null;
        $rolePermissions = [];
        $isGlobalRole = false;
        $manageablePermissionNames = $this->resolveHospitalPermissionCatalogNames();

        if (!empty($id)) {
            $role = $this->resolveAccessibleRole($id);
            $this->ensureRoleCanBeManaged($role);
            $isGlobalRole = is_null($role->hospital_id);
            $rolePermissions = $this->resolveEffectivePermissions($role);
        }

        $groupedPermissions = Permission::query()
            ->whereIn('name', $manageablePermissionNames)
            ->get()
            ->groupBy('module');

        return view('hospital.settings.role-management.form', compact('groupedPermissions', 'rolePermissions', 'role', 'id', 'isGlobalRole'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $role = null;

        if (!empty($id)) {
            $role = $this->resolveAccessibleRole($id);
            $this->ensureRoleCanBeManaged($role);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                $role && is_null($role->hospital_id) ? 'nullable' : 'required',
                'unique:roles,name,' . $id,
            ],
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $selectedPermissions = $this->sanitizeRequestedPermissions($request->permissions ?? [], $role);

        if ($role && is_null($role->hospital_id)) {
            $this->syncHospitalOverridesForGlobalRole($role, $selectedPermissions);

            return response()->json([
                'status' => true,
                'message' => 'Role permissions updated for this hospital only.',
            ]);
        }

        $role = Role::updateOrCreate(
            ['id' => $id],
            [
                'name' => $request->name,
                'guard_name' => 'web',
                'is_custom' => 1,
                'hospital_id' => $this->hospital_id,
            ]
        );

        $role->syncPermissions($selectedPermissions);

        $msg = $id ? 'Role updated successfully.' : 'Role created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Role $role)
    {
        if ((int) $role->hospital_id !== (int) $this->hospital_id) {
            return response()->json([
                'status' => false,
                'message' => 'Admin roles cannot be deleted from hospital panel.',
            ], 403);
        }

        foreach ($role->users as $user) {
            $user->removeRole($role);
        }

        HospitalRolePermissionOverride::where('role_id', $role->id)->delete();
        $role->permissions()->detach();
        $role->delete();

        return response()->json(['status' => true, 'message' => 'Role deleted successfully.']);
    }

    private function resolveAccessibleRole(int|string $id): Role
    {
        return Role::query()
            ->where('id', $id)
            ->where('name', '!=', 'Master Admin')
            ->where(function ($q) {
                $q->whereNull('hospital_id')
                    ->orWhere('hospital_id', $this->hospital_id);
            })
            ->firstOrFail();
    }

    private function syncHospitalOverridesForGlobalRole(Role $role, array $selectedPermissions): void
    {
        $selectedLookup = array_fill_keys($selectedPermissions, true);
        $manageablePermissionNames = $this->resolveHospitalPermissionCatalogNames();
        $basePermissionLookup = array_fill_keys($role->permissions()->pluck('name')->all(), true);

        HospitalRolePermissionOverride::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('role_id', $role->id)
            ->whereIn('permission_name', $manageablePermissionNames)
            ->delete();

        if (empty($manageablePermissionNames)) {
            return;
        }

        $rows = collect($manageablePermissionNames)
            ->map(function ($permissionName) use ($selectedLookup, $basePermissionLookup) {
                $isSelected = isset($selectedLookup[$permissionName]);
                $isDefault = isset($basePermissionLookup[$permissionName]);

                if ($isSelected === $isDefault) {
                    return null;
                }

                return [
                    'permission_name' => $permissionName,
                    'is_allowed' => $isSelected ? 1 : 0,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (empty($rows)) {
            return;
        }

        $now = now();
        $payload = [];
        foreach ($rows as $row) {
            $payload[] = [
                'hospital_id' => $this->hospital_id,
                'role_id' => $role->id,
                'permission_name' => $row['permission_name'],
                'is_allowed' => $row['is_allowed'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        HospitalRolePermissionOverride::insert($payload);
    }

    private function resolveEffectivePermissions(Role $role): array
    {
        $defaultPermissions = $role->permissions()->pluck('name')->all();

        if (!is_null($role->hospital_id)) {
            return $defaultPermissions;
        }

        $overrideMap = HospitalRolePermissionOverride::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('role_id', $role->id)
            ->pluck('is_allowed', 'permission_name')
            ->toArray();

        if (empty($overrideMap)) {
            return $defaultPermissions;
        }

        $effective = [];
        $allPermissionNames = Permission::query()->pluck('name')->all();
        foreach ($allPermissionNames as $permissionName) {
            if (array_key_exists($permissionName, $overrideMap)) {
                if ((bool) $overrideMap[$permissionName]) {
                    $effective[] = $permissionName;
                }
                continue;
            }

            if (in_array($permissionName, $defaultPermissions, true)) {
                $effective[] = $permissionName;
            }
        }

        return $effective;
    }

    private function ensureRoleCanBeManaged(Role $role): void
    {
        if (is_null($role->hospital_id) && $role->name === 'Admin') {
            abort(403, 'Admin role cannot be edited from the hospital panel.');
        }
    }

    private function sanitizeRequestedPermissions(array $selectedPermissions, ?Role $role = null): array
    {
        $selected = collect($selectedPermissions)
            ->filter()
            ->map(fn ($permission) => (string) $permission)
            ->unique()
            ->values()
            ->all();

        $allowedPermissions = $this->resolveHospitalPermissionCatalogNames();

        $invalidPermissions = array_values(array_diff($selected, $allowedPermissions));
        if (!empty($invalidPermissions)) {
            abort(403, 'You can only assign permissions that are already available to your login.');
        }

        return $selected;
    }

    private function resolveManageablePermissionNames(): array
    {
        return Permission::query()
            ->get()
            ->filter(fn (Permission $permission) => auth()->user()?->can($permission->name))
            ->pluck('name')
            ->values()
            ->all();
    }

    private function resolveManageablePermissionNamesForRole(Role $role): array
    {
        $basePermissions = $role->permissions()->pluck('name')->all();

        return array_values(array_intersect($this->resolveManageablePermissionNames(), $basePermissions));
    }

    private function resolveHospitalPermissionCatalogNames(): array
    {
        $adminRole = Role::query()
            ->whereNull('hospital_id')
            ->where('name', 'Admin')
            ->first();

        if (!$adminRole) {
            return [];
        }

        return $adminRole->permissions()->pluck('name')->values()->all();
    }
}
