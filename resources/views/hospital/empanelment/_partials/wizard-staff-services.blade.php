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

@if ($hospital)
<form id="staffServicesForm" enctype="multipart/form-data">
@endif

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
@endif

@if (!empty($empanelment_step_status->service_status))
    @if ($hospital)
        @php $services = App\CentralLogics\Helpers::getCommanData('Service'); @endphp
        @php $combine_staff_services = true; @endphp
        @include('hospital.empanelment._partials.services', compact('uuid', 'hospital', 'services', 'combine_staff_services'))
    @else
        <div class="eo-card">
            <div class="eo-card-body text-muted">Save basic facility information first.</div>
        </div>
    @endif
@endif

@if ($hospital)
    @if($hospital->status == "Draft" || $hospital->status == "Rejected" || !empty($is_admin_edit))
        <div class="eo-step-nav">
            <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(3);"><i class="fas fa-arrow-left"></i> Back</button>
            <span class="eo-nav-info">Save staff strength, specialities and services to continue.</span>
            <button type="button" class="eo-tb-btn primary saveStaffServices"><i class="fas fa-save"></i> Save & Continue</button>
        </div>
    @endif
</form>
@endif

@if ($hospital)
<script>
    $(document).off('click', '.saveStaffServices').on('click', '.saveStaffServices', function () {
        ldrshow();
        $('.error').remove();

        var form = $('#staffServicesForm')[0];
        var formData = new FormData(form);

        $('#staffServicesForm input[type="checkbox"]').each(function () {
            if (this.name && !this.checked) {
                formData.append(this.name, 0);
            }
        });

        $.ajax({
            url: '{{ route("hospital.empanelmentRegistration.saveStaffServices", [$uuid ?? "", $hospital->id ?? ""]) }}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                ldrhide();
                if (response.success) {
                    successMessage(response.message);
                    var ws = response.wizard_step || response.step || 5;
                    if (typeof window.eoSetUnlockedMax === 'function') {
                        window.eoSetUnlockedMax(ws);
                    }
                    if (typeof window.eoUpdateStepper === 'function') {
                        window.eoUpdateStepper(ws);
                    }
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    $('.step' + ws).addClass('show active');
                    $('.navstep' + ws).addClass('active');
                    setTimeout(function () {
                        loadStep(ws);
                    }, 600);
                } else {
                    errorMessage(response.message || 'Something went wrong!!');
                }
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();

                if (xhr.status === 422 && xhr.responseJSON) {
                    var errors = xhr.responseJSON.errors || {};
                    var errorMessages = [];
                    for (var field in errors) {
                        var $field = $('[name="' + field + '"]');
                        var message = errors[field][0];
                        if ($field.length) {
                            var $anchor = $field.closest('.serviceerror');
                            ($anchor.length ? $anchor : $field).after('<div class="error text-danger m-0">' + message + '</div>');
                        }
                        errorMessages.push(message);
                    }
                    errorMessage(errorMessages.length ? errorMessages.join('<br>') : (xhr.responseJSON.message || 'Please check required fields.'));
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    });
</script>
@endif
