<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Implant extends Model
{
    public function speciality() {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }
}
