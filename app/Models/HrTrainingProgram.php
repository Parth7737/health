<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrTrainingProgram extends Model
{
    protected $guarded = [];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function trainingCategory(): BelongsTo
    {
        return $this->belongsTo(HrTrainingCategory::class, 'hr_training_category_id');
    }

    public function trainingParticipants(): HasMany
    {
        return $this->hasMany(HrTrainingParticipant::class, 'hr_training_program_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HrTrainingProgramLog::class, 'hr_training_program_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function categoryDisplay(): string
    {
        $this->loadMissing('trainingCategory');
        if ($this->trainingCategory) {
            return (string) $this->trainingCategory->name;
        }

        return (string) ($this->category ?? '');
    }
}
