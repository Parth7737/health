<div class="modal-header bg-warning">
    <h5 class="modal-title text-dark">Adjust Bad Stock</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="bad-stock-form">
    <div class="modal-body">
        <input type="hidden" name="stock_batch_id" value="{{ $batch->id }}">
        <div class="mb-2">
            <label class="form-label">Medicine</label>
            <input type="text" class="form-control" value="{{ $batch->medicine?->name }}" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">Batch No</label>
            <input type="text" class="form-control" value="{{ $batch->batch_no }}" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">Available Qty</label>
            <input type="text" class="form-control" value="{{ $batch->available_qty }}" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">Bad Qty <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" max="{{ $batch->available_qty }}" class="form-control" name="quantity" required>
        </div>
        <div>
            <label class="form-label">Reason</label>
            <input type="text" class="form-control" name="reason" placeholder="damaged / broken / leakage">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning">Adjust</button>
    </div>
</form>