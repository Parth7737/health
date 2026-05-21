<div class="modal-header bg-primary">
    <h5 class="modal-title text-white" id="view_modal_dataModelLabel">{{ @$id ? 'Edit' : 'Add'}} Eligibility Item</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="form-group mb-2">
            <label for="title">Title<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" id="title" value="{{ @$data->title }}" placeholder="Enter Title">
        </div>
        <div class="form-group mb-2">
            <label for="subtitle">Subtitle</label>
            <input type="text" class="form-control" name="subtitle" id="subtitle" value="{{ @$data->subtitle }}" placeholder="Enter Subtitle">
        </div>
        <div class="form-group mb-2">
            <label for="is_required">Is Required</label>
            <input type="checkbox" name="is_required" id="is_required" value="1" {{ @$data->is_required == 1 ? 'checked' : ''  }}>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
