<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class HrDepartmentUnit extends Model
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

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function unitIncharge()
    {
        return $this->belongsTo(Staff::class, 'unit_incharge_id');
    }
}
