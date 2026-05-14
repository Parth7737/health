@php
    $wo = (array) (auth()->user()->wizard_onboarding ?? []);
    $om = $hospital && $hospital->onboarding_meta ? (array) $hospital->onboarding_meta : [];
@endphp

<div class="eo-panel-title"><i class="fas fa-clipboard-check" style="color:#81c784"></i> Review &amp; submit</div>
<p class="eo-panel-sub">Verify key details before declaration and final submission.</p>

<div class="eo-card mb-3">
    <div class="eo-card-hdr">
        <h3 class="eo-card-title"><i class="fas fa-list"></i> Summary</h3>
    </div>
    <div class="eo-card-body">
        <div class="eo-readonly-grid">
            <div>
                <div class="eo-k">Onboarding facility type</div>
                <div class="eo-v">{{ $wo['facility_type'] ?? '—' }}</div>
            </div>
            <div>
                <div class="eo-k">Facility name</div>
                <div class="eo-v">{{ $hospital->name ?? '—' }}</div>
            </div>
            <div>
                <div class="eo-k">Facility code</div>
                <div class="eo-v">{{ $hospital->code ?? '—' }}</div>
            </div>
            <div>
                <div class="eo-k">City / PIN</div>
                <div class="eo-v">{{ ($hospital->city ?? '—') . ' / ' . ($hospital->pincode ?? '—') }}</div>
            </div>
        </div>
    </div>
</div>

@if ($hospital)
    @php
        $allStepCompleted = \App\CentralLogics\Helpers::checkAllStepIsCompleteOrNot($uuid);
        $is_multi_branch = auth()->user()->hospital_type == 'Multi-Branch';
    @endphp
    @include('hospital.empanelment._partials.documents', array_merge(compact('uuid', 'hospital', 'allStepCompleted', 'is_multi_branch'), ['hide_document_form' => true, 'hide_declaration' => false]))
@else
    <div class="eo-card">
        <div class="eo-card-body text-muted">Complete previous steps to enable final submission.</div>
    </div>
@endif

<div class="eo-step-nav">
    <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(7)"><i class="fas fa-arrow-left"></i> Back</button>
    <span class="eo-nav-info">Step 8 of 8 — Review &amp; submit</span>
    <span></span>
</div>
