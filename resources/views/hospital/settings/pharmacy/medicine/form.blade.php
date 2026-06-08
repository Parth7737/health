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
                <select name="medicine_unit_id" id="medicine_unit_id" class="form-control select2-modal">
                    <option value="">Select unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ (isset($data) && @$data->medicine_unit_id == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
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

            <!-- Clinical & Safety Settings -->
            <div class="col-md-12 mt-3 mb-2">
                <h6 class="text-primary border-bottom pb-1">🛡️ Clinical & Safety Settings</h6>
            </div>

            <div class="col-md-4 mb-3 d-flex align-items-center">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_high_risk" value="0">
                    <input class="form-check-input" type="checkbox" name="is_high_risk" id="is_high_risk" value="1" {{ @$data->is_high_risk ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-danger" for="is_high_risk">🚨 Is High Risk?</label>
                </div>
            </div>

            <div class="col-md-4 mb-3 d-flex align-items-center">
                <div class="form-check form-switch">
                    <input type="hidden" name="requires_rx" value="0">
                    <input class="form-check-input" type="checkbox" name="requires_rx" id="requires_rx" value="1" {{ (!isset($data) || @$data->requires_rx) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="requires_rx">📄 Requires Rx?</label>
                </div>
            </div>

            <div class="col-md-4 mb-3 d-flex align-items-center">
                <div class="form-check form-switch">
                    <input type="hidden" name="weight_based_dose" value="0">
                    <input class="form-check-input" type="checkbox" name="weight_based_dose" id="weight_based_dose" value="1" {{ @$data->weight_based_dose ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="weight_based_dose">⚖️ Weight-Based Dose?</label>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <label class="form-label">Min Dose</label>
                <input type="number" step="0.01" name="min_dose" id="min_dose" value="{{ @$data->min_dose }}" class="form-control" placeholder="e.g. 250">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Max Dose</label>
                <input type="number" step="0.01" name="max_dose" id="max_dose" value="{{ @$data->max_dose }}" class="form-control" placeholder="e.g. 1000">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Max Daily Dose</label>
                <input type="number" step="0.01" name="max_daily_dose" id="max_daily_dose" value="{{ @$data->max_daily_dose }}" class="form-control" placeholder="e.g. 4000">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Dose Unit</label>
                <input type="text" name="dose_unit" id="dose_unit" value="{{ @$data->dose_unit }}" class="form-control" placeholder="e.g. mg, ml">
            </div>

            <div class="col-md-6 mb-2">
                <label class="form-label">Dose Per KG (if Weight-Based)</label>
                <input type="number" step="0.01" name="dose_per_kg" id="dose_per_kg" value="{{ @$data->dose_per_kg }}" class="form-control" placeholder="e.g. 5">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Pregnancy Risk Category</label>
                <select name="pregnancy_risk" id="pregnancy_risk" class="form-control">
                    <option value="">Select risk category</option>
                    <option value="safe" {{ @$data->pregnancy_risk == 'safe' ? 'selected' : '' }}>Safe</option>
                    <option value="caution" {{ @$data->pregnancy_risk == 'caution' ? 'selected' : '' }}>Caution</option>
                    <option value="moderate" {{ @$data->pregnancy_risk == 'moderate' ? 'selected' : '' }}>Moderate Risk</option>
                    <option value="high_risk" {{ @$data->pregnancy_risk == 'high_risk' ? 'selected' : '' }}>High Risk</option>
                    <option value="contraindicated" {{ @$data->pregnancy_risk == 'contraindicated' ? 'selected' : '' }}>Contraindicated</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="hidden" name="renal_adjustment_required" value="0">
                    <input class="form-check-input" type="checkbox" name="renal_adjustment_required" id="renal_adjustment_required" value="1" {{ @$data->renal_adjustment_required ? 'checked' : '' }}>
                    <label class="form-check-label" for="renal_adjustment_required">Renal adjustment required?</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="hidden" name="liver_adjustment_required" value="0">
                    <input class="form-check-input" type="checkbox" name="liver_adjustment_required" id="liver_adjustment_required" value="1" {{ @$data->liver_adjustment_required ? 'checked' : '' }}>
                    <label class="form-check-label" for="liver_adjustment_required">Liver adjustment required?</label>
                </div>
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