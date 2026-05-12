<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanStratification extends Model
{
    protected $table = 'stratifications';

    protected $fillable = [
        'stratification_category_id',
        'procedure_id',
        'name',
        'code',
        'code2',
        'rule',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanStratificationCategory::class, 'stratification_category_id');
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'procedure_id');
    }
}
