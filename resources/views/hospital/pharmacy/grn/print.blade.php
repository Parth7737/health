<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>GRN - ({{ $grn->grn_no }})</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #111827; background: #fff; font-size: 11px; line-height: 1.25; }
        .sheet { max-width: 980px; margin: 8px auto; background: #fff; }
        .head { padding: 8px; border-bottom: 1px solid #000; }
        .head-top { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        h2 { margin: 0; font-size: 18px; }
        .tag { text-align: right; font-size: 10px; line-height: 1.45; }
        .tag .title { font-size: 14px; font-weight: 700; letter-spacing: .4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 5px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; font-weight: 700; }
        .meta th { width: 13%; }
        .section-title { margin: 8px 0 4px; padding: 4px 5px; border: 1px solid #000; background: #f3f4f6; font-weight: 700; text-transform: uppercase; }
        .items th { font-size: 9px; text-transform: uppercase; }
        .items td { font-size: 9.5px; }
        .text-end { text-align: right; }
        .empty { text-align: center; color: #4b5563; }
        .totals { width: 320px; margin-left: auto; margin-top: 6px; }
        .notes { border: 1px solid #000; padding: 5px; min-height: 50px; margin-top: 6px; }
        .footer { margin-top: 12px; display: flex; justify-content: space-between; align-items: flex-end; }
        .sign { min-width: 180px; text-align: center; }
        .line { margin-top: 28px; border-top: 1px solid #000; padding-top: 4px; }
        @page { margin: 6mm; size: A4 portrait; }
        @media print {
            .sheet { margin: 0; max-width: none; }
        }
    </style>
</head>
<body>
@php
    $supplier = $grn->supplier;
    $purchaseBill = $grn->purchaseBill;
    $fmtQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
@endphp

<div class="sheet">
    <div class="head">
        <div class="head-top">
            <div>
                <h2>{{ config('app.name') }}</h2>
                <div>Goods Receipt Note</div>
            </div>
            <div class="tag">
                <div class="title">GOODS RECEIPT NOTE</div>
                <div><strong>GRN No:</strong> {{ $grn->grn_no }}</div>
                <div><strong>Received At:</strong> {{ optional($grn->received_at)->format('d-m-Y h:i A') ?? '-' }}</div>
                <div><strong>Printed At:</strong> {{ now()->format('d-m-Y h:i A') }}</div>
            </div>
        </div>

        <table class="meta" style="margin-top: 8px;">
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
    <table class="items">
        <thead>
        <tr>
            <th style="width:4%;">SN</th>
            <th style="width:16%;">Medicine</th>
            <th style="width:9%;">Batch</th>
            <th style="width:8%;">Expiry</th>
            <th class="text-end" style="width:7%;">Ordered</th>
            <th class="text-end" style="width:7%;">Received</th>
            <th class="text-end" style="width:6%;">Free</th>
            <th class="text-end" style="width:7%;">Rejected</th>
            <th class="text-end" style="width:7%;">Accepted</th>
            <th class="text-end" style="width:8%;">Pur. Price</th>
            <th class="text-end" style="width:8%;">MRP</th>
            <th class="text-end" style="width:6%;">Tax%</th>
            <th class="text-end" style="width:7%;">Total</th>
        </tr>
        </thead>
        <tbody>
        @forelse($grn->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->medicine?->name ?? '-' }}</td>
                <td>{{ $item->batch_no ?: '-' }}</td>
                <td>{{ optional($item->expiry_date)->format('M-Y') ?? '-' }}</td>
                <td class="text-end">{{ $fmtQty($item->quantity_ordered) }}</td>
                <td class="text-end">{{ $fmtQty($item->quantity_received) }}</td>
                <td class="text-end">{{ $fmtQty($item->quantity_free) }}</td>
                <td class="text-end">{{ $fmtQty($item->quantity_rejected) }}</td>
                <td class="text-end">{{ $fmtQty($item->quantity_accepted) }}</td>
                <td class="text-end">{{ number_format((float) $item->unit_purchase_price, 2) }}</td>
                <td class="text-end">{{ number_format((float) $item->unit_mrp, 2) }}</td>
                <td class="text-end">{{ number_format((float) $item->tax_percent, 2) }}</td>
                <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="13" class="empty">No GRN items found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tbody>
        <tr>
            <td><strong>Total Tax</strong></td>
            <td class="text-end"><strong>{{ number_format($grn->tax_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Taxable Amount</strong></td>
            <td class="text-end"><strong>{{ number_format($grn->taxable_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Total Amount</strong></td>
            <td class="text-end"><strong>{{ number_format($grn->total_amount, 2) }}</strong></td>
        </tr>
        </tbody>
    </table>

    <div class="notes">
        <strong>Notes:</strong>
        <div style="margin-top: 4px;">{{ $grn->notes ?: 'No notes added for this GRN.' }}</div>
    </div>

    <div class="footer">
        <div>Printed on {{ now()->format('d-m-Y h:i A') }}</div>
        <div class="sign"><div class="line">Receiver Signature</div></div>
    </div>
</div>

<script>
window.addEventListener('load', function () {
    window.print();
});
</script>
</body>
</html>
