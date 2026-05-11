<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrPayrollComponent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'value' => 'float',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }
}
