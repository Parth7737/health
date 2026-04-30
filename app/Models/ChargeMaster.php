<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChargeMaster extends Model
{
    protected $guarded = [];

    protected $casts = [
        'standard_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function patientCharges(): HasMany
    {
        return $this->hasMany(PatientCharge::class);
    }

    public function tpaRates(): HasMany
    {
        return $this->hasMany(ChargeMasterTpaRate::class);
    }
}