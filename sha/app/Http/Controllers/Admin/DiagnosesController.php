<?php

namespace App\Http\Controllers\Admin;

use App\Models\Diagnosis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiagnosesController extends Controller
{
    public function index()
    {
        $diagnoses = Diagnosis::all();
        return view('admin-views.diagnoses.index', compact('diagnoses'));
    }

    public function show(Diagnosis $diagnosis)
    {
        return response()->json(['data' => $diagnosis]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:diagnoses,name',
            'code' => 'nullable|string|max:20'
        ]);

        $diagnosis = Diagnosis::create($request->all());
        return response()->json(['msg' => 'Diagnosis Created Successfully']);
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        $request->validate([
            'name' => 'required|unique:diagnoses,name,' . $diagnosis->id,
            'code' => 'nullable|string|max:20'
        ]);

        $diagnosis->update($request->all());
        return response()->json(['msg' => 'Diagnosis Updated Successfully']);
    }

    public function destroy(Diagnosis $diagnosis)
    {
        $diagnosis->delete();
        return redirect()->back()->with('success', 'Diagnosis Deleted Successfully');
    }
}
