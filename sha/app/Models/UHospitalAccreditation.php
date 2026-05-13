<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UHospitalAccreditation extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'accreditation', 'accreditation_id', 'certificate_no', 'valid_from', 'valid_till', 'certificate', 'speciality_ids', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id', 'main_hospitalid', 'old_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\UHospitals', 'hospital_id');
    }

    public function accred() {
        return $this->belongsTo('App\Models\Accreditation', 'accreditation_id');
    }
}
