<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    FollowupProcedure,
    Procedure,
};
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;

class FollowUpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedures=FollowupProcedure::latest()->get();
        $regular_procedures = Procedure::whereRaw("REPLACE(LOWER(procedure_label), ' ', '') = ?", ['regularprocedure'])->get();
        $followup_procedures = Procedure::whereRaw("REPLACE(LOWER(procedure_label), ' ', '') = ?", ['follow-upprocedure'])->get();
        return view('admin-views.followup.index',compact('procedures','followup_procedures','regular_procedures'));
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
            'follow_up_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $followup = new FollowupProcedure;
        $followup->procedure_id = $request->procedure_id;
        $followup->follow_up_id = $request->follow_up_id;
        $followup->save();
        
        return response()->json(['msg'=>'FollowUp Procedure Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(FollowupProcedure $followup)
    {
        return response()->json(['data'=>$followup], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FollowupProcedure $followup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FollowupProcedure $followup)
    {
        $validator = Validator::make($request->all(), [
            'procedure_id' => 'required',
            'follow_up_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $followup->procedure_id = $request->procedure_id;
        $followup->follow_up_id = $request->follow_up_id;
        $followup->save();
        
        return response()->json(['msg'=>'FollowUp Procedure Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FollowupProcedure $followup)
    {
        $followup->delete();
        return redirect()->back()->with('success','FollowUp Procedure Deleted.');
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
                $followup = mb_convert_encoding($row['FollowUp'], 'UTF-8', 'ISO-8859-1');
                $regular_id = Procedure::where('procedure_code_2', $regular)->value('id');
                $followup_id = Procedure::where('procedure_code_2', $followup)->value('id');
                if($regular_id != '' && $followup_id != ''){
                    FollowupProcedure::updateOrInsert(
                        ['procedure_id' => $regular_id,'follow_up_id'=>$followup_id],
                        [
                            'procedure_id' => $regular_id,
                            'follow_up_id' => $followup_id,
                        ]
                    );
                }
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }
}
