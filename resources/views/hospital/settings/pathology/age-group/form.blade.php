<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Age Group</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Title</label>
            <input type="text" name="title" id="title" value="{{ @$data->title }}" class="form-control">
        </div>
        <div class="col-md-6 mt-2">
            <label class="form-label">From Age</label>
            <input type="number" name="from_age" id="from_age" value="{{ @$data->from_age }}" class="form-control">
        </div>
        <div class="col-md-6 mt-2">
            <label class="form-label">To Age</label>
            <input type="number" name="to_age" id="to_age" value="{{ @$data->to_age }}" class="form-control">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>