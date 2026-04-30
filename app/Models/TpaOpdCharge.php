<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class TpaOpdCharge extends Model
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

    public function doctorOpdCharge()
    {
        return $this->belongsTo(DoctorOpdCharge::class);
    }

    public function tpa()
    {
        return $this->belongsTo(Tpa::class);
    }
}
