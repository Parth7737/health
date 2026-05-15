<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreauthProcedure extends Model
{
    protected $guarded = [];

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'procedure_id');
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }

    public function implant(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanImplant::class, 'implant_id');
    }

    public function preauth_register(): BelongsTo
    {
        return $this->belongsTo(PreauthRegister::class, 'preauth_register_id');
    }
}
