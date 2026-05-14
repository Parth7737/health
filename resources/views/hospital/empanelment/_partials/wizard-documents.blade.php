@php
    $empanelment_step_status = auth()->user()->enable_step
        ? json_decode(auth()->user()->enable_step)
        : json_decode(\App\CentralLogics\Helpers::get_settings('empanelment_step_status') ?: '{}');
    if (!is_object($empanelment_step_status)) {
        $empanelment_step_status = (object) ['speciality_status' => 0, 'service_status' => 0, 'licenses_status' => 0];
    }
@endphp

<div class="eo-panel-title"><i class="fas fa-folder-open" style="color:#ffca28"></i> Documents</div>
<p class="eo-panel-sub">Statutory licenses and empanelment documents. Upload PDFs where required.</p>

@if ($hospital)
    @if (!empty($empanelment_step_status->licenses_status))
        @php $licenses = App\CentralLogics\Helpers::getCommanData('License'); @endphp
        @include('hospital.empanelment._partials.licenses', compact('uuid', 'hospital', 'licenses'))
    @endif

    @php
        $allStepCompleted = $allStepCompleted ?? \App\CentralLogics\Helpers::checkAllStepIsCompleteOrNot($uuid);
        $is_multi_branch = auth()->user()->hospital_type == 'Multi-Branch';
    @endphp
    @include('hospital.empanelment._partials.documents', array_merge(compact('uuid', 'hospital', 'allStepCompleted', 'is_multi_branch'), ['hide_declaration' => true]))
@else
    <div class="eo-card">
        <div class="eo-card-body text-muted">Save basic facility information first.</div>
    </div>
@endif
