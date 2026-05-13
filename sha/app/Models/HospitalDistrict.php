<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalDistrict extends Model
{
    protected $fillable = ['name', 'state_id'];

    public function state()
    {
        return $this->belongsTo(HospitalState::class, 'state_id');
    }
}
