<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AadhaarInformation extends Model
{
    protected $fillable = ['aadhaar_no', 'reference_id', 'care_of', 'full_address', 'date_of_birth', 'email_hash', 'gender', 'name', 'age', 'country', 'district', 'house', 'landmark', 'pincode', 'post_office', 'state', 'street', 'subdistrict', 'vtc', 'year_of_birth', 'mobile_hash', 'photo', 'status', 'is_verify'];

    public function aadhaarverification() {
        return $this->hasMany('App\Models\AadhaarOtpVerification', 'aadhaar_id');
    }
}
