<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadiologyPacsStudy extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope());
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(DiagnosticOrderItem::class, 'diagnostic_order_item_id');
    }
}
