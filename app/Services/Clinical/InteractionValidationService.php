<?php

namespace App\Services\Clinical;

use App\Models\MedicineInteraction;

class InteractionValidationService
{
    /**
     * Check interactions between a set of medicine IDs.
     *
     * @param array $medicineIds
     * @return array List of interaction warning arrays
     */
    public function validate(array $medicineIds): array
    {
        if (count($medicineIds) < 2) {
            return [];
        }

        $interactions = MedicineInteraction::query()
            ->whereIn('medicine_id', $medicineIds)
            ->whereIn('interact_medicine_id', $medicineIds)
            ->with(['medicine:id,name', 'interactMedicine:id,name'])
            ->get();

        $alerts = [];
        // Keep track of alert pairs to avoid duplicate alerts (A interacts with B and B interacts with A)
        $seenPairs = [];

        foreach ($interactions as $interaction) {
            $medId1 = $interaction->medicine_id;
            $medId2 = $interaction->interact_medicine_id;

            $pairKey1 = $medId1 . '-' . $medId2;
            $pairKey2 = $medId2 . '-' . $medId1;

            if (isset($seenPairs[$pairKey1]) || isset($seenPairs[$pairKey2])) {
                continue;
            }

            $seenPairs[$pairKey1] = true;

            $alerts[] = [
                'type' => 'interaction',
                'medicine_id' => $medId1,
                'interact_medicine_id' => $medId2,
                'severity' => $interaction->severity, // minor, moderate, major, critical
                'message' => sprintf(
                    "Drug Interaction (%s): %s interacts with %s. Clinical Effect: %s. Recommendation: %s.",
                    strtoupper($interaction->severity),
                    $interaction->medicine?->name ?? 'Medicine ID '.$medId1,
                    $interaction->interactMedicine?->name ?? 'Medicine ID '.$medId2,
                    $interaction->clinical_effect ?? 'None specified',
                    $interaction->recommendation ?? 'Consult prescribing physician'
                )
            ];
        }

        return $alerts;
    }
}
