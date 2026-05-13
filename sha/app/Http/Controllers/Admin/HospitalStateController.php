<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HospitalState;
use Illuminate\Http\Request;

class HospitalStateController extends Controller
{
    public function index()
    {
        // Fetch all hospital states
        $hospitalStates = HospitalState::all();
        return view('admin-views.hospital_states.index', compact('hospitalStates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'country_id' => 'required|integer',
        ]);

        HospitalState::create($request->all());

        return response()->json(['msg' => 'Hospital State Added Successfully']);
    }

    public function show($id)
    {
        $hospitalState = HospitalState::findOrFail($id);
        return response()->json(['data' => $hospitalState]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'country_id' => 'required|integer',
        ]);

        $hospitalState = HospitalState::findOrFail($id);
        $hospitalState->update($request->all());

        return response()->json(['msg' => 'Hospital State Updated Successfully']);
    }

    public function destroy($id)
    {
        $hospitalState = HospitalState::findOrFail($id);
        $hospitalState->delete();

        return redirect()->back()->with('success', 'Hospital State Deleted Successfully');
    }
}
