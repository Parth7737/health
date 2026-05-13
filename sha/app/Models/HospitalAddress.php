<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalAddress extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'address', 'pincode', 'block', 'village', 'city', 'district', 'state', 'landmark', 'telephone', 'std_code', 'mobile_no', 'otp', 'email', 'website', 'police_station', 'locality', 'latitude', 'longitude', 'is_added', 'is_approve', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }

    public function states() {
        return $this->belongsTo('App\Models\HospitalState', 'state');
    }

    public function districts() {
        return $this->belongsTo('App\Models\HospitalDistrict', 'district');
    }

    public function villages() {
        return $this->belongsTo('App\Models\Village', 'village');
    }

    public function blockdata() {
        return $this->belongsTo('App\Models\Block', 'block');
    }
}
