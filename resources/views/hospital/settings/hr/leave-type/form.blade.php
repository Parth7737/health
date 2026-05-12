<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Leave Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12 mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control">
        </div>
        <div class="col-md-12 mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_paid_time_off" id="is_paid_time_off" value="1"
                    {{ old('is_paid_time_off', is_object($data) && !empty($data->is_paid_time_off)) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_paid_time_off">Paid time off (no salary deduction up to annual limit, calendar-year order)</label>
            </div>
        </div>
        <div class="col-md-12">
            <label class="form-label">Annual entitlement (days per staff)</label>
            <input type="number" step="0.5" min="0" max="366" name="annual_entitlement_days" id="annual_entitlement_days"
                value="{{ old('annual_entitlement_days', (is_object($data) && isset($data->annual_entitlement_days)) ? $data->annual_entitlement_days : 0) }}" class="form-control">
            <small class="text-muted">Same for all employees. Yearly balance rows are created on 1 January (scheduler) or by running the Artisan command hr:provision-leave-balances.</small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
