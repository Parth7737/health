<div class="modal-header bg-info">
    <h5 class="modal-title">{{ $title ?? 'Apply Discount | ' . $patient->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="applyDiscountForm" data-submit-url="{{ $submitUrl }}">
    <div class="modal-body">
        @if(!empty($contextNote))
            <div class="alert alert-light border mb-3">{{ $contextNote }}</div>
        @endif

        @foreach(($chargeIds ?? []) as $chargeId)
            <input type="hidden" name="charge_ids[]" value="{{ $chargeId }}">
        @endforeach

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Discount Amount</label>
                <input type="number" step="0.01" min="0.01" max="{{ number_format((float) ($outstandingAmount ?? 0), 2, '.', '') }}" class="form-control" name="amount" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Reason / Notes</label>
                <input type="text" class="form-control" name="notes" placeholder="Discount reason">
            </div>
            <div class="col-md-12">
                <div class="alert alert-light border mb-0">
                    <strong>Total Pending Due:</strong> {{ number_format((float) ($outstandingAmount ?? 0), 2) }}
                </div>
            </div>
        </div>

        <hr>
        <h6 class="mb-2">Eligible Charges</h6>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Module</th>
                        <th>Particular</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($charges as $charge)
                        @php $due = (float) $charge->amount - (float) $charge->paid_amount; @endphp
                        <tr>
                            <td>{{ optional($charge->charged_at)->format('d-m-Y H:i') ?? '-' }}</td>
                            <td>{{ strtoupper($charge->module ?? $charge->charge_category ?? '-') }}</td>
                            <td>{{ $charge->particular }}</td>
                            <td>{{ number_format((float) $charge->amount, 2) }}</td>
                            <td>{{ number_format((float) $charge->paid_amount, 2) }}</td>
                            <td>{{ number_format(max(0, $due), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No pending charge found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-info">Apply Discount</button>
    </div>
</form>
