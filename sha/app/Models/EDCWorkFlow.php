<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EDCWorkFlow extends Model
{
    protected $table = "edc_work_flows";

    protected $fillable = ['action_id', 'action', 'remark', 'date_of_issuance', 'submission_date', 'action_start_date', 'action_end_date', 'due_date', 'penalty_imposed', 'penalty_recovered', 'days', 'added_by', 'authority', 'fir_case_number'];

    public function documents() {
        return $this->hasMany('App\Models\EDCWorkDocument', 'work_flow_id');
    }

    public function edcatcion() {
        return $this->belongsTo('App\Models\EDCAction', 'action_id');
    }
}
