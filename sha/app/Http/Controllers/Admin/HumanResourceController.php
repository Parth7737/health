<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HumanResource;
use Illuminate\Http\Request;

class HumanResourceController extends Controller
{
    public function index()
    {
        $humanResources = HumanResource::all();
        return view('admin-views.human_resources.index', compact('humanResources'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'type_slug' => 'required',
            'name' => 'required|string|max:255',
        ]);

        HumanResource::create($request->all());
        return response()->json(['msg' => 'Human Resource added successfully!']);
    }

    public function show($id)
    {
        $humanResource = HumanResource::findOrFail($id);
        return response()->json(['data' => $humanResource]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required',
            'type_slug' => 'required',
            'name' => 'required|string|max:255',
        ]);

        $humanResource = HumanResource::findOrFail($id);
        $humanResource->update($request->all());
        return response()->json(['msg' => 'Human Resource updated successfully!']);
    }

    public function destroy($id)
    {
        $humanResource = HumanResource::findOrFail($id);
        $humanResource->delete();
        return redirect()->back()->with('success', 'Human Resource deleted successfully.');
    }
}
