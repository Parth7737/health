<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>OPD Final Bill</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #111827; background: #fff; font-size: 11px; line-height: 1.25; }
        .sheet { max-width: 980px; margin: 8px auto; background: #fff; }
        .banner { width: 100%; margin-bottom: 4px; }
        .banner img { width: 100%; max-height: 110px; object-fit: cover; display: block; }
        .head { padding: 6px 8px 4px; border-bottom: 1px solid #000; }
        .head-top { display: flex; justify-content: space-between; gap: 10px; align-items: flex-start; }
        .brand { display: flex; align-items: center; gap: 8px; }
        .brand img { width: 40px; height: 40px; object-fit: cover; border: 1px solid #b9bec7; }
        .brand h2 { margin: 0; font-size: 17px; font-weight: 700; color: #000; }
        .brand p { margin: 2px 0 0; font-size: 10px; color: #374151; }
        .bill-tag { text-align: right; font-size: 10px; line-height: 1.4; }
        .bill-tag .title { font-size: 13px; font-weight: 700; letter-spacing: 0.4px; }
        .meta-table, .ledger-table, .totals-table { width: 100%; border-collapse: collapse; }
        .meta-table { margin-top: 4px; }
        .meta-table th, .meta-table td { border: 1px solid #000; padding: 3px 4px; vertical-align: top; font-size: 10px; }
        .meta-table th { width: 12%; background: #f3f4f6; text-align: left; font-weight: 700; }
        .section-title { margin: 6px 0 3px; padding: 3px 4px; border: 1px solid #000; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; background: #f3f4f6; }
        .ledger-table th, .ledger-table td { border: 1px solid #000; padding: 3px 4px; vertical-align: top; }
        .ledger-table th { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2px; background: #f3f4f6; color: #000; text-align: left; }
        .ledger-table td { font-size: 9.5px; }
        table { width: 100%; border-collapse: collapse; }
        .text-end { text-align: right; }
        .empty { text-align: center; color: #4b5563; }
        .totals-wrap { display: grid; grid-template-columns: minmax(0, 1fr) 280px; gap: 8px; margin-top: 4px; align-items: end; }
        .notes { border: 1px solid #000; padding: 4px 5px; font-size: 9.5px; min-height: 58px; }
        .totals-table td { border: 1px solid #000; padding: 4px 5px; font-size: 10px; }
        .totals-table tr:last-child td { font-weight: 700; font-size: 10.5px; background: #f3f4f6; }
        .footer { margin-top: 8px; padding: 0 2px; display: flex; justify-content: space-between; align-items: flex-end; font-size: 9.5px; color: #1f2937; }
        .sign { min-width: 180px; text-align: center; }
        .line { margin-top: 24px; border-top: 1px solid #000; padding-top: 4px; }
        @page { margin: 6mm; size: A4 portrait; }
        @media print { body { background: #fff; } .sheet { margin: 0; border: 0; box-shadow: none; max-width: none; } }
    </style>
</head>
<body>
    @php
        $logo = $hospital?->image ? asset('public/storage/' . $hospital->image) : asset('images/logo.png');
        $templateHeader = !empty($printTemplate?->header_image) ? asset('public/storage/' . $printTemplate->header_image) : null;
        $addressLine = trim(implode(', ', array_filter([$hospital?->address, $hospital?->city, $hospital?->pincode])));
        $invoiceNo = 'OPD-' . str_pad((string) $patient->id, 6, '0', STR_PAD_LEFT);
        $billStatus = ($balance ?? 0) > 0 ? 'Pending Due' : 'Settled';
    @endphp

    <div class="sheet">
        @if($templateHeader)
            <div class="banner"><img src="{{ $templateHeader }}" alt="OPD Bill Header"></div>
        @endif

        <div class="head">
            <div class="head-top">
                <div class="brand">
                    <img src="{{ $logo }}" alt="Hospital Logo">
                    <div>
                        <h2>{{ $hospital?->name ?? config('app.name') }}</h2>
                        <p>@if($addressLine !== ''){{ $addressLine }}@endif</p>
                    </div>
                </div>
                <div class="bill-tag">
                    <div class="title">OPD FINAL BILL</div>
                    <div><strong>Invoice No:</strong> {{ $invoiceNo }}</div>
                    <div><strong>Bill Date:</strong> {{ now()->format('d-m-Y h:i A') }}</div>
                    <div><strong>Status:</strong> {{ $billStatus }}</div>
                </div>
            </div>

            <table class="meta-table">
                <tbody>
                    <tr>
                        <th>Patient</th>
                        <td>{{ $patient->name ?? '-' }}</td>
                        <th>UHID</th>
                        <td>{{ $patient->patient_id ?? '-' }}</td>
                        <th>Phone</th>
                        <td>{{ $patient->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Age / Gender</th>
                        <td>{{ $patient->age_years ?? '-' }}Y / {{ $patient->gender ?? '-' }}</td>
                        <th>Total Charges</th>
                        <td>{{ number_format((float) ($totalCharges ?? 0), 2) }}</td>
                        <th>Balance Due</th>
                        <td>{{ number_format((float) ($balance ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Paid</th>
                        <td>{{ number_format((float) ($totalPaid ?? 0), 2) }}</td>
                        <th>Discount</th>
                        <td>{{ number_format((float) ($totalDiscount ?? 0), 2) }}</td>
                        <th>Advance / Credit</th>
                        <td>{{ number_format((float) ($advanceCredit ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section-title">Visit Summary</div>
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width:5%;">SN</th>
                    <th style="width:18%;">Visit No</th>
                    <th style="width:22%;">Date</th>
                    <th>Consultant</th>
                    <th style="width:12%;" class="text-end">Charges</th>
                    <th style="width:12%;" class="text-end">Paid</th>
                    <th style="width:12%;" class="text-end">Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visitBills ?? [] as $visitBill)
                    @php
                        $vVisit  = $visitBill['visit'] ?? null;
                        $vTitle  = $vVisit && filled($vVisit->case_no) ? $vVisit->case_no : 'General';
                        $vDate   = $vVisit ? optional($vVisit->appointment_date)->format('d-m-Y H:i') : '-';
                        $vDoc    = $vVisit?->consultant?->full_name ?? '-';
                        $vCharge = (float) ($visitBill['total_charges'] ?? 0);
                        $vPaid   = (float) ($visitBill['total_paid'] ?? 0);
                        $vDue    = (float) ($visitBill['total_due'] ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vTitle }}</td>
                        <td>{{ $vDate }}</td>
                        <td>{{ $vDoc }}</td>
                        <td class="text-end">{{ number_format($vCharge, 2) }}</td>
                        <td class="text-end">{{ number_format($vPaid, 2) }}</td>
                        <td class="text-end">{{ number_format($vDue, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">No visit data found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Charge Ledger</div>
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width:4%;">SN</th>
                    <th style="width:14%;">Date</th>
                    <th style="width:8%;">Module</th>
                    <th>Particular</th>
                    <th style="width:11%;" class="text-end">Amount</th>
                    <th style="width:11%;" class="text-end">Paid</th>
                    <th style="width:11%;" class="text-end">Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($charges ?? [] as $charge)
                    @php $lineDue = max(0, (float) $charge->amount - (float) $charge->paid_amount); @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($charge->charged_at)->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>{{ strtoupper($charge->module ?? '-') }}</td>
                        <td>{{ $charge->particular ?? '-' }}</td>
                        <td class="text-end">{{ number_format((float) $charge->amount, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $charge->paid_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($lineDue, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">No charge entries found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Payment Ledger</div>
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width:5%;">SN</th>
                    <th style="width:15%;">Date</th>
                    <th style="width:13%;">Mode</th>
                    <th>Reference</th>
                    <th style="width:13%;" class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments ?? [] as $payment)
                    @php $isRefund = (float) $payment->amount < 0; @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($payment->paid_at)->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>{{ $payment->payment_mode ?? '-' }}</td>
                        <td>{{ $payment->reference ?? '-' }}</td>
                        <td class="text-end">{{ $isRefund ? '-' : '' }}{{ number_format(abs((float) $payment->amount), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No payment entries found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals-wrap">
            <div class="notes">
                @if(!empty($printTemplate?->footer_text))
                    {!! nl2br(e($printTemplate->footer_text)) !!}
                @else
                    This is a computer-generated consolidated OPD bill.
                @endif
            </div>
            <table class="totals-table">
                <tbody>
                    <tr><td>Total Charges</td><td class="text-end">{{ number_format((float) ($totalCharges ?? 0), 2) }}</td></tr>
                    <tr><td>Total Discount</td><td class="text-end">{{ number_format((float) ($totalDiscount ?? 0), 2) }}</td></tr>
                    <tr><td>Total Tax</td><td class="text-end">{{ number_format((float) ($totalTax ?? 0), 2) }}</td></tr>
                    <tr><td>Total Paid</td><td class="text-end">{{ number_format((float) ($totalPaid ?? 0), 2) }}</td></tr>
                    <tr><td>Advance / Credit</td><td class="text-end">{{ number_format((float) ($advanceCredit ?? 0), 2) }}</td></tr>
                    <tr><td>Balance Due</td><td class="text-end">{{ number_format((float) ($balance ?? 0), 2) }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div>Printed on {{ now()->format('d-m-Y h:i A') }}</div>
            <div class="sign"><div class="line">Authorized Signature</div></div>
        </div>
    </div>

    <script>
    (function () {
        var closed = false;
        function closeAfterPrint() { if (closed) { return; } closed = true; window.close(); }
        window.addEventListener('afterprint', closeAfterPrint);
        if (window.matchMedia) {
            var mql = window.matchMedia('print');
            var listener = function (e) { if (!e.matches) { closeAfterPrint(); } };
            if (typeof mql.addEventListener === 'function') { mql.addEventListener('change', listener); }
            else if (typeof mql.addListener === 'function') { mql.addListener(listener); }
        }
        window.addEventListener('load', function () { window.print(); });
    })();
    </script>
</body>
</html>