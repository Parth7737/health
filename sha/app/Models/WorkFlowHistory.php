<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkFlowHistory extends Model
{
    protected $fillable = ['uuid', 'hospital_id', 'action', 'attachment', 'remark', 'created_by'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
}
