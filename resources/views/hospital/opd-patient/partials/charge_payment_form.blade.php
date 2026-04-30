<div class="modal-header">
    <h5 class="modal-title">{{ $title ?? 'Collect Payment | ' . $patient->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="collectPaymentForm" data-submit-url="{{ $submitUrl }}">
    <div class="modal-body">
        
        @if(!empty($contextNote))
            <!-- <div class="alert alert-light border mb-3">{{ $contextNote }}</div> -->
        @endif

        @foreach(($chargeIds ?? []) as $chargeId)
            <input type="hidden" name="charge_ids[]" value="{{ $chargeId }}">
        @endforeach

        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">Pay Amount</label>
                <input type="number" step="0.01" min="0" max="{{ number_format((float) ($outstandingAmount ?? 0), 2, '.', '') }}" class="form-control" name="amount" value="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount Amount</label>
                <input type="number" step="0.01" min="0" max="{{ number_format((float) ($outstandingAmount ?? 0), 2, '.', '') }}" class="form-control" name="discount_amount" value="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">Payment Mode</label>
                <input type="text" class="form-control" name="payment_mode" placeholder="Cash/Card/UPI">
            </div>
            <div class="col-md-3">
                <label class="form-label">Reference</label>
                <input type="text" class="form-control" name="reference" placeholder="Txn Ref">
            </div>
            <div class="col-md-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="2"></textarea>
            </div>
            <div class="col-md-12">
                <div class="alert alert-light border mb-0">
                    <div><strong>Total Pending Due:</strong> {{ number_format((float) ($outstandingAmount ?? 0), 2) }}</div>
                    <div><strong>Total Settlement (Pay + Discount):</strong> <span id="collectPaymentSettlementValue">0.00</span></div>
                </div>
            </div>
        </div>

        <hr>
        <h6 class="mb-2">Unpaid/Partial Charges</h6>
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
                        <th>Status</th>
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
                            <td>{{ ucfirst($charge->payment_status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No pending charge found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Apply Settlement</button>
    </div>
</form>

<script>
    (function () {
        var $form = $('#collectPaymentForm');
        var $pay = $form.find('input[name="amount"]');
        var $discount = $form.find('input[name="discount_amount"]');
        var $settlement = $('#collectPaymentSettlementValue');

        function toAmount(value) {
            var parsed = parseFloat(value || 0);
            return isNaN(parsed) ? 0 : parsed;
        }

        function syncSettlement() {
            var total = toAmount($pay.val()) + toAmount($discount.val());
            $settlement.text(total.toFixed(2));
        }

        $pay.off('input.settlement').on('input.settlement', syncSettlement);
        $discount.off('input.settlement').on('input.settlement', syncSettlement);
        syncSettlement();
    })();
</script>
