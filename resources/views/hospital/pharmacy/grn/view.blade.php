@php
    $supplier = $grn->supplier;
    $purchaseBill = $grn->purchaseBill;
    $fmtQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
@endphp

<div class="modal-header">
    <div>
        <h5 class="modal-title mb-1">{{ $grn->grn_no }}</h5>
        <span class="badge bg-success">Goods Received</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<style>
    .grn-view .label {
        color: #6c757d;
        font-size: .78rem;
        margin-bottom: .15rem;
    }
    .grn-view .value {
        font-size: .9rem;
        font-weight: 600;
        min-height: 1.25rem;
    }
    .grn-view .section-title {
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: .35rem;
        margin-bottom: .75rem;
    }
    .grn-view-table th,
    .grn-view-table td {
        padding: .4rem .45rem;
        white-space: nowrap;
        vertical-align: middle;
    }
</style>

<div class="modal-body grn-view">
    <div class="section-title">GRN Details</div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="label">GRN No.</div>
            <div class="value">{{ $grn->grn_no }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">PO Ref</div>
            <div class="value">{{ $purchaseBill?->bill_no ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">PO Date</div>
            <div class="value">{{ optional($purchaseBill?->bill_date)->format('d-m-Y') ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Supplier</div>
            <div class="value">{{ $supplier?->name ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Invoice No.</div>
            <div class="value">{{ $grn->invoice_no ?: '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Invoice Date</div>
            <div class="value">{{ optional($grn->invoice_date)->format('d-m-Y') ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Vehicle No.</div>
            <div class="value">{{ $grn->vehicle_no ?: '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Temperature Status</div>
            <div class="value">{{ $grn->temperature_status ?: '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Received By</div>
            <div class="value">{{ $grn->receivedByUser?->name ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Received At</div>
            <div class="value">{{ optional($grn->received_at)->format('d-m-Y h:i A') ?? '-' }}</div>
        </div>
        <div class="col-12">
            <div class="label">Notes</div>
            <div class="value">{{ $grn->notes ?: '-' }}</div>
        </div>
    </div>

    <div class="section-title">Received Items</div>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered grn-view-table mb-0">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Batch</th>
                <th>Expiry</th>
                <th class="text-end">Pack Size</th>
                <th class="text-end">Ordered Qty</th>
                <th class="text-end">Received Qty</th>
                <th class="text-end">Free Qty</th>
                <th class="text-end">Rejected Qty</th>
                <th class="text-end">Accepted Qty</th>
                <th class="text-end">Pack Pur. Price</th>
                <th class="text-end">Pack Sale Price</th>
                <th class="text-end">Pack MRP</th>
                <th class="text-end">Tax %</th>
                <th class="text-end">Tax Amt.</th>
                <th class="text-end">Line Total</th>
                <th>Reject Reason</th>
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
                    <td class="text-end">{{ number_format((float) $item->pack_sale_price, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->pack_mrp, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->tax_percent, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->tax_amount, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                    <td>{{ $item->rejection_reason ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="text-center text-muted">No GRN items found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="row justify-content-end">
        <div class="col-md-4 col-lg-3">
            <table class="table table-sm table-bordered mb-0">
                <tbody>
                <tr>
                    <td>Taxable Total</td>
                    <td class="text-end">{{ number_format($grn->taxable_amount, 2) }}</td>
                </tr>
                @if($grn->gst_type === 'interstate')
                    <tr>
                        <td>IGST</td>
                        <td class="text-end">{{ number_format($grn->total_igst, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>CGST</td>
                        <td class="text-end">{{ number_format($grn->total_cgst, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SGST</td>
                        <td class="text-end">{{ number_format($grn->total_sgst, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Total Tax</td>
                    <td class="text-end">{{ number_format($grn->total_tax, 2) }}</td>
                </tr>
                <tr class="table-light fw-bold">
                    <td>Final Total</td>
                    <td class="text-end">{{ number_format($grn->total_amount, 2) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary grn-print-btn" data-url="{{ route('hospital.pharmacy.grn.print', ['grn' => $grn->id]) }}">
        <i class="fa-solid fa-print"></i> Print
    </button>
</div>
