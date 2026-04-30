<div class="modal-header bg-primary">
    <h5 class="modal-title text-white" id="view_modal_dataModelLabel">{{ @$id ? 'Edit' : 'Add'}} Service</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" method="POST" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>