<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class MedicineDosage extends Model
{
    protected $guarded = [];

    /**
     * Boot the model and apply any global scopes.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function unit()
    {
        return $this->belongsTo(MedicineUnit::class, 'medicine_unit_id');
    }

    public function getFormattedDosageAttribute()
    {
        return $this->dosage . ($this->postfix ? ' ' . $this->postfix : '');
    }
}
