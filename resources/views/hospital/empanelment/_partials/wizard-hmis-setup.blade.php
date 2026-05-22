@php
    $om = [];
    if ($hospital && $hospital->onboarding_meta) {
        $om = (array) $hospital->onboarding_meta;
    }
    $hm = (array) ($om['hmis_setup'] ?? []);
    
    $checkedModules = old('hmis_setup.modules', $hm['modules'] ?? []);
    $checkedModules = is_array($checkedModules) ? $checkedModules : [];
    $availableModules = [
        'Patient Registration & ADT',
        'Doctor Dashboard',
        'Nursing Station',
        'Pharmacy',
        'Radiology',
        'Lab / LIS',
        'Radiology / RIS',
        'Billing & Finance',
        'Ambulance',
        'Blood Bank',
        'Inventory',
        'HR & Payroll',
        'Certificates & Documents'
    ];
@endphp

<div class="eo-panel-title"><i class="fas fa-cogs" style="color:#ce93d8"></i> HMIS &amp; IT setup</div>
<p class="eo-panel-sub">Planned admin access for HMIS (non-binding; credentials can be finalised later).</p>

@if ($hospital)
<form id="wizardHmisForm">
@endif
    <input type="hidden" name="section" value="hmis" />
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-user-shield"></i> Admin account (draft)</h3>
        </div>
        <div class="eo-card-body eo-grid-2">
            <div class="eo-form-group">
                <label for="hmis_username">Hospital admin username</label>
                <input type="text" class="form-control" id="hmis_username" name="hmis_setup[admin_username]"
                    value="{{ old('hmis_setup.admin_username', $hm['admin_username'] ?? '') }}" placeholder="e.g. dh.almora.admin" />
            </div>
            <div class="eo-form-group">
                <label for="hmis_email">Admin email</label>
                <input type="email" class="form-control" id="hmis_email" name="hmis_setup[admin_email]"
                    value="{{ old('hmis_setup.admin_email', $hm['admin_email'] ?? '') }}" />
            </div>
            <div class="eo-form-group">
                <label for="hmis_role">Role preset</label>
                @php $rp = old('hmis_setup.role_preset', $hm['role_preset'] ?? ''); @endphp
                <select class="form-select" id="hmis_role" name="hmis_setup[role_preset]">
                    <option value="">Select</option>
                    <option value="Full Admin" @selected($rp === 'Full Admin')>Full admin (all modules)</option>
                    <option value="Limited Admin" @selected($rp === 'Limited Admin')>Limited admin</option>
                    <option value="Custom" @selected($rp === 'Custom')>Custom</option>
                </select>
            </div>
            <div class="eo-form-group">
                <label for="hmis_2fa">2FA method</label>
                @php $tfa = old('hmis_setup.two_fa', $hm['two_fa'] ?? ''); @endphp
                <select class="form-select" id="hmis_2fa" name="hmis_setup[two_fa]">
                    <option value="">Select</option>
                    <option value="SMS OTP" @selected($tfa === 'SMS OTP')>SMS OTP</option>
                    <option value="Email OTP" @selected($tfa === 'Email OTP')>Email OTP</option>
                    <option value="Both" @selected($tfa === 'Both')>Both (recommended)</option>
                </select>
            </div>
            <div class="eo-form-group">
                <label for="hmis_pw">Initial password (optional)</label>
                <input type="password" class="form-control" id="hmis_pw" name="hmis_setup[admin_password]" autocomplete="new-password"
                    placeholder="Leave blank to keep unchanged" />
            </div>
        </div>
    </div>
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-puzzle-piece"></i> Modules to Enable</h3>
        </div>
        <div class="eo-card-body">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                <div style="font-weight:600">Available Modules</div>
                <div><label style="font-size:13px"><input type="checkbox" id="selectAllModules" style="margin-right:8px"> Select all</label></div>
            </div>
            <div class="eo-form-group">
                <div class="eo-grid-4">
                    @foreach($availableModules as $idx => $module)
                        @php $isModuleChecked = in_array($module, $checkedModules); @endphp
                        <label class="cl-item{{ $isModuleChecked ? ' checked' : '' }}" style="display:flex;align-items:center;gap:10px;padding:10px;border:1px solid #eee;border-radius:8px;margin:6px;cursor:pointer">
                            <input type="checkbox" name="hmis_setup[modules][]" value="{{ $module }}" class="d-none" onclick="toggleCheck(this)" {{ $isModuleChecked ? 'checked' : '' }} />
                            <div class="cl-check" style="width:20px;height:20px;border:1px solid #cfcfcf;border-radius:4px;display:flex;align-items:center;justify-content:center">{!! $isModuleChecked ? '<i class="fas fa-check" style="font-size:12px"></i>' : '' !!}</div>
                            <div style="flex:1;font-weight:600">{{ $module }}</div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-network-wired"></i> Integration settings</h3>
        </div>
        <div class="eo-card-body eo-grid-3">
            @php
                $abha = old('hmis_setup.abha_integration', $hm['abha_integration'] ?? '');
                $nic = old('hmis_setup.nic_integration', $hm['nic_integration'] ?? '');
                $hims = old('hmis_setup.hims_data_reporting', $hm['hims_data_reporting'] ?? '');
                $ambulance = old('hmis_setup.ambulance_integration', $hm['ambulance_integration'] ?? '');
                $cmss = old('hmis_setup.cmss_integration', $hm['cmss_integration'] ?? '');
                $payroll = old('hmis_setup.payroll_integration', $hm['payroll_integration'] ?? '');
            @endphp
            <div class="eo-form-group">
                <label for="hmis_abha">ABHA / Health ID Integration</label>
                <select class="form-select" id="hmis_abha" name="hmis_setup[abha_integration]">
                    <option value="">Select</option>
                    <option value="Enable" @selected($abha === 'Enable')>Enable</option>
                    <option value="Disable" @selected($abha === 'Disable')>Disable</option>
                </select>
            </div>
            <div class="eo-form-group">
                <label for="hmis_nic">NIC eSanjeevani Integration</label>
                <select class="form-select" id="hmis_nic" name="hmis_setup[nic_integration]">
                    <option value="">Select</option>
                    <option value="Enable" @selected($nic === 'Enable')>Enable</option>
                    <option value="Disable" @selected($nic === 'Disable')>Disable</option>
                </select>
            </div>
            <div class="eo-form-group">
                <label for="hmis_hims">HIMS Data Reporting (Weekly)</label>
                <select class="form-select" id="hmis_hims" name="hmis_setup[hims_data_reporting]">
                    <option value="">Select</option>
                    <option value="Automatic" @selected($hims === 'Automatic')>Automatic</option>
                    <option value="Manual" @selected($hims === 'Manual')>Manual</option>
                </select>
            </div>
            <div class="eo-form-group">
                <label for="hmis_ambulance">108 Ambulance Integration</label>
                <select class="form-select" id="hmis_ambulance" name="hmis_setup[ambulance_integration]">
                    <option value="">Select</option>
                    <option value="Enable" @selected($ambulance === 'Enable')>Enable</option>
                    <option value="Disable" @selected($ambulance === 'Disable')>Disable</option>
                </select>
            </div>
            <div class="eo-form-group">
                <label for="hmis_cmss">Drug Logistics (CMSS)</label>
                <select class="form-select" id="hmis_cmss" name="hmis_setup[cmss_integration]">
                    <option value="">Select</option>
                    <option value="Enable" @selected($cmss === 'Enable')>Enable</option>
                    <option value="Disable" @selected($cmss === 'Disable')>Disable</option>
                </select>
            </div>
            <div class="eo-form-group">
                <label for="hmis_payroll">Payroll Integration (Treasury)</label>
                <select class="form-select" id="hmis_payroll" name="hmis_setup[payroll_integration]">
                    <option value="">Select</option>
                    <option value="Enable" @selected($payroll === 'Enable')>Enable (Govt. facilities)</option>
                    <option value="Disable" @selected($payroll === 'Disable')>Disable</option>
                </select>
            </div>
        </div>
    </div>
