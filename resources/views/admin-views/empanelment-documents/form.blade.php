<div class="modal-header bg-primary ">
    <h5 class="modal-title text-white" id="view_modal_dataModelLabel">{{ @$id ? 'Add' : 'Edit'}} Hospital Empanelment Document</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" method="POST" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control">
        </div> 
        <div class="col-md-12">
            <div class="form-check form-switch form-check-inline mt-2">
                <input class="form-check-input switch-primary check-size" name="is_required" value="1" id="is_required" type="checkbox" role="switch" {{ @$data->is_required == '1' ? 'checked' : '' }}  >
                <p><i data-feather="info"></i> Is Required?</p>
            </div>
        </div>    
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>