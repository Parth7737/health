<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalSpeciality extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'speciality_id', 'available', 'offered' , 'not_offered_reason', 'remark', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
    public function speciality() {
        return $this->belongsTo('App\Models\Speciality', 'speciality_id');
    }
}
