<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diabetes;
use Illuminate\Http\Request;

class DiabetesController extends Controller
{
    public function index()
    {
        $diabetes = Diabetes::all();
        return view('admin-views.diabetes.index', compact('diabetes'));
    }

    public function create()
    {
        return view('admin.diabetes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $diabetes = new Diabetes();
        $diabetes->name = $request->name;
        $diabetes->save();

        return response()->json(['msg' => 'Diabetes record created successfully']);
    }

    public function show($id)
    {
        $diabetes = Diabetes::find($id);
        return response()->json(['data' => $diabetes]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $diabetes = Diabetes::find($id);
        $diabetes->name = $request->name;
        $diabetes->save();

        return response()->json(['msg' => 'Diabetes record updated successfully']);
    }

    public function destroy($id)
    {
        $diabetes = Diabetes::find($id);
        $diabetes->delete();

        return redirect()->back()->with('success', 'Diabetes record deleted successfully');
    }
}
