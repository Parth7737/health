<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\HospitalScope;

class Staff extends Model
{
    
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'work_timings' => 'array',
    ];

    /**
     * Boot the model and apply any global scopes.
     */
    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    /**
     * Get the hospital this staff member belongs to
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the user account associated with this staff member
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role assigned to this staff member
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the HR specialist details
     */
    public function specialist()
    {
        return $this->belongsTo(HrSpecialist::class, 'hr_specialist_id');
    }

    /**
     * Get the designation details
     */
    public function designation()
    {
        return $this->belongsTo(HrDesignation::class, 'hr_designation_id');
    }

    /**
     * Get the department details
     */
    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }

    /**
     * Get the head of department
     */
    public function hod()
    {
        return $this->belongsTo(Staff::class, 'hod_id');
    }

    /**
     * Get the line manager
     */
    public function lineManager()
    {
        return $this->belongsTo(Staff::class, 'linemanager_id');
    }

    /**
     * Get staff members under this HOD
     */
    public function hodStaff()
    {
        return $this->hasMany(Staff::class, 'hod_id');
    }

    /**
     * Get staff members under this line manager
     */
    public function subordinates()
    {
        return $this->hasMany(Staff::class, 'linemanager_id');
    }

    /**
     * Get the full name of the staff member
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    function scopeDoctor($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', 'Doctor');
        });
    }
    function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
