<div class="modal-header">
    <h5 class="modal-title">Transfer Bed</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $tfPatientName = $allocation->patient?->name ?: '—';
    $tfBedCode = $allocation->bed?->bed_code ?: '—';
    $tfWardName = $allocation->bed?->room?->ward?->ward_name ?: '—';
    $tfRoomNo = $allocation->bed?->room?->room_number ?: '—';
    $tfBedType = $allocation->bed?->bedType?->type_name ?: null;
    $tfAdmissionNo = $allocation->admission_no ?: null;
@endphp

<form method="POST" id="transfer-ipd-form" action="{{ route('hospital.ipd-patient.transfer', ['allocation' => $allocation->id]) }}">
    <div class="modal-body">
        <div class="border rounded-3 mb-3 overflow-hidden bg-white shadow-sm">
            <div class="px-3 py-2 border-bottom small fw-semibold text-secondary text-uppercase" style="letter-spacing:.04em;font-size:10.5px;background:linear-gradient(180deg,#f4f7fb,#eef2f7)">
                Current assignment
            </div>
            <div class="p-3">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 pb-3 mb-3 border-bottom">
                    <div class="flex-grow-1" style="min-width:12rem">
                        <div class="text-muted small mb-1">Patient</div>
                        <div class="fw-semibold text-dark" style="font-size:1.05rem">{{ $tfPatientName }}</div>
                    </div>
                    @if($tfAdmissionNo)
                        <div style="min-width:10rem">
                            <div class="text-muted small mb-1">Admission no.</div>
                            <div class="font-monospace small fw-semibold text-body text-break">{{ $tfAdmissionNo }}</div>
                        </div>
                    @endif
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Bed code</div>
                        <div class="fw-semibold text-dark">{{ $tfBedCode }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Ward</div>
                        <div class="fw-semibold text-dark">{{ $tfWardName }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Room</div>
                        <div class="fw-semibold text-dark">{{ $tfRoomNo }}</div>
                    </div>
                    @if($tfBedType)
                        <div class="col-6 col-md-3">
                            <div class="text-muted small mb-1">Bed type</div>
                            <div class="fw-semibold textimage.png-dark">{{ $tfBedType }}</div>
                        </div>
                    @endif
                </div>
            </div>
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
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm">Transfer</button>
    </div>
</form>