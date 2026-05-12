<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanAddonLink extends Model
{
    protected $table = 'add_on_procedures';

    protected $fillable = ['procedure_id', 'add_on_id'];

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'procedure_id');
    }

    public function addOnProcedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'add_on_id');
    }
}
