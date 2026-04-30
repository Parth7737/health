@extends('layouts.admin.app')
@section('title', 'Edit Hospital - ' . $hospital->name)
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
                    <h3>Edit Hospital - {{ $hospital->name }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}"><svg class="stroke-icon"><use href="{{ asset('public/front/assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.hospitals.index') }}" class="text-white">Hospitals</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.hospitals.show', $hospital->id) }}" class="text-white">{{ $hospital->name }}</a></li>
                        <li class="breadcrumb-item active" class="text-white">Edit</li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-12 d-flex flex-wrap justify-content-md-end align-items-center gap-2 mt-2">
                <a href="{{ route('admin.hospitals.show', $hospital->id) }}" class="btn btn-info">View</a>
                <a href="{{ route('admin.hospitals.index') }}" class="btn btn-info">Back</a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Edit Hospital Empanelment</h5>
            </div>
            <div class="card-body">
                <div class="nav-align-top empanelment-tabs">
                    <ul class="nav nav-tabs nav-fill flex-wrap gap-2 mb-0 border-0" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link navstep2 active" data-bs-toggle="tab" data-bs-target="#tab-hospital-info" role="tab" onclick="loadAdminStep(2);">Hospital Information</button>
                        </li>
                        @if($empanelment_step_status && $empanelment_step_status->speciality_status == 1)
                            <li class="nav-item">
                                <button type="button" class="nav-link navstep3" data-bs-toggle="tab" data-bs-target="#tab-speciality" role="tab" onclick="loadAdminStep(3);">Specialities</button>
                            </li>
                        @endif
                        @if($empanelment_step_status && $empanelment_step_status->service_status == 1)
                            <li class="nav-item">
                                <button type="button" class="nav-link navstep4" data-bs-toggle="tab" data-bs-target="#tab-services" role="tab" onclick="loadAdminStep(4);">Services</button>
                            </li>
                        @endif
                        @if($empanelment_step_status && $empanelment_step_status->licenses_status == 1)
                            <li class="nav-item">
                                <button type="button" class="nav-link navstep5" data-bs-toggle="tab" data-bs-target="#tab-licenses" role="tab" onclick="loadAdminStep(5);">Licenses</button>
                            </li>
                        @endif
                        <li class="nav-item">
                            <button type="button" class="nav-link navstep6" data-bs-toggle="tab" data-bs-target="#tab-documents" role="tab" onclick="loadAdminStep(6);">Documents</button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content empanelment-tab-content mt-4">
                    <div class="tab-pane fade step2 show active" id="tab-hospital-info" role="tabpanel"></div>
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
    var hospitalId = {{ $hospital->id }};
    $(document).ready(function() {
        loadAdminStep(2);
    });
    function loadAdminStep(step) {
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        $(`.navstep${step}`).addClass('active');
        $(`.step${step}`).addClass('show active');
        var $container = $(`.step${step}`);
        loader('show');
        $.ajax({
            url: '{{ route("admin.hospitals.edit.stepLoad", $hospital->id) }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', step: step },
            success: function(data) {
                loader('hide');
                $container.html(typeof data === 'string' ? data : (data.html || ''));
                loadSelect2();
            },
            error: function(xhr) {
                loader('hide');
                $container.html('<div class="alert alert-danger">Failed to load step. Please try again.</div>');
            }
        });
    }
</script>
@endpush
