<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Scopes\HospitalScope;

class Ward extends Model
{
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model and apply any global scopes.
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    /**
     * Get the hospital that owns the ward.
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the floor that has this ward.
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Get the building (via floor).
     */
    public function building()
    {
        return $this->hasOneThrough(Building::class, Floor::class);
    }

    /**
     * Get the rooms in this ward.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get all beds in the ward.
     */
    public function beds(): HasMany
    {
        return $this->hasManyThrough(Bed::class, Room::class);
    }

    /**
     * Get the available beds count.
     */
    public function getAvailableBedsCount()
    {
        return $this->beds()
            ->whereHas('bedStatus', function ($q) {
                $q->where('slug', 'available');
            })
            ->count();
    }
}

