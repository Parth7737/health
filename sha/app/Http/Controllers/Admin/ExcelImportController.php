<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ExcelImportController extends Controller
{
    public function importView()
    {
        $nameOnlyTables = [
            'admission_types', 'appetites', 'asthmas', 'bowels', 'cancers', 'diabetes', 'diets',
            'empanelment_types', 'entity_types', 'facility_ownership_types', 'facility_registration_certificates',
            'facility_speciality_types', 'facility_types', 'goverment_benefits', 'heart_diseases',
            'hypertensions', 'licenses', 'nutrition', 'preauth_cancel_reasons', 'preauth_reject_reasons',
            'registration_cancel_reasons', 'scheme_types', 'services', 'stratification_categories',
            'strokes', 'system_medicines', 'tds_exemptions', 'tuberculosis'
        ];

        $nameAndCodeTables = ['diagnoses', 'packages'];

        return view('admin-views.excel.import_excel', compact('nameOnlyTables', 'nameAndCodeTables'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'table_name' => 'required|in:admission_types,appetites,asthmas,bowels,cancers,diabetes,diets,empanelment_types,entity_types,facility_ownership_types,facility_registration_certificates,facility_speciality_types,facility_types,goverment_benefits,heart_diseases,hypertensions,licenses,nutrition,preauth_cancel_reasons,preauth_reject_reasons,registration_cancel_reasons,scheme_types,services,stratification_categories,strokes,system_medicines,tds_exemptions,tuberculosis',
            'file' => 'required|mimes:csv'
        ]);

        $table = $request->table_name;
        $file = $request->file('file');

        // Read CSV file
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);
        foreach ($csv as $row) {
            if (!empty($row['Name'])) {
                $name = mb_convert_encoding($row['Name'], 'UTF-8', 'ISO-8859-1');
                DB::table($table)->updateOrInsert(
                    ['name' => $name],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        return back()->with('success', 'Data imported successfully!');
    }

    public function importWithCode(Request $request)
    {
        $request->validate([
            'table_name' => 'required|in:diagnoses,packages',
            'file' => 'required|mimes:csv'
        ]);

        $table = $request->table_name;
        $file = $request->file('file');

        // Read CSV file
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            $name = mb_convert_encoding($row['Name'], 'UTF-8', 'ISO-8859-1');

            if (!empty($name) && !empty($row['Code'])) {
                DB::table($table)->updateOrInsert(
                    ['name' => $name],
                    ['name' => $name, 'code' => $row['Code']]
                );
            }
        }

        return back()->with('success', 'Data with Code imported successfully!');
    }
}
