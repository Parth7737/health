<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrTrainingCategory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(HrTrainingProgram::class, 'hr_training_category_id');
    }
}
