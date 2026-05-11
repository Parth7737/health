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
                <div class="pay-row"><span>{{ $item->label }}</span><span>INR {{ number_format($item->amount, 2) }}</span></div>
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
