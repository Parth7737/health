<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrPayrollSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'standard_working_days' => 'integer',
        'leave_deduction_per_day' => 'float',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }
}
