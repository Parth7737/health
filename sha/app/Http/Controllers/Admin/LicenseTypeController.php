<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Licenses;
use App\Models\LicensesType;
use Illuminate\Http\Request;

class LicenseTypeController extends Controller
{
    public function index()
    {
        $licenses = Licenses::all();
        $licensesTypes = LicensesType::with('licenses')->get();
        return view('admin-views.licenses_type.index', compact('licenses', 'licensesTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_id' => 'nullable|exists:licenses,id',
            'name' => 'required|string|max:255',
        ]);

        $validated['is_required'] = $request->has('is_required') ? 1 : 0;
        $validated['document_required'] = $request->has('document_required') ? 1 : 0;

        LicensesType::create($validated);

        return response()->json(['msg' => 'License Type added successfully.']);
    }

    public function show(LicensesType $licensesType)
    {
        return response()->json(['data' => $licensesType]);
    }

    public function update(Request $request, LicensesType $licensesType)
    {
        $validated = $request->validate([
            'license_id' => 'nullable|exists:licenses,id',
            'name' => 'required|string|max:255',
        ]);

        $validated['is_required'] = $request->has('is_required') ? 1 : 0;
        $validated['document_required'] = $request->has('document_required') ? 1 : 0;

        $licensesType->update($validated);

        return response()->json(['msg' => 'License Type updated successfully.']);
    }

    public function destroy(LicensesType $licensesType)
    {
        $licensesType->delete();
        return redirect()->back()->with('success', 'License Type deleted successfully.');
    }
}
