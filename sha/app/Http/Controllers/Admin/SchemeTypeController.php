<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchemeType;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;

class SchemeTypeController extends Controller
{
    public function index()
    {
        $schemeTypes = SchemeType::latest()->get();
        return view('admin-views.scheme-type.index', compact('schemeTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:scheme_types,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        SchemeType::create(['name' => $request->name]);

        return response()->json(['msg' => 'Scheme Type Added Successfully.'], 200);
    }

    public function show($id)
    {
        $schemeType = SchemeType::find($id);

        if (!$schemeType) {
            return response()->json(['errors' => ['SchemeType not found']], 404);
        }
        return response()->json(['data' => $schemeType], 200);
    }

    public function edit(SchemeType $schemeType)
    {

    }

    public function update(Request $request, $id)
    {
        $schemeType = SchemeType::find($id);
        if (!$schemeType) {
            return response()->json(['errors' => ['message' => 'Scheme Type not found']], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:scheme_types,name,' . $schemeType->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $schemeType->update(['name' => $request->name]);

        return response()->json(['msg' => 'Scheme Type Updated Successfully.'], 200);
    }


    public function destroy($id)
    {
        $schemeType = SchemeType::find($id);
        
        if (!$schemeType) {
            return redirect()->back()->withErrors(['error' => 'Scheme Type not found.']);
        }
        $schemeType->delete();
        return redirect()->back()->with('success', 'Scheme Type Deleted.');
    }


}
