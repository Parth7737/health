<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Medicine</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Category</label>
                <select name="medicine_category_id" id="medicine_category_id" class="form-control select2-modal">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (isset($data) && @$data->medicine_category_id == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Generic Name</label>
                <input type="text" name="generic_name" id="generic_name" value="{{ @$data->generic_name }}" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Company</label>
                <input type="text" name="company" id="company" value="{{ @$data->company }}" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Unit</label>
                <input type="text" name="unit" id="unit" value="{{ @$data->unit }}" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Composition</label>
                <input type="text" name="composition" id="composition" value="{{ @$data->composition }}" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Min Level</label>
                <input type="number" name="min_level" id="min_level" value="{{ @$data->min_level }}" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Reorder Level</label>
                <input type="number" name="reorder_level" id="reorder_level" value="{{ @$data->reorder_level }}" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">VAT</label>
                <input type="number" name="vat" id="vat" value="{{ @$data->vat }}" class="form-control">
            </div>
            <div class="col-md-12 mb-2">
                <label class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ @$data->description }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>