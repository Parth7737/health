<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;
use App\Models\MedicineInteraction;
use App\Models\MedicineAllergyMapping;
use App\Models\Allergy;
use App\Models\Hospital;

class ClinicalValidationDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospital = Hospital::first();
        $hospitalId = $hospital ? $hospital->id : 1;

        // 1. Create or update medicines with clinical data
        $meds = [
            [
                'name' => 'Warfarin 5mg',
                'is_high_risk' => true,
                'requires_rx' => true,
                'min_dose' => 1.00,
                'max_dose' => 10.00,
                'max_daily_dose' => 15.00,
                'dose_unit' => 'mg',
                'weight_based_dose' => false,
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'is_high_risk' => false,
                'requires_rx' => true,
                'min_dose' => 200.00,
                'max_dose' => 800.00,
                'max_daily_dose' => 2400.00,
                'dose_unit' => 'mg',
                'weight_based_dose' => false,
            ],
            [
                'name' => 'Clarithromycin 500mg',
                'is_high_risk' => false,
                'requires_rx' => true,
                'min_dose' => 250.00,
                'max_dose' => 500.00,
                'max_daily_dose' => 1000.00,
                'dose_unit' => 'mg',
                'weight_based_dose' => false,
            ],
            [
                'name' => 'Amlodipine 5mg',
                'is_high_risk' => false,
                'requires_rx' => true,
                'min_dose' => 2.50,
                'max_dose' => 10.00,
                'max_daily_dose' => 10.00,
                'dose_unit' => 'mg',
                'weight_based_dose' => false,
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'is_high_risk' => false,
                'requires_rx' => true,
                'min_dose' => 250.00,
                'max_dose' => 1000.00,
                'max_daily_dose' => 3000.00,
                'dose_unit' => 'mg',
                'weight_based_dose' => false,
            ],
            [
                'name' => 'Gentamicin 80mg',
                'is_high_risk' => true,
                'requires_rx' => true,
                'min_dose' => 20.00,
                'max_dose' => 120.00,
                'max_daily_dose' => 360.00,
                'dose_unit' => 'mg',
                'weight_based_dose' => true,
                'dose_per_kg' => 5.00,
            ]
        ];

        $medicineIdsByName = [];

        foreach ($meds as $medData) {
            $medData['hospital_id'] = $hospitalId;
            $med = Medicine::withoutGlobalScopes()
                ->where('hospital_id', $hospitalId)
                ->where('name', $medData['name'])
                ->first();

            if ($med) {
                // Remove unit to avoid naming conflict
                unset($medData['unit']);
                $med->update($medData);
            } else {
                $med = Medicine::create($medData);
            }

            $medicineIdsByName[$medData['name']] = $med->id;
        }

        // 2. Setup drug interactions
        MedicineInteraction::query()->delete();

        // Warfarin + Ibuprofen -> Critical
        MedicineInteraction::create([
            'hospital_id' => $hospitalId,
            'medicine_id' => $medicineIdsByName['Warfarin 5mg'],
            'interact_medicine_id' => $medicineIdsByName['Ibuprofen 400mg'],
            'severity' => 'critical',
            'clinical_effect' => 'Increased risk of severe gastrointestinal bleeding due to synergistic antiplatelet activity.',
            'recommendation' => 'Avoid co-prescribing. Use alternative analgesics like Paracetamol.'
        ]);

        // Clarithromycin + Amlodipine -> Major
        MedicineInteraction::create([
            'hospital_id' => $hospitalId,
            'medicine_id' => $medicineIdsByName['Clarithromycin 500mg'],
            'interact_medicine_id' => $medicineIdsByName['Amlodipine 5mg'],
            'severity' => 'major',
            'clinical_effect' => 'Clarithromycin is a strong CYP3A4 inhibitor; co-administration increases Amlodipine systemic exposure, risking severe hypotension and edema.',
            'recommendation' => 'Monitor blood pressure closely, consider lower dose of Amlodipine, or select a non-CYP3A4 inhibitor antibiotic (e.g. Azithromycin).'
        ]);

        // 3. Setup medicine allergy mappings
        MedicineAllergyMapping::query()->delete();

        // Check or create allergy named "Penicillin Allergy"
        $allergy = Allergy::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('name', 'like', '%Penicillin%')
            ->first();

        if (!$allergy) {
            $allergy = Allergy::create([
                'hospital_id' => $hospitalId,
                'name' => 'Penicillin Allergy'
            ]);
        }

        MedicineAllergyMapping::create([
            'hospital_id' => $hospitalId,
            'medicine_id' => $medicineIdsByName['Amoxicillin 500mg'],
            'allergy_id' => $allergy->id
        ]);
    }
}
