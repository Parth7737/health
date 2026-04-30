<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Floor</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Building <span class="text-danger">*</span></label>
                <select name="building_id" id="building_id" class="form-control select2-modal" >
                    <option value="">-- Select Building --</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ @$data->building_id == $building->id ? 'selected' : '' }}>{{ $building->building_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Floor Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ @$data->name }}" class="form-control" placeholder="e.g., Ground Floor, First Floor" >
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>