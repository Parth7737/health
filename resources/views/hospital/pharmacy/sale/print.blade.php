<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pharmacy Sale Bill</title>
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
        .items-table,
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

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
        }
        .items-table th {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            background: #f3f4f6;
            color: #000;
            text-align: left;
        }
        .items-table td { font-size: 9.5px; }

        .text-end { text-align: right; }
        .empty { text-align: center; color: #4b5563; }

        .totals-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 8px;
            margin-top: 4px;
            align-items: end;
        }
        .notes {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 9.5px;
            min-height: 70px;
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

        .no-print {
            margin: 8px auto 0;
            max-width: 980px;
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
        }
        .btn-print { background: #2563eb; }
        .btn-close { background: #6b7280; }

        @page { margin: 6mm; size: A4 portrait; }
        @media print {
            body { background: #fff; }
            .sheet { margin: 0; border: 0; box-shadow: none; max-width: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
@php
    $logo = $hospital?->image ? asset('public/storage/' . $hospital->image) : asset('images/logo.png');
    $templateHeader = !empty($printTemplate?->header_image) ? asset('public/storage/' . $printTemplate->header_image) : null;
    $addressLine = trim(implode(', ', array_filter([$hospital?->address, $hospital?->city, $hospital?->pincode])));
    $patient = $bill->patient;
@endphp


<div class="sheet">
    @if($templateHeader)
        <div class="banner"><img src="{{ $templateHeader }}" alt="Pharmacy Bill Header"></div>
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
                <div class="title">PHARMACY SALE BILL</div>
                <div><strong>Bill No:</strong> {{ $bill->bill_no }}</div>
                <div><strong>Bill Date:</strong> {{ optional($bill->bill_date)->format('d-m-Y') ?? '-' }}</div>
                <div><strong>Printed At:</strong> {{ now()->format('d-m-Y h:i A') }}</div>
            </div>
        </div>

        <table class="meta-table">
            <tbody>
                <tr>
                    <th>Patient</th>
                    <td>{{ $patient?->name ?? '-' }}</td>
                    <th>UHID</th>
                    <td>{{ $patient?->patient_id ?? '-' }}</td>
                    <th>Payment</th>
                    <td>{{ strtoupper((string) ($bill->payment_status ?? 'paid')) }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $patient?->phone ?? '-' }}</td>
                    <th>Prescription</th>
                    <td>
                        @if($bill->opd_prescription_id)
                            OPD #{{ $bill->opd_prescription_id }}
                        @elseif($bill->ipd_prescription_id)
                            IPD #{{ $bill->ipd_prescription_id }}
                        @else
                            -
                        @endif
                    </td>
                    <th>Paid / Due</th>
                    <td>{{ number_format((float) $bill->paid_amount, 2) }} / {{ number_format((float) $bill->due_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td colspan="5">{{ $bill->notes ?: '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section-title">Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%;">SN</th>
                <th>Medicine</th>
                <th style="width:8%;" class="text-end">Qty</th>
                <th style="width:10%;" class="text-end">Unit Price</th>
                <th style="width:9%;" class="text-end">MRP</th>
                <th style="width:8%;" class="text-end">Disc %</th>
                <th style="width:8%;" class="text-end">Tax %</th>
                <th style="width:11%;" class="text-end">Line Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bill->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $item->medicine?->name ?? '-' }}
                        @if($item->is_substituted)
                            <div style="font-size: 8.8px; color: #374151;">Substituted: {{ $item->substitution_note ?: 'Yes' }}</div>
                        @endif
                    </td>
                    <td class="text-end">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                    <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->unit_mrp, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->discount_percent, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->tax_percent, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">No sale items found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals-wrap">
        <div class="notes">
            <div><strong>Remarks:</strong></div>
            <div style="margin-top: 4px;">Medicine once sold will not be taken back unless authorized.</div>
            @if(!empty($printTemplate?->footer_text))
                <div style="margin-top: 8px;">{!! nl2br(e($printTemplate->footer_text)) !!}</div>
            @endif
        </div>

        <table class="totals-table">
            <tbody>
                <tr><td>Subtotal</td><td class="text-end">{{ number_format((float) $bill->subtotal, 2) }}</td></tr>
                <tr><td>Discount</td><td class="text-end">{{ number_format((float) $bill->discount_amount, 2) }}</td></tr>
                <tr><td>Tax</td><td class="text-end">{{ number_format((float) $bill->tax_amount, 2) }}</td></tr>
                <tr><td>Paid</td><td class="text-end">{{ number_format((float) $bill->paid_amount, 2) }}</td></tr>
                <tr><td>Due</td><td class="text-end">{{ number_format((float) $bill->due_amount, 2) }}</td></tr>
                <tr><td>Net Total</td><td class="text-end">{{ number_format((float) $bill->net_total, 2) }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div>Printed on {{ now()->format('d-m-Y h:i A') }}</div>
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
