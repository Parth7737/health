<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Implant,
    Procedure,
    Speciality,
};
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ImplantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $implants=Implant::latest()->get();
        return view('admin-views.implant.index',compact('implants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $procedures=Procedure::get();
        $specialities=Speciality::get();
        return view('admin-views.implant.create',compact('procedures','specialities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'speciality_id' => 'required',
            'code' => 'required',
            'procedure_id' => 'required|array',
            'price' => 'required',
        ]);

        $implant = new Implant;
        $implant->name = $request->name;
        $implant->code = $request->code;
        $implant->speciality_id = $request->speciality_id;
        $implant->price = $request->price;
        $implant->no_of_multiplier = $request->no_of_multiplier;
        $implant->procedure_id = implode(",",$request->procedure_id);
        $implant->save();
        
        return response()->json(['success' => true, 'message' => 'Implant Saved Successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Implant $implant)
    {
        return response()->json(['data'=>$implant], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Implant $implant)
    {
        $procedures=Procedure::get();
        $specialities=Speciality::get();
        return view('admin-views.implant.edit',compact('implant','procedures','specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Implant $implant)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'speciality_id' => 'required',
            'code' => 'required',
            'procedure_id' => 'required|array',
            'price' => 'required',
        ]);

        $implant->name = $request->name;
        $implant->code = $request->code;
        $implant->speciality_id = $request->speciality_id;
        $implant->price = $request->price;
        $implant->no_of_multiplier = $request->no_of_multiplier;
        $implant->procedure_id = implode(",",$request->procedure_id);
        $implant->save();
        
        return response()->json(['success' => true, 'message' => 'Implant Updated Successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Implant $implant)
    {
        $implant->delete();
        return redirect()->back()->with('success','Implant Deleted.');
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
            if (!empty($row['Name'])) {
                $code = mb_convert_encoding($row['Code'], 'UTF-8', 'ISO-8859-1');
                $name = mb_convert_encoding($row['Name'] ?? '', 'UTF-8', 'ISO-8859-1');
                $speciality = mb_convert_encoding($row['Speciality'] ?? '', 'UTF-8', 'ISO-8859-1');
                $speciality_id = Speciality::where('name', $speciality)->value('id');
                $multiplier = mb_convert_encoding($row['Multiplier'] ?? '', 'UTF-8', 'ISO-8859-1');
                $price = mb_convert_encoding($row['Price'] ?? 0, 'UTF-8', 'ISO-8859-1');

                $price = str_replace(array(',',' '), '', $price);
                $price = is_numeric($price) ? (float)$price : 0;

                Implant::updateOrInsert(
                    ['name' => $name,'speciality_id'=>$speciality_id,'no_of_multiplier' => $multiplier],
                    [
                        'name' => $name,
                        'code' => $code,
                        'speciality_id' => $speciality_id,
                        'no_of_multiplier' => $multiplier,
                        'price' => $price,
                    ]
                );
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }
    public function mapProcedure(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $file = $request->file('file');

        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            if (!empty($row['Implant Code']) && !empty($row['Procedure Code'])) {
                $implant_code = trim(mb_convert_encoding($row['Implant Code'], 'UTF-8', 'ISO-8859-1'));
                $procedure = trim(mb_convert_encoding($row['Procedure Code'], 'UTF-8', 'ISO-8859-1'));
        
                $implant = Implant::where('code', $implant_code)->first();
                $procedures = Procedure::where('procedure_code_2', $procedure)->get();
        
                if ($implant && $procedures->count() > 0) {
                    if (!empty($implant->procedure_id)) {
                        $procedure_arr = explode(",", $implant->procedure_id);
                    } else {
                        $procedure_arr = [];
                    }
                    foreach ($procedures as $proc) {
                        $procedure_arr[] = $proc->id;
                    }
                    $implant->procedure_id = implode(",", array_unique($procedure_arr));
                    $implant->save();
                }
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }

}
