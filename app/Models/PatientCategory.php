<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientCategory extends Model
{
    protected $fillable = ['hospital_id', 'name', 'waiver_percentage'];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
