<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Scopes\HospitalScope;

class Building extends Model
{
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model and apply any global scopes.
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    /**
     * Get the hospital that owns the building.
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the floors in this building.
     */
    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }
}
