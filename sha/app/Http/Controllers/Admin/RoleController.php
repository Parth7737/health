<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Entity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $entities = Entity::all();
        return view('admin-views.roles.index', compact('roles', 'entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'entity' => 'nullable',
        ]);

        $entityName = Entity::find($request->entity)->name; 

        Role::create([
            'name' => $request->name,
            'entity' => $entityName,
        ]);

        return response()->json(['msg' => 'Role Created Successfully.']);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json(['data' => $role]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'entity' => 'nullable',
        ]);

        $role = Role::findOrFail($id);
        $entityName = Entity::find($request->entity)->name; 

        $role->update([
            'name' => $request->name,
            'entity' => $entityName,
        ]);

        return response()->json(['msg' => 'Role Updated Successfully.']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('success', 'Role Deleted Successfully.');
    }
}
