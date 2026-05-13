<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /** SHA-style: many procedures per implant (procedure_id[]). */
    public function procedures(): BelongsToMany
    {
        return $this->belongsToMany(
            TreatmentPlanProcedure::class,
            'implant_procedures',
            'implant_id',
            'procedure_id'
        )->withTimestamps();
    }
}
