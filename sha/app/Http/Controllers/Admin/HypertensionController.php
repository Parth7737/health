<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hypertension;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HypertensionController extends Controller
{
    public function index()
    {
        $hypertensions = Hypertension::all();
        return view('admin-views.hypertension.index', compact('hypertensions'));
    }

    public function create()
    {
        return view('admin.hypertension.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Hypertension::create($request->all());

        return response()->json(['msg' => 'Hypertension Added Successfully.']);
    }

    public function edit(Hypertension $hypertension)
    {
        return response()->json(['data' => $hypertension]);
    }

    public function update(Request $request, Hypertension $hypertension)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $hypertension->update($request->all());

        return response()->json(['msg' => 'Hypertension Updated Successfully.']);
    }

    public function destroy(Hypertension $hypertension)
    {
        $hypertension->delete();
        return redirect()->back()->with('success', 'Hypertension Deleted Successfully.');
    }
}
