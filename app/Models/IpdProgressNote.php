<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpdProgressNote extends Model
{
    protected $guarded = [];

    protected $casts = [
        'noted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(BedAllocation::class, 'bed_allocation_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}