<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacyGrnItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'mfg_date'            => 'date',
        'expiry_date'         => 'date',
        'quantity_ordered'    => 'decimal:2',
        'quantity_received'   => 'decimal:2',
        'quantity_free'       => 'decimal:2',
        'quantity_rejected'   => 'decimal:2',
        'quantity_accepted'   => 'decimal:2',
        'unit_purchase_price' => 'decimal:2',
        'unit_sale_price'     => 'decimal:2',
        'unit_mrp'            => 'decimal:2',
        'tax_percent'         => 'decimal:2',
        'tax_amount'          => 'decimal:2',
        'line_total'          => 'decimal:2',
    ];

    public function grn(): BelongsTo
    {
        return $this->belongsTo(PharmacyGrn::class, 'grn_id');
    }

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PharmacyPurchaseItem::class, 'purchase_item_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
