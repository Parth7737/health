<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    AddOnProcedure,
    Procedure,
};
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;

class AddOnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedures=AddOnProcedure::latest()->get();
        $regular_procedures = Procedure::whereRaw("REPLACE(LOWER(procedure_label), ' ', '') = ?", ['regularprocedure'])->get();
        $addon_procedures = Procedure::whereRaw("REPLACE(LOWER(procedure_label), ' ', '') = ?", ['add-onprocedure'])->get();
        return view('admin-views.addon.index',compact('procedures','addon_procedures','regular_procedures'));
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
            'procedure_id' => 'required',
            'add_on_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $addon = new AddOnProcedure;
        $addon->procedure_id = $request->procedure_id;
        $addon->add_on_id = $request->add_on_id;
        $addon->save();
        
        return response()->json(['msg'=>'Addon Procedure Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(AddOnProcedure $addon)
    {
        return response()->json(['data'=>$addon], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AddOnProcedure $addon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AddOnProcedure $addon)
    {
        $validator = Validator::make($request->all(), [
            'procedure_id' => 'required',
            'add_on_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $addon->procedure_id = $request->procedure_id;
        $addon->add_on_id = $request->add_on_id;
        $addon->save();
        
        return response()->json(['msg'=>'Addon Procedure Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddOnProcedure $addon)
    {
        $addon->delete();
        return redirect()->back()->with('success','Addon Procedure Deleted.');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $file = $request->file('file');

        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            if (!empty($row['Regular'])) {
                $regular = mb_convert_encoding($row['Regular'], 'UTF-8', 'ISO-8859-1');
                $addon = mb_convert_encoding($row['AddOn'], 'UTF-8', 'ISO-8859-1');
                $regular_id = Procedure::where('procedure_code_2', $regular)->value('id');
                $add_on_id = Procedure::where('procedure_code_2', $addon)->value('id');
                if($regular_id != '' && $add_on_id != ''){
                    AddOnProcedure::updateOrInsert(
                        ['procedure_id' => $regular_id,'add_on_id'=>$add_on_id],
                        [
                            'procedure_id' => $regular_id,
                            'add_on_id' => $add_on_id,
                        ]
                    );
                }
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }
}
