<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\BeneficiaryImport;
use App\Jobs\ImportBeneficiariesJob;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use DataTables;

class BeneficiaryController extends Controller
{

    public function import(Request $request)
    {
        $file = $request->file('beneficiary_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            if (!empty($row['id_pk'])) {
                DB::table("benificiaries")->updateOrInsert(
                    ['card_no' => $row['card_no']], // Unique key for updating

                    // All fields from the migration
                    [
                        'ben_id' => $row['ben_id'] ?? null,
                            'family_id' => $row['family_id'] ?? null,
                            'member_id' => $row['member_id'] ?? null,
                            'bis_family_id' => $row['bis_family_id'] ?? null,
                            'bis_member_id' => $row['bis_member_id'] ?? null,
                            'ben_ref_id' => $row['ben_ref_id'] ?? null,
                            'state_cd' => $row['state_cd'] ?? null,
                            'state' => $row['state_cd'] ?? null,
                            'dist_cd' => $row['dist_cd'] ?? null,
                            'district' => $row['dist_cd'] ?? null,
                            'block_id' => $row['block_id'] ?? null,
                            'village_id' => $row['village_id'] ?? null,
                            'city' => $row['village_id'] ?? null,
                            'rural_urban_flag' => $row['rural_urban_flag'] ?? null,
                            'house_no' => $row['house_no'] ?? null,
                            'pincode' => $row['pincode'] ?? null,
                            'address' => $row['address'] ?? null,
                            'dist_name' => $row['dist_name'] ?? null,
                            'state_name' => $row['state_name'] ?? null,
                            'ben_mobile_no' => $row['ben_mobile_no'] ?? null,
                            'mobile_no' => $row['ben_mobile_no'] ?? null,
                            'ben_email_id' => $row['ben_email_id'] ?? null,
                            'json_obj_ben_source_dtl' => $row['json_obj_ben_source_dtl'] ?? null,
                            'json_obj_ben_ekyc_dtl' => $row['json_obj_ben_ekyc_dtl'] ?? null,
                            'active_status' => $row['active_status'] ?? 1,
                            'enrl_status' => $row['enrl_status'] ?? null,
                            'abha_id' => $row['abha_id'] ?? null,
                            'aabha_id' => $row['abha_id'] ?? null,
                            'payer_id' => $row['payer_id'] ?? null,
                            'tpa_isa_id' => $row['tpa_isa_id'] ?? null,
                            'json_obj_ben_othr_dtl' => $row['json_obj_ben_othr_dtl'] ?? null,
                            'src_flag' => $row['src_flag'] ?? null,
                            'enrol_status' => $row['enrol_status'] ?? null,
                            'entity_id' => $row['entity_id'] ?? null,
                            'card_no' => $row['card_no'] ?? null,
                            'card_id' => $row['card_no'] ?? null,
                            'photo' => $row['photo'] ?? null,
                            'image' => $row['photo'] ?? null,
                            'relation' => $row['relation'] ?? null,
                            'auth_mode' => $row['auth_mode'] ?? null,
                            'primary_auth_mode' => $row['primary_auth_mode'] ?? null,
                            'new_member_flag' => $row['new_member_flag'] ?? null,
                            'gender' => isset($row['gender']) ? ($row['gender'] === 'M' ? 'Male' : ($row['gender'] === 'F' ? 'Female' : 'Other'))  : null,
                            'year_of_birth' => $row['year_of_birth'] ?? null,
                            'name' => $row['name'] ?? null,
                            'father_name' => $row['father_name'] ?? null,
                            'age' => isset($row['age']) ? date('Y') - (int) $row['age'] : null,
                            'primary_ben_id' => $row['primary_ben_id'] ?? null,
                            'approve_date' => $row['approve_date'] ?? null,
                            'enrol_date' => $row['enrol_date'] ?? null,
                            'card_status' => $row['card_status'] ?? null,
                            'aadhar_status' => $row['aadhar_status'] ?? null,
                            'reject_date' => $row['reject_date'] ?? null,
                            'date_of_birth' => $row['date_of_birth'] ?? null,
                            'scheme_code' => $row['scheme_code'] ?? null,
                            'care_plan' => $row['scheme_code'] ?? null,
                            'request_type' => $row['request_type'] ?? null,
                            'auth_txn' => $row['auth_txn'] ?? null,
                            'primary_auth_txn' => $row['primary_auth_txn'] ?? null,
                            'request_agent' => $row['request_agent'] ?? null,
                            'match_score' => $row['match_score'] ?? null,
                            'source_type' => $row['source_type'] ?? null,
                    ]
                );
            }
        }

