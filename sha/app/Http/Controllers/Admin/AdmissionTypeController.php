<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdmissionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdmissionTypeController extends Controller
{
    public function index()
    {
        $admissionTypes = AdmissionType::all();
        return view('admin-views.admission_types.index', compact('admissionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:admission_types,name',
        ]);

        AdmissionType::create([
            'name' => $request->name,
        ]);

        return response()->json(['msg' => 'Admission Type Created Successfully.']);
    }

    public function show($id)
    {
        $admissionType = AdmissionType::findOrFail($id);
        return response()->json(['data' => $admissionType]);
    }

    public function update(Request $request, $id)
    {
        $admissionType = AdmissionType::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:admission_types,name,' . $admissionType->id,
        ]);

        $admissionType->update([
            'name' => $request->name,
        ]);

        return response()->json(['msg' => 'Admission Type Updated Successfully.']);
    }

    public function destroy($id)
    {
        $admissionType = AdmissionType::findOrFail($id);
        $admissionType->delete();

        return redirect()->back()->with('success', 'Admission Type Deleted Successfully.');
    }
}
