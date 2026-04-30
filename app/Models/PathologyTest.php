<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class PathologyTest extends Model
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
        return $this->belongsTo(PathologyCategory::class, 'pathology_category_id');
    }

    public function parameters()
    {
        return $this->belongsToMany(PathologyParameter::class, 'pathology_test_parameters')
            ->withTimestamps();
    }

    public function chargeMaster()
    {
        return $this->belongsTo(ChargeMaster::class);
    }
}
