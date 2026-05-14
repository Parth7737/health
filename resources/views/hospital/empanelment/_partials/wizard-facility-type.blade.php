@php
    $wo = (array) (auth()->user()->wizard_onboarding ?? []);
    $types = App\CentralLogics\Helpers::getCommanData('HospitalType');
    $hospitalModel = ($hospital && is_object($hospital)) ? $hospital : null;
    $selectedTypeId = (int) old('type_id', $hospitalModel?->type_id ?: ($wo['type_id'] ?? 0));
@endphp

<div class="eo-panel-title"><i class="fas fa-hospital" style="color:#81c784"></i> Select facility type</div>
<p class="eo-panel-sub">Choose the category that best matches this facility. Options are loaded from the same master list as empanelment records.</p>

<form id="facilityTypeForm">
    @if ($types && count($types))
        <div class="eo-type-grid">
            @foreach ($types as $type)
                <label class="eo-type-card {{ $selectedTypeId === (int) $type->id ? 'is-selected' : '' }}" style="cursor:pointer;margin:0">
                    <input type="radio" name="type_id" value="{{ $type->id }}" class="d-none eo-ft-radio"
                        {{ $selectedTypeId === (int) $type->id ? 'checked' : '' }} />
                    <div class="tc-icon" style="background:linear-gradient(135deg,#1565c0,#00695c)"><i class="fas fa-hospital"></i></div>
                    <div class="tc-name">{{ $type->name }}</div>
                    <div class="tc-sub">Tap to select</div>
                </label>
            @endforeach
        </div>
    @else
        <div class="eo-card">
            <div class="eo-card-body text-warning">No facility types are configured. Please contact support.</div>
        </div>
    @endif
    <div class="eo-step-nav">
        <a href="{{ \App\CentralLogics\Helpers::getDashboardRedirect(auth()->user()) }}" class="eo-tb-btn"><i
                class="fas fa-arrow-left"></i> Dashboard</a>
        <span class="eo-nav-info">Step 1 of 8</span>
        <button type="button" class="eo-tb-btn primary" id="saveFacilityTypeBtn" @if (!$types || !count($types)) disabled @endif><i
                class="fas fa-arrow-right"></i> Next:
            Basic information</button>
    </div>
</form>

<script>
    (function() {
        $('.eo-ft-radio').on('change', function() {
            $('.eo-type-card').removeClass('is-selected');
            $(this).closest('.eo-type-card').addClass('is-selected');
        });
        $('.eo-type-card').on('click', function() {
            $(this).find('input').prop('checked', true).trigger('change');
        });
    })();
    $('#saveFacilityTypeBtn').on('click', function() {
        if (!$('.eo-ft-radio:checked').length) {
            errorMessage('Please select a facility type.');
            return;
        }
        ldrshow();
        var fd = new FormData($('#facilityTypeForm')[0]);
        $.ajax({
            url: "{{ route('hospital.empanelmentRegistration.saveFacilityType', [$uuid]) }}",
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
                    var ws = response.wizard_step || 2;
                    if (typeof window.eoSetUnlockedMax === 'function') window.eoSetUnlockedMax(ws);
                    if (typeof window.eoUpdateStepper === 'function') window.eoUpdateStepper(ws);
                    setTimeout(function() {
                        loadStep(ws);
                    }, 400);
                }
            },
            error: function(xhr) {
                ldrhide();
                errorMessage((xhr.responseJSON && xhr.responseJSON.message) || 'Save failed.');
            }
        });
    });
</script>
