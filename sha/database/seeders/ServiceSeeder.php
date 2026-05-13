<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Service, SubService, SubServiceAction};

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::truncate();
        SubService::truncate();
        SubServiceAction::truncate();

        $services = ['Services', 'Equipments'];

        foreach ($services as $key => $value) {
            $id = Service::create(['name' => $value])->id;
        }

        $subServices1 = ['ICU Ventilators', 'Crash Cart', 'All Essential Equipments for ICU Available', 'Total Bed Strength', 'ICU', 'HDU', 'Semi Private Ward', 'Private Ward', 'General ward'];

        foreach ($subServices1 as $key => $value) {
            SubService::create([
                'service_id' => 1,
                'name' => $value,
                'is_required' => 1
            ]);   
        }

        SubServiceAction::create(['sub_service_id' => 1, 'type' => 'radio', 'label' => 'Yes', 'value' => '1', 'sublabel' => 'How Much', 'is_text_input' => '1', 'is_image' => 1]);
        SubServiceAction::create(['sub_service_id' => 1, 'type' => 'radio', 'label' => 'No', 'value' => '0']);

        SubServiceAction::create(['sub_service_id' => 2, 'type' => 'radio', 'label' => 'Yes', 'value' => '1']);
        SubServiceAction::create(['sub_service_id' => 2, 'type' => 'radio', 'label' => 'No', 'value' => '0']);

        SubServiceAction::create(['sub_service_id' => 3, 'type' => 'radio', 'label' => 'Yes', 'value' => '1']);
        SubServiceAction::create(['sub_service_id' => 3, 'type' => 'radio', 'label' => 'No', 'value' => '0']);

        SubServiceAction::create(['sub_service_id' => 4, 'type' => 'text', 'label' => 'Total Bed Strength']);
       
        SubServiceAction::create(['sub_service_id' => 5, 'type' => 'radio', 'label' => 'Yes', 'value' => '1']);
        SubServiceAction::create(['sub_service_id' => 5, 'type' => 'radio', 'label' => 'No', 'value' => '0']);

        SubServiceAction::create(['sub_service_id' => 6, 'type' => 'radio', 'label' => 'Yes', 'value' => '1']);
        SubServiceAction::create(['sub_service_id' => 6, 'type' => 'radio', 'label' => 'No', 'value' => '0']);

        SubServiceAction::create(['sub_service_id' => 7, 'type' => 'radio', 'label' => 'Yes', 'value' => '1', 'sublabel' => 'No of Wards', 'is_text_input' => '1', 'is_image' => 1]);
        SubServiceAction::create(['sub_service_id' => 7, 'type' => 'radio', 'label' => 'No', 'value' => '0']);

        SubServiceAction::create(['sub_service_id' => 8, 'type' => 'radio', 'label' => 'Yes', 'value' => '1', 'sublabel' => 'No of Wards', 'is_text_input' => '1', 'is_image' => 1]);
        SubServiceAction::create(['sub_service_id' => 8, 'type' => 'radio', 'label' => 'No', 'value' => '0']);

        SubServiceAction::create(['sub_service_id' => 9, 'type' => 'radio', 'label' => 'Yes', 'value' => '1', 'sublabel' => 'No of Wards', 'is_text_input' => '1', 'is_image' => 1]);
        SubServiceAction::create(['sub_service_id' => 9, 'type' => 'radio', 'label' => 'No', 'value' => '0']);
    }
}
