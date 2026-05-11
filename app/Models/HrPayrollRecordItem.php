<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrPayrollRecordItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'meta' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function payrollRecord()
    {
        return $this->belongsTo(HrPayrollRecord::class, 'hr_payroll_record_id');
    }
}
