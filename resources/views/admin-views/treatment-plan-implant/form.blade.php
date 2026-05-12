<div class="modal-header bg-primary ">
    <h5 class="modal-title text-white">{{ !$id ? 'Add' : 'Edit'}} implant</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12 mb-3">
            <label class="form-label">Procedure</label>
            <select name="procedure_id" id="procedure_id" class="form-select">
                <option value="">—</option>
                @foreach($procedures as $pid => $pname)
                    <option value="{{ $pid }}" @selected(@$data->procedure_id == $pid)>{{ $pname }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Speciality</label>
            <select name="speciality_id" id="speciality_id" class="form-select">
                <option value="">—</option>
                @foreach($specialities as $sid => $sname)
                    <option value="{{ $sid }}" @selected(@$data->speciality_id == $sid)>{{ $sname }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" id="code" value="{{ @$data->code }}" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Multiplier</label>
            <input type="number" min="1" name="no_of_multiplier" id="no_of_multiplier" value="{{ old('no_of_multiplier', @$data->no_of_multiplier ?? 1) }}" class="form-control">
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price', @$data->price ?? 0) }}" class="form-control">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
