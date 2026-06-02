@php
    $status = strtolower($bill->status ?? 'pending');
    $statusColor = match($status) {
        'approved' => 'success',
        'rejected' => 'danger',
        'partially_received' => 'info',
        'received' => 'primary',
        default => 'warning',
    };
    $supplier = $bill->supplier;
    $fmtQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
@endphp

<div class="modal-header">
    <div>
        <h5 class="modal-title mb-1">Purchase Order - {{ $bill->bill_no }}</h5>
        <span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<style>
    .po-view .label {
        color: #6c757d;
        font-size: .78rem;
        margin-bottom: .15rem;
    }
    .po-view .value {
        font-size: .9rem;
        font-weight: 600;
        min-height: 1.25rem;
    }
    .po-view .section-title {
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: .35rem;
        margin-bottom: .75rem;
    }
    .po-view-table th,
    .po-view-table td {
        padding: .4rem .45rem;
        white-space: nowrap;
        vertical-align: middle;
    }
</style>

<div class="modal-body po-view">
    <div class="section-title">PO Details</div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="label">PO No.</div>
            <div class="value">{{ $bill->bill_no }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Order Date</div>
            <div class="value">{{ optional($bill->bill_date)->format('d-m-Y') ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Supplier</div>
            <div class="value">{{ $supplier?->name ?? $bill->supplier_name ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Supplier Phone</div>
            <div class="value">{{ $supplier?->phone ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Created By</div>
            <div class="value">{{ $bill->createdBy?->name ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Approved / Rejected By</div>
            <div class="value">{{ $bill->approvedBy?->name ?? '-' }}</div>
        </div>
        <div class="col-md-3">
            <div class="label">Approved / Rejected At</div>
            <div class="value">{{ optional($bill->approved_at)->format('d-m-Y h:i A') ?? '-' }}</div>
        </div>
        @if($status === 'rejected')
            <div class="col-md-9">
                <div class="label">Reject Reason</div>
                <div class="value">{{ $bill->reject_reason ?: '-' }}</div>
            </div>
        @endif
        <div class="col-12">
            <div class="label">Notes</div>
            <div class="value">{{ $bill->notes ?: '-' }}</div>
        </div>
    </div>

    <div class="section-title">PO Items</div>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered po-view-table mb-0">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th class="text-end">Ordered Qty</th>
                <th class="text-end">Est. Rate</th>
                <th class="text-end">Line Total</th>
            </tr>
            </thead>
            <tbody>
            @forelse($bill->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->medicine?->name ?? '-' }}</td>
                    <td class="text-end">{{ $fmtQty($item->quantity_purchased) }} <small class="text-muted">({{ $item->medicine?->unit?->name ?? '-' }})</small></td>
                    <td class="text-end">{{ number_format((float) $item->unit_purchase_price, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center text-muted">No purchase items found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="row justify-content-end">
        <div class="col-md-5 col-lg-4">
            <table class="table table-sm table-bordered mb-0">
                <tbody>
                <tr><td>Net Total</td><td class="text-end">{{ number_format((float) $bill->net_total, 2) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
