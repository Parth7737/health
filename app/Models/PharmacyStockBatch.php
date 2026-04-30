<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacyStockBatch extends Model
{
    protected $guarded = [];

    protected $casts = [
        'mfg_date' => 'date',
        'expiry_date' => 'date',
        'unit_purchase_price' => 'decimal:2',
        'unit_sale_price' => 'decimal:2',
        'unit_mrp' => 'decimal:2',
        'available_qty' => 'decimal:2',
        'reserved_qty' => 'decimal:2',
        'damaged_qty' => 'decimal:2',
        'expired_qty' => 'decimal:2',
        'received_at' => 'datetime',
        'last_expiry_processed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PharmacyPurchaseItem::class, 'purchase_item_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(PharmacyStockLedger::class, 'stock_batch_id');
    }
}
