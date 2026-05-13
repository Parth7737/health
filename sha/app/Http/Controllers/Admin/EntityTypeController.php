<?php

namespace App\Http\Controllers\Admin;

use App\Models\EntityType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EntityTypeController extends Controller
{
    public function index()
    {
        $entityTypes = EntityType::all(); 
        return view('admin-views.entityTypes.index', compact('entityTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:entity_types,name',
        ]);

        $entityType = new EntityType();
        $entityType->name = $request->name;
        $entityType->save();

        return response()->json(['msg' => 'Entity Type Added Successfully.']);
    }

    public function show($id)
    {
        $entityType = EntityType::find($id);
        return response()->json(['data' => $entityType]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:entity_types,name,' . $id,
        ]);

        $entityType = EntityType::find($id);
        $entityType->name = $request->name;
        $entityType->save();

        return response()->json(['msg' => 'Entity Type Updated Successfully.']);
    }

    public function destroy($id)
    {
        $entityType = EntityType::find($id);
        $entityType->delete();

        return redirect()->back()->with('success', 'Entity Type Deleted Successfully.');
    }
}
