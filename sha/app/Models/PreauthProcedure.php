<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Remarkable;
use App\Scopes\ProcedureScope;

class PreauthProcedure extends Model
{
    use Remarkable;

    public function procedure() {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }
    public function preauth_register() {
        return $this->belongsTo(PreauthRegister::class, 'preauth_register_id');
    }
    public function implant() {
        return $this->belongsTo(Implant::class, 'implant_id');
    }
    public function speciality() {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new ProcedureScope);
    }
}
