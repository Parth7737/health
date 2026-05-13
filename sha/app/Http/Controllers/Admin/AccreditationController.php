<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accreditation;
use Illuminate\Http\Request;

class AccreditationController extends Controller
{
    public function index()
    {
        $accreditations = Accreditation::all();
        return view('admin-views.accreditations.index', compact('accreditations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
        ]);

        Accreditation::create($request->only('name', 'percentage'));

        return response()->json(['msg' => 'Accreditation added successfully.']);
    }

    public function show(Accreditation $accreditation)
    {
        return response()->json(['data' => $accreditation]);
    }

    public function update(Request $request, Accreditation $accreditation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
        ]);

        $accreditation->update($request->only('name', 'percentage'));

        return response()->json(['msg' => 'Accreditation updated successfully.']);
    }

    public function destroy(Accreditation $accreditation)
    {
        $accreditation->delete();
        return redirect()->back()->with('success', 'Accreditation deleted successfully.');
    }
}
