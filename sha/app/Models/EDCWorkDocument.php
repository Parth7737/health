<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EDCWorkDocument extends Model
{
    protected $table = "edc_work_documents";

    protected $fillable = ['action_id', 'work_flow_id', 'document_type', 'document', 'description'];

    public function edcatcion() {
        return $this->belongsTo('App\Models\EDCAction', 'action_id');
    }

    public function workflow() {
        return $this->belongsTo('App\Models\EDCWorkFlow', 'work_flow_id');
    }
}
