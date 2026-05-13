<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemMedicine;
use Illuminate\Http\Request;

class SystemMedicineController extends Controller
{
    public function index()
    {
        $medicines = SystemMedicine::all();
        return view('admin-views.system_medicines.index', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:system_medicines']);

        SystemMedicine::create($request->only('name'));

        return response()->json(['msg' => 'Medicine added successfully.']);
    }

    public function show(SystemMedicine $systemMedicine)
    {
        return response()->json(['data' => $systemMedicine]);
    }

    public function update(Request $request, SystemMedicine $systemMedicine)
    {
        $request->validate(['name' => 'required|unique:system_medicines,name,' . $systemMedicine->id]);

        $systemMedicine->update($request->only('name'));

        return response()->json(['msg' => 'Medicine updated successfully.']);
    }

    public function destroy(SystemMedicine $systemMedicine)
    {
        $systemMedicine->delete();

        return redirect()->back()->with('success', 'Medicine deleted successfully.');
    }
}
