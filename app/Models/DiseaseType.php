<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class DiseaseType extends Model
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
}
