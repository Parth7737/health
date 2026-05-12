<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class HrLeaveType extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_paid_time_off' => 'boolean',
        'annual_entitlement_days' => 'float',
    ];

    /**
     * Boot the model and apply any global scopes.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function staffBalances()
    {
        return $this->hasMany(HrStaffLeaveBalance::class, 'hr_leave_type_id');
    }
}
