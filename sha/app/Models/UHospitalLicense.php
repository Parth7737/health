<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UHospitalLicense extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'license_id', 'license_type_id', 'issue_date', 'expiry_date', 'remark', 'document',  'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id', 'main_hospitalid', 'old_id'];

    public function license() {
        return $this->belongsTo('App\Models\Licenses', 'license_id');
    }

    public function licenseType() {
        return $this->belongsTo('App\Models\LicensesType', 'license_type_id');
    }
}
