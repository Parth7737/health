<?php

namespace App\Services;

use App\Models\Bed;
use App\Models\BedAllocation;
use App\Models\BedStatus;
use App\Models\Patient;
use App\Models\PatientTimeline;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BedAllocationService
{
    protected $availabilityService;
    protected $timelineService;

    public function __construct(BedAvailabilityService $availabilityService, PatientTimelineService $timelineService)
    {
        $this->availabilityService = $availabilityService;
        $this->timelineService = $timelineService;
    }

    /**
     * Allocate a bed to a patient.
     */
    public function allocateBed($hospitalId, $patientId, $bedId, $staffId = null, $admissionType = 'emergency', $notes = null, array $extraAttributes = [])
    {
        return DB::transaction(function () use ($hospitalId, $patientId, $bedId, $staffId, $admissionType, $notes, $extraAttributes) {
            // Validate patient exists
            $patient = Patient::where('id', $patientId)
                ->where('hospital_id', $hospitalId)
                ->firstOrFail();

            // Validate bed exists and is available
            $bed = Bed::where('id', $bedId)
                ->where('hospital_id', $hospitalId)
                ->firstOrFail();

            if (!$this->availabilityService->isBedAvailable($bedId)) {
                throw new Exception("Bed is not available");
            }

            // Check if patient already has active allocation
            $activeAllocation = BedAllocation::where('patient_id', $patientId)
                ->whereNull('discharge_date')
                ->first();

            if ($activeAllocation) {
                throw new Exception("Patient already has an active bed allocation");
            }

            // Create allocation record
            $allocation = BedAllocation::create(array_merge([
                'hospital_id' => $hospitalId,
                'patient_id' => $patientId,
                'bed_id' => $bedId,
                'admitted_by_staff_id' => $staffId,
                'admission_date' => now(),
                'admission_type' => $admissionType,
                'admission_notes' => $notes,
            ], $extraAttributes));

            // Update bed status to occupied
            $bed->update(['bed_status_id' => BedStatus::OCCUPIED]);

            // Create patient timeline entry
            $this->timelineService->recordAdmission($patientId, $hospitalId, [
                'allocation_id' => $allocation->id,
                'bed_id' => $bedId,
                'admission_type' => $admissionType,
                'staff_id' => $staffId,
                'remarks' => $notes,
            ]);

            return $allocation->load(['bed', 'bed.room', 'bed.room.ward', 'patient']);
        });
    }

    /**
     * Discharge a patient from a bed.
     */
    public function dischargeBed($allocationId, $staffId = null, $dischargeStatus = 'recovered', $notes = null, array $extraAttributes = [])
    {
        return DB::transaction(function () use ($allocationId, $staffId, $dischargeStatus, $notes, $extraAttributes) {
            $allocation = BedAllocation::findOrFail($allocationId);

            if ($allocation->isDischarged()) {
                throw new Exception("Patient is already discharged");
            }

            // Update allocation
            $allocation->update(array_merge([
                'discharge_date' => now(),
                'discharged_by_staff_id' => $staffId,
                'discharge_status' => $dischargeStatus,
                'discharge_notes' => $notes,
            ], $extraAttributes));

            // Update bed status back to available
            $bed = $allocation->bed;
            $bed->update(['bed_status_id' => BedStatus::AVAILABLE]);

            // Create timeline entry
            $this->timelineService->recordDischarge($allocation->patient_id, $allocation->hospital_id, [
                'allocation_id' => $allocation->id,
                'bed_id' => $bed->id,
                'discharge_status' => $dischargeStatus,
                'staff_id' => $staffId,
                'remarks' => $notes,
            ]);

            return $allocation->load(['bed', 'patient']);
        });
    }

    /**
     * Transfer patient to another bed.
     */
    public function transferBed($hospitalId, $currentAllocationId, $newBedId, $staffId = null, $reason = null, array $extraAttributes = [])
    {
        return DB::transaction(function () use ($hospitalId, $currentAllocationId, $newBedId, $staffId, $reason, $extraAttributes) {
            // Get current allocation
            $currentAllocation = BedAllocation::where('id', $currentAllocationId)
                ->where('hospital_id', $hospitalId)
                ->firstOrFail();

            if ($currentAllocation->isDischarged()) {
                throw new Exception(" Cannot transfer discharged patient");
            }

            // Validate new bed
            $newBed = Bed::where('id', $newBedId)
                ->where('hospital_id', $hospitalId)
                ->firstOrFail();

            if (!$this->availabilityService->isBedAvailable($newBedId)) {
                throw new Exception("New bed is not available");
            }

            // Free up current bed
            $currentAllocation->bed->update(['bed_status_id' => BedStatus::AVAILABLE]);

            // Discharge from current bed
            $currentAllocation->update([
                'discharge_date' => now(),
                'discharge_notes' => $reason,
                'discharged_by_staff_id' => $staffId,
                'discharge_status' => 'transferred',
            ]);

            // Allocate to new bed
            $newAllocation = BedAllocation::create(array_merge([
                'hospital_id' => $hospitalId,
                'patient_id' => $currentAllocation->patient_id,
                'bed_id' => $newBedId,
                'admitted_by_staff_id' => $staffId,
                'admission_date' => now(),
                'admission_type' => 'transfer',
                'admission_notes' => "Transfer reason: {$reason}",
            ], $extraAttributes));

            // Update new bed status
            $newBed->update(['bed_status_id' => BedStatus::OCCUPIED]);

            // Record timeline
            $this->timelineService->recordBedTransfer(
                $currentAllocation->patient_id,
                $hospitalId,
                [
                    'allocation_id' => $newAllocation->id,
                    'from_bed_id' => $currentAllocation->bed_id,
                    'to_bed_id' => $newBedId,
                    'reason' => $reason,
                    'staff_id' => $staffId,
                ]
            );

            return $newAllocation->load(['bed', 'bed.room', 'bed.room.ward', 'patient']);
        });
    }

    /**
     * Reserve a bed.
     */
    public function reserveBed($bedId, $reason = null)
    {
        $bed = Bed::findOrFail($bedId);

        if ($bed->bed_status_id != BedStatus::AVAILABLE) {
            throw new Exception("Bed cannot be reserved");
        }

        $bed->update(['bed_status_id' => BedStatus::RESERVED]);

        return $bed;
    }

    /**
     * Release a reserved bed.
     */
    public function releaseBed($bedId)
    {
        $bed = Bed::findOrFail($bedId);

        if ($bed->bed_status_id == BedStatus::RESERVED) {
            $bed->update(['bed_status_id' => BedStatus::AVAILABLE]);
        }

        return $bed;
    }

    /**
     * Mark bed for maintenance.
     */
    public function markForMaintenance($bedId, $reason = null)
    {
        $bed = Bed::findOrFail($bedId);

        $bed->update([
            'bed_status_id' => BedStatus::MAINTENANCE,
            'last_maintained_at' => now(),
        ]);

        return $bed;
    }

    /**
     * Get patient's current allocation.
     */
    public function getPatientCurrentAllocation($patientId, $hospitalId)
    {
        return BedAllocation::where('patient_id', $patientId)
            ->where('hospital_id', $hospitalId)
            ->whereNull('discharge_date')
            ->with(['bed', 'bed.room', 'bed.room.ward', 'admittedBy'])
            ->first();
    }

    /**
     * Get patient's allocation history.
     */
    public function getPatientAllocationHistory($patientId, $hospitalId)
    {
        return BedAllocation::where('patient_id', $patientId)
            ->where('hospital_id', $hospitalId)
            ->with(['bed', 'bed.room', 'bed.room.ward', 'admittedBy', 'dischargedBy'])
            ->orderBy('admission_date', 'desc')
            ->get();
    }

    /**
     * Get length of stay for a patient allocation.
     */
    public function getLengthOfStay($allocationId)
    {
        $allocation = BedAllocation::findOrFail($allocationId);
        return $allocation->getLengthOfStay();
    }
}
