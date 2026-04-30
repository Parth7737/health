<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class PathologySampleType extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }
}