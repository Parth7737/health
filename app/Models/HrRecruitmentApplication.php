<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrRecruitmentApplication extends Model
{
    protected $guarded = [];

    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function vacancy()
    {
        return $this->belongsTo(HrRecruitmentVacancy::class, 'hr_recruitment_vacancy_id');
    }
}
