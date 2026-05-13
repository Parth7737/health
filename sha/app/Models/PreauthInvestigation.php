<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ProcedureInvestigationScope;

class PreauthInvestigation extends Model
{
    protected $fillable = ['preauth_register_id','investigation_id','file', 'cex_status', 'cex_status_verify_date', 'cpd_status', 'cpd_status_verify_date','is_resubmission', 'sha_status', 'sha_status_verify_date', 'acs_status'];
    
    public function investigation() {
        return $this->belongsTo(Investigation::class, 'investigation_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new ProcedureInvestigationScope);
    }
}
