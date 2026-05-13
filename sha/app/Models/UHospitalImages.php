<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UHospitalImages extends Model
{
    protected $fillable = ['hospital_id', 'image'];

    public function hospital() {
        return $this->belongsTo('App\Models\UHospitals', 'hospital_id');
    }
}
