@php
    $om = [];
    if (old('onboarding_meta')) {
        $om = (array) old('onboarding_meta');
    } elseif ($hospital && $hospital->onboarding_meta) {
        $om = (array) $hospital->onboarding_meta;
    }
@endphp

<form id="hospitalinfraForm">
    <div class="eo-panel-title"><i class="fas fa-building" style="color:#ffb74d"></i> Infrastructure details</div>
    <p class="eo-panel-sub">Bed strength, utilities and connectivity. Optional fields help complete your facility profile.</p>

    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-bed"></i> Infrastructure &amp; utilities</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_sanctioned_beds">Sanctioned beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_sanctioned_beds"
                        name="onboarding_meta[infra_sanctioned_beds]"
                        value="{{ old('onboarding_meta.infra_sanctioned_beds', $om['infra_sanctioned_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_functional_beds">Functional beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_functional_beds"
                        name="onboarding_meta[infra_functional_beds]"
                        value="{{ old('onboarding_meta.infra_functional_beds', $om['infra_functional_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_icu_beds">ICU beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_icu_beds"
                        name="onboarding_meta[infra_icu_beds]"
                        value="{{ old('onboarding_meta.infra_icu_beds', $om['infra_icu_beds'] ?? '') }}" />
                </div>
            </div>
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_ot">Operation theatres</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_ot" name="onboarding_meta[infra_ot]"
                        value="{{ old('onboarding_meta.infra_ot', $om['infra_ot'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_inhouse_lab">In-house laboratory</label>
                    @php $lab = old('onboarding_meta.infra_inhouse_lab', $om['infra_inhouse_lab'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_inhouse_lab" name="onboarding_meta[infra_inhouse_lab]">
                        <option value="">Select</option>
                        <option value="Yes" @selected($lab === 'Yes')>Yes</option>
                        <option value="No" @selected($lab === 'No')>No</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_blood_bank">Blood bank</label>
                    @php $bb = old('onboarding_meta.infra_blood_bank', $om['infra_blood_bank'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_blood_bank" name="onboarding_meta[infra_blood_bank]">
                        <option value="">Select</option>
                        <option value="Yes" @selected($bb === 'Yes')>Yes</option>
                        <option value="Blood storage" @selected($bb === 'Blood storage')>Blood storage</option>
                        <option value="No" @selected($bb === 'No')>No</option>
                    </select>
                </div>
            </div>
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_power_backup">Power backup</label>
                    @php $pb = old('onboarding_meta.infra_power_backup', $om['infra_power_backup'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_power_backup" name="onboarding_meta[infra_power_backup]">
                        <option value="">Select</option>
                        <option value="Generator" @selected($pb === 'Generator')>Generator</option>
                        <option value="Solar+Generator" @selected($pb === 'Solar+Generator')>Solar + generator</option>
                        <option value="Solar Only" @selected($pb === 'Solar Only')>Solar only</option>
                        <option value="None" @selected($pb === 'None')>None</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_oxygen_supply">Oxygen supply</label>
                    @php $ox = old('onboarding_meta.infra_oxygen_supply', $om['infra_oxygen_supply'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_oxygen_supply" name="onboarding_meta[infra_oxygen_supply]">
                        <option value="">Select</option>
                        <option value="PSA Plant" @selected($ox === 'PSA Plant')>PSA plant</option>
                        <option value="Cylinders" @selected($ox === 'Cylinders')>Cylinders</option>
                        <option value="Both" @selected($ox === 'Both')>Both</option>
                        <option value="None" @selected($ox === 'None')>None</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_internet">Internet connectivity</label>
                    @php $net = old('onboarding_meta.infra_internet', $om['infra_internet'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_internet" name="onboarding_meta[infra_internet]">
                        <option value="">Select</option>
                        <option value="Broadband (10+ Mbps)" @selected($net === 'Broadband (10+ Mbps)' || $net === 'Broadband (≥10 Mbps)')>Broadband (10+ Mbps)</option>
                        <option value="Broadband (under 10 Mbps)" @selected($net === 'Broadband (under 10 Mbps)' || $net === 'Broadband (<10 Mbps)')>Broadband (under 10 Mbps)</option>
                        <option value="Mobile Data Only" @selected($net === 'Mobile Data Only')>Mobile data only</option>
                        <option value="None" @selected($net === 'None')>None</option>
                    </select>
                </div>
            </div>
            <div class="eo-form-group">
                <label for="onboarding_meta_infra_ambulance">Ambulance</label>
                @php $amb = old('onboarding_meta.infra_ambulance', $om['infra_ambulance'] ?? ''); @endphp
                <select class="form-select" id="onboarding_meta_infra_ambulance" name="onboarding_meta[infra_ambulance]">
                    <option value="">Select</option>
                    <option value="Yes (facility-owned)" @selected($amb === 'Yes (facility-owned)')>Yes (facility-owned)</option>
                    <option value="Yes (108)" @selected($amb === 'Yes (108)')>Yes (108)</option>
                    <option value="No" @selected($amb === 'No')>No</option>
                </select>
            </div>
        </div>
    </div>

    @if (@$hospital && (@$hospital->status == 'Draft' || @$hospital->status == 'Rejected'))
        <div class="eo-step-nav">
            <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(2)"><i class="fas fa-arrow-left"></i> Back</button>
            <span class="eo-nav-info">Save infrastructure details.</span>
            <button type="button" class="eo-tb-btn primary" id="saveHospitalInfra"><i class="fas fa-save"></i> Save &amp; continue</button>
        </div>
    @elseif(!@$hospital)
        <div class="eo-alert-reject" role="alert">
            <p class="mb-0">Save <strong>Basic information</strong> first to create the facility record.</p>
        </div>
    @endif
</form>

<script>
    $('#saveHospitalInfra').off('click').on('click', function() {
        ldrshow();
        $('.error').remove();
        var formData = new FormData($('#hospitalinfraForm')[0]);
        $.ajax({
            url: "{{ route('hospital.empanelmentRegistration.saveInfrastructure', [$uuid]) }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                ldrhide();
                if (response.success) {
                    successMessage(response.message);
                    var ws = response.wizard_step || 4;
                    if (typeof window.eoSetUnlockedMax === 'function') window.eoSetUnlockedMax(ws);
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    $('.step' + ws).addClass('show active');
                    $('.navstep' + ws).addClass('active');
                    if (typeof window.eoUpdateStepper === 'function') window.eoUpdateStepper(ws);
                    setTimeout(function() {
                        loadStep(ws);
                    }, 500);
                }
            },
            error: function(xhr) {
                ldrhide();
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage(xhr.responseJSON.message || 'Validation failed');
                } else {
                    errorMessage('Something went wrong.');
                }
            }
        });
    });
</script>
