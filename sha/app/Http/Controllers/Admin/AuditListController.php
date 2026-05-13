<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ AuditCategory,AuditSubCategory,AuditList };


class AuditListController extends Controller
{
    public function index()
    {
        $auditcategories = AuditCategory::get();
        $categories = AuditList::orderBy('id', 'DESC')->get();
        return view('admin-views.audit.index', compact('categories', 'auditcategories'));
    }

    public function show(AuditList $AuditList)
    {
        return response()->json(['data' => $AuditList]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
        ]);

        $diagnosis = AuditList::create($request->all());
        return response()->json(['msg' => 'Audit Sub Category Created Successfully']);
    }

    public function getauditsubcategory(Request $request, $id) {
        $data = AuditSubCategory::where('category_id', $id)->get();
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, AuditList $AuditList)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
        ]);

        $AuditList->update($request->all());
        return response()->json(['msg' => 'Audit Sub Category Updated Successfully']);
    }

    public function destroy(AuditList $AuditList)
    {
        $AuditList->delete();
        return redirect()->back()->with('success', 'Audit Sub Category Deleted Successfully');
    }
}
