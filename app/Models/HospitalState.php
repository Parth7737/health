<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalState extends Model
{
    protected $fillable = ['name', 'country_id'];

    public function districts() {
        return $this->hasMany('App\Models\HospitalDistrict', 'state_id');
    }
}
