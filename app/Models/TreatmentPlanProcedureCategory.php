<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentPlanProcedureCategory extends Model
{
    protected $table = 'procedure_categories';

    protected $fillable = ['name', 'code'];

    public function procedures(): HasMany
    {
        return $this->hasMany(TreatmentPlanProcedure::class, 'procedure_category_id');
    }
}
