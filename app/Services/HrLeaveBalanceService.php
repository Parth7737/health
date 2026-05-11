<?php

namespace App\Services;

use App\Models\HrLeaveRequest;
use App\Models\HrLeaveType;
use App\Models\HrStaffLeaveBalance;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HrLeaveBalanceService
{
    /**
     * Create or refresh annual balance rows (entitled days) for all hospitals, then sync used.
     */
    public function provisionAnnualBalancesForYear(int $year, ?int $onlyHospitalId = null): void
    {
        $hospitalIds = $onlyHospitalId
            ? collect([(int) $onlyHospitalId])
            : DB::table('hospitals')->pluck('id');

        foreach ($hospitalIds as $hospitalId) {
            $this->provisionHospitalYear((int) $hospitalId, $year);
        }
    }

    protected function provisionHospitalYear(int $hospitalId, int $year): void
    {
        $types = HrLeaveType::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('is_paid_time_off', true)
            ->where('annual_entitlement_days', '>', 0)
            ->get(['id', 'hospital_id', 'annual_entitlement_days']);

        if ($types->isEmpty()) {
            return;
        }

        $staffIds = Staff::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('status', 'Active')
            ->pluck('id');

        foreach ($staffIds as $staffId) {
            foreach ($types as $type) {
                HrStaffLeaveBalance::withoutGlobalScopes()->updateOrCreate(
                    [
                        'staff_id' => $staffId,
                        'hr_leave_type_id' => $type->id,
                        'year' => $year,
                    ],
                    [
                        'hospital_id' => $hospitalId,
                        'entitled_days' => (float) $type->annual_entitlement_days,
                    ]
                );
            }
        }

        $this->syncUsedDaysForHospitalYear($hospitalId, $year);
    }

    public function syncUsedDaysForHospitalYear(int $hospitalId, int $year): void
    {
        $balances = HrStaffLeaveBalance::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('year', $year)
            ->get();

        foreach ($balances as $balance) {
            $used = $this->computeApprovedUsedDaysForStaffTypeYear(
                $balance->staff_id,
                $hospitalId,
                $balance->hr_leave_type_id,
                $year
            );
            $balance->used_days = $used;
            $balance->save();
        }
    }

    public function syncUsedDaysForStaff(int $staffId, int $hospitalId): void
    {
        $yearsFromLeaves = HrLeaveRequest::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('staff_id', $staffId)
            ->where('status', 'Approved')
            ->get(['from_date', 'to_date']);

        $years = collect();
        foreach ($yearsFromLeaves as $row) {
            $years->push(Carbon::parse($row->from_date)->year);
            $years->push(Carbon::parse($row->to_date)->year);
        }

        $years = $years->filter()->unique()->values();

        $yearsFromBalances = HrStaffLeaveBalance::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('staff_id', $staffId)
            ->pluck('year');

        foreach ($yearsFromBalances as $y) {
            $years->push((int) $y);
        }

        foreach ($years->unique() as $y) {
            if ($y) {
                $this->syncUsedDaysForStaffYear($staffId, $hospitalId, (int) $y);
            }
        }
    }

    protected function syncUsedDaysForStaffYear(int $staffId, int $hospitalId, int $year): void
    {
        $balances = HrStaffLeaveBalance::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('staff_id', $staffId)
            ->where('year', $year)
            ->get();

        foreach ($balances as $balance) {
            $used = $this->computeApprovedUsedDaysForStaffTypeYear(
                $staffId,
                $hospitalId,
                $balance->hr_leave_type_id,
                $year
            );
            $balance->used_days = $used;
            $balance->save();
        }
    }

    public function computeApprovedUsedDaysForStaffTypeYear(int $staffId, int $hospitalId, int $leaveTypeId, int $year): float
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->startOfDay();

        $requests = HrLeaveRequest::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('staff_id', $staffId)
            ->where('status', 'Approved')
            ->where('hr_leave_type_id', $leaveTypeId)
            ->whereDate('from_date', '<=', $yearEnd->toDateString())
            ->whereDate('to_date', '>=', $yearStart->toDateString())
            ->orderBy('from_date')
            ->orderBy('id')
            ->get(['from_date', 'to_date']);

        $total = 0.0;
        foreach ($requests as $req) {
            $from = Carbon::parse($req->from_date)->startOfDay()->max($yearStart);
            $to = Carbon::parse($req->to_date)->startOfDay()->min($yearEnd);
            if ($to->lt($from)) {
                continue;
            }
            $total += (float) ($from->diffInDays($to) + 1);
        }

        return $total;
    }

    /**
     * Leave days in the payroll month that reduce net pay (after paid-time-off pools are applied in calendar-year order).
     */
    public function countUnpaidLeaveDaysInMonth(int $staffId, int $hospitalId, Carbon $monthStart, Carbon $monthEnd): float
    {
        $year = (int) $monthStart->year;
        if ((int) $monthEnd->year !== $year) {
            $monthEnd = $monthStart->copy()->endOfMonth();
        }

        $types = HrLeaveType::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->get()
            ->keyBy('id');

        $dayTypeMap = $this->buildApprovedLeaveDayTypeMap($staffId, $hospitalId, $year);
        ksort($dayTypeMap);

        $remainingPaidSlots = [];
        foreach ($types as $tid => $type) {
            if ($type->is_paid_time_off && (float) $type->annual_entitlement_days > 0) {
                $remainingPaidSlots[(int) $tid] = (int) floor((float) $type->annual_entitlement_days);
            }
        }

        $unpaidByDate = [];
        foreach ($dayTypeMap as $dateStr => $typeId) {
            $isUnpaid = true;
            if ($typeId === null || !$types->has($typeId)) {
                $isUnpaid = true;
            } else {
                $type = $types->get($typeId);
                if (!$type->is_paid_time_off || (float) $type->annual_entitlement_days <= 0) {
                    $isUnpaid = true;
                } else {
                    $tid = (int) $typeId;
                    if (($remainingPaidSlots[$tid] ?? 0) > 0) {
                        $remainingPaidSlots[$tid]--;
                        $isUnpaid = false;
                    } else {
                        $isUnpaid = true;
                    }
                }
            }
            $unpaidByDate[$dateStr] = $isUnpaid;
        }

        $total = 0.0;
        for ($d = $monthStart->copy()->startOfDay(); $d->lte($monthEnd); $d->addDay()) {
            $key = $d->format('Y-m-d');
            if (!empty($unpaidByDate[$key])) {
                $total += 1.0;
            }
        }

        return $total;
    }

    /**
     * @return array<string, int|null> date Y-m-d => hr_leave_type_id
     */
    protected function buildApprovedLeaveDayTypeMap(int $staffId, int $hospitalId, int $year): array
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->startOfDay();

        $requests = HrLeaveRequest::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('staff_id', $staffId)
            ->where('status', 'Approved')
            ->whereDate('from_date', '<=', $yearEnd->toDateString())
            ->whereDate('to_date', '>=', $yearStart->toDateString())
            ->orderBy('from_date')
            ->orderBy('id')
            ->get(['from_date', 'to_date', 'hr_leave_type_id']);

        $map = [];
        foreach ($requests as $req) {
            $from = Carbon::parse($req->from_date)->startOfDay()->max($yearStart);
            $to = Carbon::parse($req->to_date)->startOfDay()->min($yearEnd);
            if ($to->lt($from)) {
                continue;
            }
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $key = $d->format('Y-m-d');
                if (!array_key_exists($key, $map)) {
                    $map[$key] = $req->hr_leave_type_id ? (int) $req->hr_leave_type_id : null;
                }
            }
        }

        return $map;
    }
}
