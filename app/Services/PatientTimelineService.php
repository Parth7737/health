<?php

namespace App\Services;

use App\Models\BedAllocation;
use App\Models\OpdPatient;
use App\Models\Patient;
use App\Models\PatientTimeline;
use Illuminate\Support\Facades\Auth;

class PatientTimelineService
{
    public function log(Patient $patient, array $payload): PatientTimeline
    {
        return PatientTimeline::create([
            'hospital_id' => $patient->hospital_id,
            'patient_id' => $patient->id,
            'encounter_type' => $payload['encounter_type'] ?? 'general',
            'encounter_id' => $payload['encounter_id'] ?? null,
            'event_key' => $payload['event_key'],
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'meta' => $payload['meta'] ?? null,
            'logged_at' => $payload['logged_at'] ?? now(),
            'created_by' => $payload['created_by'] ?? Auth::id(),
            'updated_by' => $payload['updated_by'] ?? Auth::id(),
        ]);
    }

    public function logForOpdVisit(OpdPatient $opdPatient, array $payload): PatientTimeline
    {
        return $this->log($opdPatient->patient, [
            'encounter_type' => 'opd',
            'encounter_id' => $opdPatient->id,
            'logged_at' => $payload['logged_at'] ?? now(),
            ...$payload,
        ]);
    }

    public function logForIpdAdmission(BedAllocation $allocation, array $payload): PatientTimeline
    {
        return $this->log($allocation->patient, [
            'encounter_type' => 'ipd',
            'encounter_id' => $allocation->id,
            'logged_at' => $payload['logged_at'] ?? now(),
            ...$payload,
        ]);
    }

    public function recordAdmission(int $patientId, int $hospitalId, array $meta = []): PatientTimeline
    {
        $patient = Patient::query()
            ->where('id', $patientId)
            ->where('hospital_id', $hospitalId)
            ->firstOrFail();

        $allocation = isset($meta['allocation_id'])
            ? BedAllocation::query()->where('hospital_id', $hospitalId)->find($meta['allocation_id'])
            : null;

        $title = $allocation?->admission_no
            ? 'IPD Admission Created (' . $allocation->admission_no . ')'
            : 'IPD Admission Created';

        return $this->log($patient, [
            'encounter_type' => 'ipd',
            'encounter_id' => $allocation?->id,
            'event_key' => 'ipd.admission.created',
            'title' => $title,
            'description' => $meta['remarks'] ?? 'Patient has been admitted to IPD.',
            'meta' => $meta,
            'logged_at' => $meta['logged_at'] ?? now(),
        ]);
    }

    public function recordDischarge(int $patientId, int $hospitalId, array $meta = []): PatientTimeline
    {
        $patient = Patient::query()
            ->where('id', $patientId)
            ->where('hospital_id', $hospitalId)
            ->firstOrFail();

        return $this->log($patient, [
            'encounter_type' => 'ipd',
            'encounter_id' => $meta['allocation_id'] ?? null,
            'event_key' => 'ipd.discharge.completed',
            'title' => 'IPD Discharge Completed',
            'description' => $meta['remarks'] ?? 'Patient has been discharged from IPD.',
            'meta' => $meta,
            'logged_at' => $meta['logged_at'] ?? now(),
        ]);
    }

    public function recordBedTransfer(int $patientId, int $hospitalId, array $meta = []): PatientTimeline
    {
        $patient = Patient::query()
            ->where('id', $patientId)
            ->where('hospital_id', $hospitalId)
            ->firstOrFail();

        return $this->log($patient, [
            'encounter_type' => 'ipd',
            'encounter_id' => $meta['allocation_id'] ?? null,
            'event_key' => 'ipd.bed.transferred',
            'title' => 'Bed Transfer Completed',
            'description' => $meta['reason'] ?? 'Patient bed has been transferred.',
            'meta' => $meta,
            'logged_at' => $meta['logged_at'] ?? now(),
        ]);
    }
}
