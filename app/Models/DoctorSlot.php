<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSlot extends Model
{
    protected $fillable = [
        'hospital_id',
        'doctor_id',
        'slot_date',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_label',
        'max_patients',
        'is_blocked',
    ];

    protected $casts = [
        'slot_date'   => 'date',
        'is_blocked'  => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }
}
