<div class="modal-header">
    <h5 class="modal-title" id="addAppointmentModalLabel">{{ !$id ? 'Add' : 'Edit' }} Appointment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" name="id" value="{{ $id }}">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" name="patient_phone" class="form-control" value="{{ $data->patient_phone ?? '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="text" name="appointment_date" class="form-control" value="{{ !empty($data?->appointment_date) ? \Carbon\Carbon::parse($data->appointment_date)->format('Y-m-d') : '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $data->name ?? '' }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <select class="form-select" name="gender">
                    <option value="">Select</option>
                    <option value="Male" {{ ($data->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ ($data->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ ($data->gender ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Age</label>
                <input type="number" name="age" class="form-control" min="0" max="120" value="{{ $data->age ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Email</label>
                <input type="email" name="patient_email" class="form-control" value="{{ $data->patient_email ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Doctor</label>
                <select class="form-select select2-modal" name="doctor_id" id="appointment-doctor-id">
                    <option value="">Select</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ (string) ($data->doctor_id ?? '') === (string) $doctor->id ? 'selected' : '' }}>{{ $doctor->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Slot</label>
                <select class="form-select select2-modal" name="appointment_slot" id="appointment-slot">
                    <option value="">Select Slot</option>
                    @if(!empty($data?->appointment_slot))
                        <option value="{{ $data->appointment_slot }}" selected>{{ $data->appointment_slot }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Appointment Priority</label>
                <select class="form-select select2-modal" name="priority">
                    <option value="">Select Priority</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority }}" {{ ($data->priority ?? '') === $priority ? 'selected' : '' }}>{{ $priority }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Live Consultant</label>
                <select class="form-select" name="live_consultation">
                    <option value="No" {{ ($data->live_consultation ?? 'No') === 'No' ? 'selected' : '' }}>No</option>
                    <option value="Yes" {{ ($data->live_consultation ?? 'No') === 'Yes' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                @php
                    $displayStatus = ($data->status ?? 'Pending') === 'Confirmed' ? 'Approved' : ($data->status ?? 'Pending');
                @endphp
                <select class="form-select" name="status">
                    <option value="Pending" {{ $displayStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ $displayStatus === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Cancelled" {{ $displayStatus === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Note</label>
                <textarea class="form-control" name="notes" rows="2">{{ $data->notes ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
