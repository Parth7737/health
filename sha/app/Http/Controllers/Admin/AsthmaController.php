<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asthma;

class AsthmaController extends Controller
{
    public function index()
    {
        $asthmas = Asthma::all();
        return view('admin-views.asthmas.index', compact('asthmas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Asthma::create(['name' => $request->name]);

        return response()->json(['msg' => 'Asthma Type added successfully!']);
    }

    public function show($id)
    {
        $asthma = Asthma::findOrFail($id);
        return response()->json(['data' => $asthma]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $asthma = Asthma::findOrFail($id);
        $asthma->update(['name' => $request->name]);

        return response()->json(['msg' => 'Asthma Type updated successfully!']);
    }

    public function destroy($id)
    {
        Asthma::destroy($id);
        return redirect()->back()->with('success', 'Asthma Type deleted successfully!');
    }
}
