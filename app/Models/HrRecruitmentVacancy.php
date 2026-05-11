<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrRecruitmentVacancy extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_date' => 'date',
        'open_from' => 'date',
        'open_till' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(HrDesignation::class, 'hr_designation_id');
    }

    public function applications()
    {
        return $this->hasMany(HrRecruitmentApplication::class, 'hr_recruitment_vacancy_id');
    }
}
