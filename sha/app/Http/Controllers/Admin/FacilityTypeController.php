<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilityTypeController extends Controller
{
    public function index()
    {
        $facilityTypes = FacilityType::all();
        return view('admin-views.facility_types.index', compact('facilityTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:facility_types,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        FacilityType::create(['name' => $request->name]);

        return response()->json(['msg' => 'Facility Type Created Successfully.'], 201);
    }

    public function show($id)
    {
        $facilityType = FacilityType::findOrFail($id);
        return response()->json(['data' => $facilityType]);
    }

    public function update(Request $request, FacilityType $facilityType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:facility_types,name,' . $facilityType->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $facilityType->update(['name' => $request->name]);

        return response()->json(['msg' => 'Facility Type Updated Successfully.'], 200);
    }

    public function destroy(FacilityType $facilityType)
    {
        $facilityType->delete();

        return redirect()->back()->with('success', 'Facility Type Deleted Successfully.');
    }
}
