<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Visitor Purpose</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control" placeholder="" required>
            <span class="text-danger">
                <span class="err_name"></span>
            </span>
        </div>
        <div class="col-md-12 mt-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-control" required>
                <option value="">-- Select Status --</option>
                <option value="active" {{ @$data->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ @$data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <span class="text-danger">
                <span class="err_status"></span>
            </span>
        </div>  
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
