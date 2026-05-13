<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxDetails extends Model
{
    protected $fillable = ['hospital_id','uuid','pan_no','pan_name','tan_no','tan_holder_name','gst_no','gst_name','tds_exemption','tds_exemption_id','tds_rate','after_tds_rate','tds_exemption_certificate_no','tds_exemption_valid_from','tds_exemption_valid_till','tds_exemption_amount', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id', 'pan_certificate', 'gst_certificate', 'tds_exemption_certificate'];

    public function tdsexemption()
    {
        return $this->belongsTo('App\Models\TdsExemption', 'tds_exemption_id');
    }
}
