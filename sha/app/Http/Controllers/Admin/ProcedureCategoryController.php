<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcedureCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;

class ProcedureCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedure_categories=ProcedureCategory::latest()->get();
        return view('admin-views.procedure_category.index',compact('procedure_categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:procedure_categories,name',
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $procedure_category = new ProcedureCategory;
        $procedure_category->name = $request->name;
        $procedure_category->code = $request->code;
        $procedure_category->save();
        
        return response()->json(['msg'=>'Procedure Category Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProcedureCategory $procedure_category)
    {
        return response()->json(['data'=>$procedure_category], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProcedureCategory $procedure_category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProcedureCategory $procedure_category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:procedure_categories,name,' . $procedure_category->id,
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $procedure_category->name = $request->name;
        $procedure_category->code = $request->code;
        $procedure_category->save();
        
        return response()->json(['msg'=>'Procedure Category Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProcedureCategory $procedure_category)
    {
        $procedure_category->delete();
        return redirect()->back()->with('success','Procedure Category Deleted.');
    }
}
