<div class="modal-header">
    <h5 class="modal-title">{{ !$id ? 'Add' : 'Edit' }} Ward</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Floor<span class="text-danger">*</span></label>
                <select name="floor_id" class="form-control select2-modal" >
                    <option value="">Select Floor</option>
                    @foreach($floors as $floor)
                        <option value="{{ $floor->id }}" @selected(($data->floor_id ?? null) == $floor->id)>
                            {{ $floor->building?->building_name }} - {{ $floor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ward Name <span class="text-danger">*</span></label>
                <input type="text" name="ward_name" value="{{ $data->ward_name ?? '' }}" placeholder="General / ICU / Private" class="form-control" >
            </div>
            <div class="col-md-6">
                <label class="form-label">Ward Code <span class="text-danger">*</span></label>
                <input type="text" name="ward_code" value="{{ $data->ward_code ?? '' }}" class="form-control" >
            </div>
            <div class="col-md-6">
                <label class="form-label">Total Beds <span class="text-danger">*</span></label>
                <input type="number" min="0" name="total_beds" value="{{ $data->total_beds ?? 0 }}" class="form-control" >
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $data->description ?? '' }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="ward_active" value="1" {{ (!isset($data) || $data->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="ward_active">Active</label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
