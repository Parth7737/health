<div class="modal-header">
    <h5 class="modal-title">{{ !$id ? 'Add' : 'Edit' }} Room</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">
        <input type="hidden" id="is_bulk" name="is_bulk" value="{{ $isBulk ? 1 : 0 }}">

        <div class="row g-3">
            @if(!$id)
                <div class="col-md-12">
                    <label class="form-label">Creation Mode</label>
                    <select id="room_creation_mode" class="form-control">
                        <option value="single" {{ $isBulk ? '' : 'selected' }}>Single Room</option>
                        <option value="bulk" {{ $isBulk ? 'selected' : '' }}>Bulk Rooms (Range)</option>
                    </select>
                </div>
            @endif

            <div class="col-md-6">
                <label class="form-label">Ward <span class="text-danger">*</span></label>
                <select name="ward_id" class="form-control select2-modal" >
                    <option value="">Select Ward</option>
                    @foreach($wards as $ward)
                        <option value="{{ $ward->id }}" @selected(($data->ward_id ?? null) == $ward->id)>
                            {{ $ward->floor?->building?->building_name }} - {{ $ward->ward_name }} ({{ $ward->floor?->name }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 room-single-field" style="display: {{ $isBulk ? 'none' : 'block' }};">
                <label class="form-label">Room Number <span class="text-danger">*</span></label>
                <input type="text" name="room_number" value="{{ $data->room_number ?? '' }}" class="form-control" >
            </div>
            <div class="col-md-6 room-single-field" style="display: {{ $isBulk ? 'none' : 'block' }};">
                <label class="form-label">Room Code <span class="text-danger">*</span></label>
                <input type="text" name="room_code" value="{{ $data->room_code ?? '' }}" class="form-control" >
            </div>

            <div class="col-md-4 room-bulk-field" style="display: {{ $isBulk ? 'block' : 'none' }};">
                <label class="form-label">Room Code Prefix <span class="text-danger">*</span></label>
                <input type="text" name="room_code_prefix" class="form-control" placeholder="RM" value="{{ old('room_code_prefix', 'RM') }}">
            </div>
            <div class="col-md-4 room-bulk-field" style="display: {{ $isBulk ? 'block' : 'none' }};">
                <label class="form-label">From Number <span class="text-danger">*</span></label>
                <input type="number" min="1" name="bulk_from" class="form-control" value="{{ old('bulk_from') }}">
            </div>
            <div class="col-md-4 room-bulk-field" style="display: {{ $isBulk ? 'block' : 'none' }};">
                <label class="form-label">To Number <span class="text-danger">*</span></label>
                <input type="number" min="1" name="bulk_to" class="form-control" value="{{ old('bulk_to') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Bed Capacity <span class="text-danger">*</span></label>
                <input type="number" min="1" max="50" name="bed_capacity" value="{{ $data->bed_capacity ?? 1 }}" class="form-control" >
            </div>

            @if(!$id)
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="generate_beds" id="generate_beds" value="1" checked>
                        <label class="form-check-label" for="generate_beds">Auto-generate beds using capacity</label>
                    </div>
                </div>
                <div class="col-md-6 generate-beds-field">
                    <label class="form-label">Default Bed Type for Auto Beds</label>
                    <select name="bed_type_id" class="form-control select2-modal">
                        <option value="">Select Bed Type</option>
                        @foreach($bedTypes as $bedType)
                            <option value="{{ $bedType->id }}">{{ $bedType->type_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ $data->notes ?? '' }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="room_active" value="1" {{ (!isset($data) || $data->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="room_active">Active</label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
