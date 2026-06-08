<?php

namespace App\Services\Clinical;

use App\Models\Patient;
use App\Models\MedicineAllergyMapping;
use App\Models\Allergy;

class AllergyValidationService
{
    /**
     * Check if the patient is allergic to any of the prescribed medicines.
     *
     * @param Patient $patient
     * @param array $medicineIds
     * @return array List of allergy warning arrays
     */
    public function validate(Patient $patient, array $medicineIds): array
    {
        $allergyIds = $patient->allergy_id;

        // Ensure we have an array of allergy IDs
        if (is_string($allergyIds)) {
            $allergyIds = json_decode($allergyIds, true);
        }
        $allergyIds = collect((array) $allergyIds)->filter()->map(fn($id) => (int) $id)->values()->all();

        if (empty($allergyIds) || empty($medicineIds)) {
            return [];
        }

        $mappings = MedicineAllergyMapping::query()
            ->whereIn('medicine_id', $medicineIds)
            ->whereIn('allergy_id', $allergyIds)
            ->with(['medicine:id,name', 'allergy:id,name'])
            ->get();

        $alerts = [];
        foreach ($mappings as $mapping) {
            $alerts[] = [
                'type' => 'allergy',
                'medicine_id' => $mapping->medicine_id,
                'severity' => 'critical', // Allergy warnings are critical
                'message' => sprintf(
                    "Allergy Alert: Patient is allergic to '%s' (matches allergy category: %s).",
                    $mapping->medicine?->name ?? 'Medicine ID '.$mapping->medicine_id,
                    $mapping->allergy?->name ?? 'Allergy ID '.$mapping->allergy_id
                )
            ];
        }

        return $alerts;
    }
}
