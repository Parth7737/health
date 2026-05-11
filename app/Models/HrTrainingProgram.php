<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrTrainingProgram extends Model
{
    protected $guarded = [];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }
}
