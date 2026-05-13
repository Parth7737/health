<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionDetails extends Model
{
    public function admission_type() {
        return $this->belongsTo('App\Models\AdmissionType', 'admission_type_id');
    }
}
