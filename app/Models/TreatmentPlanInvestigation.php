<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanInvestigation extends Model
{
    protected $table = 'investigations';

    protected $fillable = [
        'name',
        'code',
        'scheme_type_id',
        'type',
        'is_required',
    ];

    protected $casts = [
        'scheme_type_id' => 'integer',
    ];

    public function schemeType(): BelongsTo
    {
        return $this->belongsTo(SchemeType::class, 'scheme_type_id');
    }
}
