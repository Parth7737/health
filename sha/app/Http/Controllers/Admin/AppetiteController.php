<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appetite;
use Illuminate\Http\Request;

class AppetiteController extends Controller
{
    public function index()
    {
        $appetites = Appetite::all();
        return view('admin-views.appetites.index', compact('appetites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Appetite::create($request->only('name'));

        return response()->json(['msg' => 'Appetite created successfully.']);
    }

    public function show($id)
    {
        $appetite = Appetite::findOrFail($id);
        return response()->json(['data' => $appetite]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $appetite = Appetite::findOrFail($id);
        $appetite->update($request->only('name'));

        return response()->json(['msg' => 'Appetite updated successfully.']);
    }

    public function destroy($id)
    {
        $appetite = Appetite::findOrFail($id);
        $appetite->delete();

        return redirect()->back()->with('success', 'Appetite deleted successfully.');
    }
}
