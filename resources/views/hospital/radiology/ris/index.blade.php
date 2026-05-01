@extends('layouts.hospital.app')
@section('title', 'Radiology / RIS')
@section('page_subtitle', 'Imaging worklist, modalities, and reporting')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/front/assets/js/editor/ckeditor/contents.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/hospital/radiology-ris.css') }}">
@include('layouts.partials.datatable-css')
@include('layouts.partials.flatpickr-css')
@endpush

@section('content')
<div class="rad-ris-scope" data-pacs-viewer-template="{{ e($pacs_viewer_url_template ?? '') }}" data-worklist-save-url="{{ route('hospital.radiology.worklist.save', ['item' => '__ITEM__']) }}"@can('view-radiology-test')@if (\Illuminate\Support\Facades\Route::has('hospital.settings.radiology.test.index')) data-settings-tests-url="{{ route('hospital.settings.radiology.test.index') }}"@endif @endcan>
    <div class="rad-ris-toolbar">
        <div>
            <div class="rad-ris-breadcrumb">Clinical diagnostics → Radiology RIS</div>
            <h1><i class="fa-solid fa-x-ray" style="color:#1565c0"></i> Radiology information system</h1>
        </div>
        <div class="rad-ris-toolbar-actions">
            <span class="d-flex align-items-center gap-2 rad-ris-text-sm rad-ris-text-muted">
                <span class="rad-ris-status-dot"></span> Worklist live
            </span>
            <button type="button" class="rad-ris-btn rad-ris-btn-primary rad-ris-btn-sm" id="rad-ris-toolbar-new-order"><i class="fa-solid fa-plus"></i> New order</button>
            <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="rad-ris-toolbar-refresh"><i class="fa-solid fa-rotate"></i> Refresh</button>
            <a href="{{ route('hospital.radiology.worklist.index') }}" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm">Classic worklist</a>
        </div>
    </div>

    <div class="rad-ris-stats-grid" id="rad-ris-stats-grid">
        <div class="rad-ris-stat-card blue">
            <i class="fa-solid fa-list-ul s-icon"></i>
            <div class="s-label">Today's orders</div>
            <div class="s-value" id="rad-ris-stat-total">—</div>
            <div class="s-meta" id="rad-ris-stat-pending-meta"><i class="fa-solid fa-clock"></i> —</div>
        </div>
        <div class="rad-ris-stat-card green">
            <i class="fa-solid fa-circle-check s-icon"></i>
            <div class="s-label">Completed</div>
            <div class="s-value" id="rad-ris-stat-completed">—</div>
            <div class="s-meta" id="rad-ris-stat-completion">—</div>
        </div>
        <div class="rad-ris-stat-card orange">
            <i class="fa-solid fa-triangle-exclamation s-icon"></i>
            <div class="s-label">Urgent / STAT (open)</div>
            <div class="s-value" id="rad-ris-stat-urgent">—</div>
            <div class="s-meta">Awaiting completion</div>
        </div>
        <div class="rad-ris-stat-card purple">
            <i class="fa-solid fa-file-medical s-icon"></i>
            <div class="s-label">In progress</div>
            <div class="s-value" id="rad-ris-stat-report-pending">—</div>
            <div class="s-meta">Reporting / acquisition queue</div>
        </div>
        <div class="rad-ris-stat-card teal">
            <i class="fa-solid fa-robot s-icon"></i>
            <div class="s-label">AI flagged</div>
            <div class="s-value" id="rad-ris-stat-ai">0</div>
            <div class="s-meta">Module not connected</div>
        </div>
    </div>

    <div class="rad-ris-tab-bar" role="tablist">
        <button type="button" class="rad-ris-tab-btn active" data-rad-tab="worklist"><i class="fa-solid fa-list-ul"></i> Worklist</button>
        <button type="button" class="rad-ris-tab-btn" data-rad-tab="schedule"><i class="fa-solid fa-calendar-alt"></i> Schedule</button>
        <button type="button" class="rad-ris-tab-btn" data-rad-tab="modalities"><i class="fa-solid fa-x-ray"></i> Modalities</button>
        <button type="button" class="rad-ris-tab-btn" data-rad-tab="reporting"><i class="fa-solid fa-file-medical"></i> Reporting</button>
        <button type="button" class="rad-ris-tab-btn" data-rad-tab="ai"><i class="fa-solid fa-brain"></i> AI findings</button>
        <button type="button" class="rad-ris-tab-btn" data-rad-tab="protocols"><i class="fa-solid fa-book-medical"></i> Protocols</button>
        <button type="button" class="rad-ris-tab-btn" data-rad-tab="completed"><i class="fa-solid fa-circle-check"></i> Completed reports</button>
        <button type="button" class="rad-ris-tab-btn" data-rad-tab="reports"><i class="fa-solid fa-chart-bar"></i> Analytics</button>
    </div>

    @include('hospital.radiology.ris.tabs.worklist')
    @include('hospital.radiology.ris.tabs.schedule')
    @include('hospital.radiology.ris.tabs.modalities')
    @include('hospital.radiology.ris.tabs.reporting')
    @include('hospital.radiology.ris.tabs.ai')
    @include('hospital.radiology.ris.tabs.protocols')
    @include('hospital.radiology.ris.tabs.completed')
    @include('hospital.radiology.ris.tabs.analytics')
