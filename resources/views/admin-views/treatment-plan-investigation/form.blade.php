<div class="modal-header bg-primary ">
    <h5 class="modal-title text-white">{{ !$id ? 'Add' : 'Edit'}} investigation</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12 mb-3">
            <label class="form-label">Name</label>
            <textarea name="name" id="name" class="form-control" rows="2">{{ @$data->name }}</textarea>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" id="code" value="{{ @$data->code }}" class="form-control">
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Scheme type</label>
            <select name="scheme_type_id" class="form-select select2">
                <option value="">—</option>
                @foreach($schemeTypes as $stid => $stname)
                    <option value="{{ $stid }}" @selected(old('scheme_type_id', @$data->scheme_type_id) == $stid)>{{ $stname }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select select2">
                <option value="">—</option>
                <option value="Pre" @selected(old('type', @$data->type) == 'Pre')>Pre</option>
                <option value="Post" @selected(old('type', @$data->type) == 'Post')>Post</option>
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Is required</label>
            <input type="checkbox" name="is_required" id="is_required" value="1" {{ @$data->is_required == 1 ? 'checked' : ''  }}>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
