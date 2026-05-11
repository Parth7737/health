<?php

namespace App\Services;

use App\Models\HrLeaveRequest;
use App\Models\HrPayrollComponent;
use App\Models\HrPayrollRecord;
use App\Models\HrPayrollRecordItem;
use App\Models\HrPayrollSetting;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollProcessingService
{
    public function processMonth(int $hospitalId, Carbon $month): array
    {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $setting = HrPayrollSetting::query()->first();
        $components = HrPayrollComponent::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $staffRows = Staff::query()
            ->where('hospital_id', $hospitalId)
            ->where('status', 'Active')
            ->select('id', 'basic_pay')
            ->get();

        $created = 0;
        $updated = 0;

        DB::transaction(function () use (
            $staffRows,
            $components,
            $monthStart,
            $monthEnd,
            $hospitalId,
            $setting,
            &$created,
            &$updated
        ) {
            foreach ($staffRows as $staff) {
                $basicPay = (float) ($staff->basic_pay ?? 0);

                $allowanceItems = [];
                $deductionItems = [];
                $allowancesTotal = 0.0;
                $componentDeductionsTotal = 0.0;

                foreach ($components as $component) {
                    $amount = $this->resolveComponentAmount($basicPay, $component->value_type, (float) $component->value);
                    if ($amount <= 0) {
                        continue;
                    }

                    $itemPayload = [
                        'label' => $component->name,
                        'amount' => round($amount, 2),
                        'meta' => [
                            'value_type' => $component->value_type,
                            'configured_value' => (float) $component->value,
                        ],
                    ];

                    if ($component->component_type === 'Allowance') {
                        $allowancesTotal += $amount;
                        $allowanceItems[] = $itemPayload;
                    } else {
                        $componentDeductionsTotal += $amount;
                        $deductionItems[] = $itemPayload;
                    }
                }

                $grossPay = $basicPay + $allowancesTotal;

                $leaveDays = $this->resolveApprovedLeaveDays($staff->id, $monthStart, $monthEnd);
                $leaveDeduction = $this->resolveLeaveDeduction(
                    $basicPay,
                    $leaveDays,
                    (int) ($setting->standard_working_days ?? 30),
                    (float) ($setting->leave_deduction_per_day ?? 0)
                );

                if ($leaveDeduction > 0) {
                    $deductionItems[] = [
                        'label' => 'Leave Deduction',
                        'amount' => round($leaveDeduction, 2),
                        'meta' => [
                            'leave_days' => $leaveDays,
                        ],
                    ];
                }

                $deductionsTotal = $componentDeductionsTotal + $leaveDeduction;
                $netPay = max(0, $grossPay - $deductionsTotal);

                $record = HrPayrollRecord::query()->updateOrCreate(
                    [
                        'hospital_id' => $hospitalId,
                        'staff_id' => $staff->id,
                        'payroll_month' => $monthStart->toDateString(),
                    ],
                    [
                        'basic_pay' => round($basicPay, 2),
                        'allowances' => round($allowancesTotal, 2),
                        'deductions' => round($deductionsTotal, 2),
                        'net_pay' => round($netPay, 2),
                    ]
                );

                if ((string) $record->status !== 'Paid') {
                    $record->status = 'Generated';
                    $record->paid_at = null;
                }

                if (empty($record->slip_no)) {
                    $record->slip_no = $this->generateSlipNo($hospitalId, $monthStart);
                }

                $record->save();

                if ($record->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

                HrPayrollRecordItem::query()->where('hr_payroll_record_id', $record->id)->delete();

                foreach ($allowanceItems as $item) {
                    HrPayrollRecordItem::query()->create([
                        'hospital_id' => $hospitalId,
                        'hr_payroll_record_id' => $record->id,
                        'label' => $item['label'],
                        'item_type' => 'Allowance',
                        'amount' => $item['amount'],
                        'meta' => $item['meta'],
                    ]);
                }

                foreach ($deductionItems as $item) {
                    HrPayrollRecordItem::query()->create([
                        'hospital_id' => $hospitalId,
                        'hr_payroll_record_id' => $record->id,
                        'label' => $item['label'],
                        'item_type' => 'Deduction',
                        'amount' => $item['amount'],
                        'meta' => $item['meta'],
                    ]);
                }
            }
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'total' => $created + $updated,
            'month_label' => $monthStart->format('F Y'),
        ];
    }

    private function resolveComponentAmount(float $basicPay, string $valueType, float $value): float
    {
        if ($value <= 0) {
            return 0;
        }

        if ($valueType === 'Percentage') {
            return ($basicPay * $value) / 100;
        }

        return $value;
    }

    private function resolveApprovedLeaveDays(int $staffId, Carbon $monthStart, Carbon $monthEnd): float
    {
        $leaveRequests = HrLeaveRequest::query()
            ->where('staff_id', $staffId)
            ->where('status', 'Approved')
            ->whereDate('from_date', '<=', $monthEnd->toDateString())
            ->whereDate('to_date', '>=', $monthStart->toDateString())
            ->get(['from_date', 'to_date']);

        $total = 0.0;
        foreach ($leaveRequests as $request) {
            $start = Carbon::parse($request->from_date)->max($monthStart);
            $end = Carbon::parse($request->to_date)->min($monthEnd);

            if ($end->lt($start)) {
                continue;
            }

            $total += (float) ($start->diffInDays($end) + 1);
        }

        return $total;
    }

    private function resolveLeaveDeduction(float $basicPay, float $leaveDays, int $workingDays, float $configuredPerDay): float
    {
        if ($leaveDays <= 0 || $basicPay <= 0) {
            return 0;
        }

        if ($configuredPerDay > 0) {
            return $leaveDays * $configuredPerDay;
        }

        $safeWorkingDays = max($workingDays, 1);
        $perDay = $basicPay / $safeWorkingDays;

        return $leaveDays * $perDay;
    }

    private function generateSlipNo(int $hospitalId, Carbon $monthStart): string
    {
        $prefix = $monthStart->format('Y-m');

        $existing = HrPayrollRecord::query()
            ->where('hospital_id', $hospitalId)
            ->whereYear('payroll_month', $monthStart->year)
            ->whereMonth('payroll_month', $monthStart->month)
            ->whereNotNull('slip_no')
            ->pluck('slip_no');

        $maxSequence = 0;
        foreach ($existing as $slipNo) {
            if (preg_match('/^\d{4}-\d{2}-(\d{4})$/', (string) $slipNo, $matches)) {
                $maxSequence = max($maxSequence, (int) $matches[1]);
            }
        }

        return $prefix . '-' . str_pad((string) ($maxSequence + 1), 4, '0', STR_PAD_LEFT);
    }
}
