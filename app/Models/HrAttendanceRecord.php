<?php

namespace App\Models;

use App\Scopes\HospitalScope;
use Illuminate\Database\Eloquent\Model;

class HrAttendanceRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'attendance_date' => 'date',
        'in_time' => 'datetime:H:i',
        'out_time' => 'datetime:H:i',
        'late_count' => 'integer',
        'is_miss_punch' => 'boolean',
        'is_overtime' => 'boolean',
        'overtime_hours' => 'float',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new HospitalScope);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function getCombinedStatusLabelAttribute(): string
    {
        if (!empty($this->combined_status)) {
            return (string) $this->combined_status;
        }

        if ($this->status === 'Absent') {
            return 'A';
        }

        if ($this->status === 'Leave') {
            return 'OnLeave';
        }

        if ($this->status === 'Holiday') {
            return 'H';
        }

        $parts = ['P'];

        if (($this->late_count ?? 0) > 0) {
            $parts[] = 'L';
        }

        if ($this->day_type === 'Half Day') {
            $parts[] = 'HD';
        } elseif ($this->day_type === 'Full Day') {
            $parts[] = 'FD';
        }

        if ($this->is_miss_punch) {
            $parts[] = 'MP';
        }

        if ($this->is_overtime) {
            $parts[] = 'OT';
        }

        return implode('/', $parts);
    }
}
