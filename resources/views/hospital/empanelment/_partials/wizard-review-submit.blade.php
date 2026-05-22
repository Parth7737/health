@php
    $wo = (array) (auth()->user()->wizard_onboarding ?? []);
    $om = $hospital && $hospital->onboarding_meta ? (array) $hospital->onboarding_meta : [];
    $ab = (array) ($om['ab_empanelment'] ?? []);
    $hm = (array) ($om['hmis_setup'] ?? []);
    $stepStatus = \App\CentralLogics\Helpers::checkstepComplete($uuid);
    $wizardSteps = \App\CentralLogics\Helpers::empanelmentWizardStepConfig();
    $allStepCompleted = \App\CentralLogics\Helpers::checkAllStepIsCompleteOrNot($uuid);

    $documentsList = \App\CentralLogics\Helpers::getCommanData('EmpanelmentDocument');
    $docTotal = count($documentsList);
    $docUploaded = $hospital ? $hospital->documents()->whereNotNull('document')->where('document', '!=', '')->count() : 0;

    $sanctionedBeds = $om['infra_sanctioned_beds'] ?? null;
    $functionalBeds = $om['infra_functional_beds'] ?? null;
    $bedsLabel = '—';
    if ($sanctionedBeds !== null && $sanctionedBeds !== '') {
        $bedsLabel = (int) $sanctionedBeds . ' sanctioned';
        if ($functionalBeds !== null && $functionalBeds !== '') {
            $bedsLabel .= ' (' . (int) $functionalBeds . ' functional)';
        }
    }

    $districtLabel = trim(($om['district'] ?? '') . ($hospital && $hospital->city ? ', ' . $hospital->city : ''));
    if ($districtLabel === '') {
        $districtLabel = ($hospital->city ?? '—') . ($hospital && $hospital->pincode ? ' — ' . $hospital->pincode : '');
    }

    $staffCategories = \App\Models\StaffStrength::count();
    $staffFilled = count((array) ($om['staff_strength'] ?? []));
    $specCount = $hospital ? $hospital->specialities()->count() : 0;
    $svcCount = $hospital ? $hospital->services()->count() : 0;
    $hmisModules = is_array($hm['modules'] ?? null) ? count($hm['modules']) : 0;

    $abLabel = '—';
    if (!empty($ab)) {
        if (!empty($ab['sha_code'])) {
            $abLabel = 'SHA: ' . $ab['sha_code'];
        } elseif (is_array($ab['eligibility'] ?? null) && count($ab['eligibility']) > 0) {
            $abLabel = count($ab['eligibility']) . ' eligibility criteria met';
        } else {
            $abLabel = 'Details saved';
        }
    }

    $appIdLabel = '—';
    if ($hospital && in_array($hospital->status, ['Submitted', 'Approved'], true)) {
        $appIdLabel = $hospital->hospital_id ?? $hospital->code ?? '—';
    } else {
        $appIdLabel = null;
    }
@endphp

<div class="eo-panel-title"><i class="fas fa-check-double" style="color:#81c784"></i> Review &amp; submit</div>
<p class="eo-panel-sub">Review all entered information before final submission to the State Health Authority for approval.</p>

@if (!$hospital)
    <div class="eo-card">
        <div class="eo-card-body text-muted">Complete previous steps to enable final submission.</div>
    </div>
