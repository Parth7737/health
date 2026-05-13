<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Licenses;
use Illuminate\Http\Request;

class LicensesController extends Controller
{
    public function index()
    {
        $licenses = Licenses::all();
        return view('admin-views.licenses.index', compact('licenses'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Licenses::create($request->only('name'));
        return response()->json(['msg' => 'License added successfully.']);
    }

    public function show($id)
    {
        $license = Licenses::findOrFail($id);
        return response()->json(['data' => $license]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $license = Licenses::findOrFail($id);
        $license->update($request->only('name'));
        return response()->json(['msg' => 'License updated successfully.']);
    }

    public function destroy($id)
    {
        Licenses::destroy($id);
        return redirect()->back()->with('success', 'License deleted successfully.');
    }
}
