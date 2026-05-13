<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        BusinessSetting::truncate();
        BusinessSetting::create([
            'key'=> 'site_title',
            'value' => 'SHA: State Health Authority',
        ]);

        BusinessSetting::create([
            'key'=> 'hospital_empanelment_fee',
            'value' => 10000,
        ]);
    }
}
