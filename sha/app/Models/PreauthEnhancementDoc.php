<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ProcedureEnhancementScope;

class PreauthEnhancementDoc extends Model
{
    protected $fillable = ['preauth_register_id', 'temp_enhancement_id','name','file','is_draft', 'acs_status'];
    protected static function booted()
    {
        static::addGlobalScope(new ProcedureEnhancementScope);
    }
}
