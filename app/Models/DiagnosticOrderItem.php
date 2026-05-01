<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DiagnosticOrderItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'standard_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'sample_collected_at' => 'datetime',
        'billed_at' => 'datetime',
        'paid_at' => 'datetime',
        'reported_at' => 'datetime',
        'critical_doctor_called_at' => 'datetime',
        'critical_acknowledged_at' => 'datetime',
        'report_is_draft' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(DiagnosticOrder::class, 'diagnostic_order_id');
    }

    public function testable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(DiagnosticOrderItemParameter::class, 'diagnostic_order_item_id');
    }

    public function patientCharge(): MorphOne
    {
        return $this->morphOne(PatientCharge::class, 'source');
    }

    public function pathologyStatus(): BelongsTo
    {
        return $this->belongsTo(PathologyStatus::class, 'pathology_status_id');
    }

    public function sampleCollectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sample_collected_by');
    }

    public function reportRadiologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'report_radiologist_id');
    }

    public function pacsStudies(): HasMany
    {
        return $this->hasMany(RadiologyPacsStudy::class, 'diagnostic_order_item_id');
    }
}
