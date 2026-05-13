<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalImages extends Model
{
    protected $fillable = ['hospital_id', 'image'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
}
