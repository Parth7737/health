<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\HospitalDistrict;
use App\Models\HospitalState;
use Illuminate\Http\Request;
use League\Csv\Reader;

class BlockController extends Controller
{
    public function index()
    {
        $blocks = Block::paginate(50);
        $states = HospitalState::all();
        return view('admin-views.blocks.index', compact('blocks', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'state_id' => 'required',
            'district_id' => 'required|integer',
            'name' => 'required|string',
        ]);

        Block::create($request->all());

        return response()->json(['msg' => 'Block added successfully!']);
    }

    public function getDistrict(Request $request, $id) {
        $data = HospitalDistrict::where('state_id', $id)->get();
        return response()->json(['data' => $data]);
    }

    public function getblocks(Request $request, $id) {
        $data = Block::where('district_id', $id)->get();
        return response()->json(['data' => $data]);
    }

    public function import(Request $request) {
        // try {
            // $file = $request->file('blockfile');
            // echo $file->getClientOriginalExtension();
            // exit;
            $request->validate([
                'statei_id' => 'required',
                'districti_id' => 'required',
                'blockfile' => 'required'
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
                    Block::updateOrCreate(
                        ['state_id' => $request->statei_id, 'district_id' => $request->districti_id, 'name' => $name],
                        ['name' => $name]
                    );
                }
            }

            return back()->with('success', 'Data imported successfully!');
        // } catch(\Exception $e) {
        //     print_r($e->getMessage());
        //     exit;
        // }
    }

    public function show($id)
    {
        $village = Block::findOrFail($id);
        return response()->json(['data' => $village]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'state_id' => 'required',
            'district_id' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        $village = Block::findOrFail($id);
        $village->update($request->all());

        return response()->json(['msg' => 'Block updated successfully!']);
    }

    public function destroy($id)
    {
        $village = Block::findOrFail($id);
        $village->delete();

        return redirect()->back()->with('success', 'Village deleted successfully!');
    }
}
