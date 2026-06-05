<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pharmacy Sale Bill - {{ $bill->bill_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f4f4f2;
            color: #222;
            font-family: "Courier New", Courier, monospace;
            font-size: 12px;
            line-height: 1.12;
        }
        .bill-paper {
            width: 840px;
            min-height: 420px;
            margin: 10px auto;
            padding: 18px 26px 12px;
            background: #fff;
            border: 1px solid #cfcfcf;
            position: relative;
        }
        .bill-paper:before,
        .bill-paper:after {
            content: "";
            position: absolute;
            top: 16px;
            bottom: 16px;
            width: 14px;
            background:
                radial-gradient(circle at center, #d9d9d9 0 4px, transparent 5px) center top / 14px 54px repeat-y;
        }
        .bill-paper:before { left: 6px; }
        .bill-paper:after { right: 6px; }
        .content { padding: 0 18px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .strong { font-weight: 700; }
        .muted { color: #666; }
        .brand-name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .brand-line { margin-top: 2px; }
        .top-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            margin-top: 6px;
        }
        .meta-line {
            display: grid;
            grid-template-columns: 68px 1fr 78px 1fr;
            column-gap: 6px;
            margin-top: 2px;
            white-space: nowrap;
        }
        .invoice-line {
            display: grid;
            grid-template-columns: 128px 1fr 68px 150px;
            gap: 8px;
            margin: 7px 0 2px;
            align-items: end;
            font-size: 16px;
        }
        .invoice-line .value {
            font-weight: 700;
            border-bottom: 1px solid #777;
            min-height: 18px;
        }
        .rule { border-top: 1px solid #777; height: 0; margin: 2px 0 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            padding: 2px 3px;
            vertical-align: top;
            overflow: hidden;
            text-overflow: clip;
        }
        thead th {
            border-bottom: 1px solid #777;
            font-weight: 400;
        }
        tbody td { height: 20px; }
        .product { white-space: nowrap; }
        .mono-small { font-size: 11px; }
        .totals {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 18px;
            margin-top: 86px;
        }
        .qty-total {
            border-top: 1px solid #777;
            padding-top: 3px;
            width: 70px;
            text-align: center;
        }
        .amount-lines {
            border-top: 1px solid #777;
            padding-top: 3px;
        }
        .amount-row {
            display: grid;
            grid-template-columns: 1fr 110px;
            gap: 8px;
            min-height: 20px;
        }
        .grand {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .footer-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 14px;
            margin-top: 8px;
            border-top: 1px solid #777;
            padding-top: 5px;
        }
        .no-print {
            width: 840px;
            margin: 8px auto 0;
            display: flex;
            gap: 8px;
        }
        .no-print button {
            padding: 6px 14px;
            border: 0;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
            font-size: 12px;
            font-family: Arial, sans-serif;
        }
        .btn-print { background: #2563eb; }
        .btn-close { background: #6b7280; }
        @page { margin: 5mm; size: 9.5in 5.5in landscape; }
        @media print {
            body { background: #fff; }
            .bill-paper {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 12px 20px 8px;
                border: 0;
            }
            .content { padding: 0 18px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
@php
    $patient = $bill->patient;
    $addressLine = trim(implode(', ', array_filter([$hospital?->address, $hospital?->city, $hospital?->pincode])));
    $patientAddress = trim(implode(', ', array_filter([$patient?->address, $patient?->district, $patient?->state, $patient?->pin_code])));
    $doctor = $bill->opdPrescription?->doctor ?? $bill->ipdPrescription?->doctor;
    $doctorName = $doctor?->full_name ? trim($doctor->full_name) : null;
    $rxNo = $bill->opdPrescription?->prescription_no
        ?? $bill->ipdPrescription?->prescription_no
        ?? ($bill->opd_prescription_id ? 'OPD #' . $bill->opd_prescription_id : ($bill->ipd_prescription_id ? 'IPD #' . $bill->ipd_prescription_id : '-'));
    $fmtQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
    $money = fn ($value) => number_format((float) $value, 2);
    $roundOff = (float) ($bill->round_off ?? 0);
    $discount = (float) ($bill->discount_amount ?? 0);
    $grossTotal = (float) ($bill->subtotal ?? 0) + (float) ($bill->tax_amount ?? 0);
    $saved = max(0, $discount + max(0, -1 * $roundOff));
    $roundedGrand = round((float) $bill->net_total);
    $roundingDiff = $roundedGrand - (float) $bill->net_total;
    $totalQty = $bill->items->sum(fn ($item) => (float) $item->quantity);
@endphp

<div class="no-print">
    <button type="button" class="btn-print" onclick="window.print()">Print</button>
    <button type="button" class="btn-close" onclick="window.close()">Close</button>
</div>

<div class="bill-paper">
    <div class="content">
        <div class="center">
            <div class="brand-name">{{ $hospital?->name ?? config('app.name') }}</div>
            <div class="brand-line">{{ $addressLine !== '' ? $addressLine : '-' }}</div>
            <div class="brand-line">
                Phone: {{ $hospital?->phone ?? '-' }}
                @if(!empty($hospital?->gstin))
                    , GSTIN: {{ $hospital->gstin }}
                @endif
            </div>
            @if(!empty($printTemplate?->footer_text))
                <div class="brand-line">{!! nl2br(e($printTemplate->footer_text)) !!}</div>
            @endif
        </div>

        <div class="top-grid">
            <div>
                <div class="meta-line">
                    <span>Patient:</span><span>{{ $patient?->name ?? 'Walk-in Customer' }}</span>
                    <span>Address:</span><span>{{ $patientAddress !== '' ? $patientAddress : '-' }}</span>
                </div>
                <div class="meta-line">
                    <span>UHID:</span><span>{{ $patient?->patient_id ?? '-' }}</span>
                    <span>Phone:</span><span>{{ $patient?->phone ?? '-' }}</span>
                </div>
                <div class="meta-line">
                    <span>Dr.:</span><span>{{ $doctorName ?: '-' }}</span>
                    <span>Rx:</span><span>{{ $rxNo }}</span>
                </div>
            </div>
            <div class="right mono-small">
                <div>Payment: {{ strtoupper((string) ($bill->payment_mode ?: 'Cash')) }}</div>
                <div>Status: {{ strtoupper((string) ($bill->payment_status ?: 'paid')) }}</div>
            </div>
        </div>

        <div class="invoice-line">
            <div>Invoice No:</div>
            <div class="value">{{ $bill->bill_no }}</div>
            <div class="right">Date:</div>
            <div class="value right">{{ optional($bill->bill_date)->format('d-m-y') ?? '-' }}</div>
        </div>

        <table>
            <thead>
            <tr>
                <th style="width:6%;" class="right">Qty</th>
                <th style="width:28%;">Product</th>
                <th style="width:8%;">Pack</th>
                <th style="width:8%;">Mfg</th>
                <th style="width:10%;" class="right">MRP</th>
                <th style="width:12%;">Batch</th>
                <th style="width:8%;">Exp.</th>
                <th style="width:7%;" class="right">Disc</th>
                <th style="width:6%;" class="right">GST</th>
                <th style="width:7%;" class="right">Amount</th>
            </tr>
            </thead>
            <tbody>
            @forelse($bill->items as $item)
                @php
                    $packSize = (int) ($item->stockBatch?->pack_size ?? $item->medicine?->default_pack_size ?? 1);
                    $packSize = max(1, $packSize);
                    $packMrp = (float) ($item->stockBatch?->pack_mrp ?? 0);
                    if ($packMrp <= 0) {
                        $packMrp = (float) $item->unit_mrp * $packSize;
                    }
                    $packLabel = $packSize . ' ' . ($item->medicine?->unit?->name ?? 'Unit');
                @endphp
                <tr>
                    <td class="right">{{ $fmtQty($item->quantity) }}</td>
                    <td class="product">
                        {{ $item->medicine?->name ?? '-' }}
                        @if($item->is_substituted)
                            <span class="muted">*</span>
                        @endif
                    </td>
                    <td>{{ $packLabel }}</td>
                    <td>{{ $item->medicine?->company ?? '-' }}</td>
                    <td class="right">{{ $money($packMrp) }}</td>
                    <td>{{ $item->batch_no ?: '-' }}</td>
                    <td>{{ $item->expiry_date ? $item->expiry_date->format('m-y') : '-' }}</td>
                    <td class="right">{{ $fmtQty($item->discount_percent) }}</td>
                    <td class="right">{{ $fmtQty($item->tax_percent) }}%</td>
                    <td class="right">{{ $money($item->line_total) }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="center muted">No sale items found.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="totals">
            <div>
                <div class="qty-total">{{ $fmtQty($totalQty) }}</div>
            </div>
            <div class="amount-lines">
                <div class="amount-row">
                    <div class="center">Total:</div>
                    <div class="right">{{ $money($grossTotal) }}</div>
                </div>
                @if($saved > 0)
                    <div class="amount-row">
                        <div class="center">You saved:</div>
                        <div class="right">{{ $money($saved) }}</div>
                    </div>
                @endif
                @if(abs($roundingDiff) >= 0.005 || abs($roundOff) >= 0.005)
                    <div class="amount-row">
                        <div class="center">Round Off:</div>
                        <div class="right">{{ $money(abs($roundingDiff) >= 0.005 ? $roundingDiff : $roundOff) }}</div>
                    </div>
                @endif
                <div class="amount-row grand">
                    <div>Grand Total:</div>
                    <div class="right">{{ $money($roundedGrand) }}</div>
                </div>
            </div>
        </div>

        <div class="footer-row">
            <div>E.&amp; O.E. Subject to {{ strtoupper((string) ($hospital?->city ?: 'local')) }} jurisdiction.</div>
            <div>For, {{ strtoupper((string) ($hospital?->name ?? config('app.name'))) }}</div>
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
