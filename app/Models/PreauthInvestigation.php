<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreauthInvestigation extends Model
{
    protected $guarded = [];

    public function investigation(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanInvestigation::class, 'investigation_id');
    }
}
