@php
    $empanelment_step_status = auth()->user()->enable_step
        ? json_decode(auth()->user()->enable_step)
        : json_decode(\App\CentralLogics\Helpers::get_settings('empanelment_step_status') ?: '{}');
    if (!is_object($empanelment_step_status)) {
        $empanelment_step_status = (object) ['speciality_status' => 0, 'service_status' => 0, 'licenses_status' => 0];
    }
@endphp

<div class="eo-panel-title"><i class="fas fa-users" style="color:#4db6ac"></i> Staff Strength & Services</div>
<p class="eo-panel-sub">Provide sanctioned vs filled positions and available clinical services.</p>

{{-- ─── Staff Strength Table ─── --}}
@if ($hospital)

    <div class="eo-card">
        @include('hospital.empanelment._partials.staff-strength-table', compact('uuid', 'hospital'))
    </div>
@else
    <div class="eo-card">
        <div class="eo-card-body text-muted">Save basic facility information first to fill staff strength.</div>
    </div>
@endif

@if (!empty($empanelment_step_status->speciality_status))
    @if ($hospital)
        @php $specialities = App\CentralLogics\Helpers::getCommanData('Speciality'); @endphp
        
        <div class="eo-card">
            @include('hospital.empanelment._partials.speciality', compact('uuid', 'hospital', 'specialities'))
        </div>
    @else
        <div class="eo-card">
            <div class="eo-card-body text-muted">Save basic facility information first.</div>
        </div>
    @endif
@else
    <div class="eo-card mb-3">
        <div class="eo-card-body text-muted">Specialities module is disabled for your scheme.</div>
    </div>
@endif

@if (!empty($empanelment_step_status->service_status))
    @if ($hospital)
        @php $services = App\CentralLogics\Helpers::getCommanData('Service'); @endphp
        @include('hospital.empanelment._partials.services', compact('uuid', 'hospital', 'services'))
    @else
        <div class="eo-card">
            <div class="eo-card-body text-muted">Save basic facility information first.</div>
        </div>
    @endif
@else
    <div class="eo-card">
        <div class="eo-card-body text-muted">Services module is disabled for your scheme.</div>
    </div>
@endif
