<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityOwnershipType;
use Illuminate\Http\Request;

class FacilityOwnershipTypeController extends Controller
{
    public function index()
    {
        $facilityOwnershipTypes = FacilityOwnershipType::all();
        return view('admin-views.facility_ownership_types.index', compact('facilityOwnershipTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:facility_ownership_types,name',
        ]);

        FacilityOwnershipType::create(['name' => $request->name]);

        return response()->json(['msg' => 'Facility Ownership Type Added Successfully.']);
    }

    public function show(FacilityOwnershipType $facilityOwnershipType)
    {
        return response()->json(['data' => $facilityOwnershipType]);
    }

    public function update(Request $request, FacilityOwnershipType $facilityOwnershipType)
    {
        $request->validate([
            'name' => 'required|unique:facility_ownership_types,name,' . $facilityOwnershipType->id,
        ]);

        $facilityOwnershipType->update(['name' => $request->name]);

        return response()->json(['msg' => 'Facility Ownership Type Updated Successfully.']);
    }

    public function destroy(FacilityOwnershipType $facilityOwnershipType)
    {
        $facilityOwnershipType->delete();

        return redirect()->back()->with('success', 'Facility Ownership Type Deleted.');
    }
}
