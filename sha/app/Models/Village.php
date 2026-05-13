<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = ['district_id', 'name', 'state_id', 'block_id'];

    public function district()
    {
        return $this->belongsTo(HospitalDistrict::class, 'district_id');
    }

    public function block() {
        return $this->belongsTo('App\Models\Block', 'block_id');
    }

    public function state() {
        return $this->belongsTo('App\Models\HospitalState', 'state_id');
    }
}