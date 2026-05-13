<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bowels;
use Illuminate\Http\Request;

class BowelController extends Controller
{
    public function index()
    {
        $bowels = Bowels::all();
        return view('admin-views.bowels.index', compact('bowels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bowel = Bowels::create(['name' => $request->name]);

        return response()->json(['msg' => 'Bowel added successfully!', 'data' => $bowel]);
    }

    public function show($id)
    {
        $bowel = Bowels::findOrFail($id);
        return response()->json(['data' => $bowel]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bowel = Bowels::findOrFail($id);
        $bowel->update(['name' => $request->name]);

        return response()->json(['msg' => 'Bowel updated successfully!', 'data' => $bowel]);
    }

    public function destroy($id)
    {
        $bowel = Bowels::findOrFail($id);
        $bowel->delete();

        return redirect()->back()->with('success', 'Bowel deleted successfully!');
    }
}
