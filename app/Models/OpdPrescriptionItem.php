<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpdPrescriptionItem extends Model
{
    protected $fillable = [
        'opd_prescription_id',
        'medicine_id',
        'medicine_category_id',
        'medicine_dosage_id',
        'medicine_instruction_id',
        'medicine_route_id',
        'medicine_frequency_id',
        'no_of_day',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(OpdPrescription::class, 'opd_prescription_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicineCategory::class, 'medicine_category_id');
    }

    public function dosage(): BelongsTo
    {
        return $this->belongsTo(MedicineDosage::class, 'medicine_dosage_id');
    }

    public function instruction(): BelongsTo
    {
        return $this->belongsTo(MedicineInstruction::class, 'medicine_instruction_id');
    }

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(MedicineFrequency::class, 'medicine_frequency_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(MedicineRoute::class, 'medicine_route_id');
    }
}
