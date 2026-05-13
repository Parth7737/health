<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilitySpecialityType;
use Illuminate\Http\Request;

class FacilitySpecialityTypeController extends Controller
{
    public function index()
    {
        $specialities = FacilitySpecialityType::all();
        return view('admin-views.facility_speciality_types.index', compact('specialities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:facility_speciality_types,name',
        ]);

        FacilitySpecialityType::create(['name' => $request->name]);

        return response()->json(['msg' => 'Facility Speciality Type Added Successfully!']);
    }

    public function show(FacilitySpecialityType $facilitySpecialityType)
    {
        return response()->json(['data' => $facilitySpecialityType]);
    }

    public function update(Request $request, FacilitySpecialityType $facilitySpecialityType)
    {
        $request->validate([
            'name' => 'required|unique:facility_speciality_types,name,' . $facilitySpecialityType->id,
        ]);

        $facilitySpecialityType->update(['name' => $request->name]);

        return response()->json(['msg' => 'Facility Speciality Type Updated Successfully!']);
    }

    public function destroy(FacilitySpecialityType $facilitySpecialityType)
    {
        $facilitySpecialityType->delete();

        return redirect()->back()->with('success', 'Facility Speciality Type Deleted Successfully!');
    }
}
