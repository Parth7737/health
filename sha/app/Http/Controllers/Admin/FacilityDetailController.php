<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;

class FacilityDetailController extends Controller
{
    public function index()
    {
        $facilities = FacilityDetail::all();
        return view('admin-views.facility_details.index', compact('facilities'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required|unique:facility_details',
            'facility_name' => 'required',
            'state' => 'required',
            'district' => 'required',
            'sub_district' => 'required',
            'facility_ownership' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        FacilityDetail::create($request->all());
        return response()->json(['msg' => 'Facility added successfully.'], 200);
    }

    public function show(FacilityDetail $facility)
    {
        return response()->json(['data' => $facility]);
    }

    public function update(Request $request, FacilityDetail $facility)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required|unique:facility_details,facility_id,' . $facility->id,
            'facility_name' => 'required',
            'state' => 'required',
            'district' => 'required',
            'sub_district' => 'required',
            'facility_ownership' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $facility->update($request->all());
        return response()->json(['msg' => 'Facility updated successfully.'], 200);
    }

    public function destroy(FacilityDetail $facility)
    {
        $facility->delete();
        return redirect()->back()->with('success', 'Facility deleted successfully.');
    }

    public function import(Request $request)
{
    $request->validate([
        'facility_file' => 'required|mimes:csv,txt'
    ]);

    $file = $request->file('facility_file');
    $csv = Reader::createFromPath($file->getPathname(), 'r');
    $csv->setHeaderOffset(0);

    $facilities = [];
    $timestamp = now();

    foreach ($csv as $row) {
        $validator = Validator::make($row, [
            'facility_id' => 'required|unique:facility_details,facility_id',
            'facility_name' => 'required',
            'state' => 'required',
            'district' => 'required',
            'sub_district' => 'required',
            'facility_ownership' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $facilities[] = [
            'facility_id' => $row['facility_id'],
            'facility_name' => $row['facility_name'],
            'state' => $row['state'],
            'district' => $row['district'],
            'sub_district' => $row['sub_district'],
            'facility_ownership' => $row['facility_ownership'],
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    DB::table('facility_details')->insert($facilities);

    return back()->with('success', 'Facilities imported successfully!');
}
}
