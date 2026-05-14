@php
    $addressLine = trim(implode(', ', array_filter([
        $hospital?->address,
        $hospital?->city,
        $hospital?->pincode,
    ])));
    $phone = implode(' | ', array_filter([$hospital?->phone, $hospital?->email]));

    $logoData = null;
    if ($hospital?->image) {
        $logoPath = public_path('storage/' . ltrim($hospital->image, '/'));
        if (is_file($logoPath)) {
            $mime = @mime_content_type($logoPath) ?: 'image/png';
            $logoData = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
        }
    }

    $headerData = null;
    if (!empty($printTemplate?->header_image)) {
        $hdrPath = public_path('storage/' . ltrim($printTemplate->header_image, '/'));
        if (is_file($hdrPath)) {
            $mime = @mime_content_type($hdrPath) ?: 'image/png';
            $headerData = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($hdrPath));
        }
    }

    $grossPay = (float) $slipBasic + (float) $slipAllowances;
    $printedAt = now()->format('d-m-Y H:i');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payroll Payslip - {{ $slipEmployeeId }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 11px; color: #1a2a3a; margin: 0; padding: 0; }
        .hdr-banner img { display: block; width: 100%; max-height: 100px; object-fit: cover; }
        .hosp-bar { width: 100%; border-bottom: 2px solid #1565c0; background: #f4f8ff; padding: 10px 14px; }
        .hosp-bar-inner { width: 100%; border-collapse: collapse; }
        .hosp-bar-inner td { vertical-align: middle; padding: 4px 6px; }
        .hosp-logo { width: 48px; height: 48px; border-radius: 6px; }
        .hosp-name { font-size: 15px; font-weight: bold; color: #0f4c81; }
        .hosp-sub { font-size: 9px; color: #5d7285; line-height: 1.45; margin-top: 2px; }
        .hosp-right { font-size: 9px; color: #5d7285; text-align: right; line-height: 1.5; }
        .hosp-right strong { font-size: 10px; color: #1a2a3a; }
        .report-band { width: 100%; background: #1565c0; color: #fff; padding: 8px 14px; }
        .report-band-inner { width: 100%; border-collapse: collapse; }
        .report-band-inner td { vertical-align: middle; padding: 2px 0; }
        .report-band h1 { font-size: 14px; margin: 0; font-weight: bold; letter-spacing: 0.3px; }
        .report-band .meta { font-size: 9px; opacity: 0.95; text-align: right; line-height: 1.5; }

        .section { padding: 10px 14px 6px; }
        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.6px; color: #1565c0; border-bottom: 1px solid #dce8f8; padding-bottom: 3px; margin-bottom: 6px; }
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .info-grid td { width: 25%; vertical-align: top; padding: 6px; border: 1px solid #e4ebf2; background: #fbfdff; }
        .info-label { font-size: 8px; text-transform: uppercase; color: #7a93aa; display: block; margin-bottom: 2px; }
        .info-value { font-size: 10px; font-weight: bold; color: #1a2a3a; }

        .grid-two { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-top: 6px; }
        .grid-two td { width: 50%; vertical-align: top; }
        .card { border: 1px solid #d8e2ee; border-radius: 4px; background: #fbfdff; }
        .card-head { padding: 8px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #0f4c81; border-bottom: 1px solid #e4ebf2; }
        .rows { padding: 6px 10px; }
        .row { width: 100%; border-collapse: collapse; }
        .row td { padding: 5px 0; font-size: 10px; }
        .row td:last-child { text-align: right; font-weight: 600; }
        .line { border-top: 1px dashed #c8d6e5; margin: 5px 0; }
        .totals { margin: 0 14px; border: 1px solid #cfdbea; border-radius: 4px; background: #f7fbff; padding: 10px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { font-size: 11px; padding: 4px 0; }
        .totals td:last-child { text-align: right; font-weight: 700; }
        .net-row td { font-size: 13px; color: #0f4c81; border-top: 1px solid #bfd3ea; padding-top: 8px; }

        .tmpl-footer { margin: 10px 14px 0; padding: 8px 10px; border: 1px solid #dbe4ef; border-radius: 4px; background: #f9fbff; font-size: 10px; color: #334155; line-height: 1.5; }
        .page-footer { width: 100%; border-top: 1px dashed #c0d0e0; margin-top: 12px; padding: 10px 14px 14px; font-size: 9px; color: #6a7f95; }
        .page-footer-inner { width: 100%; border-collapse: collapse; }
        .page-footer-inner td { vertical-align: bottom; }
        .sig-line { border-top: 1px solid #8aa0b8; padding-top: 4px; margin-top: 32px; font-size: 9px; text-align: center; min-width: 140px; }
    </style>
</head>
<body>

@if($headerData)
    <div class="hdr-banner"><img src="{{ $headerData }}" alt="Header"></div>
@endif

<table class="hosp-bar" cellpadding="0" cellspacing="0"><tr><td>
    <table class="hosp-bar-inner" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:56px;">
                @if($logoData)
                    <img src="{{ $logoData }}" class="hosp-logo" alt="">
                @endif
            </td>
            <td>
                <div class="hosp-name">{{ $hospital?->name ?? config('app.name') }}</div>
                <div class="hosp-sub">
                    @if($addressLine !== ''){{ $addressLine }}<br>@endif
                    @if($phone !== ''){{ $phone }}<br>@endif
                    HR & Payroll Department
                </div>
            </td>
            <td class="hosp-right" style="width:38%;">
                <strong>SALARY SLIP</strong><br>
                Slip No: <strong>{{ $record->slip_no ?? 'N/A' }}</strong><br>
                Staff ID: <strong>{{ $slipEmployeeId }}</strong><br>
                Month: <strong>{{ $slipMonthLabel }}</strong><br>
                Printed: <strong>{{ $printedAt }}</strong>
            </td>
        </tr>
    </table>
</td></tr></table>

<table class="report-band" cellpadding="0" cellspacing="0"><tr><td>
    <table class="report-band-inner" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:62%;"><h1>{{ strtoupper($slipName) }}</h1></td>
            <td class="meta">
                Designation: {{ $slipDesig }}<br>
                Department: {{ $slipDepartment }}<br>
                Joining Date: {{ $slipJoinDate }}
            </td>
        </tr>
    </table>
</td></tr></table>

<div class="section">
    <div class="section-title">Employee information</div>
    <table class="info-grid" cellspacing="0" cellpadding="0">
        <tr>
            <td><span class="info-label">Employee</span><span class="info-value">{{ $slipName }}</span></td>
            <td><span class="info-label">Employee Code</span><span class="info-value">{{ $slipEmployeeId }}</span></td>
            <td><span class="info-label">Department</span><span class="info-value">{{ $slipDepartment }}</span></td>
            <td><span class="info-label">Designation</span><span class="info-value">{{ $slipDesig }}</span></td>
        </tr>
    </table>
</div>

<table class="grid-two" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div class="card">
                <div class="card-head">Allowances / Earnings</div>
                <div class="rows">
                    <table class="row" cellspacing="0" cellpadding="0">
                        <tr><td>Basic Pay</td><td>INR {{ number_format($slipBasic, 2) }}</td></tr>
                    </table>
                    <div class="line"></div>
                    <table class="row" cellspacing="0" cellpadding="0">
                        @if(isset($allowanceItems) && $allowanceItems->count() > 0)
                            @foreach($allowanceItems as $item)
                                <tr><td>{{ $item->label }}</td><td>INR {{ number_format($item->amount, 2) }}</td></tr>
                            @endforeach
                        @else
                            <tr><td>Total Allowances</td><td>INR {{ number_format($slipAllowances, 2) }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </td>
        <td>
            <div class="card">
                <div class="card-head">Deductions</div>
                <div class="rows">
                    <table class="row" cellspacing="0" cellpadding="0">
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
                                <tr>
                                    <td>
                                        {{ $item->label }}
                                        @if($item->label === 'Leave Deduction' && $leaveUnpaid !== null)
                                            <div style="font-size:8px;color:#5d7285;font-weight:400;margin-top:2px;">Unpaid leave days (this month): {{ number_format($leaveUnpaid, 1) }}@if($leaveApprovedMonth !== null) · Approved in month: {{ number_format($leaveApprovedMonth, 1) }}@endif</div>
                                        @elseif($item->label === 'Leave Deduction' && $leaveLegacy !== null)
                                            <div style="font-size:8px;color:#5d7285;font-weight:400;margin-top:2px;">Leave days: {{ number_format($leaveLegacy, 1) }}</div>
                                        @elseif($item->label === 'Attendance Deduction' && $attUnits !== null)
                                            <div style="font-size:8px;color:#5d7285;font-weight:400;margin-top:2px;">Units (month): {{ number_format($attUnits, 2) }}@if($attAbsentFull !== null) · Full absent: {{ number_format($attAbsentFull, 1) }}@endif @if($attAbsentHalf !== null && $attAbsentHalf > 0) · Half absent (units): {{ number_format($attAbsentHalf, 2) }}@endif @if($attPresentHalf !== null && $attPresentHalf > 0) · Half present (units): {{ number_format($attPresentHalf, 2) }}@endif</div>
                                        @endif
                                    </td>
                                    <td>INR {{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td>Total Deductions</td><td>INR {{ number_format($slipDeductions, 2) }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>

<div class="totals">
    <table>
        <tr><td>Gross Pay</td><td>INR {{ number_format($grossPay, 2) }}</td></tr>
        <tr><td>Total Deductions</td><td>INR {{ number_format($slipDeductions, 2) }}</td></tr>
        <tr class="net-row"><td>Net Pay</td><td>INR {{ number_format($slipNet, 2) }}</td></tr>
    </table>
</div>

@if(!empty($printTemplate?->footer_text))
    <div class="tmpl-footer">{!! nl2br(e($printTemplate->footer_text)) !!}</div>
@endif

<table class="page-footer" cellpadding="0" cellspacing="0"><tr><td>
    <table class="page-footer-inner" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:48%;">
                Generated by HR Payroll Engine<br>
                Printed: {{ $printedAt }}<br>
                {{ $hospital?->name ?? '' }}
            </td>
            <td style="width:26%;">
                <div class="sig-line">Prepared By</div>
                <div style="text-align:center;font-size:9px;margin-top:2px;">HR / Payroll Desk</div>
            </td>
            <td style="width:26%;">
                <div class="sig-line">Authorized Signature</div>
            </td>
        </tr>
    </table>
</td></tr></table>

</body>
</html>
