<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Medicine Interaction</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Primary Medicine <span class="text-danger">*</span></label>
                <select name="medicine_id" id="medicine_id" class="form-control select2-modal" required>
                    <option value="">Select primary medicine</option>
                    @foreach($medicines as $med)
                        <option value="{{ $med->id }}" {{ (isset($data) && @$data->medicine_id == $med->id) ? 'selected' : '' }}>{{ $med->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">Interacting Medicine <span class="text-danger">*</span></label>
                <select name="interact_medicine_id" id="interact_medicine_id" class="form-control select2-modal" required>
                    <option value="">Select interacting medicine</option>
                    @foreach($medicines as $med)
                        <option value="{{ $med->id }}" {{ (isset($data) && @$data->interact_medicine_id == $med->id) ? 'selected' : '' }}>{{ $med->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Severity Level <span class="text-danger">*</span></label>
                <select name="severity" id="severity" class="form-control select2-modal" required>
                    <option value="">Select severity level</option>
                    <option value="minor" {{ (isset($data) && @$data->severity == 'minor') ? 'selected' : '' }}>Minor</option>
                    <option value="moderate" {{ (isset($data) && @$data->severity == 'moderate') ? 'selected' : '' }}>Moderate</option>
                    <option value="major" {{ (isset($data) && @$data->severity == 'major') ? 'selected' : '' }}>Major</option>
                    <option value="critical" {{ (isset($data) && @$data->severity == 'critical') ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Clinical Effect</label>
                <textarea name="clinical_effect" id="clinical_effect" class="form-control" rows="3" placeholder="Describe clinical consequences of co-administration (e.g., increased risk of bleeding)">{{ @$data->clinical_effect }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Recommendation / Intervention</label>
                <textarea name="recommendation" id="recommendation" class="form-control" rows="3" placeholder="Suggested pharmacist action (e.g., monitor INR, adjust dose, separate administration by 2 hours)">{{ @$data->recommendation }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
