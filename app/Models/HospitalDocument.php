<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalDocument extends Model
{
    protected $fillable = ['hospital_id','uuid','document_id','document', 'remarks', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospital', 'hospital_id');
    }

    public function doc() {
        return $this->belongsTo('App\Models\EmpanelmentDocument', 'document_id');
    }
    public function getDocUrlAttribute(){
        return asset('public/storage/'.$this->document);
    }
}
