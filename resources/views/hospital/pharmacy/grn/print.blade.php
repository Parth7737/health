<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>GRN - ({{ $grn->grn_no }})</title>
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
    $supplier = $grn->supplier;
    $purchaseBill = $grn->purchaseBill;
    $fmtQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
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
                <div class="title">GOODS RECEIPT NOTE</div>
                <div><strong>GRN No:</strong> {{ $grn->grn_no }}</div>
                <div><strong>Received At:</strong> {{ optional($grn->received_at)->format('d-m-Y h:i A') ?? '-' }}</div>
            </div>
        </div>

        <table class="meta-table">
            <tbody>
            <tr>
                <th>PO Ref</th>
                <td>{{ $purchaseBill?->bill_no ?? '-' }}</td>
                <th>PO Date</th>
                <td>{{ optional($purchaseBill?->bill_date)->format('d-m-Y') ?? '-' }}</td>
                <th>Supplier</th>
                <td>{{ $supplier?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Invoice No</th>
                <td>{{ $grn->invoice_no ?: '-' }}</td>
                <th>Invoice Date</th>
                <td>{{ optional($grn->invoice_date)->format('d-m-Y') ?? '-' }}</td>
                <th>Received By</th>
                <td>{{ $grn->receivedByUser?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Vehicle No</th>
                <td>{{ $grn->vehicle_no ?: '-' }}</td>
                <th>Temperature</th>
                <td>{{ $grn->temperature_status ?: '-' }}</td>
                <th>Items</th>
                <td>{{ $grn->items->count() }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="section-title">Received Items</div>
    <table class="items-table">
        <thead>
        <tr>
            <th style="width:3%;">SN</th>
            <th style="width:17%;">Medicine</th>
            <th style="width:8%;">Batch</th>
            <th style="width:7%;">Expiry</th>
            <th class="text-end" style="width:6%;">Pack Size</th>
            <th class="text-end" style="width:6%;">Ordered</th>
            <th class="text-end" style="width:6%;">Received</th>
            <th class="text-end" style="width:5%;">Free</th>
            <th class="text-end" style="width:6%;">Rejected</th>
            <th class="text-end" style="width:6%;">Accepted</th>
            <th class="text-end" style="width:9%;">Pack Pur. Price</th>
            <th class="text-end" style="width:8%;">Pack MRP</th>
            <th class="text-end" style="width:5%;">Tax%</th>
            <th class="text-end" style="width:8%;">Total</th>
        </tr>
        </thead>
        <tbody>
        @forelse($grn->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->medicine?->name ?? '-' }}</td>
                <td>{{ $item->batch_no ?: '-' }}</td>
                <td>{{ optional($item->expiry_date)->format('M-Y') ?? '-' }}</td>
                <td class="text-end">{{ $item->pack_size }}</td>
                <td class="text-end">{{ $fmtQty($item->quantity_ordered) }} <small class="text-muted">({{ $item->medicine?->unit?->name ?? '-' }})</small></td>
                <td class="text-end">{{ $fmtQty($item->quantity_received) }} <small class="text-muted">({{ $item->medicine?->unit?->name ?? '-' }})</small></td>
                <td class="text-end">{{ $fmtQty($item->quantity_free) }} <small class="text-muted">({{ $item->medicine?->unit?->name ?? '-' }})</small></td>
                <td class="text-end">{{ $fmtQty($item->quantity_rejected) }} <small class="text-muted">({{ $item->medicine?->unit?->name ?? '-' }})</small></td>
                <td class="text-end">{{ $fmtQty($item->quantity_accepted) }} <small class="text-muted">({{ $item->medicine?->unit?->name ?? '-' }})</small></td>
                <td class="text-end">{{ number_format((float) $item->pack_purchase_price, 2) }}</td>
                <td class="text-end">{{ number_format((float) $item->pack_mrp, 2) }}</td>
                <td class="text-end">{{ number_format((float) $item->tax_percent, 2) }}</td>
                <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="14" class="empty">No GRN items found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="totals-wrap">
        <div class="notes">
            <div><strong>Notes:</strong></div>
            <div style="margin-top: 4px;">{{ $grn->notes ?: 'No notes added for this GRN.' }}</div>
            @if(!empty($printTemplate?->footer_text))
                <div style="margin-top: 8px;">{!! nl2br(e($printTemplate->footer_text)) !!}</div>
            @endif
        </div>

        <table class="totals-table">
            <tbody>
            <tr>
                <td>Taxable Amount</td>
                <td class="text-end">{{ number_format($grn->taxable_amount, 2) }}</td>
            </tr>
            @if($grn->gst_type === 'interstate')
                <tr>
                    <td><strong>IGST</strong></td>
                    <td class="text-end"><strong>{{ number_format($grn->total_igst, 2) }}</strong></td>
                </tr>
            @else
                <tr>
                    <td><strong>CGST</strong></td>
                    <td class="text-end"><strong>{{ number_format($grn->total_cgst, 2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>SGST</strong></td>
                    <td class="text-end"><strong>{{ number_format($grn->total_sgst, 2) }}</strong></td>
                </tr>
            @endif
            <tr>
                <td>Total Tax</td>
                <td class="text-end">{{ number_format($grn->total_tax, 2) }}</td>
            </tr>
            <tr>
                <td>Total Amount</td>
                <td class="text-end">{{ number_format($grn->total_amount, 2) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div>Printed on {{ now()->format('d-m-Y h:i A') }}</div>
        <div class="sign">
            <div class="line">Receiver Signature</div>
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
