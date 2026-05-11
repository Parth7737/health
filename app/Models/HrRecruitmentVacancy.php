<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrRecruitmentVacancy extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_date' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }
}
