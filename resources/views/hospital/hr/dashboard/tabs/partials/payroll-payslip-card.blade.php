<div id="hrxPayslipPanel" class="payslip">
    <div class="payslip-header text-white">
        <!-- <div class="hrx-slip-brand">
            @if(!empty($hospitalLogoUrl))
                <img src="{{ $hospitalLogoUrl }}" alt="Hospital Logo" id="hrxSlipLogo" class="hrx-slip-logo">
            @else
                <span id="hrxSlipLogoFallback" class="hrx-slip-logo-fallback">{{ $hospitalInitials ?? 'H' }}</span>
            @endif
        </div> -->
        <h2 style="color: #fff !important;">Salary Slip</h2>
        <p style="font-size: 12px;opacity: .7;margin-top: 2px;color: #fff !important;">{{ $hospitalName }} · {{ $slipMonthLabel }}</p>
        <p style="margin-top:10px;font-size:14px;font-weight:600; margin-bottom:0;color: #fff !important;" id="hrxSlipName">{{ $slipName }}</p>
        <p style="font-size:12px;opacity:.7;color: #fff !important;"><span id="hrxSlipDesig">{{ $slipDesig }}</span> · <span id="hrxSlipDept">{{ $slipDepartment }}</span></p>
    </div>

    <div class="payslip-section">
        <h3>Earnings</h3>
        <div class="pay-row"><span>Basic Pay</span><span id="hrxSlipBasic">INR {{ number_format($slipBasic, 2) }}</span></div>
        @if(isset($allowanceItems) && $allowanceItems->count() > 0)
            @foreach($allowanceItems as $item)
                <div class="pay-row"><span>{{ $item->label }}</span><span>INR {{ number_format($item->amount, 2) }}</span></div>
            @endforeach
        @else
            <div class="pay-row"><span>Allowances</span><span id="hrxSlipAllowances">INR {{ number_format($slipAllowances, 2) }}</span></div>
        @endif
        <div class="pay-row total"><span>Gross Pay</span><span id="hrxSlipGross">INR {{ number_format($slipBasic + $slipAllowances, 2) }}</span></div>
    </div>

    <div class="payslip-section">
        <h3>Deductions</h3>
        @if(isset($deductionItems) && $deductionItems->count() > 0)
            @foreach($deductionItems as $item)
                @php
                    $meta = $item->meta ?? [];
                    $leaveUnpaid = isset($meta['leave_days_unpaid']) ? (float) $meta['leave_days_unpaid'] : null;
                    $leaveApprovedMonth = isset($meta['leave_days_total_approved']) ? (float) $meta['leave_days_total_approved'] : null;
                    $leaveLegacy = isset($meta['leave_days']) ? (float) $meta['leave_days'] : null;
                    $attUnits = isset($meta['deduction_units']) ? (float) $meta['deduction_units'] : null;
                    $attAbsentFull = isset($meta['absent_full_days']) ? (float) $meta['absent_full_days'] : null;
                    $attAbsentHalf = isset($meta['absent_half_day_units']) ? (float) $meta['absent_half_day_units'] : null;
                    $attPresentHalf = isset($meta['present_half_day_units']) ? (float) $meta['present_half_day_units'] : null;
                @endphp
                <div class="pay-row">
                    <span>
                        {{ $item->label }}
                        @if($item->label === 'Leave Deduction' && $leaveUnpaid !== null)
                            <br><small style="opacity:.75;font-weight:400;display:block;margin-top:2px;">Unpaid leave days (this month): {{ number_format($leaveUnpaid, 1) }}@if($leaveApprovedMonth !== null) · Approved leave days in month: {{ number_format($leaveApprovedMonth, 1) }}@endif</small>
                        @elseif($item->label === 'Leave Deduction' && $leaveLegacy !== null)
                            <br><small style="opacity:.75;font-weight:400;display:block;margin-top:2px;">Leave days: {{ number_format($leaveLegacy, 1) }}</small>
                        @elseif($item->label === 'Attendance Deduction' && $attUnits !== null)
                            <br><small style="opacity:.75;font-weight:400;display:block;margin-top:2px;">Units (month): {{ number_format($attUnits, 2) }} @if($attAbsentFull !== null) · Full absent: {{ number_format($attAbsentFull, 1) }}@endif @if($attAbsentHalf !== null && $attAbsentHalf > 0) · Half absent (units): {{ number_format($attAbsentHalf, 2) }}@endif @if($attPresentHalf !== null && $attPresentHalf > 0) · Half present (units): {{ number_format($attPresentHalf, 2) }} @endif </small>
                        @endif
                    </span>
                    <span>INR {{ number_format($item->amount, 2) }}</span>
                </div>
            @endforeach
        @endif
         <div class="pay-row total"><span>Total Deductions</span><span id="hrxSlipGross">INR {{ number_format($slipDeductions, 2) }}</span></div>
    </div>

    <div class="payslip-section" style="border:none">
        <div class="pay-row" style="font-size:16px;color:#4a148c"><span style="font-weight:700">Net Pay</span><span id="hrxSlipNet" style="font-weight:700">INR {{ number_format($slipNet, 2) }}</span></div>
    </div>
</div>

<div class="payslip-actions">
    <button type="button" class="hrx-pay-btn success hrx-slip-print"><i class="fas fa-print"></i> Print</button>
    <button type="button" class="hrx-pay-btn outline hrx-slip-email"><i class="fas fa-envelope"></i> Email</button>
</div>
