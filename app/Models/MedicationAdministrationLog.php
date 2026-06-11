<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationAdministrationLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'scheduled_date' => 'date',
        'administered_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function bedAllocation(): BelongsTo
    {
        return $this->belongsTo(BedAllocation::class, 'bed_allocation_id');
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(IpdPrescription::class, 'ipd_prescription_id');
    }

    public function prescriptionItem(): BelongsTo
    {
        return $this->belongsTo(IpdPrescriptionItem::class, 'ipd_prescription_item_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function administeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
