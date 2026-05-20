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
    <p class="eo-panel-sub">Enter bed strength, wards, OT, ICU and utility information.</p>

    {{-- ─── Bed Strength ─── --}}
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-bed"></i> Bed Strength</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-grid-4">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_sanctioned_beds">Sanctioned Beds <span class="eo-req">*</span></label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_sanctioned_beds"
                        name="onboarding_meta[infra_sanctioned_beds]"
                        value="{{ old('onboarding_meta.infra_sanctioned_beds', $om['infra_sanctioned_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_functional_beds">Functional Beds <span class="eo-req">*</span></label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_functional_beds"
                        name="onboarding_meta[infra_functional_beds]"
                        value="{{ old('onboarding_meta.infra_functional_beds', $om['infra_functional_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_icu_beds">ICU Beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_icu_beds"
                        name="onboarding_meta[infra_icu_beds]"
                        value="{{ old('onboarding_meta.infra_icu_beds', $om['infra_icu_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_nicu_picu_beds">NICU / PICU Beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_nicu_picu_beds"
                        name="onboarding_meta[infra_nicu_picu_beds]"
                        value="{{ old('onboarding_meta.infra_nicu_picu_beds', $om['infra_nicu_picu_beds'] ?? '') }}" />
                </div>
            </div>
            <div class="eo-grid-4">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_hdu_beds">HDU Beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_hdu_beds"
                        name="onboarding_meta[infra_hdu_beds]"
                        value="{{ old('onboarding_meta.infra_hdu_beds', $om['infra_hdu_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_labour_room_beds">Labour Room Beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_labour_room_beds"
                        name="onboarding_meta[infra_labour_room_beds]"
                        value="{{ old('onboarding_meta.infra_labour_room_beds', $om['infra_labour_room_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_isolation_beds">Isolation Beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_isolation_beds"
                        name="onboarding_meta[infra_isolation_beds]"
                        value="{{ old('onboarding_meta.infra_isolation_beds', $om['infra_isolation_beds'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_burns_trauma_beds">Burns / Trauma Beds</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_burns_trauma_beds"
                        name="onboarding_meta[infra_burns_trauma_beds]"
                        value="{{ old('onboarding_meta.infra_burns_trauma_beds', $om['infra_burns_trauma_beds'] ?? '') }}" />
                </div>
            </div>
        </div>
    </div>

    {{-- ─── OT, Lab & Imaging ─── --}}
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-procedures"></i> OT, Lab &amp; Imaging</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-grid-4">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_ot">Operation Theatres</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_ot"
                        name="onboarding_meta[infra_ot]"
                        value="{{ old('onboarding_meta.infra_ot', $om['infra_ot'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_labour_rooms">Labour Rooms</label>
                    <input type="number" min="0" class="form-control" id="onboarding_meta_infra_labour_rooms"
                        name="onboarding_meta[infra_labour_rooms]"
                        value="{{ old('onboarding_meta.infra_labour_rooms', $om['infra_labour_rooms'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_inhouse_lab">In-house Laboratory</label>
                    @php $lab = old('onboarding_meta.infra_inhouse_lab', $om['infra_inhouse_lab'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_inhouse_lab" name="onboarding_meta[infra_inhouse_lab]">
                        <option value="">Select</option>
                        <option value="Yes" @selected($lab === 'Yes')>Yes</option>
                        <option value="No" @selected($lab === 'No')>No</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_blood_bank">Blood Bank</label>
                    @php $bb = old('onboarding_meta.infra_blood_bank', $om['infra_blood_bank'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_blood_bank" name="onboarding_meta[infra_blood_bank]">
                        <option value="">Select</option>
                        <option value="Yes" @selected($bb === 'Yes')>Yes</option>
                        <option value="Blood storage" @selected($bb === 'Blood storage')>Blood storage</option>
                        <option value="No" @selected($bb === 'No')>No</option>
                    </select>
                </div>
            </div>
            <div class="eo-grid-4">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_xray">X-Ray</label>
                    @php $xray = old('onboarding_meta.infra_xray', $om['infra_xray'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_xray" name="onboarding_meta[infra_xray]">
                        <option value="">Select</option>
                        <option value="Yes" @selected($xray === 'Yes')>Yes</option>
                        <option value="No" @selected($xray === 'No')>No</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_ultrasound">Ultrasound</label>
                    @php $usg = old('onboarding_meta.infra_ultrasound', $om['infra_ultrasound'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_ultrasound" name="onboarding_meta[infra_ultrasound]">
                        <option value="">Select</option>
                        <option value="Yes" @selected($usg === 'Yes')>Yes</option>
                        <option value="No" @selected($usg === 'No')>No</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_ct_scan">CT Scan</label>
                    @php $ct = old('onboarding_meta.infra_ct_scan', $om['infra_ct_scan'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_ct_scan" name="onboarding_meta[infra_ct_scan]">
                        <option value="">Select</option>
                        <option value="No" @selected($ct === 'No')>No</option>
                        <option value="Yes" @selected($ct === 'Yes')>Yes</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_mri">MRI</label>
                    @php $mri = old('onboarding_meta.infra_mri', $om['infra_mri'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_mri" name="onboarding_meta[infra_mri]">
                        <option value="">Select</option>
                        <option value="No" @selected($mri === 'No')>No</option>
                        <option value="Yes" @selected($mri === 'Yes')>Yes</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Utilities & Connectivity ─── --}}
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-bolt"></i> Utilities &amp; Connectivity</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-grid-4">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_power_backup">Power Backup</label>
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
                    <label for="onboarding_meta_infra_water_supply">Water Supply</label>
                    @php $ws = old('onboarding_meta.infra_water_supply', $om['infra_water_supply'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_water_supply" name="onboarding_meta[infra_water_supply]">
                        <option value="">Select</option>
                        <option value="24/7 Piped" @selected($ws === '24/7 Piped')>24/7 Piped</option>
                        <option value="Tank/Tanker" @selected($ws === 'Tank/Tanker')>Tank / Tanker</option>
                        <option value="Borewell" @selected($ws === 'Borewell')>Borewell</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_internet">Internet Connectivity</label>
                    @php $net = old('onboarding_meta.infra_internet', $om['infra_internet'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_internet" name="onboarding_meta[infra_internet]">
                        <option value="">Select</option>
                        <option value="Broadband (10+ Mbps)" @selected($net === 'Broadband (10+ Mbps)' || $net === 'Broadband (≥10 Mbps)')>Broadband (10+ Mbps)</option>
                        <option value="Broadband (under 10 Mbps)" @selected($net === 'Broadband (under 10 Mbps)' || $net === 'Broadband (<10 Mbps)')>Broadband (under 10 Mbps)</option>
                        <option value="Mobile Data Only" @selected($net === 'Mobile Data Only')>Mobile data only</option>
                        <option value="None" @selected($net === 'None')>None</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_ambulance">Ambulance Service</label>
                    @php $amb = old('onboarding_meta.infra_ambulance', $om['infra_ambulance'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_ambulance" name="onboarding_meta[infra_ambulance]">
                        <option value="">Select</option>
                        <option value="Yes (facility-owned)" @selected($amb === 'Yes (facility-owned)')>Yes (facility-owned)</option>
                        <option value="Yes (108)" @selected($amb === 'Yes (108)')>Yes (108)</option>
                        <option value="No" @selected($amb === 'No')>No</option>
                    </select>
                </div>
            </div>
            <div class="eo-grid-4">
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_oxygen_supply">Oxygen Supply</label>
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
                    <label for="onboarding_meta_infra_pharmacy">Pharmacy</label>
                    @php $ph = old('onboarding_meta.infra_pharmacy', $om['infra_pharmacy'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_pharmacy" name="onboarding_meta[infra_pharmacy]">
                        <option value="">Select</option>
                        <option value="Yes (In-house)" @selected($ph === 'Yes (In-house)')>Yes (In-house)</option>
                        <option value="Outsourced" @selected($ph === 'Outsourced')>Outsourced</option>
                        <option value="No" @selected($ph === 'No')>No</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_waste_management">Waste Management</label>
                    @php $wm = old('onboarding_meta.infra_waste_management', $om['infra_waste_management'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_waste_management" name="onboarding_meta[infra_waste_management]">
                        <option value="">Select</option>
                        <option value="Certified BMW facility" @selected($wm === 'Certified BMW facility')>Certified BMW facility</option>
                        <option value="CPCB tie-up" @selected($wm === 'CPCB tie-up')>CPCB tie-up</option>
                        <option value="No formal arrangement" @selected($wm === 'No formal arrangement')>No formal arrangement</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_infra_fire_noc">Fire NOC</label>
                    @php $fn = old('onboarding_meta.infra_fire_noc', $om['infra_fire_noc'] ?? ''); @endphp
                    <select class="form-select" id="onboarding_meta_infra_fire_noc" name="onboarding_meta[infra_fire_noc]">
                        <option value="">Select</option>
                        <option value="Valid NOC" @selected($fn === 'Valid NOC')>Valid NOC</option>
                        <option value="Applied" @selected($fn === 'Applied')>Applied</option>
                        <option value="Not Applied" @selected($fn === 'Not Applied')>Not Applied</option>
                    </select>
                </div>
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
