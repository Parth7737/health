<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Radiology Parameter</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $valueType = (isset($data) && !empty($data->value_type)) ? $data->value_type : 'numeric';
    $flagRules = (isset($data) && is_array($data->flag_rules)) ? $data->flag_rules : [];
    $normalValues = isset($flagRules['normal']) && is_array($flagRules['normal']) ? implode(', ', $flagRules['normal']) : '';
    $abnormalValues = isset($flagRules['abnormal']) && is_array($flagRules['abnormal'])
        ? implode(', ', $flagRules['abnormal'])
        : ((isset($flagRules['high']) && is_array($flagRules['high'])) ? implode(', ', $flagRules['high']) : '');
    $lowValues = isset($flagRules['low']) && is_array($flagRules['low']) ? implode(', ', $flagRules['low']) : '';
    $highValues = isset($flagRules['high']) && is_array($flagRules['high']) ? implode(', ', $flagRules['high']) : '';
    $criticalLowValues = isset($flagRules['critical_low']) && is_array($flagRules['critical_low']) ? implode(', ', $flagRules['critical_low']) : '';
    $criticalHighValues = isset($flagRules['critical_high']) && is_array($flagRules['critical_high']) ? implode(', ', $flagRules['critical_high']) : '';
@endphp

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
            <select name="radiology_unit_id" id="radiology_unit_id" class="form-control">
                <option value="">Select Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ (isset($data) && @$data->radiology_unit_id == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <label class="form-label">Range (Display Text)</label>
            <input type="text" name="range" id="range" value="{{ @$data->range }}" class="form-control" placeholder="e.g., M: 13-17 | F: 12-15">
        </div>

        <div class="col-md-12 mt-2">
            <label class="form-label">Result Value Type <span class="text-danger">*</span></label>
            <select name="value_type" id="value_type" class="form-control" required>
                <option value="numeric" {{ $valueType === 'numeric' ? 'selected' : '' }}>Numeric (e.g., 2.5, 13, 145)</option>
                <option value="ordinal" {{ $valueType === 'ordinal' ? 'selected' : '' }}>Ordinal/Text (e.g., Normal, Increased, Decreased)</option>
                <option value="boolean" {{ $valueType === 'boolean' ? 'selected' : '' }}>Boolean (e.g., Yes/No, Positive/Negative)</option>
            </select>
        </div>

        <hr class="my-3">
        <h6 class="mb-3">Reference Values (for auto-flagging)</h6>

        <div id="numericSectionWrap">
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
        </div>

        <div id="nonNumericSectionWrap" style="display: none;">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Non-Numeric Flag Mapping</h6>
                </div>
                <div class="card-body">
                    <div class="row" id="normalValuesRow">
                        <div class="col-md-12">
                            <label class="form-label">Normal Values (comma-separated) <span class="text-danger">*</span></label>
                            <input type="text" name="normal_values" id="normal_values" value="{{ $normalValues }}" class="form-control" placeholder="e.g., normal, negative, no">
                        </div>
                    </div>
                    <div class="row mt-2" id="abnormalValuesRow" style="display: none;">
                        <div class="col-md-12">
                            <label class="form-label">Abnormal Values (comma-separated) <span class="text-danger">*</span></label>
                            <input type="text" name="abnormal_values" id="abnormal_values" value="{{ $abnormalValues }}" class="form-control" placeholder="e.g., yes">
                            <small class="text-muted">Boolean type supports only Yes/No mapping (no low/high/critical).</small>
                        </div>
                    </div>
                    <div class="row mt-2" id="ordinalLowHighRow">
                        <div class="col-md-6">
                            <label class="form-label">Low Values</label>
                            <input type="text" name="low_values" id="low_values" value="{{ $lowValues }}" class="form-control" placeholder="e.g., decreased, mild">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">High Values</label>
                            <input type="text" name="high_values" id="high_values" value="{{ $highValues }}" class="form-control" placeholder="e.g., increased, elevated, positive">
                        </div>
                    </div>
                    <div class="row mt-2" id="ordinalCriticalRow">
                        <div class="col-md-6">
                            <label class="form-label">Critical Low Values</label>
                            <input type="text" name="critical_low_values" id="critical_low_values" value="{{ $criticalLowValues }}" class="form-control" placeholder="e.g., severely decreased">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Critical High Values</label>
                            <input type="text" name="critical_high_values" id="critical_high_values" value="{{ $criticalHighValues }}" class="form-control" placeholder="e.g., grossly increased">
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">Matching is case-insensitive and exact after trimming spaces.</small>
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
            ↓↓ Critical Low: Below Critical Low | ↑↑ Critical High: Above Critical High<br>
            <span class="text-muted">For Ordinal: low/high/critical mapping. For Boolean: only Yes/No normal/abnormal mapping.</span>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">💾 Save</button>
    </div>
</form>

<script>
(function initParameterSections() {
    const genderRadios = document.querySelectorAll('input[name="applicable_gender"]');
    const valueTypeInput = document.getElementById('value_type');
    const numericSectionWrap = document.getElementById('numericSectionWrap');
    const nonNumericSectionWrap = document.getElementById('nonNumericSectionWrap');
    const generalSection = document.getElementById('generalSection');
    const maleSection = document.getElementById('maleSection');
    const femaleSection = document.getElementById('femaleSection');
    const abnormalValuesRow = document.getElementById('abnormalValuesRow');
    const ordinalLowHighRow = document.getElementById('ordinalLowHighRow');
    const ordinalCriticalRow = document.getElementById('ordinalCriticalRow');
    const normalValuesInput = document.getElementById('normal_values');
    const abnormalValuesInput = document.getElementById('abnormal_values');
    const lowValuesInput = document.getElementById('low_values');
    const highValuesInput = document.getElementById('high_values');
    const criticalLowValuesInput = document.getElementById('critical_low_values');
    const criticalHighValuesInput = document.getElementById('critical_high_values');

    if (!genderRadios.length || !valueTypeInput || !numericSectionWrap || !nonNumericSectionWrap || !generalSection || !maleSection || !femaleSection || !abnormalValuesRow || !ordinalLowHighRow || !ordinalCriticalRow) {
        return;
    }

    function updateGenderSections() {
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

    function updateTypeSections() {
        const selectedType = valueTypeInput.value || 'numeric';
        const isNumeric = selectedType === 'numeric';
        const isBoolean = selectedType === 'boolean';

        numericSectionWrap.style.display = isNumeric ? 'block' : 'none';
        nonNumericSectionWrap.style.display = isNumeric ? 'none' : 'block';
        abnormalValuesRow.style.display = isBoolean ? 'flex' : 'none';
        ordinalLowHighRow.style.display = isBoolean ? 'none' : 'flex';
        ordinalCriticalRow.style.display = isBoolean ? 'none' : 'flex';

        if (normalValuesInput) {
            normalValuesInput.placeholder = isBoolean ? 'e.g., no' : 'e.g., normal, negative, no';
        }

        if (!isBoolean) {
            if (abnormalValuesInput) {
                abnormalValuesInput.value = '';
            }
        } else {
            if (lowValuesInput) {
                lowValuesInput.value = '';
            }
            if (highValuesInput) {
                highValuesInput.value = '';
            }
            if (criticalLowValuesInput) {
                criticalLowValuesInput.value = '';
            }
            if (criticalHighValuesInput) {
                criticalHighValuesInput.value = '';
            }
        }

        if (isNumeric) {
            updateGenderSections();
        }
    }

    genderRadios.forEach(function (radio) {
        radio.addEventListener('change', updateGenderSections);
    });

    valueTypeInput.addEventListener('change', updateTypeSections);

    updateTypeSections();
})();
</script>