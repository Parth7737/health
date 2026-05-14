@php
    $om = [];
    if ($hospital && $hospital->onboarding_meta) {
        $om = (array) $hospital->onboarding_meta;
    }
    $ab = (array) ($om['ab_empanelment'] ?? []);
@endphp

<div class="eo-panel-title"><i class="fas fa-hospital-user" style="color:#60a5fa"></i> Ayushman Bharat empanelment</div>
<p class="eo-panel-sub">Optional PM-JAY readiness fields. Stored with your facility profile.</p>

<form id="wizardAbForm">
    <input type="hidden" name="section" value="ab" />
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-list-ul"></i> Provider &amp; banking</h3>
        </div>
        <div class="eo-card-body eo-grid-2">
            <div class="eo-form-group">
                <label for="ab_sha_code">SHA provider code (if existing)</label>
                <input type="text" class="form-control" id="ab_sha_code" name="ab_empanelment[sha_code]"
                    value="{{ old('ab_empanelment.sha_code', $ab['sha_code'] ?? '') }}" placeholder="SHA-XXX-XXXXX" />
            </div>
            <div class="eo-form-group">
                <label for="ab_rohini">ROHINI ID</label>
                <input type="text" class="form-control" id="ab_rohini" name="ab_empanelment[rohini_id]"
                    value="{{ old('ab_empanelment.rohini_id', $ab['rohini_id'] ?? '') }}" placeholder="As per NHA portal" />
            </div>
            <div class="eo-form-group">
                <label for="ab_bank">Bank account (claim settlement)</label>
                <input type="text" class="form-control" id="ab_bank" name="ab_empanelment[bank_account]"
                    value="{{ old('ab_empanelment.bank_account', $ab['bank_account'] ?? '') }}" />
            </div>
            <div class="eo-form-group">
                <label for="ab_ifsc">IFSC code</label>
                <input type="text" class="form-control" id="ab_ifsc" name="ab_empanelment[ifsc]"
                    value="{{ old('ab_empanelment.ifsc', $ab['ifsc'] ?? '') }}" />
            </div>
        </div>
    </div>
    <div class="eo-step-nav">
        <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(5)"><i class="fas fa-arrow-left"></i> Back</button>
        <span class="eo-nav-info">Step 6 of 8 — AB empanelment</span>
        <button type="button" class="eo-tb-btn primary" id="wizardAbSave"><i class="fas fa-save"></i> Save &amp; continue</button>
    </div>
</form>

<script>
    $('#wizardAbSave').on('click', function() {
        ldrshow();
        var fd = new FormData($('#wizardAbForm')[0]);
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
                    var ws = response.wizard_step || 7;
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
