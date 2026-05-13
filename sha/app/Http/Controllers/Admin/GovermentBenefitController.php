<?php

namespace App\Http\Controllers\Admin;

use App\Models\GovermentBenefits;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GovermentBenefitController extends Controller
{
    public function index()
    {
        $benefits = GovermentBenefits::all();
        return view('admin-views.goverment_benefits.index', compact('benefits'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:goverment_benefits,name']);

        GovermentBenefits::create(['name' => $request->name]);

        return response()->json(['msg' => 'Goverment Benefit added successfully!'], 200);
    }

    public function show(GovermentBenefits $goverment_benefit)
    {
        return response()->json(['data' => $goverment_benefit]);
    }

    public function update(Request $request, GovermentBenefits $goverment_benefit)
    {
        $request->validate(['name' => 'required|unique:goverment_benefits,name,' . $goverment_benefit->id]);

        $goverment_benefit->update(['name' => $request->name]);

        return response()->json(['msg' => 'Goverment Benefit updated successfully!'], 200);
    }

    public function destroy(GovermentBenefits $goverment_benefit)
    {
        $goverment_benefit->delete();

        return redirect()->back()->with('success', 'Goverment Benefit deleted successfully!');
    }

}
