<?php

namespace App\Services\Clinical;

use App\Models\OpdPrescription;
use App\Models\IpdPrescription;
use App\Models\RxValidationLog;
use App\Models\Medicine;
use App\Models\Patient;

class RxValidationEngine
{
    protected DoseValidationService $doseValidator;
    protected InteractionValidationService $interactionValidator;
    protected AllergyValidationService $allergyValidator;

    public function __construct(
        DoseValidationService $doseValidator,
        InteractionValidationService $interactionValidator,
        AllergyValidationService $allergyValidator
    ) {
        $this->doseValidator = $doseValidator;
        $this->interactionValidator = $interactionValidator;
        $this->allergyValidator = $allergyValidator;
    }

    /**
     * Run all clinical validations for a given prescription (OPD or IPD).
     *
     * @param mixed $prescription
     * @return array List of alerts logged
     */
    public function validate($prescription): array
    {
        $type = $prescription instanceof OpdPrescription ? 'opd' : ($prescription instanceof IpdPrescription ? 'ipd' : null);
        if (!$type) {
            return [];
        }

        $hospitalId = $prescription->hospital_id;
        $patientId = $prescription->patient_id;
        $patient = Patient::find($patientId);

        if (!$patient) {
            return [];
        }

        // 1. Get patient weight
        $weight = null;
        if ($type === 'opd') {
            $opdPatient = $prescription->opdPatient;
            if ($opdPatient && $opdPatient->weight) {
                $weight = $this->parseWeight($opdPatient->weight);
            }
        } else {
            $allocation = $prescription->allocation;
            if ($allocation && $allocation->weight) {
                $weight = $this->parseWeight($allocation->weight);
            }
        }

        // 2. Fetch all prescription items and their medicine IDs
        $items = $prescription->items()->with('medicine')->get();
        $medicineIds = $items->pluck('medicine_id')->filter()->unique()->all();

        if (empty($medicineIds)) {
            // No medicines, clean up old pending logs and return
            RxValidationLog::where('prescription_id', $prescription->id)
                ->where('prescription_type', $type)
                ->where('status', 'pending')
                ->delete();
            return [];
        }

        $alerts = [];

        // --- Check 1: Allergy Validation ---
        $allergyAlerts = $this->allergyValidator->validate($patient, $medicineIds);
        foreach ($allergyAlerts as $alert) {
            $alerts[] = $alert;
        }

        // --- Check 2: Drug Interactions ---
        $interactionAlerts = $this->interactionValidator->validate($medicineIds);
        foreach ($interactionAlerts as $alert) {
            $alerts[] = $alert;
        }

        // --- Check 3: Dose limits & High Risk flags ---
        foreach ($items as $item) {
            if (!$item->medicine_id) {
                continue;
            }

            $medicine = $item->medicine;
            if (!$medicine) {
                continue;
            }

            // High Risk check
            if ($medicine->is_high_risk) {
                $alerts[] = [
                    'type' => 'high_risk',
                    'medicine_id' => $medicine->id,
                    'prescription_item_id' => $item->id,
                    'severity' => 'major',
                    'message' => sprintf(
                        "High-Risk Drug Warning: '%s' is classified as high-risk/high-alert. Monitor patient closely and verify indication.",
                        $medicine->name
                    )
                ];
            }

            // Dose Validation
            $doseAlert = $this->doseValidator->validate(
                $medicine,
                $weight,
                $item->medicine_dosage_id,
                $item->medicine_frequency_id
            );

            if ($doseAlert) {
                $doseAlert['medicine_id'] = $medicine->id;
                $doseAlert['prescription_item_id'] = $item->id;
                $alerts[] = $doseAlert;
            }
        }

        // 3. Persist new alerts in database
        // Delete previous pending warnings for this prescription
        RxValidationLog::where('prescription_id', $prescription->id)
            ->where('prescription_type', $type)
            ->where('status', 'pending')
            ->delete();

        foreach ($alerts as $alert) {
            $medicineId = $alert['medicine_id'] ?? ($alert['interact_medicine_id'] ?? 0);

            $exists = RxValidationLog::where('prescription_id', $prescription->id)
                ->where('prescription_type', $type)
                ->where('medicine_id', $medicineId)
                ->where('validation_type', $alert['type'])
                ->where('message', $alert['message'])
                ->exists();

            if (!$exists) {
                RxValidationLog::create([
                    'hospital_id' => $hospitalId,
                    'patient_id' => $patientId,
                    'prescription_id' => $prescription->id,
                    'prescription_type' => $type,
                    'prescription_item_id' => $alert['prescription_item_id'] ?? null,
                    'medicine_id' => $medicineId,
                    'validation_type' => $alert['type'],
                    'severity' => $alert['severity'],
                    'message' => $alert['message'],
                    'status' => 'pending'
                ]);
            }
        }

        return $alerts;
    }

    /**
     * Parse weight value from string (e.g. "70.5 kg" -> 70.5).
     */
    private function parseWeight(?string $weightStr): ?float
    {
        if (!$weightStr) {
            return null;
        }
        if (preg_match('/[0-9]+(?:\.[0-9]+)?/', $weightStr, $matches)) {
            return (float) $matches[0];
        }
        return null;
    }
}
