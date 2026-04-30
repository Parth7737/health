<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class RadiologyUnit extends Model
{
    protected $guarded = [];

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

    public function parameters()
    {
        return $this->hasMany(RadiologyParameter::class, 'radiology_unit_id');
    }
}

