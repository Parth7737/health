<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalServices extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'service_id', 'sub_service_id', 'action_id', 'service_value', 'text_value', 'remark', 'image', 'dec_verify_service_value', 'dec_verify_text_value', 'dec_verify_image', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }

    public function service() {
        return $this->belongsTo('App\Models\Service', 'service_id');
    }

    public function subService() {
        return $this->belongsTo('App\Models\SubService', 'sub_service_id');
    }

    public function action() {
        return $this->belongsTo('App\Models\SubServiceAction', 'sub_service_id');        
    }
}
