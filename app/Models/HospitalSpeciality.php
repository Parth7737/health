<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalSpeciality extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'speciality_id', 'available', 'remark', 'admin_verify_status', 'admin_verify_remark'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospital', 'hospital_id');
    }
    public function speciality() {
        return $this->belongsTo('App\Models\Speciality', 'speciality_id');
    }
}
