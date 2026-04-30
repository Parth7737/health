<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BedType extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'base_charge' => 'decimal:2',
        'is_active' => 'boolean',
        'charge_master_id' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function chargeMaster(): BelongsTo
    {
        return $this->belongsTo(ChargeMaster::class);
    }

    /**
     * Backward compatibility for legacy code that still reads $bedType->name.
     */
    public function getNameAttribute(): ?string
    {
        return $this->attributes['name'] ?? $this->attributes['type_name'] ?? null;
    }

    /**
     * Get the beds of this type.
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get available beds of this type.
     */
    public function getAvailableBeds()
    {
        return $this->beds()
            ->where('bed_status_id', BedStatus::AVAILABLE)
            ->count();
    }
}
