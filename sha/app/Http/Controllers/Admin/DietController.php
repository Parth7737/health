<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diet;
use Illuminate\Http\Request;

class DietController extends Controller
{
    public function index()
    {
        $diets = Diet::all();
        return view('admin-views.diets.index', compact('diets'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Diet::create($request->only('name'));
        return response()->json(['msg' => 'Diet added successfully.']);
    }

    public function show(Diet $diet)
    {
        return response()->json(['data' => $diet]);
    }

    public function update(Request $request, Diet $diet)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $diet->update($request->only('name'));
        return response()->json(['msg' => 'Diet updated successfully.']);
    }

    public function destroy(Diet $diet)
    {
        $diet->delete();
        return back()->with('success', 'Diet deleted successfully.');
    }
}
