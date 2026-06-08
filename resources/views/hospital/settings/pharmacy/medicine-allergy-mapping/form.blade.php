<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Medicine Allergy Mapping</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Medicine <span class="text-danger">*</span></label>
                <select name="medicine_id" id="medicine_id" class="form-control select2-modal" required>
                    <option value="">Select medicine</option>
                    @foreach($medicines as $med)
                        <option value="{{ $med->id }}" {{ (isset($data) && @$data->medicine_id == $med->id) ? 'selected' : '' }}>{{ $med->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-12 mb-3">
                <label class="form-label">Allergy / Allergen Category <span class="text-danger">*</span></label>
                <select name="allergy_id" id="allergy_id" class="form-control select2-modal" required>
                    <option value="">Select allergy category</option>
                    @foreach($allergies as $allergy)
                        <option value="{{ $allergy->id }}" {{ (isset($data) && @$data->allergy_id == $allergy->id) ? 'selected' : '' }}>{{ $allergy->name }} ({{ $allergy->description ?? 'No description' }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
