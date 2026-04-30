<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Allergy Reaction</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" method="POST" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            @php $allergies = App\Models\Allergy::get() @endphp
            <label class="form-label">Allergy</label>
            <select name="allergy_id" class="form-control select2">
                <option value="">Select</option>
                @foreach($allergies as $allergy)
                    <option value="{{ $allergy->id }}" {{ @$data->allergy_id == $allergy->id?"selected":"" }}>{{ $allergy->name }}</option>
                @endforeach
            </select>
        </div>  
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