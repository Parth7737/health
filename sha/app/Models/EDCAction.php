<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EDCAction extends Model
{
    protected $table = "edc_actions";

    protected $fillable = ['hospital_id', 'order_id', 'last_action', 'main_status', 'status', 'next_status', 'submission_date', 'added_by', 'is_close_action', 'is_stop_payment', 'is_stop_preauth', 'hospital_user'];

    public function workflow() {
        return $this->hasMany('App\Models\EDCWorkFlow', 'action_id');
    }
    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
}
