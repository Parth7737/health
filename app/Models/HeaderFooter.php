<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class HeaderFooter extends Model
{
    protected $guarded = [];

    /**
     * Boot the model and apply any global scopes.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }
    public const TYPES = [
        'opd_prescription' => 'OPD Prescription',
        'opd_bill' => 'OPD Bill',
        'ipd_prescription' => 'IPD Prescription',
        'ipd_bill' => 'IPD Bill',
        'pharmacy_bill' => 'Pharmacy Bill',
        'payslip' => 'Payslip',
        'birth_record' => 'Birth Record',
        'death_record' => 'Death Record',
        'pathology' => 'Pathology',
        'radiology' => 'Radiology',
        'operation_theatre' => 'Operation Theatre',
        'blood_bank' => 'Blood Bank',
        'ambulance' => 'Ambulance',
        'discharge_summary' => 'Discharge Summary',
    ];

    public static function getTypes(): array
    {
        return self::TYPES;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucwords(str_replace('_', ' ', $this->type));
    }
}
