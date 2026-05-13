<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nutrition;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    public function index()
    {
        $nutrition = Nutrition::all();
        return view('admin-views.nutrition.index', compact('nutrition'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Nutrition::create($request->only('name'));

        return response()->json(['msg' => 'Nutrition Type Added Successfully.']);
    }

    public function show($id)
    {
        $nutrition = Nutrition::findOrFail($id);
        return response()->json(['data' => $nutrition]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        $nutrition = Nutrition::findOrFail($id);
        $nutrition->update($request->only('name'));

        return response()->json(['msg' => 'Nutrition Type Updated Successfully.']);
    }

    public function destroy($id)
    {
        $nutrition = Nutrition::findOrFail($id);
        $nutrition->delete();

        return redirect()->back()->with('success', 'Nutrition Type Deleted Successfully.');
    }
}
