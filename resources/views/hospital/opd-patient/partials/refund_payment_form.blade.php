<div class="modal-header bg-warning">
    <h5 class="modal-title text-dark">{{ $title ?? 'Refund Advance | ' . $patient->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="collectPaymentForm" data-submit-url="{{ $submitUrl }}">
    <div class="modal-body">
        <div class="alert alert-warning border">
            <strong>Available Advance/Credit:</strong> {{ number_format((float) ($availableCredit ?? 0), 2) }}
        </div>

        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Refund Amount</label>
                <input type="number" step="0.01" min="0.01" max="{{ number_format((float) ($availableCredit ?? 0), 2, '.', '') }}" class="form-control" name="amount" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Refund Mode</label>
                <input type="text" class="form-control" name="payment_mode" placeholder="Cash/UPI/Bank Transfer">
            </div>
            <div class="col-md-4">
                <label class="form-label">Reference</label>
                <input type="text" class="form-control" name="reference" placeholder="Refund Ref">
            </div>
            <div class="col-md-12">
                <label class="form-label">Reason / Notes</label>
                <textarea class="form-control" name="notes" rows="2" placeholder="Reason for refund"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning">Confirm Refund</button>
    </div>
</form>
