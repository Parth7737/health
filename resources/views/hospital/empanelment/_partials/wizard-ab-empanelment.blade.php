@php
    $om = [];
    if ($hospital && $hospital->onboarding_meta) {
        $om = (array) $hospital->onboarding_meta;
    }
    $ab = (array) ($om['ab_empanelment'] ?? []);
    $checkedEligibility = old('ab_empanelment.eligibility', $ab['eligibility'] ?? []);
    $checkedEligibility = is_array($checkedEligibility) ? $checkedEligibility : [];
    $eligibilities = $eligibilities ?? \App\Models\EmpanelmentEligibility::orderBy('id')->get();
@endphp

<div class="eo-panel-title"><i class="fas fa-hospital-user" style="color:#60a5fa"></i> Ayushman Bharat empanelment</div>
<p class="eo-panel-sub">Configure Ayushman Bharat PM-JAY empanelment. Complete all eligibility criteria for SHA approval.</p>

@if ($hospital)
<form id="wizardAbForm">
@endif
    <input type="hidden" name="section" value="ab" />
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-check-circle"></i>Empanelment Eligibility Checklist</h3>
        </div>
        <div class="eo-card-body">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                <div style="font-weight:600">Checklist</div>
                <div><label style="font-size:13px"><input type="checkbox" id="selectAllEligibility" style="margin-right:8px"> Select all</label></div>
            </div>
            <div class="checklist">
                @if($eligibilities->isEmpty())
                    <div class="text-muted">No eligibility checklist items have been configured yet.</div>
                @else
                    @foreach($eligibilities as $eligibility)
                        @php $isChecked = in_array($eligibility->id, $checkedEligibility); @endphp
                        <label class="cl-item{{ $isChecked ? ' checked' : '' }}">
                            <input type="checkbox"
                                name="ab_empanelment[eligibility][]"
                                value="{{ $eligibility->id }}"
                                class="d-none" onclick="toggleCheck(this)"
                                {{ $isChecked ? 'checked' : '' }} />
                            <div class="cl-check">{!! $isChecked ? '<i class="fas fa-check"></i>' : '' !!}</div>
                            <div class="cl-txt">
                                <div class="cl-title">
                                    {{ $eligibility->title }}
                                    @if($eligibility->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </div>
                                @if($eligibility->subtitle)
                                    <div class="cl-desc">{{ $eligibility->subtitle }}</div>
                                @endif
                                @if($eligibility->is_required)
                                    <div class="cl-required">Mandatory</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-list-ul"></i> Provider &amp; banking</h3>
        </div>
        <div class="eo-card-body">
            
            <div class="eo-form-group"><label>Specialties for AB Empanelment (select all applicable)</label>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                    <div style="font-weight:600">Specialties</div>
                    <div><label style="font-size:13px"><input type="checkbox" id="selectAllSpecialities" style="margin-right:8px"> Select all</label></div>
                </div>
                <div class="eo-grid-4" id="abPackageGrid">
                    @if(isset($specialities) && count($specialities) > 0)
                        @php
                            $checkedSpecs = old('ab_empanelment.specialities', $ab['specialities'] ?? []);
                            $checkedSpecs = is_array($checkedSpecs) ? $checkedSpecs : [];
                        @endphp
                        @foreach($specialities as $spec)
                            @php $isSpecChecked = in_array($spec->id, $checkedSpecs); @endphp
                            <label class="cl-item{{ $isSpecChecked ? ' checked' : '' }}" style="display:flex;align-items:center;gap:10px;padding:10px;border:1px solid #eee;border-radius:8px;margin:6px;cursor:pointer">
                                <input type="checkbox" name="ab_empanelment[specialities][]" value="{{ $spec->id }}" class="d-none" onclick="toggleCheck(this)" {{ $isSpecChecked ? 'checked' : '' }} />
                                <div class="cl-check" style="width:20px;height:20px;border:1px solid #cfcfcf;border-radius:4px;display:flex;align-items:center;justify-content:center">{!! $isSpecChecked ? '<i class="fas fa-check" style="font-size:12px"></i>' : '' !!}</div>
                                <div style="flex:1">
                                    <div style="font-weight:600">{{ $spec->name ?? $spec->title ?? 'Speciality' }}</div>
                                    <!-- @if(!empty($spec->code))<div style="font-size:12px;color:var(--muted2)">{{ $spec->code }}</div>@endif -->
                                </div>
                            </label>
                        @endforeach
                    @else
                        <div class="text-muted">No specialties configured.</div>
                    @endif
                </div>
            </div>
            <div class="eo-grid-2">
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
    </div>
@if ($hospital)
    @if($hospital->status == "Draft" || $hospital->status == "Rejected" || !empty($is_admin_edit))
    <div class="eo-step-nav">
        <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(5)"><i class="fas fa-arrow-left"></i> Back</button>
        <span class="eo-nav-info">Step 6 of 8 — AB empanelment</span>
        <button type="button" class="eo-tb-btn primary" id="wizardAbSave"><i class="fas fa-save"></i> Save &amp; continue</button>
    </div>
    @endif
</form>
@endif

<script>
    @if ($hospital)
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
    @endif
    function toggleCheck(el){
        el.parentElement.classList.toggle('checked');
        const cb = el.parentElement.querySelector('.cl-check');
        if(el.parentElement.classList.contains('checked')){ cb.innerHTML='<i class="fas fa-check" style="font-size:12px"></i>'; }
        else { cb.innerHTML=''; }
    }

    function setCheckboxState(inputElem, checked) {
        try {
            inputElem.checked = checked;
            var parent = inputElem.parentElement;
            if (parent) {
                parent.classList.toggle('checked', checked);
                var cb = parent.querySelector('.cl-check');
                if (cb) cb.innerHTML = checked ? '<i class="fas fa-check" style="font-size:12px"></i>' : '';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // Wire Select All for Eligibility (initialize immediately)
    (function initSelectAll() {
        var selAllEl = document.getElementById('selectAllEligibility');
        var selAllSpec = document.getElementById('selectAllSpecialities');

        function updateSelectAllState(groupName, selAllCheckbox) {
            var items = Array.from(document.querySelectorAll('input[name="' + groupName + '[]"]'));
            if (items.length === 0) return;
            selAllCheckbox.checked = items.every(function(i){ return i.checked; });
        }

        if (selAllEl) {
            selAllEl.addEventListener('change', function() {
                var checked = this.checked;
                document.querySelectorAll('input[name="ab_empanelment[eligibility][]"]').forEach(function(i){ setCheckboxState(i, checked); });
            });
            updateSelectAllState('ab_empanelment[eligibility]', selAllEl);
        }

        if (selAllSpec) {
            selAllSpec.addEventListener('change', function() {
                var checked = this.checked;
                document.querySelectorAll('input[name="ab_empanelment[specialities][]"]').forEach(function(i){ setCheckboxState(i, checked); });
            });
            updateSelectAllState('ab_empanelment[specialities]', selAllSpec);
        }

        // Keep select-all in sync when individual toggles change
        document.querySelectorAll('input[name="ab_empanelment[eligibility][]"], input[name="ab_empanelment[specialities][]"], input[name="ab_empanelment[modules][]"]').forEach(function(i){
            i.addEventListener('change', function() {
                setCheckboxState(this, this.checked);
                if (this.name.indexOf('eligibility') !== -1 && selAllEl) updateSelectAllState('ab_empanelment[eligibility]', selAllEl);
                if (this.name.indexOf('specialities') !== -1 && selAllSpec) updateSelectAllState('ab_empanelment[specialities]', selAllSpec);
                if (this.name.indexOf('modules') !== -1 && selAllMod) updateSelectAllState('ab_empanelment[modules]', selAllMod);
            });
        });
    })();
</script>
