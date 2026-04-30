<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Doctor OPD Charge</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{$id}}">

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Doctor <span class="text-danger">*</span></label>
                <select name="doctor_id" id="doctor_id" class="form-control select2-modal">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ (string) old('doctor_id', @$data->doctor_id) === (string) $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">New Case Charge <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="charge" id="charge" value="{{ old('charge', @$data->charge) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Follow-up Charge</label>
                <input type="number" step="0.01" min="0" name="follow_up_charge" id="follow_up_charge" value="{{ old('follow_up_charge', @$data->follow_up_charge) }}" class="form-control">
                <small class="text-muted">Leave blank if same charge should continue on every visit.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Follow-up Validity Window (Months)</label>
                <input type="number" min="1" max="24" name="follow_up_validity_months" id="follow_up_validity_months" value="{{ old('follow_up_validity_months', @$data->follow_up_validity_months) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Emergency Charge</label>
                <input type="number" step="0.01" min="0" name="emergency_charge" id="emergency_charge" value="{{ old('emergency_charge', @$data->emergency_charge) }}" class="form-control">
                <small class="text-muted">Emergency visit type par yahi doctor charge apply hoga.</small>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
            <h6 class="mb-0">TPA Wise Charges</h6>
            <button type="button" id="apply-all-tpa-charges" class="btn btn-sm btn-outline-primary">Apply To All</button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>TPA Name</th>
                        <th style="width: 220px;">OPD Charge</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tpas as $index => $tpa)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $tpa->name }}</td>
                            <td>
                                <input type="hidden" name="tpa_charges[{{ $index }}][tpa_id]" value="{{ $tpa->id }}">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="tpa_charges[{{ $index }}][charge]"
                                    value="{{ old('tpa_charges.' . $index . '.charge', $tpaCharges[$tpa->id] ?? '') }}"
                                    class="form-control tpa-charge-input"
                                >
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No TPA records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
