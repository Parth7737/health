<?php
// app/Http/Controllers/Admin/TuberculosisController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tuberculosis;
use Illuminate\Http\Request;

class TuberculosisController extends Controller
{
    public function index()
    {
        $tuberculosis = Tuberculosis::all();
        return view('admin-views.tuberculoses.index', compact('tuberculosis'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $tuberculosis = new Tuberculosis;
        $tuberculosis->name = $request->name;
        $tuberculosis->save();
        return response()->json(['msg' => 'Tuberculosis Type Added Successfully']);
    }

    public function show($id)
    {
        $tuberculosis = Tuberculosis::find($id);
        return response()->json(['data' => $tuberculosis]);
    }

    public function update(Request $request, Tuberculosis $tuberculosis)
    {
        $request->validate(['name' => 'required|string|max:255']);
        // $tuberculosis->update(['name' => $request->only('name')]);
        $tuberculosis = Tuberculosis::find($id);
        $tuberculosis->name = $request->name;
        $tuberculosis->save();
        return response()->json(['msg' => 'Tuberculosis Type Updated Successfully']);
    }

    public function destroy($id)
    {
        $tuberculosis = Tuberculosis::find($id);
        $tuberculosis->delete();
        return redirect()->back()->with('success', 'Tuberculosis Type Deleted Successfully');
    }
}
