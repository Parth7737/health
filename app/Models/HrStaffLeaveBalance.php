<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrStaffLeaveBalance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
        'entitled_days' => 'float',
        'used_days' => 'float',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(HrLeaveType::class, 'hr_leave_type_id');
    }

    public function getAvailableDaysAttribute(): float
    {
        return max(0, (float) $this->entitled_days - (float) $this->used_days);
    }
}
