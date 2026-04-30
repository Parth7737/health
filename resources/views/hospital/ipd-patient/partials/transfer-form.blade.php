<div class="modal-header bg-warning">
    <h5 class="modal-title text-dark">Transfer Bed</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="transfer-ipd-form" action="{{ route('hospital.ipd-patient.transfer', ['allocation' => $allocation->id]) }}">
    <div class="modal-body">
        <div class="alert alert-light border">
            <strong>{{ $allocation->patient?->name }}</strong><br>
            Current Bed: {{ $allocation->bed?->bed_code }} | {{ $allocation->bed?->room?->ward?->ward_name }} / {{ $allocation->bed?->room?->room_number }}
        </div>

        <div class="mb-3">
            <label class="form-label">New Bed <span class="text-danger">*</span></label>
            <select class="form-select select2-modal" name="new_bed_id">
                <option value="">Select</option>
                @foreach($availableBeds as $bed)
                    <option value="{{ $bed->id }}">
                        {{ $bed->bed_code }} | {{ $bed->room?->ward?->ward_name ?? '-' }} / {{ $bed->room?->room_number ?? '-' }} | {{ $bed->bedType?->type_name ?? '-' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-0">
            <label class="form-label">Transfer Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" name="transfer_reason" rows="3" placeholder="Reason for transfer"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning">Transfer</button>
    </div>
</form>