@else
    @if (!$allStepCompleted)
        <div class="eo-alert-reject mb-3" role="alert">
            <h6 class="mb-2"><i class="fas fa-exclamation-circle me-2"></i>Complete these sections before submitting</h6>
            <ul class="eo-review-pending-list mb-0">
                @foreach ($wizardSteps as $num => $ws)
                    @if ($num >= 8)
                        @continue
                    @endif
                    @php $key = $ws['key']; @endphp
                    @if (empty($stepStatus[$key]))
                        <li>
                            <a href="javascript:;" onclick="if(typeof loadStep==='function')loadStep({{ $num }})">
                                Step {{ $num }}: {{ $ws['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    <div class="eo-card mb-3">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-clipboard-check"></i> Submission summary</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-review-row">
                <div class="eo-rv-key">Facility type</div>
                <div class="eo-rv-val">{{ $wo['facility_type'] ?? '—' }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Facility name</div>
                <div class="eo-rv-val">{{ $hospital->name ?? '—' }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Facility code</div>
                <div class="eo-rv-val">{{ $hospital->code ?? '—' }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">District / location</div>
                <div class="eo-rv-val">{{ $districtLabel ?: '—' }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Beds (functional)</div>
                <div class="eo-rv-val">{{ $bedsLabel }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Medical superintendent</div>
                <div class="eo-rv-val">{{ $om['ms_name'] ?? '—' }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Staff strength</div>
                <div class="eo-rv-val">
                    @if ($staffCategories > 0)
                        {{ $staffFilled }} / {{ $staffCategories }} categories filled
                    @else
                        Not configured
                    @endif
                </div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Specialities</div>
                <div class="eo-rv-val">{{ $specCount > 0 ? $specCount . ' selected' : '—' }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Services</div>
                <div class="eo-rv-val">{{ $svcCount > 0 ? $svcCount . ' configured' : '—' }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">AB empanelment</div>
                <div class="eo-rv-val">
                    @if (!empty($ab))
                        <span class="eo-badge eo-badge-teal">{{ $abLabel }}</span>
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Documents uploaded</div>
                <div class="eo-rv-val">{{ $docUploaded }} / {{ $docTotal }} uploaded</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">HMIS modules</div>
                <div class="eo-rv-val">{{ $hmisModules > 0 ? $hmisModules . ' modules enabled' : (!empty($hm['admin_username']) ? 'Admin account configured' : '—') }}</div>
            </div>
            <div class="eo-review-row">
                <div class="eo-rv-key">Application ID</div>
                <div class="eo-rv-val">
                    @if ($appIdLabel)
                        <span class="eo-badge eo-badge-blue">{{ $appIdLabel }}</span>
                    @else
                        <span class="eo-badge eo-badge-muted">Will be generated on submit</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="eo-card eo-card-info mb-3">
        <div class="eo-card-body d-flex align-items-start gap-3">
            <i class="fas fa-info-circle eo-info-icon"></i>
            <div>
                <div class="eo-info-title">What happens after submission?</div>
                <ul class="eo-info-list">
                    <li>Application reviewed by District Health Officer (DHO) within <strong>3 working days</strong></li>
                    <li>Physical inspection scheduled by state team within <strong>7 working days</strong></li>
                    <li>AB empanelment reviewed by SHA within <strong>15 working days</strong></li>
                    <li>HMIS credentials activated <strong>immediately on approval</strong></li>
                    <li>Application ID sent via SMS &amp; email to Medical Superintendent</li>
                </ul>
            </div>
        </div>
    </div>

    @if ($hospital->status == 'Draft' || $hospital->status == 'Rejected')
        <div class="eo-declaration-box mb-3 @if(!$allStepCompleted) eo-declaration-disabled @endif">
            <input type="checkbox" id="eo_declaration_check" @if(!$allStepCompleted) disabled @endif />
            <label for="eo_declaration_check">
                I declare that all information provided is accurate and complete. I authorise the State Health Authority to verify the details and conduct a physical inspection of the facility.
            </label>
        </div>
    @elseif($hospital->status != 'Approved')
        <div class="eo-card mb-3">
            <div class="eo-card-body text-success"><i class="fas fa-check-circle me-2"></i>Application is submitted.</div>
        </div>
    @endif
@endif

<div class="eo-step-nav">
    <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(7)"><i class="fas fa-arrow-left"></i> Back</button>
    <span class="eo-nav-info">Step 8 of 8 — Review &amp; submit</span>
    @if ($hospital && ($hospital->status == 'Draft' || $hospital->status == 'Rejected'))
        <button type="button" class="eo-tb-btn primary" id="eoSubmitOnboarding" @if(!$allStepCompleted) disabled title="Complete all sections first" @endif>
            <i class="fas fa-paper-plane"></i> {{ $hospital->status == 'Rejected' ? 'Resubmit for approval' : 'Submit for approval' }}
        </button>
    @else
        <span></span>
    @endif
</div>

@if ($hospital && ($hospital->status == 'Draft' || $hospital->status == 'Rejected'))
<script>
(function () {
    $('#eoSubmitOnboarding').off('click').on('click', function () {
        @if(!$allStepCompleted)
        if (typeof errorMessage === 'function') {
            errorMessage('Please complete all previous steps before submitting.');
        } else {
            alert('Please complete all previous steps before submitting.');
        }
        return;
        @endif

        if (!$('#eo_declaration_check').is(':checked')) {
            if (typeof swal === 'function') {
                swal({
                    title: 'Declaration required',
                    text: 'You must accept the declaration before submitting.',
                    icon: 'error',
                    buttons: { confirm: { text: 'Ok', className: 'btn btn-danger' } }
                });
            } else if (typeof errorMessage === 'function') {
                errorMessage('You must accept the declaration before submitting.');
            }
            return;
        }

        if (typeof swal === 'function') {
            swal({
                title: 'Confirm submission?',
                text: 'Are you sure you want to submit this application for approval?',
                icon: 'warning',
                buttons: {
                    cancel: { visible: true, text: 'No, cancel', className: 'btn btn-danger' },
                    confirm: { text: 'Yes, submit', className: 'btn btn-success' }
                }
            }).then(function (confirmed) {
                if (confirmed) {
                    eoSubmitHospital();
                }
            });
        } else if (confirm('Submit this application for approval?')) {
            eoSubmitHospital();
        }
    });

    function eoSubmitHospital() {
        ldrshow();
        $.ajax({
            url: '{{ route('hospital.empanelmentRegistration.hospitalSubmit', [$uuid, $hospital->id]) }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (response) {
                ldrhide();
                if (response.success) {
                    if (typeof swal === 'function') {
                        swal({
                            title: 'Application submitted',
                            text: response.message,
                            icon: 'success',
                            buttons: { confirm: { text: 'Ok', className: 'btn btn-success' } }
                        }).then(function () {
                            if (typeof successMessage === 'function') successMessage(response.message);
                            setTimeout(function () { location.reload(); }, 1000);
                        });
                    } else {
                        if (typeof successMessage === 'function') successMessage(response.message);
                        setTimeout(function () { location.reload(); }, 1000);
                    }
                } else if (typeof errorMessage === 'function') {
                    errorMessage(response.message || 'Submission failed.');
                }
            },
            error: function () {
                ldrhide();
                if (typeof errorMessage === 'function') {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }
})();
</script>
@endif
