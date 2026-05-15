<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyHistory extends Model
{
    protected $guarded = [];

    public function diabetes(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'diabetes_id');
    }

    public function hypertension(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'hypertension_id');
    }

    public function heartdisease(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'heartdisease_id');
    }

    public function stroke(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'stroke_id');
    }

    public function cancer(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'cancer_id');
    }

    public function tuberculosis(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'tuberculosis_id');
    }

    public function asthma(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'asthma_id');
    }
}
