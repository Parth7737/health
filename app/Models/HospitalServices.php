<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalServices extends Model
{
    protected $fillable = ['hospital_id', 'uuid', 'service_id', 'sub_service_id', 'action_id', 'service_value', 'text_value', 'remark', 'image', 'admin_verify_status', 'admin_verify_remark'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospital', 'hospital_id');
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
