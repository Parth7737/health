<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OpdPatient extends Model
{
    protected $fillable = [
        'hospital_id',
        'patient_id',
        'doctor_id',
        'hr_department_id',
        'appointment_date',
        'case_no',
        'booking_number',
        'token_no',
        'casualty',
        'mlc_patient',
        'tpa_id',
        'tpa_reference_no',
        'symptoms_type_id',
        'symptoms',
        'symptoms_description',
        'height',
        'weight',
        'spo2',
        'bp',
        'systolic_bp',
        'diastolic_bp',
        'pluse',
        'temperature',
        'respiration',
        'diabetes',
        'bmi',
        'body_area',
        'social_known_allergies',
        'social_allergic_reactions',
        'occupation',
        'social_marital_status',
        'place_of_birth',
        'current_location',
        'years_in_current_location',
        'social_habits',
        'family_history',
        'subjective_notes',
        'objective_notes',
        'assessment_notes',
        'plan_notes',
        'patient_instructions',
        'follow_up_date',
        'disposition',
        'slot',
        'visit_type',
        'standard_charge',
        'applied_charge',
        'consultation_case_type',
        'consultation_case_label',
        'consultation_charge_source',
        'consultation_reference_opd_patient_id',
        'consultation_valid_until',
        'payment_mode',
        'live_consultation',
        'status',
        'ipd_admitted_at',
        'ipd_bed_allocation_id',
        'absent_flag',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'token_no' => 'integer',
        'absent_flag' => 'boolean',
        'symptoms_type_id' => 'array',
        'symptoms' => 'array',
        'social_known_allergies' => 'array',
        'social_allergic_reactions' => 'array',
        'social_habits' => 'array',
        'family_history' => 'array',
        'follow_up_date' => 'date',
        'consultation_valid_until' => 'datetime',
        'standard_charge' => 'decimal:2',
        'applied_charge' => 'decimal:2',
        'ipd_admitted_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(OpdPrescription::class, 'opd_patient_id');
    }

    public function diagnosticOrders(): MorphMany
    {
        return $this->morphMany(DiagnosticOrder::class, 'visitable');
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(PatientTimeline::class, 'encounter_id')
            ->where('encounter_type', 'opd');
    }

    public function ipdAllocation(): BelongsTo
    {
        return $this->belongsTo(BedAllocation::class, 'ipd_bed_allocation_id');
    }

    public function getSymptomsNameAttribute(): string
    {
        $ids = $this->symptoms ? $this->symptoms : [];
        $ids = array_values(array_filter($ids));

        if (empty($ids)) {
            return '-';
        }

        $namesById = Symptoms::query()
            ->whereIn('id', $ids)
            ->pluck('name', 'id');

        return collect($ids)
            ->map(fn (int $id) => $namesById[$id] ?? null)
            ->filter()
            ->implode(', ');
    }
}
