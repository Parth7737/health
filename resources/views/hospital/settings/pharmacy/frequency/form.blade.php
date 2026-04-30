<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Frequency</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Frequency</label>
            <input type="text" name="frequency" id="frequency" value="{{ @$data->frequency }}" class="form-control">
        </div>  
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>