<div class="modal-header bg-primary ">
    <h5 class="modal-title text-white">{{ !$id ? 'Add' : 'Edit'}} mapping</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12 mb-3">
            <label class="form-label">Add-on procedure <span class="text-danger">*</span></label>
            <select name="add_on_id" id="add_on_id" class="form-select" required>
                <option value="">—</option>
                @foreach($procedures as $pid => $pname)
                    <option value="{{ $pid }}" @selected(@$data->add_on_id == $pid)>{{ $pname }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Speciality <span class="text-danger">*</span></label>
            <select name="speciality_id" id="speciality_id" class="form-select" required>
                <option value="">—</option>
                @foreach($specialities as $sid => $sname)
                    <option value="{{ $sid }}" @selected(@$data->speciality_id == $sid)>{{ $sname }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
