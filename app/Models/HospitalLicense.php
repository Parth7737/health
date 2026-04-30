<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalLicense extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'license_id', 'license_type_id', 'issue_date', 'expiry_date', 'remark', 'document', 'admin_verify_status', 'admin_verify_remark'];

    public function license() {
        return $this->belongsTo('App\Models\Licenses', 'license_id');
    }

    public function licenseType() {
        return $this->belongsTo('App\Models\LicenseType', 'license_type_id');
    }
    public function getDocUrlAttribute(){
        return asset('public/storage/'.$this->document);
    }
}
