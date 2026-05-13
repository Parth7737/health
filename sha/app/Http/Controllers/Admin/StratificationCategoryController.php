<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StratificationCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;

class StratificationCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories=StratificationCategory::latest()->get();
        return view('admin-views.stratification_category.index',compact('categories'));
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
            'name' => 'required|unique:stratification_categories,name',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $stratification_category = new StratificationCategory;
        $stratification_category->name = $request->name;
        $stratification_category->save();
        
        return response()->json(['msg'=>'Stratification Category Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(StratificationCategory $stratification_category)
    {
        return response()->json(['data'=>$stratification_category], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StratificationCategory $stratification_category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StratificationCategory $stratification_category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:stratification_categories,name,' . $stratification_category->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $stratification_category->name = $request->name;
        $stratification_category->save();
        
        return response()->json(['msg'=>'Stratification Category Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StratificationCategory $stratification_category)
    {
        $stratification_category->delete();
        return redirect()->back()->with('success','Stratification Category Deleted.');
    }
}
