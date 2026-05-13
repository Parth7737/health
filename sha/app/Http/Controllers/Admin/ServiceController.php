<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = Service::latest()->get();
        return view('admin-views.service.index', compact('service'));
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
            'name' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $speciality = new Service;
        $speciality->name = $request->name;
        $speciality->save();
        
        return response()->json(['success' => true, 'message'=>'Service Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $Service)
    {
        return response()->json(['data'=>$Service], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $Service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $Service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $Service->name = $request->name;
        $Service->save();
        
        return response()->json(['success' => true, 'message'=>'Service Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $Service)
    {
        $Service->delete();
        return redirect()->back()->with('success','Service Deleted.');
    }
}
