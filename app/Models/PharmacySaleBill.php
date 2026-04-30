<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PharmacySaleBill extends Model
{
    protected $guarded = [];

    protected $casts = [
        'bill_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'is_from_prescription' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PharmacySaleItem::class, 'sale_bill_id');
    }

    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    public function source(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'source_type', 'source_id');
    }

    public function opdPrescription(): BelongsTo
    {
        return $this->belongsTo(OpdPrescription::class, 'opd_prescription_id');
    }

    public function ipdPrescription(): BelongsTo
    {
        return $this->belongsTo(IpdPrescription::class, 'ipd_prescription_id');
    }
}
