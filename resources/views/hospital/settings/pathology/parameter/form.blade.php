<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Pathology Parameter</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        
        <!-- Basic Info -->
        <div class="col-md-12">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ @$data->name }}" class="form-control" required>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Unit</label>
            <select name="pathology_unit_id" id="pathology_unit_id" class="form-control">
                <option value="">Select Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ (isset($data) && @$data->pathology_unit_id == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Range (Display Text)</label>
            <input type="text" name="range" id="range" value="{{ @$data->range }}" class="form-control" placeholder="e.g., M: 13-17 | F: 12-15">
        </div>

        <hr class="my-3">
        <h6 class="mb-3">Reference Values (for auto-flagging)</h6>

        <!-- Applicable Gender Selection -->
        <div class="col-md-12 mb-3">
            <label class="form-label">Applicable For <span class="text-danger">*</span></label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="applicable_gender" value="all" id="genderAll" {{ (isset($data) && @$data->applicable_gender === 'all') || !isset($data) ? 'checked' : '' }}>
                    <label class="form-check-label" for="genderAll">All (Male + Female)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="applicable_gender" value="male" id="genderMale" {{ (isset($data) && @$data->applicable_gender === 'male') ? 'checked' : '' }}>
                    <label class="form-check-label" for="genderMale">Male ♂</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="applicable_gender" value="female" id="genderFemale" {{ (isset($data) && @$data->applicable_gender === 'female') ? 'checked' : '' }}>
                    <label class="form-check-label" for="genderFemale">Female ♀</label>
                </div>
            </div>
        </div>

        <!-- General Values (optional fallback) -->
        <div id="generalSection">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">General Reference Values (Fallback)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Minimum Normal</label>
                            <input type="number" name="min_value" id="min_value" value="{{ @$data->min_value }}" class="form-control" step="0.01" placeholder="e.g., 13">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Maximum Normal</label>
                            <input type="number" name="max_value" id="max_value" value="{{ @$data->max_value }}" class="form-control" step="0.01" placeholder="e.g., 17">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Critical Low</label>
                            <input type="number" name="critical_low" id="critical_low" value="{{ @$data->critical_low }}" class="form-control" step="0.01">
                            <small class="text-muted">Below this = Critical</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Critical High</label>
                            <input type="number" name="critical_high" id="critical_high" value="{{ @$data->critical_high }}" class="form-control" step="0.01">
                            <small class="text-muted">Above this = Critical</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Male Specific Values -->
        <div id="maleSection" style="display: none;">
            <div class="card mb-3" style="border-color: #1e90ff;">
                <div class="card-header" style="background-color: #e3f2fd;">
                    <h6 class="mb-0">♂ Male Reference Values</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Min Normal (Male)</label>
                            <input type="number" name="min_value_male" id="min_value_male" value="{{ @$data->min_value_male }}" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Normal (Male)</label>
                            <input type="number" name="max_value_male" id="max_value_male" value="{{ @$data->max_value_male }}" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Critical Low (Male)</label>
                            <input type="number" name="critical_low_male" id="critical_low_male" value="{{ @$data->critical_low_male }}" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Critical High (Male)</label>
                            <input type="number" name="critical_high_male" id="critical_high_male" value="{{ @$data->critical_high_male }}" class="form-control" step="0.01">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Female Specific Values -->
        <div id="femaleSection" style="display: none;">
            <div class="card mb-3" style="border-color: #ff1493;">
                <div class="card-header" style="background-color: #ffe0f0;">
                    <h6 class="mb-0">♀ Female Reference Values</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Min Normal (Female)</label>
                            <input type="number" name="min_value_female" id="min_value_female" value="{{ @$data->min_value_female }}" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Normal (Female)</label>
                            <input type="number" name="max_value_female" id="max_value_female" value="{{ @$data->max_value_female }}" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Critical Low (Female)</label>
                            <input type="number" name="critical_low_female" id="critical_low_female" value="{{ @$data->critical_low_female }}" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Critical High (Female)</label>
                            <input type="number" name="critical_high_female" id="critical_high_female" value="{{ @$data->critical_high_female }}" class="form-control" step="0.01">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-3">
            <label class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="2">{{ @$data->description }}</textarea>
        </div>

        <div class="alert alert-info mt-3 mb-0" style="font-size: 0.85rem;">
            <strong>📌 Flag Logic:</strong><br>
            ✓ Normal: Value between Min and Max | ↓ Low: Below Min | ↑ High: Above Max<br>
            ↓↓ Critical Low: Below Critical Low | ↑↑ Critical High: Above Critical High
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">💾 Save</button>
    </div>
</form>

<script>
(function initGenderSections() {
    const genderRadios = document.querySelectorAll('input[name="applicable_gender"]');
    const generalSection = document.getElementById('generalSection');
    const maleSection = document.getElementById('maleSection');
    const femaleSection = document.getElementById('femaleSection');

    if (!genderRadios.length || !generalSection || !maleSection || !femaleSection) {
        return;
    }

    function updateSections() {
        const selectedRadio = document.querySelector('input[name="applicable_gender"]:checked');
        const selected = selectedRadio ? selectedRadio.value : 'all';

        if (selected === 'all') {
            generalSection.style.display = 'block';
            maleSection.style.display = 'block';
            femaleSection.style.display = 'block';
            return;
        }

        generalSection.style.display = 'none';
        maleSection.style.display = selected === 'male' ? 'block' : 'none';
        femaleSection.style.display = selected === 'female' ? 'block' : 'none';
    }

    genderRadios.forEach(function (radio) {
        radio.addEventListener('change', updateSections);
    });

    updateSections();
})();
</script>
