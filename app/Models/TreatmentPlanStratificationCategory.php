<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentPlanStratificationCategory extends Model
{
    protected $table = 'stratification_categories';

    protected $fillable = ['name'];

    public function stratifications(): HasMany
    {
        return $this->hasMany(TreatmentPlanStratification::class, 'stratification_category_id');
    }
}
