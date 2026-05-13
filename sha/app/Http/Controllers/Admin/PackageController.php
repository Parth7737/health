<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages=Package::latest()->get();
        return view('admin-views.package.index',compact('packages'));
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
            'name' => 'required|unique:packages,name',
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $package = new Package;
        $package->name = $request->name;
        $package->code = $request->code;
        $package->save();
        
        return response()->json(['msg'=>'Package Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        return response()->json(['data'=>$package], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:packages,name,' . $package->id,
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $package->name = $request->name;
        $package->code = $request->code;
        $package->save();
        
        return response()->json(['msg'=>'Package Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->back()->with('success','Package Deleted.');
    }
}
