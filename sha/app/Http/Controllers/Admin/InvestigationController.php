<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investigation;
use App\Models\SchemeType;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;

class InvestigationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents=Investigation::latest()->get();
        return view('admin-views.document.index',compact('documents'));
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
            'name' => 'required|unique:investigations,name',
            'code' => 'required',
            'scheme_type_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $document = new Investigation;
        $document->name = $request->name;
        $document->code = $request->code;
        $document->scheme_type_id = $request->scheme_type_id;
        $document->save();
        
        return response()->json(['msg'=>'Document Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Investigation $document)
    {
        return response()->json(['data'=>$document], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investigation $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Investigation $document)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:investigations,name,' . $document->id,
            'code' => 'required',
            'scheme_type_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $document->name = $request->name;
        $document->code = $request->code;
        $document->scheme_type_id = $request->scheme_type_id;
        $document->save();
        
        return response()->json(['msg'=>'Document Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investigation $document)
    {
        $document->delete();
        return redirect()->back()->with('success','Document Deleted.');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $file = $request->file('file');

        // Read CSV file
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            $name = mb_convert_encoding($row['Name'], 'UTF-8', 'ISO-8859-1');
            $scheme_type_id = SchemeType::where('name',$row['Scheme Type'])->value('id');
            if (!empty($name) && !empty($row['Code']) && !empty($scheme_type_id)) {
                Investigation::updateOrInsert(
                    ['name' => $name, 'scheme_type_id' => $scheme_type_id],
                    ['name' => $name, 'code' => $row['Code'], 'type' => $row['Type'], 'scheme_type_id' => $scheme_type_id]
                );
            }
        }

        return back()->with('success', 'Specialities imported successfully!');
    }
}
