<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Scopes\HospitalScope;

class Bed extends Model
{
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'bed_status_id' => 'integer',
    ];

    /**
     * Boot the model and apply any global scopes.
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    /**
     * Get the hospital that owns the bed.
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the room that has this bed.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the bed type.
     */
    public function bedType(): BelongsTo
    {
        return $this->belongsTo(BedType::class);
    }

    /**
     * Get the current status.
     */
    public function bedStatus(): BelongsTo
    {
        return $this->belongsTo(BedStatus::class);
    }

    /**
     * Get the bed allocations for this bed.
     */
    public function bedAllocations(): HasMany
    {
        return $this->hasMany(BedAllocation::class);
    }

    /**
     * Get the current allocation (if any).
     */
    public function currentAllocation()
    {
        return $this->bedAllocations()
            ->whereNull('discharge_date')
            ->latest()
            ->first();
    }

    /**
     * Get the ward through room.
     */
    public function getWardAttribute()
    {
        return $this->room?->ward;
    }

    /**
     * Get the floor through room.
     */
    public function getFloorAttribute()
    {
        return $this->room?->floor;
    }

    /**
     * Get full bed identifier like "Building-Floor-Ward-Room-BedNo".
     */
    public function getFullBedIdentifier()
    {
        $room = $this->room;
        $ward = $room?->ward;
        $floor = $room?->ward?->floor;

        return implode(' - ', array_filter([
            $ward?->ward_name,
            $floor?->name,
            $room?->room_number,
            'Bed ' . $this->bed_number,
        ]));
    }

    /**
     * Check if bed is available.
     */
    public function isAvailable(): bool
    {
        return $this->bed_status_id == BedStatus::AVAILABLE;
    }

    /**
     * Check if bed is occupied.
     */
    public function isOccupied(): bool
    {
        return $this->bed_status_id == BedStatus::OCCUPIED;
    }

    /**
     * Check if bed is under maintenance.
     */
    public function isUnderMaintenance(): bool
    {
        return $this->bed_status_id == BedStatus::MAINTENANCE;
    }
}
