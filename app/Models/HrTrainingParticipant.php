<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrTrainingParticipant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'certificate_issued_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(HrTrainingProgram::class, 'hr_training_program_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
