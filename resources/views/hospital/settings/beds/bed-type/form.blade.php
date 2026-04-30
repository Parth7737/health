<div class="modal-header">
    <h5 class="modal-title">{{ !$id ? 'Add' : 'Edit' }} Bed Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Type Name <span class="text-danger">*</span></label>
                <input type="text" name="type_name" value="{{ old('type_name', $data->type_name ?? '') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Charge Master <span class="text-danger">*</span></label>
                <select name="charge_master_id" id="charge_master_id" class="form-control select2-modal">
                    <option value="">Select Charge Master</option>
                    @foreach($chargeMasters as $chargeMaster)
                        <option value="{{ $chargeMaster->id }}"
                            data-standard-rate="{{ number_format((float) $chargeMaster->standard_rate, 2, '.', '') }}"
                            data-tpa-count="{{ (int) ($chargeMaster->tpa_rates_count ?? 0) }}"
                            {{ (int) old('charge_master_id', $data->charge_master_id ?? 0) === (int) $chargeMaster->id ? 'selected' : '' }}>
                            {{ $chargeMaster->name }} ({{ $chargeMaster->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Base Charge</label>
                <input type="number" step="0.01" min="0" id="base_charge_preview" value="{{ number_format((float) old('base_charge', $data->base_charge ?? 0), 2, '.', '') }}" class="form-control" readonly>
                <small class="text-muted">Auto-synced from selected charge master standard rate.</small>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $data->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $data->description ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>