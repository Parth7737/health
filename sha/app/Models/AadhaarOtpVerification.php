<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AadhaarOtpVerification extends Model
{
    protected $fillable = ['aadhaar_no', 'reference_id', 'aadhaar_id', 'reference_id', 'is_verify'];

    public function aadhaarverification() {
        return $this->belongsTo('App\Models\AadhaarInformation', 'aadhaar_id');
    }
}
