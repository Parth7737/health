<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalTeam extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'speciality_id', 'hpr_id', 'name', 'designation', 'employement_type', 'registration_no', 'email', 'mobile', 'registration_certificate', 'declaration_certificate', 'declaration_certificate_expiry', 'registration_certificate_expiry', 'is_approve', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id'];

    public function speciality() {
        return $this->belongsTo('App\Models\Speciality', 'speciality_id');
    }
}
