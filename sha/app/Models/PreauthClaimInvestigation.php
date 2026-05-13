<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreauthClaimInvestigation extends Model
{
    protected $fillable = ['preauth_register_id','investigation_id','file', 'ppd_status', 'ppd_status_verify_date', 'cex_status', 'cex_status_verify_date', 'cpd_status', 'cpd_status_verify_date', 'sha_status', 'sha_status_verify_date', 'acs_status'];
    
    public function investigation() {
        return $this->belongsTo(Investigation::class, 'investigation_id');
    }
}
