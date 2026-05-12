<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanFollowupLink extends Model
{
    protected $table = 'followup_procedures';

    protected $fillable = ['procedure_id', 'follow_up_id'];

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'procedure_id');
    }

    public function followUpProcedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'follow_up_id');
    }
}
