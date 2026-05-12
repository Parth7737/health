<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanNonAddonLink extends Model
{
    protected $table = 'non_add_on_procedures';

    protected $fillable = ['procedure_id', 'non_add_on_id'];

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'procedure_id');
    }

    public function nonAddOnProcedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'non_add_on_id');
    }
}
