<?php
namespace App\Http\Controllers\Admin;

use App\Models\Cancer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CancerController extends Controller
{
    public function index()
    {
        $cancers = Cancer::all();
        return view('admin-views.cancers.index', compact('cancers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Cancer::create(['name' => $request->name]);

        return response()->json(['msg' => 'Cancer Type Added Successfully']);
    }

    public function show(Cancer $cancer)
    {
        return response()->json(['data' => $cancer]);
    }

    public function update(Request $request, Cancer $cancer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $cancer->update(['name' => $request->name]);

        return response()->json(['msg' => 'Cancer Type Updated Successfully']);
    }

    public function destroy(Cancer $cancer)
    {
        $cancer->delete();

        return redirect()->back()->with('success', 'Cancer Type Deleted Successfully');
    }
}
