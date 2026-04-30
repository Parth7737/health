<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPaymentAllocation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PatientPayment::class, 'patient_payment_id');
    }

    public function charge(): BelongsTo
    {
        return $this->belongsTo(PatientCharge::class, 'patient_charge_id');
    }
}
