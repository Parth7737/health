<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacySaleItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_mrp' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_subtotal' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_substituted' => 'boolean',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(PharmacySaleBill::class, 'sale_bill_id');
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
