<div class="modal-header bg-primary">
    <h5 class="modal-title text-white" id="view_modal_dataModelLabel">{{ @$id ? 'Edit' : 'Add'}} License Type</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" method="POST" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="form-group mb-2">
            <label for="name">Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="name" value="{{ @$data->name }}" placeholder="Enter License Type">
        </div>
        <div class="form-group mb-2">
            <label for="license_id">License</label>
            @php $licenses = App\Models\License::get(); @endphp
            <select class="form-control" name="license_id" id="license_id">
                <option value="">Select License</option>
                @foreach($licenses as $license)
                <option value="{{ $license->id }}" {{ @$data->license_id == $license->id ? 'selected' : ''  }}>{{ $license->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2">
            <label for="is_required">Is Required</label>
            <input type="checkbox" name="is_required" id="is_required" value="1" {{ @$data->is_required == 1 ? 'checked' : ''  }}>
        </div>
        <div class="form-group mb-2">
            <label for="document_required">Document Required</label>
            <input type="checkbox" name="document_required" id="document_required" value="1" {{ @$data->document_required == 1 ? 'checked' : ''  }}>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>