<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Scopes\HospitalScope;

class BedAllocation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
        'expected_discharge_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model and apply any global scopes.
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    /**
     * Get the hospital.
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the bed.
     */
    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Get the doctor who admitted the patient.
     */
    public function admittedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'admitted_by_staff_id');
    }

    public function consultantDoctor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'consultant_doctor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }

    public function tpa(): BelongsTo
    {
        return $this->belongsTo(Tpa::class);
    }

    public function sourceOpdPatient(): BelongsTo
    {
        return $this->belongsTo(OpdPatient::class, 'source_opd_patient_id');
    }

    /**
     * Get the doctor who discharged the patient.
     */
    public function dischargedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'discharged_by_staff_id');
    }

    public function charges(): MorphMany
    {
        return $this->morphMany(PatientCharge::class, 'visitable');
    }

    public function progressNotes(): HasMany
    {
        return $this->hasMany(IpdProgressNote::class, 'bed_allocation_id');
    }

    /**
     * Check if allocation is active (not discharged).
     */
    public function isActive(): bool
    {
        return is_null($this->discharge_date);
    }

    /**
     * Check if patient is discharged.
     */
    public function isDischarged(): bool
    {
        return !is_null($this->discharge_date);
    }

    /**
     * Get length of stay in days.
     */
    public function getLengthOfStay()
    {
        $endDate = $this->discharge_date ?? now();
        return $this->admission_date->diffInDays($endDate);
    }

    /**
     * Get the room identifier.
     */
    public function getRoomIdentifier()
    {
        return $this->bed->room->room_number ?? 'N/A';
    }

    /**
     * Get the ward identifier.
     */
    public function getWardIdentifier()
    {
        return $this->bed->room->ward->ward_name ?? 'N/A';
    }
}
