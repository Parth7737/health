<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IpdPrescription extends Model
{
    protected $fillable = [
        'hospital_id',
        'patient_id',
        'bed_allocation_id',
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

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(BedAllocation::class, 'bed_allocation_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(IpdPrescriptionItem::class, 'ipd_prescription_id');
    }

    public function saleBill(): HasOne
    {
        return $this->hasOne(PharmacySaleBill::class, 'ipd_prescription_id');
    }
}
