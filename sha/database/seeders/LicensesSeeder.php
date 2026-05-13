<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Licenses, LicensesType};

class LicensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Licenses::truncate();
        LicensesType::truncate();

        $services = ['Building & Infrastructure', 'Registrations and Certificates'];

        foreach ($services as $key => $value) {
            $id = Licenses::create(['name' => $value])->id;
        }

        LicensesType::create(['license_id' => 1, 'name' => 'Building Plan Approval', 'is_required' => 1, 'document_required' => 1]);
        LicensesType::create(['license_id' => 1, 'name' => 'Fire Department Clearance Certificate', 'is_required' => 1, 'document_required' => 1]);
        LicensesType::create(['license_id' => 1, 'name' => 'Pollution Control Board Certificate', 'is_required' => 1, 'document_required' => 1]);
        LicensesType::create(['license_id' => 2, 'name' => 'Hospital Registration Certificate', 'is_required' => 1, 'document_required' => 1]);
        LicensesType::create(['license_id' => 2, 'name' => 'Bio-Medical Waste Management', 'is_required' => 1, 'document_required' => 1]);
        LicensesType::create(['license_id' => 2, 'name' => 'Pharmacy Registration', 'is_required' => 0, 'document_required' => 1]);

    }
}
