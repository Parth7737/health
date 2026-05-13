<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalHumanResource extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'healthcare_proffessionals_registry_id', 'type_of_human_resource', 'sub_type_of_human_resource', 'name', 'registration_number', 'email', 'mobile_no', 'registration_certificate', 'declaration_certificate', 'is_added', 'is_approve', 'type',  'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }

    public function humanResource() {
        return $this->belongsTo('App\Models\HumanResource', 'sub_type_of_human_resource');
    }
}
