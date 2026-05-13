<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOnSpeciality extends Model
{
    protected $fillable = ['add_on_id','speciality_id'];
    public function speciality() {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }
    public function addon_procedure() {
        return $this->belongsTo(Procedure::class, 'add_on_id');
    }
}
