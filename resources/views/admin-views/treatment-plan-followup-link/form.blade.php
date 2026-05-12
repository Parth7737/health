<div class="modal-header bg-primary ">
    <h5 class="modal-title text-white">{{ !$id ? 'Add' : 'Edit'}} follow-up link</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12 mb-3">
            <label class="form-label">Procedure <span class="text-danger">*</span></label>
            <select name="procedure_id" id="procedure_id" class="form-select" required>
                <option value="">—</option>
                @foreach($procedures as $pid => $pname)
                    <option value="{{ $pid }}" @selected(@$data->procedure_id == $pid)>{{ $pname }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Follow-up procedure <span class="text-danger">*</span></label>
            <select name="follow_up_id" id="follow_up_id" class="form-select" required>
                <option value="">—</option>
                @foreach($procedures as $pid => $pname)
                    <option value="{{ $pid }}" @selected(@$data->follow_up_id == $pid)>{{ $pname }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
