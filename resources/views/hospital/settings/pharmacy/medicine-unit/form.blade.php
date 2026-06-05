<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Medicine Unit</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12 mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control">
        </div>
        <div class="col-md-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch"
                    id="apply_frequency" name="apply_frequency" value="1"
                    {{ (!isset($data) || $data->apply_frequency) ? 'checked' : '' }}>
                <label class="form-check-label" for="apply_frequency">
                    Apply Frequency Multiplication
                </label>
            </div>
            <small class="text-muted d-block mt-1">
                <strong>ON</strong>: Dispense qty = Days × Frequency (e.g. Tablets, Capsules — 5 days × 3 times = 15 tabs)<br>
                <strong>OFF</strong>: Dispense qty = 1 (e.g. Creams, Syrups, Powders — dispense 1 pack regardless of frequency)
            </small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
