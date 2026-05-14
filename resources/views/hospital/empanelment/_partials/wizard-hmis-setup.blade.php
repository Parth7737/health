@php
    $om = [];
    if ($hospital && $hospital->onboarding_meta) {
        $om = (array) $hospital->onboarding_meta;
    }
    $hm = (array) ($om['hmis_setup'] ?? []);
@endphp

<div class="eo-panel-title"><i class="fas fa-cogs" style="color:#ce93d8"></i> HMIS &amp; IT setup</div>
<p class="eo-panel-sub">Planned admin access for HMIS (non-binding; credentials can be finalised later).</p>

<form id="wizardHmisForm">
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
    <div class="eo-step-nav">
        <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(6)"><i class="fas fa-arrow-left"></i> Back</button>
        <span class="eo-nav-info">Step 7 of 8 — HMIS setup</span>
        <button type="button" class="eo-tb-btn primary" id="wizardHmisSave"><i class="fas fa-save"></i> Save &amp; continue</button>
    </div>
</form>

<script>
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
</script>
