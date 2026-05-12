<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentPlanPackage extends Model
{
    protected $table = 'packages';

    protected $fillable = ['name', 'code'];

    public function procedures(): HasMany
    {
        return $this->hasMany(TreatmentPlanProcedure::class, 'package_id');
    }
}