</div>

<div class="rad-ris-modal-overlay" id="rad-ris-order-modal" aria-hidden="true">
    <div class="rad-ris-modal" role="dialog" aria-labelledby="rad-ris-order-modal-title">
        <div class="rad-ris-modal-header">
            <h2 id="rad-ris-order-modal-title"><i class="fa-solid fa-plus-circle" style="color:#1565c0;margin-right:8px"></i> New radiology order</h2>
            <button type="button" class="rad-ris-modal-close rad-ris-close-modal" data-target="rad-ris-order-modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="rad-ris-modal-body">
            <p class="rad-ris-text-muted rad-ris-text-sm">Radiology orders are created from <strong>OPD / IPD</strong> patient visits (diagnostics). Use the clinical workspace to place imaging requests; they will appear on this worklist automatically.</p>
            <div class="rad-ris-form-grid mt-3">
                <div class="rad-ris-form-group full">
                    <label>Quick link</label>
                    <a href="{{ route('hospital.opd-patient.index') }}" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm w-100 justify-content-center">OPD patients</a>
                </div>
            </div>
        </div>
        <div class="rad-ris-modal-footer">
            <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-close-modal" data-target="rad-ris-order-modal">Close</button>
        </div>
    </div>
</div>

<div class="rad-ris-modal-overlay" id="rad-ris-schedule-modal" aria-hidden="true">
    <div class="rad-ris-modal" role="dialog" aria-labelledby="rad-ris-schedule-modal-title">
        <div class="rad-ris-modal-header">
            <h2 id="rad-ris-schedule-modal-title"><i class="fa-solid fa-calendar-plus" style="color:#1565c0;margin-right:8px"></i> Book scan slot</h2>
            <button type="button" class="rad-ris-modal-close rad-ris-close-modal" data-target="rad-ris-schedule-modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="rad-ris-modal-body">
            <p class="rad-ris-text-muted rad-ris-text-sm">Slot booking is managed through front office / appointments in your workflow. This dialog is a layout placeholder matching the RIS design.</p>
        </div>
        <div class="rad-ris-modal-footer">
            <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-close-modal" data-target="rad-ris-schedule-modal">Close</button>
        </div>
    </div>
</div>

<div class="rad-ris-toast-container" id="rad-ris-toast-container"></div>
@endsection

@push('scripts')
@include('layouts.partials.datatable-js')
@include('layouts.partials.flatpickr-js')
<script src="{{ asset('public/front/assets/js/editor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('public/js/hospital/radiology-ris.js') }}"></script>
@endpush