@if ($hospital)
    @if($hospital->status == "Draft" || $hospital->status == "Rejected" || !empty($is_admin_edit))
    <div class="eo-step-nav">
        <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(6)"><i class="fas fa-arrow-left"></i> Back</button>
        <span class="eo-nav-info">Step 7 of 8 — HMIS setup</span>
        <button type="button" class="eo-tb-btn primary" id="wizardHmisSave"><i class="fas fa-save"></i> Save &amp; continue</button>
    </div>
    @endif
</form>
@endif

<script>
    function toggleCheck(el) {
        var parent = el.closest('.cl-item');
        if (el.checked) {
            parent.classList.add('checked');
            parent.querySelector('.cl-check').innerHTML = '<i class="fas fa-check"></i>';
        } else {
            parent.classList.remove('checked');
            parent.querySelector('.cl-check').innerHTML = '';
        }
    }

    function setCheckboxState(inputElem, checked) {
        inputElem.checked = checked;
        toggleCheck(inputElem);
    }

    // Wire Select All for Modules
    (function initSelectAll() {
        var selAllMod = document.getElementById('selectAllModules');

        function updateSelectAllState(groupName, selAllCheckbox) {
            var items = Array.from(document.querySelectorAll('input[name="' + groupName + '[]"]'));
            if (items.length === 0) return;
            selAllCheckbox.checked = items.every(function(i){ return i.checked; });
        }

        if (selAllMod) {
            selAllMod.addEventListener('change', function() {
                var checked = this.checked;
                document.querySelectorAll('input[name="hmis_setup[modules][]"]').forEach(function(i){ setCheckboxState(i, checked); });
            });
            updateSelectAllState('hmis_setup[modules]', selAllMod);
        }

        // Keep select-all in sync when individual toggles change
        document.querySelectorAll('input[name="hmis_setup[modules][]"]').forEach(function(i){
            i.addEventListener('change', function() {
                setCheckboxState(this, this.checked);
                if (selAllMod) updateSelectAllState('hmis_setup[modules]', selAllMod);
            });
        });
    })();

    @if ($hospital)
    $('#wizardHmisSave').on('click', function() {
        ldrshow();
        var fd = new FormData($('#wizardHmisForm')[0]);
        $.ajax({
            url: "{{ route('hospital.empanelmentRegistration.saveWizardMeta', [$uuid]) }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: fd,
            processData: false,
            contentType: false,
            success: function(response) {
                ldrhide();
                if (response.success) {
                    successMessage(response.message);
                    var ws = response.wizard_step || 8;
                    if (typeof window.eoUpdateStepper === 'function') window.eoUpdateStepper(ws);
                    setTimeout(function() {
                        loadStep(ws);
                    }, 400);
                }
            },
            error: function(xhr) {
                ldrhide();
                errorMessage((xhr.responseJSON && xhr.responseJSON.message) || 'Save failed');
            }
        });
    });
    @endif
</script>
