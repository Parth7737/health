<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityOwnershipSubType;
use App\Models\FacilityOwnershipType;
use Illuminate\Http\Request;

class FacilityOwnershipSubTypeController extends Controller
{
    public function index()
    {
        $subTypes = FacilityOwnershipSubType::orderBy('id', 'DESC')->get();
        $ownershipTypes = FacilityOwnershipType::all();
        return view('admin-views.facility_ownership_sub_types.index', compact('subTypes', 'ownershipTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'facility_ownership_type_id' => 'required|exists:facility_ownership_types,id',
            'name' => 'required|unique:facility_ownership_sub_types,name',
        ]);

        FacilityOwnershipSubType::create([
            'facility_ownership_type_id' => $request->facility_ownership_type_id,
            'name' => $request->name,
        ]);

        return response()->json(['msg' => 'Facility Ownership Sub Type Added Successfully.'], 200);
    }

    public function subtype2(Request $request) {
        $request->validate([
            'facility_ownership_type_id' => 'required|exists:facility_ownership_types,id',
            'type_id' => 'sometimes',
            'name' => 'required',
        ]);

        FacilityOwnershipSubType::create([
            'facility_ownership_type_id' => $request->facility_ownership_type_id,
            'type_id' => $request->type_id,
            'type' => 1,
            'name' => $request->name,
        ]);

        return response()->json(['msg' => 'Facility Ownership Sub Type 2 Added Successfully.'], 200);
    }

    public function getsubtypes($id) {
        $data = FacilityOwnershipSubType::where('facility_ownership_type_id', $id)->where('type', 0)->get();
        return response()->json(['data' => $data]);
    }

    public function getsubtypes2($id) {
        $data = FacilityOwnershipSubType::where('type_id', $id)->where('type', 1)->get();
        return response()->json(['data' => $data]);
    }

    
    public function subtype3(Request $request) {
        $request->validate([
            'facility_ownership_type_id' => 'required|exists:facility_ownership_types,id',
            'type_id' => 'required',
            'type2_id' => 'required',
            'name' => 'required',
        ]);

        FacilityOwnershipSubType::create([
            'facility_ownership_type_id' => $request->facility_ownership_type_id,
            'type_id' => $request->type_id,
            'type2_id' => $request->type2_id,
            'type' => 2,
            'name' => $request->name,
        ]);

        return response()->json(['msg' => 'Facility Ownership Sub Type 2 Added Successfully.'], 200);
    }

    public function subtype3edit(Request $request, $id) {
        $request->validate([
            'facility_ownership_type_id' => 'required|exists:facility_ownership_types,id',
            'name' => 'required',
            'type_id' => 'required',
            'type2_id' => 'required',
        ]);

        $subType = FacilityOwnershipSubType::findOrFail($id);
        $subType->update([
            'facility_ownership_type_id' => $request->facility_ownership_type_id,
            'name' => $request->name,
            'type_id' => $request->type_id,
            'type2_id' => $request->type2_id,
            'type' => 2
        ]);

        return response()->json(['msg' => 'Facility Ownership Sub Type 3 Added Successfully.'], 200);
    }

    public function subtype2edit(Request $request, $id) {
        $request->validate([
            'facility_ownership_type_id' => 'required|exists:facility_ownership_types,id',
            'name' => 'required',
            'type_id' => 'sometimes',
        ]);

        $subType = FacilityOwnershipSubType::findOrFail($id);
        $subType->update([
            'facility_ownership_type_id' => $request->facility_ownership_type_id,
            'name' => $request->name,
            'type_id' => $request->type_id,
            'type' => 1
        ]);

        return response()->json(['msg' => 'Facility Ownership Sub Type 2 Updated Successfully.'], 200);
    }

    public function show($id)
    {
        $subType = FacilityOwnershipSubType::findOrFail($id);
        return response()->json(['data' => $subType]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'facility_ownership_type_id' => 'required|exists:facility_ownership_types,id',
            'name' => 'required|unique:facility_ownership_sub_types,name,' . $id,
        ]);

        $subType = FacilityOwnershipSubType::findOrFail($id);
        $subType->update([
            'facility_ownership_type_id' => $request->facility_ownership_type_id,
            'name' => $request->name,
        ]);

        return response()->json(['msg' => 'Facility Ownership Sub Type Updated Successfully.'], 200);
    }

    public function destroy($id)
    {
        $subType = FacilityOwnershipSubType::findOrFail($id);
        $subType->delete();
        return redirect()->back()->with('success', 'Facility Ownership Sub Type Deleted.');
    }
}
