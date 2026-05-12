<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit' }} training category</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">
        <div class="col-md-12 mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control" maxlength="150" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="2" maxlength="500">{{ @$data->description }}</textarea>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" id="sort_order" min="0" max="9999" class="form-control"
                value="{{ old('sort_order', is_object($data) && isset($data->sort_order) ? $data->sort_order : 0) }}">
        </div>
        <div class="col-md-12 mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', is_object($data) && ($data->is_active ?? true)) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active (shown in training schedule dropdown)</label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
