<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>IPD Final Bill</title>
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

        .meta-table,
        .ledger-table,
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table { margin-top: 4px; }
        .meta-table th,
        .meta-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
            font-size: 10px;
        }
        .meta-table th { width: 12%; background: #f3f4f6; text-align: left; font-weight: 700; }

        .section-title {
            margin: 6px 0 3px;
            padding: 3px 4px;
            border: 1px solid #000;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            background: #f3f4f6;
        }

        .ledger-table th,
        .ledger-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
        }
        .ledger-table th {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            background: #f3f4f6;
            color: #000;
            text-align: left;
        }
        .ledger-table td { font-size: 9.5px; }

        table { width: 100%; border-collapse: collapse; }
        .text-end { text-align: right; }
        .empty { text-align: center; color: #4b5563; }

        .totals-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 8px;
            margin-top: 4px;
            align-items: end;
        }
        .notes {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 9.5px;
            min-height: 58px;
        }
        .totals-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 10px;
        }
        .totals-table tr:last-child td {
            font-weight: 700;
            font-size: 10.5px;
            background: #f3f4f6;
        }

        .footer {
            margin-top: 8px;
            padding: 0 2px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 9.5px;
            color: #1f2937;
        }
        .sign {
            min-width: 180px;
            text-align: center;
        }
        .line {
            margin-top: 24px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        @page { margin: 6mm; size: A4 portrait; }
        @media print {
            body { background: #fff; }
            .sheet { margin: 0; border: 0; box-shadow: none; max-width: none; }
        }
    </style>
</head>
<body>
@php
    $logo = $hospital?->image ? asset('public/storage/' . $hospital->image) : asset('images/logo.png');
    $templateHeader = !empty($printTemplate?->header_image) ? asset('public/storage/' . $printTemplate->header_image) : null;
    $addressLine = trim(implode(', ', array_filter([$hospital?->address, $hospital?->city, $hospital?->pincode])));
    $invoiceNo = 'IPD-' . str_pad((string) $allocation->id, 6, '0', STR_PAD_LEFT);
    $admissionNo = $allocation->admission_no ?: $invoiceNo;
@endphp

<div class="sheet">
    @if($templateHeader)
        <div class="banner"><img src="{{ $templateHeader }}" alt="IPD Bill Header"></div>
    @endif

    <div class="head">
        <div class="head-top">
            <div class="brand">
                <img src="{{ $logo }}" alt="Hospital Logo">
                <div>
                    <h2>{{ $hospital?->name ?? config('app.name') }}</h2>
                    <p>
                        @if($addressLine !== ''){{ $addressLine }}@endif
                    </p>
                </div>
            </div>
            <div class="bill-tag">
                <div class="title">IPD BILL</div>
                <div><strong>Invoice No:</strong> {{ $invoiceNo }}</div>
                <div><strong>Bill Date:</strong> {{ now()->format('d-m-Y h:i A') }}</div>
            </div>
        </div>

        <table class="meta-table">
            <tbody>
                <tr>
                    <th>Patient</th>
                    <td>{{ $allocation->patient?->name ?? '-' }}</td>
                    <th>UHID</th>
                    <td>{{ $allocation->patient?->patient_id ?? '-' }}</td>
                    <th>Admission No</th>
                    <td>{{ $admissionNo }}</td>
                </tr>
                <tr>
                    <th>Age / Gender</th>
                    <td>{{ $allocation->patient?->age_years ?? '-' }}Y / {{ $allocation->patient?->gender ?? '-' }}</td>
                    <th>Admission Date</th>
                    <td>{{ optional($allocation->admission_date)->format('d-m-Y H:i') ?? '-' }}</td>
                    <th>Status</th>
                    <td>{{ $allocation->discharge_date ? 'Discharged' : 'Active' }}</td>
                </tr>
                <tr>
                    <th>Consultant</th>
                    <td>{{ $allocation->consultantDoctor?->full_name ?? '-' }}</td>
                    <th>Payer</th>
                    <td>{{ $allocation->tpa?->name ?: 'Self' }}</td>
                    <th>Phone</th>
                    <td>{{ $allocation->patient?->phone ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section-title">Charges</div>
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width:4%;">SN</th>
                <th style="width:12%;">Date</th>
                <th style="width:7%;">Module</th>
                <th>Particular</th>
                <th style="width:6%;" class="text-end">Qty</th>
                <th style="width:9%;" class="text-end">Rate</th>
                <th style="width:10%;" class="text-end">Amount</th>
                <th style="width:10%;" class="text-end">Paid</th>
                <th style="width:10%;" class="text-end">Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($episodeCharges ?? collect()) as $charge)
                @php $rowDue = max(0, (float) ($charge->amount ?? 0) - (float) ($charge->paid_amount ?? 0)); @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($charge->charged_at)->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ strtoupper($charge->module ?? '-') }}</td>
                    <td>{{ $charge->particular ?? '-' }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format((float) ($charge->quantity ?? 0), 2), '0'), '.') }}</td>
                    <td class="text-end">{{ number_format((float) ($charge->unit_rate ?? 0), 2) }}</td>
                    <td class="text-end">{{ number_format((float) ($charge->amount ?? 0), 2) }}</td>
                    <td class="text-end">{{ number_format((float) ($charge->paid_amount ?? 0), 2) }}</td>
                    <td class="text-end">{{ number_format($rowDue, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty">No IPD charges found for this episode.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Payments</div>
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width:5%;">SN</th>
                <th style="width:14%;">Date</th>
                <th style="width:12%;">Mode</th>
                <th>Reference</th>
                <th style="width:12%;" class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($payments ?? collect()) as $payment)
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
                This is a computer-generated consolidated IPD bill.
            @endif
        </div>

        <table class="totals-table">
            <tbody>
                <tr><td>Total Charges</td><td class="text-end">{{ number_format((float) $totalCharges, 2) }}</td></tr>
                <tr><td>Total Discount</td><td class="text-end">{{ number_format((float) $totalDiscount, 2) }}</td></tr>
                <tr><td>Total Tax</td><td class="text-end">{{ number_format((float) $totalTax, 2) }}</td></tr>
                <tr><td>Total Paid</td><td class="text-end">{{ number_format((float) $totalPaid, 2) }}</td></tr>
                <tr><td>Advance / Credit</td><td class="text-end">{{ number_format((float) $advanceCredit, 2) }}</td></tr>
                <tr><td>Total Due</td><td class="text-end">{{ number_format((float) $totalDue, 2) }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div>
            Printed on {{ now()->format('d-m-Y h:i A') }}
        </div>
        <div class="sign">
            <div class="line">Authorized Signature</div>
        </div>
    </div>

</div>

<script>
(function () {
    var closed = false;
    function closeAfterPrint() {
        if (closed) {
            return;
        }
        closed = true;
        window.close();
    }

    window.addEventListener('afterprint', closeAfterPrint);

    if (window.matchMedia) {
        var mql = window.matchMedia('print');
        var listener = function (event) {
            if (!event.matches) {
                closeAfterPrint();
            }
        };

        if (typeof mql.addEventListener === 'function') {
            mql.addEventListener('change', listener);
        } else if (typeof mql.addListener === 'function') {
            mql.addListener(listener);
        }
    }

    window.addEventListener('load', function () {
        window.print();
    });
})();
</script>
</body>
</html>
