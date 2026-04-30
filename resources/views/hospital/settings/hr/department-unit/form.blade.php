<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Department Unit</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Department</label>
            <select name="department_id" id="department_id" class="form-control select2-modal">
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ (isset($data) && @$data->department_id == $department->id) ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Floor</label>
            <select name="floor_id" id="floor_id" class="form-control select2-modal">
                <option value="">Select Floor</option>
                @foreach($floors as $floor)
                    <option value="{{ $floor->id }}" @selected(($data->floor_id ?? null) == $floor->id)>
                        {{ $floor->building?->building_name }} - {{ $floor->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Unit Incharge</label>
            <select name="unit_incharge_id" id="unit_incharge_id" class="form-control select2-modal">
                <option value="">Select Staff</option>
                @foreach($staff as $s)
                    <option value="{{ $s->id }}" {{ (isset($data) && @$data->unit_incharge_id == $s->id) ? 'selected' : '' }}>{{ $s->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control">
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Is Video Consultation</label>
            <select name="is_video_consultation" id="is_video_consultation" class="form-control">
                <option value="No" {{ (isset($data) && @$data->is_video_consultation == 'No') ? 'selected' : '' }}>No</option>
                <option value="Yes" {{ (isset($data) && @$data->is_video_consultation == 'Yes') ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Daily Capacity</label>
            <input type="number" name="daily_capacity" id="daily_capacity" value="{{ @$data->daily_capacity }}" class="form-control" min="0">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>