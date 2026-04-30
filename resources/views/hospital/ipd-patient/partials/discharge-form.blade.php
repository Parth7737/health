<div class="modal-header bg-success">
    <h5 class="modal-title">Discharge Patient</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="discharge-ipd-form" action="{{ route('hospital.ipd-patient.discharge', ['allocation' => $allocation->id]) }}">
    <div class="modal-body">
        <div class="alert alert-light border">
            <div><strong>{{ $allocation->patient?->name }}</strong></div>
            <div>Admission No: {{ $allocation->admission_no }}</div>
            <div>Outstanding Due: {{ number_format((float) $outstandingAmount, 2) }}</div>
        </div>

        @if(!$canDischarge)
            <div class="alert alert-danger">
                Please clear the outstanding IPD bill before discharge.
            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Discharge Date <span class="text-danger">*</span></label>
                <input type="text" id="ipd-discharge-date" name="discharge_date" class="form-control" value="{{ now()->format('d-m-Y H:i') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Discharge Status <span class="text-danger">*</span></label>
                <select class="form-select select2-modal" name="discharge_status">
                    <option value="recovered">Recovered</option>
                    <option value="referred">Referred</option>
                    <option value="lama">LAMA</option>
                    <option value="expired">Expired</option>
                    <option value="transferred">Transferred</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Discharge Notes</label>
                <textarea class="form-control" name="discharge_notes" rows="3" placeholder="Summary / advice / notes"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" {{ $canDischarge ? '' : 'disabled' }}>Complete Discharge</button>
    </div>
</form>