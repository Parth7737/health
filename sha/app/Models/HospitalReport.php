<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalReport extends Model
{
    protected $fillable = ['hospital_id', 'document_type', 'document', 'description', 'remark', 'latitude', 'longitude', 'verifier_id', 'dec_action', 'dec_document', 'dec_remarks', 'dec_verifier_id', 'sec_action', 'sec_document', 'sec_remarks', 'sec_verifier_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
}
