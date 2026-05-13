<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrTrainingProgramLog extends Model
{
    public $timestamps = false;

    protected $table = 'hr_training_program_logs';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(HrTrainingProgram::class, 'hr_training_program_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
