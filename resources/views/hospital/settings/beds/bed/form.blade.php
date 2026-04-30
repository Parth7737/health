<div class="modal-header">
    <h5 class="modal-title">{{ !$id ? 'Add' : 'Edit' }} Bed</h5>
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
                    <select id="creation_mode" class="form-control">
                        <option value="single" {{ $isBulk ? '' : 'selected' }}>Single Bed</option>
                        <option value="bulk" {{ $isBulk ? 'selected' : '' }}>Bulk Beds (Range)</option>
                    </select>
                </div>
            @endif

            <div class="col-md-6">
                <label class="form-label">Room</label>
                <select name="room_id" class="form-control select2-modal">
                    <option value="">Select Room</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}"
                            data-capacity="{{ (int) $room->bed_capacity }}"
                            data-used="{{ (int) ($room->beds_count ?? 0) }}"
                            @selected(($data->room_id ?? null) == $room->id)>
                            {{ $room->room_number }} ({{ (int) ($room->beds_count ?? 0) }}/{{ (int) $room->bed_capacity }})
                        </option>
                    @endforeach
                </select>
                <small id="room_capacity_hint" class="text-muted"></small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Bed Type</label>
                <select name="bed_type_id" class="form-control select2-modal">
                    <option value="">Select Type</option>
                    @foreach($bedTypes as $bedType)
                        <option value="{{ $bedType->id }}" @selected(($data->bed_type_id ?? null) == $bedType->id)>{{ $bedType->type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 single-bed-fields" style="display: {{ $isBulk ? 'none' : 'block' }};">
                <label class="form-label">Bed Number</label>
                <input type="text" name="bed_number" value="{{ $data->bed_number ?? '' }}" class="form-control">
            </div>

            <div class="col-md-3 bulk-bed-fields" style="display: {{ $isBulk ? 'block' : 'none' }};">
                <label class="form-label">From Number</label>
                <input type="number" min="1" name="bulk_from" class="form-control" value="{{ old('bulk_from') }}">
            </div>

            <div class="col-md-3 bulk-bed-fields" style="display: {{ $isBulk ? 'block' : 'none' }};">
                <label class="form-label">To Number</label>
                <input type="number" min="1" name="bulk_to" class="form-control" value="{{ old('bulk_to') }}">
            </div>

            @if($id)
                <div class="col-md-6">
                    <label class="form-label">Barcode</label>
                    <div class="border rounded p-2 text-center">
                        <img src="{{ route('hospital.settings.beds.bed.barcode', ['bed' => $id]) }}" alt="Bed Barcode" style="max-width: 220px;">
                        <div class="small text-muted mt-1">{{ $data->bed_code }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Current Status</label>
                    <div>
                        <span class="badge" style="background: {{ $data->bedStatus?->color_code ?? '#6c757d' }};">
                            {{ $data->bedStatus?->status_name ?? 'Unknown' }}
                        </span>
                    </div>
                    <small class="text-muted">Status change Bed Dashboard se manage karein.</small>
                </div>
            @endif

            <div class="col-md-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ $data->notes ?? '' }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
