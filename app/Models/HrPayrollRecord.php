<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrPayrollRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payroll_month' => 'date',
        'basic_pay' => 'float',
        'allowances' => 'float',
        'deductions' => 'float',
        'net_pay' => 'float',
        'paid_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function items()
    {
        return $this->hasMany(HrPayrollRecordItem::class, 'hr_payroll_record_id');
    }
}
