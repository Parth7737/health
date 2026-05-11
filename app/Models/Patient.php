<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $appends = [
        'full_name',
    ];

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

    public function patientCategory(): BelongsTo
    {
        return $this->belongsTo(PatientCategory::class, 'patient_category_id');
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(PatientTimeline::class);
    }

    public function getNameAttribute($value): string
    {
        return $this->formatNameWithTitle($value, $this->attributes['title'] ?? '', '');
    }

    public function getFullNameAttribute(): string
    {
        return $this->formatNameWithTitle($this->attributes['name'] ?? '', $this->attributes['title'] ?? '', '-');
    }

    private function formatNameWithTitle($nameValue, $titleValue, string $fallback = ''): string
    {
        $name = trim((string) ($nameValue ?? ''));
        $title = trim((string) ($titleValue ?? ''));

        if ($name === '' && $title === '') {
            return $fallback;
        }

        if ($title === '') {
            return $name;
        }

        if ($name === '') {
            return $title;
        }

        $lowerName = strtolower($name);
        $lowerTitle = strtolower($title);

        if ($lowerName === $lowerTitle || str_starts_with($lowerName, $lowerTitle . ' ')) {
            return $name;
        }

        return trim($title . ' ' . $name);
    }
}
