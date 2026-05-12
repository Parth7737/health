<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchemeType extends Model
{
    protected $fillable = ['name'];

    public function procedures(): HasMany
    {
        return $this->hasMany(TreatmentPlanProcedure::class, 'scheme_type_id');
    }

    public function investigations(): HasMany
    {
        return $this->hasMany(TreatmentPlanInvestigation::class, 'scheme_type_id');
    }
}
