<?php

namespace Database\Seeders;

use App\Models\FacilityType;
use App\Models\FacilityOwnershipSubType;
use App\Models\FacilityOwnershipType;
use App\Models\FacilityRegistrationCertificate;
use App\Models\FacilitySpecialityType;
use App\Models\GovermentBenefits;
use App\Models\SystemMedicine;
use App\Models\EntityType;
use App\Models\Entity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class EmpanelMentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        FacilityType::truncate();
        FacilitySpecialityType::truncate();
        FacilityOwnershipType::truncate();
        FacilityOwnershipSubType::truncate();
        FacilityRegistrationCertificate::truncate();
        GovermentBenefits::truncate();
        SystemMedicine::truncate();
        EntityType::truncate();
        Entity::truncate();

        EntityType::create(['name' => 'Hospital']);
        EntityType::create(['name' => 'Division']);
        EntityType::create(['name' => 'Pathology Lab']);
        EntityType::create(['name' => 'Pharmacy']);
        EntityType::create(['name' => 'Radiology Lab']);
        // EntityType::create(['name' => 'ISA']);
        EntityType::create(['name' => 'SHA']);

        Entity::create(['type' => 'Hospital', 'name' => 'Hospital Onboarding Entity']);
        Entity::create(['type' => 'Division', 'name' => 'District Empanelment Committee']);
        Entity::create(['type' => 'Division', 'name' => 'State Empanelment Committee']);
        Entity::create(['type' => 'Pathology Lab', 'name' => 'Pathology Lab Onboarding Entity']);
        Entity::create(['type' => 'Pharmacy', 'name' => 'Pharmacy Onboarding Entity']);
        Entity::create(['type' => 'Radiology Lab', 'name' => 'Radiology Lab Hospital Onboarding Entity']);
        Entity::create(['type' => 'SHA', 'name' => 'ISA Entity']);
        Entity::create(['type' => 'SHA', 'name' => 'SHA Entity']);

        FacilityType::create(['name' => 'Hospital']);
        FacilitySpecialityType::create(['name' => 'Multi Speciality']);
        FacilitySpecialityType::create(['name' => 'Single Speciality']);
        FacilitySpecialityType::create(['name' => 'Standalone Dialysis Center']);
        FacilitySpecialityType::create(['name' => 'Super Speciality']);
        DB::transaction(function () {
            $id = FacilityOwnershipType::create(['name' => 'Private'])->id;
            FacilityOwnershipSubType::create(['facility_ownership_type_id' => $id, 'name' => 'Profit']);
        });
        FacilityRegistrationCertificate::create(['name' => 'Hospital Registration Act']);
        GovermentBenefits::create(['name' => 'Avaling Tax Benifits']);
        SystemMedicine::create(['name' => 'Modern Medicine(Allopathy)']);
    }
}
