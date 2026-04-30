<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargeMasterTpaRate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'rate' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function chargeMaster(): BelongsTo
    {
        return $this->belongsTo(ChargeMaster::class);
    }

    public function tpa(): BelongsTo
    {
        return $this->belongsTo(Tpa::class);
    }
}