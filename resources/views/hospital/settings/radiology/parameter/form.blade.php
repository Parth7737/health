<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Radiology Parameter</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control">
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Unit</label>
            <select name="radiology_unit_id" id="radiology_unit_id" class="form-control">
                <option value="">Select Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ (isset($data) && @$data->radiology_unit_id == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Range (text)</label>
            <input type="text" name="range" id="range" value="{{ @$data->range }}" class="form-control" placeholder="e.g. 10–15 mm">
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <label class="form-label">Min (normal)</label>
                <input type="number" step="0.0001" name="min_value" id="min_value" value="{{ @$data->min_value }}" class="form-control" placeholder="Optional — for numeric auto-flag">
            </div>
            <div class="col-md-6">
                <label class="form-label">Max (normal)</label>
                <input type="number" step="0.0001" name="max_value" id="max_value" value="{{ @$data->max_value }}" class="form-control">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <label class="form-label">Critical low</label>
                <input type="number" step="0.0001" name="critical_low" id="critical_low" value="{{ @$data->critical_low }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Critical high</label>
                <input type="number" step="0.0001" name="critical_high" id="critical_high" value="{{ @$data->critical_high }}" class="form-control">
            </div>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control">{{ @$data->description }}</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>