        return back()->with('success', 'Beneficiaries imported successfully.');
    }
    public function importManual(Request $request)
    {
        // $file = $request->file('beneficiary_file');
        // $csv = Reader::createFromPath($file->getPathname(), 'r');
        // $csv->setHeaderOffset(0);
        set_time_limit(0);
        $filePath = storage_path('app/public/ben/beneficiaries-master2.csv');
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found!');
        }
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $batchSize = 1000;
        $batch = [];

        // Fetch all existing card numbers to reduce queries
        $existingCards = Beneficiary::pluck('card_no')->toArray();
        foreach ($csv as $key => $row) {
            if (!empty($row['id_pk']) && !in_array($row['card_no'], $existingCards)) {
                $batch[] = [
                    'ben_id' => $row['ben_id'] ?? null,
                    'family_id' => $row['family_id'] ?? null,
                    'member_id' => $row['member_id'] ?? null,
                    'bis_family_id' => $row['bis_family_id'] ?? null,
                    'bis_member_id' => $row['bis_member_id'] ?? null,
                    'ben_ref_id' => $row['ben_ref_id'] ?? null,
                    'state_cd' => $row['state_cd'] ?? null,
                    'state' => $row['state_cd'] ?? null,
                    'dist_cd' => $row['dist_cd'] ?? null,
                    'district' => $row['dist_cd'] ?? null,
                    'block_id' => $row['block_id'] ?? null,
                    'village_id' => $row['village_id'] ?? null,
                    'city' => $row['village_id'] ?? null,
                    'rural_urban_flag' => $row['rural_urban_flag'] ?? null,
                    'house_no' => $row['house_no'] ?? null,
                    'pincode' => $row['pincode'] ?? null,
                    'address' => $row['address'] ?? null,
                    'dist_name' => $row['dist_name'] ?? null,
                    'state_name' => $row['state_name'] ?? null,
                    'ben_mobile_no' => $row['ben_mobile_no'] ?? null,
                    'mobile_no' => $row['ben_mobile_no'] ?? null,
                    'ben_email_id' => $row['ben_email_id'] ?? null,
                    'json_obj_ben_source_dtl' => $row['json_obj_ben_source_dtl'] ?? null,
                    'json_obj_ben_ekyc_dtl' => $row['json_obj_ben_ekyc_dtl'] ?? null,
                    'active_status' => $row['active_status'] ?? 1,
                    'enrl_status' => $row['enrl_status'] ?? null,
                    'abha_id' => $row['abha_id'] ?? null,
                    'aabha_id' => $row['abha_id'] ?? null,
                    'payer_id' => $row['payer_id'] ?? null,
                    'tpa_isa_id' => $row['tpa_isa_id'] ?? null,
                    'json_obj_ben_othr_dtl' => $row['json_obj_ben_othr_dtl'] ?? null,
                    'src_flag' => $row['src_flag'] ?? null,
                    'enrol_status' => $row['enrol_status'] ?? null,
                    'entity_id' => $row['entity_id'] ?? null,
                    'card_no' => $row['card_no'] ?? null,
                    'card_id' => $row['card_no'] ?? null,
                    'photo' => $row['photo'] ?? null,
                    'image' => $row['photo'] ?? null,
                    'relation' => $row['relation'] ?? null,
                    'auth_mode' => $row['auth_mode'] ?? null,
                    'primary_auth_mode' => $row['primary_auth_mode'] ?? null,
                    'new_member_flag' => $row['new_member_flag'] ?? null,
                    'gender' => isset($row['gender']) ? ($row['gender'] === 'M' ? 'Male' : ($row['gender'] === 'F' ? 'Female' : 'Other'))  : null,
                    'year_of_birth' => $row['year_of_birth'] ?? null,
                    'name' => $row['name'] ?? null,
                    'father_name' => $row['father_name'] ?? null,
                    'age' => isset($row['age']) ? date('Y') - (int) $row['age'] : null,
                    'primary_ben_id' => $row['primary_ben_id'] ?? null,
                    'approve_date' => $row['approve_date'] ?? null,
                    'enrol_date' => $row['enrol_date'] ?? null,
                    'card_status' => $row['card_status'] ?? null,
                    'aadhar_status' => $row['aadhar_status'] ?? null,
                    'reject_date' => $row['reject_date'] ?? null,
                    'date_of_birth' => $row['date_of_birth'] ?? null,
                    'scheme_code' => $row['scheme_code'] ?? null,
                    'care_plan' => $row['scheme_code'] ?? null,
                    'request_type' => $row['request_type'] ?? null,
                    'auth_txn' => $row['auth_txn'] ?? null,
                    'primary_auth_txn' => $row['primary_auth_txn'] ?? null,
                    'request_agent' => $row['request_agent'] ?? null,
                    'match_score' => $row['match_score'] ?? null,
                    'source_type' => $row['source_type'] ?? null,
                ];
            }

            if (count($batch) >= $batchSize) {
                
                // $datareq = array('key'=>$key,'time'=>date('Y-m-d H:i:s'));
                // $myfile = fopen(public_path()."/ben-logs.txt", "a") or die("Unable to open file!");
                // fwrite($myfile,json_encode($datareq));
                // fclose($myfile);
                ImportBeneficiariesJob::dispatch($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            ImportBeneficiariesJob::dispatch($batch);
        }
        return redirect()->route('admin.beneficiaries.index')->with('success', 'Beneficiary imported successfully.');
    }
    public function index()
    {
        return view('admin-views.beneficiaries.index');
    }

    public function getBeneficiariesData(Request $request)
    {
        $beneficiaries = Beneficiary::select(['id', 'name', 'father_name', 'card_id', 'aabha_id','source_type', 'family_id', 'member_id', 'mobile_no']);
    
        return DataTables::of($beneficiaries)
            ->addIndexColumn()
            ->addColumn('actions', function ($beneficiary) {
                return '<div class="btn--container justify-content-center">
                            <a class="btn action-btn btn--info btn-outline-info" 
                               href="' . route('admin.beneficiaries.show', $beneficiary->id) . '" 
                               title="View Beneficiary">
                               <i class="fa fa-eye"></i>
                            </a>
                        </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
        ]);

        Beneficiary::create([
            'name' => $request->name,
            'father_name' => $request->father_name,
        ]);

        return response()->json(['msg' => 'Beneficiary added successfully!']);
    }

    public function show($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        return view('admin-views.beneficiaries.show', compact('beneficiary'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
        ]);

        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->update([
            'name' => $request->name,
            'father_name' => $request->father_name,
        ]);

        return response()->json(['msg' => 'Beneficiary updated successfully!']);
    }

    public function destroy($id)
    {
        Beneficiary::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Beneficiary deleted successfully.');
    }
}
