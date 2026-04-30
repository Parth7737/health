<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DiagnosticOrderItemParameter extends Model
{
    protected $guarded = [];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(DiagnosticOrderItem::class, 'diagnostic_order_item_id');
    }

    public function parameterable(): MorphTo
    {
        return $this->morphTo();
    }
}
