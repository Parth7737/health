<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class RadiologyParameter extends Model
{
    protected $guarded = [];

    protected $casts = [
        'min_value' => 'decimal:4',
        'max_value' => 'decimal:4',
        'critical_low' => 'decimal:4',
        'critical_high' => 'decimal:4',
    ];

    /**
     * Boot the model and apply any global scopes.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function unit()
    {
        return $this->belongsTo(RadiologyUnit::class, 'radiology_unit_id');
    }
}

