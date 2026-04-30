<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PharmacyPurchaseItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'mfg_date' => 'date',
        'expiry_date' => 'date',
        'unit_purchase_price' => 'decimal:2',
        'unit_sale_price' => 'decimal:2',
        'unit_mrp' => 'decimal:2',
        'quantity_purchased' => 'decimal:2',
        'quantity_free' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'total_quantity' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_subtotal' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(PharmacyPurchaseBill::class, 'purchase_bill_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function stockBatch(): HasOne
    {
        return $this->hasOne(PharmacyStockBatch::class, 'purchase_item_id');
    }
}
