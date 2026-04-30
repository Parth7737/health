<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'hospital_id',
        'patient_id',
        'mrn',
        'title',
        'name',
        'guardian_name',
        'date_of_birth',
        'age_years',
        'age_months',
        'country_code',
        'phone',
        'alternate_phone',
        'email',
        'image',
        'gender',
        'patient_category_id',
        'nationality_id',
        'religion_id',
        'dietary_id',
        'allergy_id',
        'habit_id',
        'disease_type_id',
        'disease_id',
        'blood_group',
        'marital_status',
        'address',
        'pin_code',
        'district',
        'state',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'known_allergies',
        'chronic_conditions',
        'past_surgical_history',
        'current_medications',
        'family_history',
        'smoking_status',
        'alcohol_status',
        'vaccination_status',
        'aadhar_no',
        'ayushman_bharat_id',
        'category',
        'occupation',
        'is_staff',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'dietary_id' => 'array',
        'allergy_id' => 'array',
        'habit_id' => 'array',
        'disease_type_id' => 'array',
        'disease_id' => 'array',
        'chronic_conditions' => 'array',
    ];

    public function diagnosticOrders(): HasMany
    {
        return $this->hasMany(DiagnosticOrder::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(PatientTimeline::class);
    }
}