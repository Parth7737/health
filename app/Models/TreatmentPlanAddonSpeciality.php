<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanAddonSpeciality extends Model
{
    protected $table = 'add_on_specialities';

    protected $fillable = ['add_on_id', 'speciality_id'];

    public function addOnProcedure(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanProcedure::class, 'add_on_id');
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }
}
