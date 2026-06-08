<?php

namespace App\Services\Clinical;

use App\Models\Medicine;
use App\Models\MedicineDosage;
use App\Models\MedicineFrequency;

class DoseValidationService
{
    /**
     * Validate dosage for a prescription item.
     *
     * @param Medicine $medicine
     * @param float|null $weight
     * @param int|null $dosageId
     * @param int|null $frequencyId
     * @return array|null Null if valid, or array with warning details
     */
    public function validate(Medicine $medicine, ?float $weight, ?int $dosageId, ?int $frequencyId): ?array
    {
        if (!$dosageId) {
            return null;
        }

        $dosageObj = MedicineDosage::find($dosageId);
        if (!$dosageObj) {
            return null;
        }

        $dosageNumeric = $this->parseDosageNumeric($dosageObj->dosage);
        if ($dosageNumeric === null) {
            return null;
        }

        $frequencyObj = $frequencyId ? MedicineFrequency::find($frequencyId) : null;
        $dailyFrequency = $frequencyObj ? (int) $frequencyObj->no_of_medicine : 1;
        if ($dailyFrequency <= 0) {
            $dailyFrequency = 1;
        }

        $dailyDose = $dosageNumeric * $dailyFrequency;

        // 1. Single Dose Check
        if ($medicine->min_dose !== null && $dosageNumeric < (float) $medicine->min_dose) {
            return [
                'type' => 'dose',
                'severity' => 'minor',
                'message' => sprintf(
                    "Dose under minimum: Prescribed dose %s %s is below the recommended minimum of %s %s for %s.",
                    $dosageNumeric,
                    $medicine->dose_unit ?? '',
                    (float) $medicine->min_dose,
                    $medicine->dose_unit ?? '',
                    $medicine->name
                )
            ];
        }

        if ($medicine->max_dose !== null && $dosageNumeric > (float) $medicine->max_dose) {
            return [
                'type' => 'dose',
                'severity' => 'major',
                'message' => sprintf(
                    "Dose over maximum: Prescribed dose %s %s exceeds the recommended maximum of %s %s for %s.",
                    $dosageNumeric,
                    $medicine->dose_unit ?? '',
                    (float) $medicine->max_dose,
                    $medicine->dose_unit ?? '',
                    $medicine->name
                )
            ];
        }

        // 2. Weight-based Dose Check
        if ($medicine->weight_based_dose && (float) $medicine->dose_per_kg > 0) {
            if ($weight > 0) {
                $targetSingleDose = (float) $medicine->dose_per_kg * $weight;
                // Allow a small margin of error (e.g., 10%)
                $lowerBound = $targetSingleDose * 0.9;
                $upperBound = $targetSingleDose * 1.1;

                if ($dosageNumeric < $lowerBound || $dosageNumeric > $upperBound) {
                    return [
                        'type' => 'dose',
                        'severity' => 'major',
                        'message' => sprintf(
                            "Weight-based dose mismatch: Prescribed dose %s %s deviates from weight-based recommended target dose of %s %s (based on weight %s kg and %s %s/kg) for %s.",
                            $dosageNumeric,
                            $medicine->dose_unit ?? '',
                            round($targetSingleDose, 2),
                            $medicine->dose_unit ?? '',
                            $weight,
                            (float) $medicine->dose_per_kg,
                            $medicine->dose_unit ?? '',
                            $medicine->name
                        )
                    ];
                }
            } else {
                return [
                    'type' => 'dose',
                    'severity' => 'minor',
                    'message' => sprintf(
                        "Weight-based dosing requires patient weight. Please record weight to verify dose of %s.",
                        $medicine->name
                    )
                ];
            }
        }

        // 3. Daily Dose Check
        if ($medicine->max_daily_dose !== null && $dailyDose > (float) $medicine->max_daily_dose) {
            return [
                'type' => 'dose',
                'severity' => 'critical',
                'message' => sprintf(
                    "Daily dose exceeded: Prescribed daily cumulative dose %s %s exceeds the absolute maximum daily dose of %s %s for %s.",
                    $dailyDose,
                    $medicine->dose_unit ?? '',
                    (float) $medicine->max_daily_dose,
                    $medicine->dose_unit ?? '',
                    $medicine->name
                )
            ];
        }

        return null;
    }

    /**
     * Parse numeric value from dosage string.
     */
    private function parseDosageNumeric(?string $dosageString): ?float
    {
        if (!$dosageString) {
            return null;
        }
        if (preg_match('/[0-9]+(?:\.[0-9]+)?/', $dosageString, $matches)) {
            return (float) $matches[0];
        }
        return null;
    }
}
