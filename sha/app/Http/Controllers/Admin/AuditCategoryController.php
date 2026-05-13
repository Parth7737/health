<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditCategory;

class AuditCategoryController extends Controller
{
    public function index()
    {
        $categories = AuditCategory::orderBy('id', 'DESC')->get();
        return view('admin-views.audit.categoryindex', compact('categories'));
    }

    public function show(AuditCategory $AuditCategory)
    {
        return response()->json(['data' => $AuditCategory]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $diagnosis = AuditCategory::create($request->all());
        return response()->json(['msg' => 'Audit Category Created Successfully']);
    }

    public function update(Request $request, AuditCategory $AuditCategory)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $AuditCategory->update($request->all());
        return response()->json(['msg' => 'Audit Category Updated Successfully']);
    }

    public function destroy(AuditCategory $AuditCategory)
    {
        $AuditCategory->delete();
        return redirect()->back()->with('success', 'Audit Category Deleted Successfully');
    }
}
