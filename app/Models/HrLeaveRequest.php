<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use App\Services\HrLeaveAttendanceSyncService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class HrLeaveRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'total_days' => 'float',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);

        static::deleting(function (HrLeaveRequest $leave) {
            if (Schema::hasTable('hr_attendance_records')) {
                app(HrLeaveAttendanceSyncService::class)->removeLinkedLeaveAttendance($leave);
            }
        });
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(HrLeaveType::class, 'hr_leave_type_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(HrAttendanceRecord::class, 'hr_leave_request_id');
    }
}
