<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use App\Models\EmpanelmentDocument;
use App\Models\StaffStrength;
use App\Models\EmpanelmentEligibility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SettingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        BusinessSetting::firstOrCreate(
            ['key' => 'site_title'],
            ['value' => 'Paracare +']
        );
        $sourcePath = public_path('front/assets/img/paracare-logo.png');
        $destinationDir = storage_path('app/public/logo');
        $destinationPath = $destinationDir . '/paracare-logo.png';

        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true); // recursive = true
        }

        $url = 'logo/paracare-logo.png';
        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);
        }

        BusinessSetting::firstOrCreate(
            ['key' => 'front_logo'],
            ['value' => $url]
        );

        
        BusinessSetting::firstOrCreate(
            ['key' => 'registration_fee'],
            ['value' => 10000]
        );

        BusinessSetting::firstOrCreate(
            ['key' => 'ccavenue'],
            ['value' => '{"status":0,"client_id":null,"secret_id":null}']
        );

        BusinessSetting::firstOrCreate(
            ['key' => 'empanelment_step_status'],
            ['value' => '{"speciality_status":"1","service_status":"1","licenses_status":"1"}']
        );
        
        EmpanelmentDocument::firstOrCreate(
            ['name' => 'Legal Entity Certificate in Case of PAN Card Issued on Individual Name'],
            ['is_required' => 1]
        );
        EmpanelmentDocument::firstOrCreate(
            ['name' => 'Hospital Registration Certificate'],
            ['is_required' => 1]
        );
        EmpanelmentDocument::firstOrCreate(
            ['name' => 'Other documents'],
            ['is_required' => 0]
        );

        EmpanelmentEligibility::firstOrCreate(
            ['title' => 'Minimum 50 inpatient beds'],
            ['subtitle' => 'The hospital must have a minimum of 50 inpatient beds to be eligible for empanelment.', 'is_required' => 1]
        );
        EmpanelmentEligibility::firstOrCreate(
            ['title' => 'Valid registration certificate'],
            ['subtitle' => 'Active facility registration under Clinical Establishments Act', 'is_required' => 0]
        );
        EmpanelmentEligibility::firstOrCreate(
            ['title' => 'Empanelment agreement signed'],
            ['subtitle' => 'MoA with SHA Uttarakhand executed and notarised', 'is_required' => 0]
        );
        EmpanelmentEligibility::firstOrCreate(
            ['title' => 'Cashless service commitment'],
            ['subtitle' => 'Facility commits to providing cashless treatment to AB beneficiaries', 'is_required' => 0]
        );
        EmpanelmentEligibility::firstOrCreate(
            ['title' => 'Package rate acceptance'],
            ['subtitle' => 'Facility accepts PMJAY package rates without additional charges', 'is_required' => 0]
        );
        EmpanelmentEligibility::firstOrCreate(
            ['title' => 'TMS portal integration ready'],
            ['subtitle' => 'Transaction Management System access for real-time claim submission', 'is_required' => 0]
        );
        EmpanelmentEligibility::firstOrCreate(
            ['title' => 'Inspection report cleared'],
            ['subtitle' => 'Physical inspection by DISHA/SHA team — all parameters met', 'is_required' => 0]
        );

        //Staff Strength
        $staffStrengths = ['Specialist Doctors','Medical Officers','Staff Nurses','ANM / LHV','Pharmacist','Lab Technician'];
        foreach ($staffStrengths as $staffStrength) {
            StaffStrength::firstOrCreate(
                ['name' => $staffStrength],
            );
        }
    }
}
