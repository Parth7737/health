<?php 
namespace App\Http\Controllers\Admin;

use App\Models\HeartDisease;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HeartDiseaseController extends Controller
{
    public function index()
    {
        $heartDiseases = HeartDisease::all();
        return view('admin-views.heart_disease.index', compact('heartDiseases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $heartDisease = new HeartDisease();
        $heartDisease->name = $request->name;
        $heartDisease->save();

        return response()->json(['msg' => 'Heart Disease Added Successfully']);
    }

    public function show($id)
    {
        $heartDisease = HeartDisease::find($id);
            return response()->json(['data' => $heartDisease]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $heartDisease = HeartDisease::find($id);
            $heartDisease->name = $request->name;
            $heartDisease->save();

            return response()->json(['msg' => 'Heart Disease Updated Successfully']);
    }

    public function destroy($id)
    {
        $heartDisease = HeartDisease::find($id);
            $heartDisease->delete();
            return redirect()->back()->with('success', 'Heart Disease Deleted Successfully');
    }
}
