<div class="modal-header">
    <h5 class="modal-title">{{ $bill ? 'Edit Purchase Order — '.$bill->bill_no : 'New Purchase Order' }}</h5>
    @if($bill)
        @php
            $statusColor = match($bill->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' };
        @endphp
        <span class="badge bg-{{ $statusColor }} ms-2">{{ ucfirst($bill->status ?? 'pending') }}</span>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<style>
    .purchase-compact .form-label {
        margin-bottom: .2rem;
        font-size: .78rem;
    }
    .purchase-compact .form-control,
    .purchase-compact .input-group-text,
    .purchase-compact .btn {
        font-size: .82rem;
    }
    #purchase-items-table th,
    #purchase-items-table td {
        padding: .35rem .4rem;
        white-space: nowrap;
        vertical-align: middle;
    }
</style>
<form method="POST" id="savedata" class="purchase-compact">
    @if($bill)<input type="hidden" name="bill_id" id="bill_id" value="{{ $bill->id }}">@else<input type="hidden" name="bill_id" id="bill_id" value="">@endif
    <div class="modal-body p-0">
        <div class="row g-0">
            <div class="col-12 p-3">
                {{-- PO Header --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bill_date" id="bill_date"
                               value="{{ $bill ? $bill->bill_date->format('Y-m-d') : now()->toDateString() }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-control">
                            <option value="">— Select Supplier —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ $bill && $bill->supplier_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}{{ $s->phone ? ' ('.$s->phone.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control form-control-sm" name="notes" rows="1">{{ $bill?->notes }}</textarea>
                    </div>
                </div>

                {{-- Items Table --}}
                @if($bill)
                    <p class="text-muted small mb-1"><i class="fa fa-info-circle"></i> Items are locked after creation. Only header info can be updated.</p>
                    <div class="table-responsive mb-2">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Medicine</th>
                                <th>Qty</th>
                                <th>Est. Rate</th>
                                <th class="text-end">Est. Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bill->items as $i => $item)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $item->medicine?->name }}</td>
                                    <td>{{ $item->quantity_purchased }}</td>
                                    <td>{{ number_format($item->unit_purchase_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->line_subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" id="purchase-items-table">
                            <thead class="table-secondary">
                            <tr>
                                <th style="min-width:150px">Medicine <span class="text-danger">*</span></th>
                                <th style="min-width:60px">Qty <span class="text-danger">*</span></th>
                                <th style="min-width:85px">Est. Rate ₹</th>
                                <th style="min-width:85px" class="text-end">Est. Amount</th>
                                <th style="width:36px"></th>
                            </tr>
                            </thead>
                            <tbody id="purchase-items-body"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-purchase-item">
                        <i class="fa fa-plus"></i> Add Item
                    </button>
                @endif

                <div class="d-flex justify-content-end mt-3">
                    <div><span class="text-muted">Estimated Total:</span> <strong class="text-success fs-5" id="summary-net-total">₹{{ $bill ? number_format($bill->net_total, 2) : '0.00' }}</strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="save-purchase-btn">
            {{ $bill ? 'Update Purchase Order' : 'Create Purchase Order (Pending Approval)' }}
        </button>
    </div>
</form>

<script>
window.purchaseMedicines = @json($medicines);
window.isEditMode = {{ $bill ? 'true' : 'false' }};
@if($bill)
window.editBillData = {
    net_total: {{ $bill->net_total }},
};
@endif
</script>
