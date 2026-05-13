<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpdAllocationTreatmentPlanProcedure extends Model
{
    protected $table = 'ipd_allocation_treatment_plan_procedures';

    protected $guarded = [];

    protected $casts = [
        'amount_value' => 'decimal:2',
        'u100_amount' => 'decimal:2',
        'is_unverified_price' => 'boolean',
        'billed_at' => 'datetime',
    ];

    public function bedAllocation(): BelongsTo
    {
        return $this->belongsTo(BedAllocation::class, 'bed_allocation_id');
    }
}
