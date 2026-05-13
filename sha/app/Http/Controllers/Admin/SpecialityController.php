<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Speciality;
use App\Models\SchemeType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use League\Csv\Reader;

class SpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specialities=Speciality::latest()->get();
        return view('admin-views.speciality.index',compact('specialities'));
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
            'name' => 'required|unique:specialities,name',
            'code' => 'required',
            'scheme_type_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $speciality = new Speciality;
        $speciality->name = $request->name;
        $speciality->code = $request->code;
        $speciality->scheme_type_id = $request->scheme_type_id;
        $speciality->save();
        
        return response()->json(['msg'=>'Speciality Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Speciality $speciality)
    {
        return response()->json(['data'=>$speciality], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Speciality $speciality)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Speciality $speciality)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:specialities,name,' . $speciality->id,
            'code' => 'required',
            'scheme_type_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $speciality->name = $request->name;
        $speciality->code = $request->code;
        $speciality->scheme_type_id = $request->scheme_type_id;
        $speciality->save();
        
        return response()->json(['msg'=>'Speciality Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Speciality $speciality)
    {
        $speciality->delete();
        return redirect()->back()->with('success','Speciality Deleted.');
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
                Speciality::updateOrInsert(
                    ['name' => $name, 'scheme_type_id' => $scheme_type_id],
                    ['name' => $name, 'code' => $row['Code'], 'scheme_type_id' => $scheme_type_id]
                );
            }
        }

        return back()->with('success', 'Specialities imported successfully!');
    }
}
