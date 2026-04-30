<div class="modal-header">
    <h5 class="modal-title">{{ !$id ? 'Add' : 'Edit' }} Building</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Building Name</label>
                <input type="text" name="building_name" value="{{ $data->building_name ?? '' }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Building Code</label>
                <input type="text" name="building_code" value="{{ $data->building_code ?? '' }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Floors Count</label>
                <input type="number" min="1" name="floors_count" value="{{ $data->floors_count ?? 1 }}" class="form-control">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $data->description ?? '' }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
