@extends('layouts.admin.app')
@section('title', 'Create Hospital')
@push('styles')
<style>
    .empanelment-tabs .nav-link { border: 1px solid #e7e7e7 !important; border-radius: 0.375rem; margin: 0 2px; padding: 0.6rem 1rem; color: #697a8d !important; font-weight: 500 !important; }
    .empanelment-tabs .nav-link.active,.empanelment-tabs .nav-link:hover { background: var(--theme-default) !important; border-color: var(--theme-default) !important; color: #fff !important; }
    .empanelment-tabs .nav-link.disabled { opacity: 0.6 !important; pointer-events: none; }
    .empanelment-tab-content { min-height: 280px !important; padding: 1.5rem 0 !important; }
</style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Create Hospital</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}"><svg class="stroke-icon"><use href="{{ asset('public/front/assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.hospitals.index') }}" class="text-white">Hospitals</a></li>
                        <li class="breadcrumb-item active" class="text-white">Create</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-12 d-flex flex-wrap justify-content-md-end align-items-center gap-2 mt-2">
                <a href="{{ route('admin.hospitals.index') }}" class="btn btn-info">Back</a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Create Hospital</h5>
            </div>
            <div class="card-body">
                <div class="nav-align-top empanelment-tabs">
                    <ul class="nav nav-tabs nav-fill flex-wrap gap-2 mb-0 border-0" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link navstep1 active" data-bs-toggle="tab" data-bs-target="#tab-profile" role="tab" onclick="loadAdminStep(1);">Profile</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link navstep2 disabled" data-bs-toggle="tab" data-bs-target="#tab-hospital-info" role="tab" onclick="loadAdminStep(2);">Hospital Information</button>
                        </li>
                        @if($empanelment_step_status && $empanelment_step_status->speciality_status == 1)
                            <li class="nav-item">
                                <button type="button" class="nav-link navstep3 disabled" data-bs-toggle="tab" data-bs-target="#tab-speciality" role="tab" onclick="loadAdminStep(3);">Specialities</button>
                            </li>
                        @endif
                        @if($empanelment_step_status && $empanelment_step_status->service_status == 1)
                            <li class="nav-item">
                                <button type="button" class="nav-link navstep4 disabled" data-bs-toggle="tab" data-bs-target="#tab-services" role="tab" onclick="loadAdminStep(4);">Services</button>
                            </li>
                        @endif
                        @if($empanelment_step_status && $empanelment_step_status->licenses_status == 1)
                            <li class="nav-item">
                                <button type="button" class="nav-link navstep5 disabled" data-bs-toggle="tab" data-bs-target="#tab-licenses" role="tab" onclick="loadAdminStep(5);">Licenses</button>
                            </li>
                        @endif
                        <li class="nav-item">
                            <button type="button" class="nav-link navstep6 disabled" data-bs-toggle="tab" data-bs-target="#tab-documents" role="tab" onclick="loadAdminStep(6);">Documents</button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content empanelment-tab-content mt-4">
                    <div class="tab-pane fade step1 show active" id="tab-profile" role="tabpanel">
                        @include('admin-views.hospitals.create-partials.profile', ['states' => $states])
                    </div>
                    <div class="tab-pane fade step2" id="tab-hospital-info" role="tabpanel"></div>
                    <div class="tab-pane fade step3" id="tab-speciality" role="tabpanel"></div>
                    <div class="tab-pane fade step4" id="tab-services" role="tabpanel"></div>
                    <div class="tab-pane fade step5" id="tab-licenses" role="tabpanel"></div>
                    <div class="tab-pane fade step6" id="tab-documents" role="tabpanel"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    var createWizardHospitalId = '';
    var user_id = '';
    var createStepLoadUrl = '';

    $(document).ready(function() {
        $('#createHospitalType').on('change', function() {
            var v = $(this).val();
            if (v === 'Multi-Branch') $('.create-branch-wrap').removeClass('d-none');
            else $('.create-branch-wrap').addClass('d-none');
        });
        if (typeof loadSelect2 === 'function') loadSelect2();

        $(document).on('click', '#adminCreateProfileSubmit', function() {
            var $form = $('#adminCreateProfileForm');
            if ($form[0].checkValidity && !$form[0].checkValidity()) { $form[0].reportValidity(); return; }
            var pass = $form.find('[name="password"]').val();
            var conf = $form.find('[name="confirmation_password"]').val();
            if (pass !== conf) {
                (typeof sendmsg === 'function' ? sendmsg('error', 'Password and Confirm password do not match.') : alert('Password and Confirm password do not match.'));
                return;
            }
            loader('show');
            var formData = new FormData($form[0]);
            formData.append('user_id',user_id);
            formData.append('hospital_id',createWizardHospitalId);
            $.ajax({
                url: '{{ route("admin.hospitals.create-wizard.store-profile") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(r) {
                    loader('hide');
                    if (r.success && r.hospital_id) {
                        createWizardHospitalId = r.hospital_id;
                        user_id = r.user_id;
                        createStepLoadUrl = '{{ url("admin/hospitals") }}/' + r.hospital_id + '/edit-step-load';
                        $('.empanelment-tabs .nav-link').removeClass('disabled');
                        (typeof sendmsg === 'function' ? sendmsg('success', r.message) : alert(r.message));
                        loadAdminStep(2);
                    }
                },
                error: function(xhr) {
                    loader('hide');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.responseJSON && xhr.responseJSON.errors) ? Object.values(xhr.responseJSON.errors).flat().join(' ') : 'Something went wrong.';
                    (typeof sendmsg === 'function' ? sendmsg('error', msg) : alert(msg));
                }
            });
        });
    });

    function loadAdminStep(step) {
        if (step >= 2 && !createWizardHospitalId) {
            (typeof sendmsg === 'function' ? sendmsg('warning', 'Please complete Profile step first.') : alert('Please complete Profile step first.'));
            return;
        }
        $('.empanelment-tabs .nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        $(`.navstep${step}`).addClass('active');
        $(`.tab-pane.step${step}`).addClass('show active');
        var $container = $(`.tab-pane.step${step}`);
        if (step >= 2 && createStepLoadUrl) {
            $.ajax({
                url: createStepLoadUrl,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', step: step },
                success: function(data) {
                    $container.html(typeof data === 'string' ? data : (data.html || ''));
                    if (typeof loadSelect2 === 'function') loadSelect2();
                },
                error: function(xhr) {
                    $container.html('<div class="alert alert-danger">Failed to load step. Please try again.</div>');
                }
            });
        }
    }
</script>
@endpush
