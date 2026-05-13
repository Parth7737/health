<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityType;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index()
    {
        $entities = Entity::all();
        $entityTypes = EntityType::all(); 
        return view('admin-views.entities.index', compact('entities', 'entityTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|exists:entity_types,name', 
        ]);

        Entity::create([
            'name' => $request->name,
            'type' => $request->type, 
        ]);

        return response()->json(['msg' => 'Entity Added Successfully.']);
    }

    public function show($id)
    {
        $entity = Entity::find($id);
        return response()->json(['data' => $entity]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|exists:entity_types,name',
        ]);

        $entity = Entity::find($id);
        $entity->update([
            'name' => $request->name,
            'type' => $request->type, 
        ]);

        return response()->json(['msg' => 'Entity Updated Successfully.']);
    }

    public function destroy($id)
    {
        $entity = Entity::find($id);
        $entity->delete();

        return redirect()->back()->with('success', 'Entity Deleted Successfully.');
    }
}
