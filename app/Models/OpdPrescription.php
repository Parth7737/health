<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OpdPrescription extends Model
{
    protected $fillable = [
        'hospital_id',
        'patient_id',
        'opd_patient_id',
        'doctor_id',
        'header_note',
        'footer_note',
        'valid_till',
        'notification_to',
    ];

    protected $casts = [
        'valid_till' => 'date',
        'notification_to' => 'array',
    ];

    public function opdPatient(): BelongsTo
    {
        return $this->belongsTo(OpdPatient::class, 'opd_patient_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OpdPrescriptionItem::class, 'opd_prescription_id');
    }

    public function saleBill(): HasOne
    {
        return $this->hasOne(PharmacySaleBill::class, 'opd_prescription_id');
    }
}
