<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UHospitalCeo extends Model
{
    protected $fillable = ['hospital_id', 'name', 'email', 'password', 'designation', 'mobile_no', 'email_otp', 'mobile_otp', 'aadhaar_no', 'is_detail_added', 'is_approve', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id', 'main_hospitalid', 'old_id'];

    public function hospital() {
        return $this->belongsTo('App\Model\UHospitals', 'hospital_id');
    }
}
