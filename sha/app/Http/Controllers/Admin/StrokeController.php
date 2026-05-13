<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stroke;
use Illuminate\Http\Request;

class StrokeController extends Controller
{
    public function index()
    {
        $strokes = Stroke::all();
        return view('admin-views.strokes.index', compact('strokes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Stroke::create(['name' => $request->name]);

        return response()->json(['msg' => 'Stroke added successfully.']);
    }

    public function show($id)
    {
        $stroke = Stroke::findOrFail($id);
        return response()->json(['data' => $stroke]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $stroke = Stroke::findOrFail($id);
        $stroke->update(['name' => $request->name]);

        return response()->json(['msg' => 'Stroke updated successfully.']);
    }

    public function destroy($id)
    {
        $stroke = Stroke::findOrFail($id);
        $stroke->delete();

        return redirect()->back()->with('success', 'Stroke deleted successfully.');
    }
}
