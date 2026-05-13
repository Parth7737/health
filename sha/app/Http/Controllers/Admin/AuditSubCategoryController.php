<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ AuditCategory,AuditSubCategory };


class AuditSubCategoryController extends Controller
{
    public function index()
    {
        $auditcategories = AuditCategory::get();
        $categories = AuditSubCategory::orderBy('id', 'DESC')->get();
        return view('admin-views.audit.subcategoryindex', compact('categories', 'auditcategories'));
    }

    public function show(AuditSubCategory $AuditSubCategory)
    {
        return response()->json(['data' => $AuditSubCategory]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
        ]);

        $diagnosis = AuditSubCategory::create($request->all());
        return response()->json(['msg' => 'Audit Sub Category Created Successfully']);
    }

    public function update(Request $request, AuditSubCategory $AuditSubCategory)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
        ]);

        $AuditSubCategory->update($request->all());
        return response()->json(['msg' => 'Audit Sub Category Updated Successfully']);
    }

    public function destroy(AuditSubCategory $AuditSubCategory)
    {
        $AuditSubCategory->delete();
        return redirect()->back()->with('success', 'Audit Sub Category Deleted Successfully');
    }
}
