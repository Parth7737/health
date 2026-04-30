<div class="modal-header">
    <h5 class="modal-title">Add Charge | {{ $allocation->admission_no }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="addIpdChargeForm" data-submit-url="{{ $submitUrl }}">
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Other Charge Master <span class="text-danger">*</span></label>
                <select class="form-select select2-modal" name="charge_master_id" id="ipd_add_charge_master" required>
                    <option value="">Select Charge Master</option>
                    @foreach($chargeMasters as $chargeMaster)
                        <option value="{{ $chargeMaster->id }}" data-rate="{{ number_format((float) $chargeMaster->standard_rate, 2, '.', '') }}">
                            {{ $chargeMaster->name }} ({{ $chargeMaster->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="quantity" min="1" step="0.01" value="1" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit Rate <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="unit_rate" min="0" step="0.01" value="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Charged At</label>
                <input type="text" class="form-control" name="charged_at" id="ipd-add-charge-datetime" value="{{ now()->format('d-m-Y H:i') }}">
            </div>
            <div class="col-md-12">
                <label class="form-label">Particular (Optional)</label>
                <input type="text" class="form-control" name="particular" placeholder="Optional custom particular">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Add Charge</button>
    </div>
</form>

<script>
    (function () {
        var $master = $('#ipd_add_charge_master');
        var $rate = $('#addIpdChargeForm [name="unit_rate"]');

        $master.off('change.ipdcharge').on('change.ipdcharge', function () {
            var selectedRate = $(this).find('option:selected').data('rate');
            if (selectedRate !== undefined && selectedRate !== null && selectedRate !== '') {
                $rate.val(selectedRate);
            }
        });

        if (window.flatpickr && document.getElementById('ipd-add-charge-datetime')) {
            flatpickr('#ipd-add-charge-datetime', { enableTime: true, dateFormat: 'd-m-Y H:i' });
        }
    })();
</script>
