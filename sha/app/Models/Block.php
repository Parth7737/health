<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = ['state_id', 'district_id', 'name'];

    public function district() {
        return $this->belongsTo('App\Models\HospitalDistrict', 'district_id');
    }
}
