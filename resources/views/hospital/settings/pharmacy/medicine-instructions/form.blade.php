<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Medicine Instruction</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Instruction</label>
            <textarea name="instruction" id="instruction" class="form-control">{{ @$data->instruction }}</textarea>
        </div>
        <div class="col-md-12 mt-8">
            <label class="form-label">Meal Relation (for MAR scheduling)</label>
            @php $mealRelation = @$data->meal_relation ?: 'none'; @endphp
            <select name="meal_relation" id="meal_relation" class="form-control">
                <option value="none" {{ $mealRelation === 'none' ? 'selected' : '' }}>Any Time</option>
                <option value="before_food" {{ $mealRelation === 'before_food' ? 'selected' : '' }}>Before Food</option>
                <option value="after_food" {{ $mealRelation === 'after_food' ? 'selected' : '' }}>After Food</option>
                <option value="with_food" {{ $mealRelation === 'with_food' ? 'selected' : '' }}>With Food</option>
                <option value="empty_stomach" {{ $mealRelation === 'empty_stomach' ? 'selected' : '' }}>Empty Stomach</option>
            </select>
            <div class="fs-11 text-muted mt-4">Used for MAR time calculation only when frequency <b>MAR Schedule Times</b> are empty. Before/After/With food uses hospital breakfast, lunch, dinner settings.</div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>