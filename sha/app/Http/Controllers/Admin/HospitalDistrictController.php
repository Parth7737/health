<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HospitalDistrict;
use App\Models\HospitalState;
use Illuminate\Http\Request;

class HospitalDistrictController extends Controller
{
    public function index()
    {
        $hospitalDistricts = HospitalDistrict::all();
        $hospitalStates = HospitalState::all();
        return view('admin-views.hospital_districts.index', compact('hospitalDistricts', 'hospitalStates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|integer',
        ]);

        HospitalDistrict::create([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return response()->json(['msg' => 'Hospital District Created Successfully.']);
    }

    public function show($id)
    {
        $district = HospitalDistrict::findOrFail($id);
        return response()->json(['data' => $district]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|integer',
        ]);

        $district = HospitalDistrict::findOrFail($id);
        $district->update([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return response()->json(['msg' => 'Hospital District Updated Successfully.']);
    }

    public function destroy($id)
    {
        $district = HospitalDistrict::findOrFail($id);
        $district->delete();

        return redirect()->back()->with('success', 'Hospital District Deleted Successfully.');
    }
}
