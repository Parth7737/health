<?php

namespace App\Services;

use App\Models\HrAttendanceRecord;
use App\Models\HrLeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HrLeaveAttendanceSyncService
{
    /**
     * Remove attendance rows linked to this leave (by hr_leave_request_id, or legacy notes match).
     */
    public function removeLinkedLeaveAttendance(HrLeaveRequest $leave): void
    {
        if (!Schema::hasTable('hr_attendance_records') || !$leave->staff_id || !$leave->hospital_id) {
            return;
        }

        $base = HrAttendanceRecord::withoutGlobalScopes()
            ->where('hospital_id', (int) $leave->hospital_id)
            ->where('staff_id', (int) $leave->staff_id);

        if (Schema::hasColumn('hr_attendance_records', 'hr_leave_request_id')) {
            $base->where('hr_leave_request_id', (int) $leave->id)->delete();

            return;
        }

        $from = Carbon::parse($leave->from_date)->toDateString();
        $to = Carbon::parse($leave->to_date)->toDateString();
        $needle = 'Leave approved: ' . $leave->request_no;

        $base->whereBetween('attendance_date', [$from, $to])
            ->where('status', 'Leave')
            ->where('notes', 'like', '%' . $needle . '%')
            ->delete();
    }

    /**
     * Upsert attendance as Leave for each day of an approved request; links hr_leave_request_id when present.
     */
    public function syncApprovedLeaveToAttendance(HrLeaveRequest $leave): void
    {
        if (!Schema::hasTable('hr_attendance_records')) {
            return;
        }

        if ((string) $leave->status !== 'Approved' || !$leave->staff_id) {
            return;
        }

        $from = Carbon::parse($leave->from_date)->startOfDay();
        $to = Carbon::parse($leave->to_date)->startOfDay();
        $leave->loadMissing('leaveType');
        $typeLabel = $leave->leaveType?->name ?? 'Leave';

        $singleDayHalf = $from->equalTo($to)
            && (float) $leave->total_days > 0
            && (float) $leave->total_days < 1;

        $reasonNote = $leave->reason ? Str::limit((string) $leave->reason, 120) : '';

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $dayType = 'Full Day';
            if ($singleDayHalf && $d->equalTo($from)) {
                $dayType = 'Half Day';
            }

            $notes = 'Leave approved: ' . $leave->request_no . ' · ' . $typeLabel;
            if ($reasonNote !== '') {
                $notes .= ' — ' . $reasonNote;
            }

            $payload = [
                'status' => 'Leave',
                'day_type' => $dayType,
                'late_count' => 0,
                'is_miss_punch' => false,
                'is_overtime' => false,
                'overtime_hours' => 0,
                'combined_status' => null,
                'notes' => $notes,
            ];

            if (Schema::hasColumn('hr_attendance_records', 'hr_leave_request_id')) {
                $payload['hr_leave_request_id'] = $leave->id;
            }

            HrAttendanceRecord::withoutGlobalScopes()->updateOrCreate(
                [
                    'hospital_id' => (int) $leave->hospital_id,
                    'staff_id' => (int) $leave->staff_id,
                    'attendance_date' => $d->toDateString(),
                ],
                $payload
            );
        }
    }
}
