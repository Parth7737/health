<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class DoctorOpdCharge extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    public function tpaOpdCharges()
    {
        return $this->hasMany(TpaOpdCharge::class);
    }

    public function chargeMaster(): MorphOne
    {
        return $this->morphOne(ChargeMaster::class, 'related');
    }
}
