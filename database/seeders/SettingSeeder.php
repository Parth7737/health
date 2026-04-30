<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use App\Models\EmpanelmentDocument;
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
    }
}
