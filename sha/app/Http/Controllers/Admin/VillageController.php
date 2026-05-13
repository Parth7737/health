<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\{HospitalDistrict, HospitalState};
use Illuminate\Http\Request;
use League\Csv\Reader;


class VillageController extends Controller
{
    public function index()
    {
        $villages = Village::paginate(50);
        $states = HospitalState::all();
        // $districts = HospitalDistrict::all();
        return view('admin-views.villages.index', compact('villages', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'state_id' => 'required|integer',
            'block_id' => 'required|integer',
            'district_id' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        Village::create($request->all());

        return response()->json(['msg' => 'Village added successfully!']);
    }

    public function show($id)
    {
        $village = Village::findOrFail($id);
        return response()->json(['data' => $village]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'state_id' => 'required|integer',
            'block_id' => 'required|integer',
            'district_id' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        $village = Village::findOrFail($id);
        $village->update($request->all());

        return response()->json(['msg' => 'Village updated successfully!']);
    }

    public function destroy($id)
    {
        $village = Village::findOrFail($id);
        $village->delete();

        return redirect()->back()->with('success', 'Village deleted successfully!');
    }

    public function import(Request $request) {
        $request->validate([
            'statei_id' => 'required',
            'districti_id' => 'required',
            'blockfile' => 'required',
            'blocki_id' => 'required'
        ]);

        $file = $request->file('blockfile');
        if($file->getClientOriginalExtension() != "csv") {
            return back()->with('error', 'File Formate not Valid');
        }
        // Read CSV file
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);
        foreach ($csv as $row) {          
            if (!empty($row['Name'])) {
                $name = mb_convert_encoding($row['Name'], 'UTF-8', 'ISO-8859-1');
                Village::updateOrCreate(
                    ['state_id' => $request->statei_id, 'district_id' => $request->districti_id, 'block_id' => $request->blocki_id, 'name' => $name],
                    ['name' => $name]
                );
            }
        }

        return back()->with('success', 'Block imported successfully!');
    }
}
