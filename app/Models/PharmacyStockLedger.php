<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacyStockLedger extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'unit_purchase_price' => 'decimal:2',
        'unit_sale_price' => 'decimal:2',
        'entry_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function stockBatch(): BelongsTo
    {
        return $this->belongsTo(PharmacyStockBatch::class, 'stock_batch_id');
    }
}
