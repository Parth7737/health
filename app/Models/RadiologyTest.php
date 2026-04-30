<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class RadiologyTest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'standard_charge' => 'decimal:2',
        'charge_master_id' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function category()
    {
        return $this->belongsTo(RadiologyCategory::class, 'radiology_category_id');
    }

    public function parameters()
    {
        return $this->belongsToMany(RadiologyParameter::class, 'radiology_test_parameters')
            ->withTimestamps();
    }

    public function chargeMaster()
    {
        return $this->belongsTo(ChargeMaster::class);
    }
}
