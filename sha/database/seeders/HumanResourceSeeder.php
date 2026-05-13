<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HumanResource;

class HumanResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HumanResource::truncate();
       
        $options = [
            'Medical Superintendent',
            'Nursing ICU',
            'Nursing IP Wards',
            'Resident Medical Officer'
        ];

        foreach ($options as $key => $value) {
            HumanResource::create([
                'type' => 'Medical Human Resource',
                'type_slug' => 'mhr',
                'name' => $value
            ]);    
        }

        $departments = [
            'BioChemistry',
            'Blood Bank',
            'MicrobioLogy',
            'Pathology',
            'Pharmacy',
            'Physiotherapy',
            'Radiology',
            'Anaessthetist'
        ];

        foreach ($departments as $key => $value) {
            HumanResource::create([
                'type' => 'Support Service Human Resource',
                'type_slug' => 'sshr',
                'name' => $value
            ]);    
        }
    }
}
