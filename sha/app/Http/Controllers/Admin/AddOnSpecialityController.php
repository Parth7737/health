<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    AddOnSpeciality,
    Procedure,
    Speciality,
};
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;

class AddOnSpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedures=AddOnSpeciality::latest()->get();
        $specialities = Speciality::get();
        $addon_procedures = Procedure::whereRaw("REPLACE(LOWER(procedure_label), ' ', '') = ?", ['add-onprocedure'])->get();
        return view('admin-views.addon-speciality.index',compact('procedures','addon_procedures','specialities'));
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
            'speciality_id' => 'required',
            'add_on_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $addon = new AddOnSpeciality;
        $addon->speciality_id = $request->speciality_id;
        $addon->add_on_id = $request->add_on_id;
        $addon->save();
        
        return response()->json(['msg'=>'Addon Speciality Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $addon = AddOnSpeciality::find($id);
        return response()->json(['data'=>$addon], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AddOnSpeciality $addon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AddOnSpeciality $addon)
    {
        $validator = Validator::make($request->all(), [
            'speciality_id' => 'required',
            'add_on_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $addon->speciality_id = $request->speciality_id;
        $addon->add_on_id = $request->add_on_id;
        $addon->save();
        
        return response()->json(['msg'=>'Addon Speciality Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddOnSpeciality $addon)
    {
        $addon->delete();
        return redirect()->back()->with('success','Addon Speciality Deleted.');
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
            if (!empty($row['Code'])) {
                $code = mb_convert_encoding($row['Code'], 'UTF-8', 'ISO-8859-1');
                $addon = mb_convert_encoding($row['AddOn'], 'UTF-8', 'ISO-8859-1');
                $speciality_id = Speciality::where('code', $code)->value('id');
                $add_on_id = Procedure::where('procedure_code_2', $addon)->value('id');
                if($speciality_id != '' && $add_on_id != ''){
                    AddOnSpeciality::updateOrInsert(
                        ['speciality_id' => $speciality_id,'add_on_id'=>$add_on_id],
                        [
                            'speciality_id' => $speciality_id,
                            'add_on_id' => $add_on_id,
                        ]
                    );
                }
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }
}
