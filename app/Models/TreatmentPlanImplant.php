<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanImplant extends Model
{
    protected $table = 'implants';

    protected $fillable = [
        'procedure_id',
        'speciality_id',
        'name',
        'code',
        'no_of_multiplier',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'no_of_multiplier' => 'integer',
        'speciality_id' => 'integer',
    ];

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'procedure_id');
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }
}
