<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use App\Scopes\HospitalScope;

class Room extends Model
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
     * Get the hospital that owns the room.
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the ward that has this room.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the floor (via ward).
     */
    public function floor(): HasOneThrough
    {
        return $this->hasOneThrough(
            Floor::class,
            Ward::class,
            'id',
            'id',
            'ward_id',
            'floor_id'
        );
    }

    /**
     * Get the bed in this room.
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get available beds in this room.
     */
    public function getAvailableBedsCount()
    {
        return $this->beds()
            ->where('bed_status_id', BedStatus::AVAILABLE)
            ->count();
    }

    /**
     * Get occupied beds in this room.
     */
    public function getOccupiedBedsCount()
    {
        return $this->beds()
            ->where('bed_status_id', BedStatus::OCCUPIED)
            ->count();
    }
}

