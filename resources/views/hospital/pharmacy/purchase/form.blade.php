<div class="modal-header">
    <h5 class="modal-title">{{ $bill ? 'Edit Purchase Bill — '.$bill->bill_no : 'New Purchase Bill' }}</h5>
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
                {{-- Bill Header --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bill Date <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-semibold">Supplier Invoice No</label>
                        <input type="text" class="form-control" name="supplier_invoice_no"
                               value="{{ $bill?->supplier_invoice_no }}">
                    </div>
                </div>

                {{-- Items Table --}}
                @if($bill)
                    {{-- EDIT MODE: Read-only items preview --}}
                    <p class="text-muted small mb-1"><i class="fa fa-info-circle"></i> Items are locked after creation. Only header info and discount can be updated.</p>
                    <div class="table-responsive mb-2">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Medicine</th>
                                <th>Pack</th>
                                <th>Batch</th>
                                <th>Expiry</th>
                                <th>Pur. Price</th>
                                <th>MRP</th>
                                <th>Qty</th>
                                <th>Free</th>
                                <th>Tax%</th>
                                <th class="text-end">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bill->items as $i => $item)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $item->medicine?->name }}</td>
                                    <td>{{ $item->pack_size }}</td>
                                    <td>{{ $item->batch_no }}</td>
                                    <td>{{ $item->expiry_date?->format('M-Y') }}</td>
                                    <td>{{ number_format($item->unit_purchase_price, 2) }}</td>
                                    <td>{{ number_format($item->unit_mrp, 2) }}</td>
                                    <td>{{ $item->quantity_purchased }}</td>
                                    <td>{{ $item->quantity_free }}</td>
                                    <td>{{ $item->tax_percent }}%</td>
                                    <td class="text-end">{{ number_format($item->line_subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- CREATE MODE: Editable item rows --}}
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" id="purchase-items-table">
                            <thead class="table-secondary">
                            <tr>
                                <th style="min-width:150px">Medicine <span class="text-danger">*</span></th>
                                <th style="min-width:90px">Pack Size</th>
                                <th style="min-width:90px">Batch No <span class="text-danger">*</span></th>
                                <th style="min-width:90px">Expiry</th>
                                <th style="min-width:85px">Pur. Price</th>
                                <th style="min-width:75px">MRP</th>
                                <th style="min-width:75px">Sale Price</th>
                                <th style="min-width:60px">Qty</th>
                                <th style="min-width:60px">Free</th>
                                <th style="min-width:60px">Tax%</th>
                                <th style="min-width:85px" class="text-end">Amount</th>
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

                {{-- Notes --}}
                <div class="mt-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea class="form-control form-control-sm" name="notes" rows="2">{{ $bill?->notes }}</textarea>
                </div>
                <div class="card mt-3 mb-0 border-0">
                    <div class="card-body py-2 px-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold mb-1">Discount</label>
                                <div class="input-group input-group-sm">
                                    <button type="button" class="btn btn-outline-secondary discount-type-btn active" data-type="fixed" id="disc-fixed-btn">₹</button>
                                    <button type="button" class="btn btn-outline-secondary discount-type-btn" data-type="percent" id="disc-percent-btn">%</button>
                                    <input type="hidden" name="discount_type" id="discount_type" value="{{ $bill?->discount_type ?? 'fixed' }}">
                                    <input type="number" step="0.01" min="0" class="form-control" name="discount_value" id="discount_value"
                                           value="{{ $bill ? ($bill->discount_type === 'percent' ? '' : $bill->discount_amount) : '0' }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold mb-1">Shipping</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" min="0" class="form-control" name="shipping_amount" id="shipping_amount"
                                           value="{{ $bill?->shipping_amount ?? '0' }}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold mb-1">Round Off</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">±₹</span>
                                    <input type="number" step="0.01" class="form-control" name="round_off" id="round_off"
                                           value="{{ $bill?->round_off ?? '0' }}">
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-12">
                                <div class="d-flex flex-wrap justify-content-lg-end gap-3 pt-1">
                                    <div class="small"><span class="text-muted">Subtotal:</span> <strong id="summary-subtotal">₹0.00</strong></div>
                                    <div class="small"><span class="text-muted">Tax:</span> <strong id="summary-tax">₹0.00</strong></div>
                                    <div class="small"><span class="text-muted">Discount:</span> <strong class="text-danger" id="summary-discount">−₹0.00</strong></div>
                                    <div><span class="text-muted">Net Total:</span> <strong class="text-success fs-5" id="summary-net-total">₹0.00</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="save-purchase-btn">
            {{ $bill ? 'Update Bill' : 'Save Purchase Bill' }}
        </button>
    </div>
</form>

<script>
window.purchaseMedicines = @json($medicines);
window.isEditMode = {{ $bill ? 'true' : 'false' }};
@if($bill)
window.editBillData = {
    subtotal: {{ $bill->subtotal }},
    tax: {{ $bill->tax_amount }},
    discount_type: '{{ $bill->discount_type ?? 'fixed' }}',
    discount_amount: {{ $bill->discount_amount }},
    shipping: {{ $bill->shipping_amount }},
    round_off: {{ $bill->round_off }},
    net_total: {{ $bill->net_total }},
};
@endif
</script>
