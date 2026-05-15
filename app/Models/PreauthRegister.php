<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreauthRegister extends Model
{
    public const STATUS_REGISTER = 0;

    public const STATUS_PREAUTH_PENDING = 1;

    public const STATUS_PREAUTH_APPROVED = 2;

    public const STATUS_PREAUTH_REJECTED = 3;

    public const STATUS_PREAUTH_QUERIED = 4;

    public const STATUS_CLAIM_SUBMITTED = 5;

    public const STATUS_CLAIM_PENDING = 6;

    public const STATUS_CLAIM_APPROVED = 7;

    public const STATUS_CLAIM_REJECTED = 8;

    public const STATUS_CLAIM_QUERIED = 9;

    protected $guarded = [];

    protected $casts = [
        'preauth_submission_date' => 'datetime',
        'preauth_approved_date' => 'datetime',
        'claim_submited_date' => 'datetime',
        'claim_approved_date' => 'datetime',
        'claim_aco_approved_date' => 'datetime',
        'claim_paid_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function bedAllocation(): BelongsTo
    {
        return $this->belongsTo(BedAllocation::class, 'bed_allocation_id');
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(PreauthProcedure::class, 'preauth_register_id');
    }

    public function investigations(): HasMany
    {
        return $this->hasMany(PreauthInvestigation::class, 'preauth_register_id');
    }

    public function enhancement_docs(): HasMany
    {
        return $this->hasMany(PreauthEnhancementDoc::class, 'preauth_register_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_REGISTER => 'Draft',
            self::STATUS_PREAUTH_PENDING => 'Preauth pending',
            self::STATUS_PREAUTH_APPROVED => 'Preauth approved',
            self::STATUS_PREAUTH_REJECTED => 'Preauth rejected',
            self::STATUS_PREAUTH_QUERIED => 'Preauth queried',
            default => 'Unknown',
        };
    }
}
