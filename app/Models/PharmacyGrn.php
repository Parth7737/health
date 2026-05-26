<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacyGrn extends Model
{
    protected $table = 'pharmacy_grns';

    protected $guarded = [];

    protected $casts = [
        'invoice_date' => 'date',
        'total_value'  => 'decimal:2',
        'received_at'  => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PharmacyGrnItem::class, 'grn_id');
    }

    public function purchaseBill(): BelongsTo
    {
        return $this->belongsTo(PharmacyPurchaseBill::class, 'purchase_bill_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(PharmacySupplier::class, 'supplier_id');
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
