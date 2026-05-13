<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    HospitalState,
    HospitalDistrict,
    Village,
    StateBankDetail
};

class StateDistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HospitalState::truncate();
        HospitalDistrict::truncate();
        Village::truncate();
        StateBankDetail::truncate();

        HospitalState::create(['name' => 'Uttarakhand', 'country_id' => 101]);
        $HospitalDistrict = [
            "ALMORA",
            "BAGESHWAR",
            "CHAMOLI",
            "CHAMPAWAT",
            "DEHRADUN",
            "HARIDWAR",
            "NAINITAL",
            "PAURI GARHWAL",
            "PITHORAGARH",
            "Rudraprayag",
            "TEHRI GARHWAL",
            "Udham Singh Nagar",
            "Uttarkashi"
        ];
        foreach ($HospitalDistrict as $district) {
            HospitalDistrict::create([
                'state_id' => 1,
                'name' => $district
            ]);
        }

        Village::create(['district_id' => 1, 'name' => 'Adhar Muafi']);
        Village::create(['district_id' => 1, 'name' => 'Adheli Sunar']);
        Village::create(['district_id' => 1, 'name' => 'Aditha']);
        Village::create(['district_id' => 1, 'name' => 'Aicholi']);
        Village::create(['district_id' => 1, 'name' => 'Almora Range']);

        
        Village::create(['district_id' => 2, 'name' => 'Adhyali']);
        Village::create(['district_id' => 2, 'name' => 'Bajel']);
        Village::create(['district_id' => 2, 'name' => 'Balsuna']);
        Village::create(['district_id' => 2, 'name' => 'Chouhana']);
        Village::create(['district_id' => 2, 'name' => 'Dhapti']);

        Village::create(['district_id' => 7, 'name' => 'Abbaltani']);
        Village::create(['district_id' => 7, 'name' => 'Aghora']);
        Village::create(['district_id' => 7, 'name' => 'Bhadueni']);
        Village::create(['district_id' => 7, 'name' => 'Chopra']);
        Village::create(['district_id' => 7, 'name' => 'Duhra']);

        StateBankDetail::create([
            'bank_name' => 'ICICI BANK LIMITED',
            'state_id' => 1,
            'ifsc_code' => 'ICICI0000256',
            'account_name' => 'Atal Ayusman',
            'account_number' => '014565464646'
        ]);
    }
}
