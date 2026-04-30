@extends('layouts.hospital.app')
@section('title', 'IPD Profile')
@section('page_header_icon', '🏥')
@section('page_subtitle', 'Manage IPD Profile')
@section('page_header_actions')
<a href="{{ route('hospital.ipd-patient.index') }}" class="btn btn-info">Back To IPD</a>
<a href="{{ route('hospital.settings.beds.bed-dashboard') }}" class="btn btn-info">Bed Dashboard</a>
@if($allocation->discharge_date)
    <a href="{{ route('hospital.ipd-patient.discharge-summary.print', ['allocation' => $allocation->id, 'autoprint' => 1]) }}" target="_blank" class="btn btn-info">Print Discharge Summary</a>
@endif
@php
    $stayDays = max(1, \Carbon\Carbon::parse($allocation->admission_date)->copy()->startOfDay()->diffInDays(($allocation->discharge_date ? \Carbon\Carbon::parse($allocation->discharge_date) : now())->copy()->startOfDay()) + 1);
    $canDischargeNow = ((float) $outstandingAmount) <= 0;
@endphp
@if(!$allocation->discharge_date)
    @can('edit-ipd-patient')
        <button type="button" class="btn btn-warning transfer-ipd-btn" data-id="{{ $allocation->id }}" data-url="{{ route('hospital.ipd-patient.transfer.showform', ['allocation' => $allocation->id]) }}">Transfer Bed</button>
        <button
            type="button"
            class="btn {{ $canDischargeNow ? 'btn-success discharge-ipd-btn' : 'btn-secondary' }}"
            data-id="{{ $allocation->id }}"
            data-url="{{ route('hospital.ipd-patient.discharge.showform', ['allocation' => $allocation->id]) }}"
            {{ $canDischargeNow ? '' : 'disabled' }}
            title="{{ $canDischargeNow ? 'Discharge Patient' : 'Clear outstanding bill before discharge' }}"
        >
            {{ $canDischargeNow ? 'Discharge' : 'Clear Bill To Discharge' }}
        </button>
    @endcan
@endif
@endsection
@section('content')
<div class="container-fluid ipd-profile-v2">

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Current Bed</p>
                    <h5 class="mb-0">{{ $allocation->bed?->bed_code ?? '-' }}</h5>
                    <small>{{ $allocation->bed?->room?->ward?->ward_name ?? '-' }} / {{ $allocation->bed?->room?->room_number ?? '-' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Length Of Stay</p>
                    <h5 class="mb-0">{{ $stayDays }} day(s)</h5>
                    <small>Admitted {{ optional($allocation->admission_date)->format('d-m-Y H:i') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body">
                    <p class="text-muted mb-1">IPD Outstanding</p>
                    <h5 class="mb-0 {{ $outstandingAmount > 0 ? 'text-danger' : 'text-success' }}">{{ number_format((float) $outstandingAmount, 2) }}</h5>
                    <small>{{ $allocation->tpa?->name ?: 'Self Payer' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Status</p>
                    <h5 class="mb-0">{{ $allocation->discharge_date ? 'Discharged' : 'Active' }}</h5>
                    <small>{{ $allocation->discharge_date ? optional($allocation->discharge_date)->format('d-m-Y H:i') : 'Under care' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-2 p-md-3">
            <ul class="nav nav-pills ipd-tabs mb-3" id="ipdTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#ipd-overview" type="button" role="tab">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ipd-orders" type="button" role="tab">Pathology / Radiology</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ipd-billing" type="button" role="tab">Charges/Payments</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ipd-history" type="button" role="tab">Bed History</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ipd-prescriptions" type="button" role="tab">Prescriptions</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ipd-timeline" type="button" role="tab">Timeline</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ipd-discharge" type="button" role="tab">Discharge</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="ipd-overview" role="tabpanel">
                    <div class="row g-2">
                        <div class="col-xl-6">
                            <div class="card h-100 border-0 bg-light-subtle ipd-overview-card">
                                <div class="card-header bg-info ipd-overview-card-header"><strong>Patient Summary</strong></div>
                                <div class="card-body small ipd-overview-card-body">
                                    <div class="row g-1 ipd-summary-grid">
                                        <div class="col-sm-6"><strong>Phone:</strong> {{ $allocation->patient?->phone ?? '-' }}</div>
                                        <div class="col-sm-6"><strong>Guardian:</strong> {{ $allocation->patient?->guardian_name ?? '-' }}</div>
                                        <div class="col-sm-6"><strong>Age / Gender:</strong> {{ $allocation->patient?->age_years ?? '-' }}y / {{ $allocation->patient?->gender ?? '-' }}</div>
                                        <div class="col-sm-6"><strong>Consultant:</strong> {{ $allocation->consultantDoctor?->full_name ?? '-' }}</div>
                                        <div class="col-sm-6"><strong>Department:</strong> {{ $allocation->department?->name ?? '-' }}</div>
                                        <div class="col-sm-6"><strong>Admission Type:</strong> {{ ucfirst($allocation->admission_type) }}</div>
                                        <div class="col-sm-6"><strong>Source:</strong> {{ strtoupper($allocation->admission_source) }}</div>
                                        <div class="col-sm-6"><strong>Expected Discharge:</strong> {{ optional($allocation->expected_discharge_date)->format('d-m-Y') ?? '-' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="mb-1 ipd-text-block"><strong>Admission Reason:</strong><br>{{ $allocation->admission_reason ?: '-' }}</div>
                                    <div class="mb-0"><strong>Provisional Diagnosis:</strong><br>{{ $allocation->provisional_diagnosis ?: '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="card h-100 border-0 bg-light-subtle ipd-overview-card">
                                <div class="card-header bg-info d-flex align-items-center justify-content-between flex-wrap gap-2 ipd-overview-card-header">
                                    <strong>Clinical Snapshot</strong>
                                    <div class="d-flex align-items-center gap-2 flex-wrap clinical-header-actions">
                                        @if($allocation->sourceOpdPatient)
                                            <span class="badge bg-light text-dark clinical-source-badge">Moved from OPD</span>
                                        @endif
                                        @can('edit-ipd-patient')
                                            @if(!$allocation->discharge_date)
                                                <button type="button" class="btn btn-primary btn-sm ipd-clinical-edit-toggle clinical-edit-btn">Edit</button>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body small ipd-overview-card-body">
                                    @php
                                        $familyHistorySelected = collect(explode(',', (string) $allocation->family_history))
                                            ->map(fn ($item) => strtolower(trim($item)))
                                            ->filter()
                                            ->values();
                                        // $familyHistoryOptions is passed dynamically from controller (Disease master list)
                                    @endphp
                                    <div class="row g-1 ipd-summary-grid">
                                        <div class="col-6"><strong>Height:</strong> {{ $allocation->height ?: '-' }}</div>
                                        <div class="col-6"><strong>Weight:</strong> {{ $allocation->weight ?: '-' }}</div>
                                        <div class="col-6"><strong>BP:</strong> {{ $allocation->bp ?: '-' }}</div>
                                        <div class="col-6"><strong>Systolic / Diastolic:</strong> {{ ($allocation->systolic_bp ?: '-') . ' / ' . ($allocation->diastolic_bp ?: '-') }}</div>
                                        <div class="col-6"><strong>Pulse:</strong> {{ $allocation->pulse ?: '-' }}</div>
                                        <div class="col-6"><strong>Temperature:</strong> {{ $allocation->temperature ?: '-' }}</div>
                                        <div class="col-6"><strong>Respiration:</strong> {{ $allocation->respiration ?: '-' }}</div>
                                        <div class="col-6"><strong>BMI:</strong> {{ $allocation->bmi ?: '-' }}</div>
                                        <div class="col-6"><strong>Diabetes:</strong> {{ $allocation->diabetes ?: '-' }}</div>
                                        <div class="col-12"><strong>Family History:</strong> {{ $allocation->family_history ?: '-' }}</div>
                                    </div>

                                    @can('edit-ipd-patient')
                                        @if(!$allocation->discharge_date)
                                            <form id="ipd-clinical-form" class="mt-2 d-none" data-submit-url="{{ route('hospital.ipd-patient.clinical.update', ['allocation' => $allocation->id]) }}">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Height</label>
                                                        <input type="text" class="form-control form-control-sm" name="height" value="{{ $allocation->height }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Weight</label>
                                                        <input type="text" class="form-control form-control-sm" name="weight" value="{{ $allocation->weight }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">BP</label>
                                                        <input type="text" class="form-control form-control-sm" name="bp" value="{{ $allocation->bp }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Systolic BP</label>
                                                        <input type="text" class="form-control form-control-sm" name="systolic_bp" value="{{ $allocation->systolic_bp }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Diastolic BP</label>
                                                        <input type="text" class="form-control form-control-sm" name="diastolic_bp" value="{{ $allocation->diastolic_bp }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Pulse</label>
                                                        <input type="text" class="form-control form-control-sm" name="pulse" value="{{ $allocation->pulse }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Temperature</label>
                                                        <input type="text" class="form-control form-control-sm" name="temperature" value="{{ $allocation->temperature }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Respiration</label>
                                                        <input type="text" class="form-control form-control-sm" name="respiration" value="{{ $allocation->respiration }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">BMI</label>
                                                        <input type="text" class="form-control form-control-sm" name="bmi" value="{{ $allocation->bmi }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label mb-1">Diabetes</label>
                                                        <select class="form-select form-select-sm" name="diabetes">
                                                            <option value="" @selected(empty($allocation->diabetes))>Select</option>
                                                            <option value="yes" @selected(strtolower((string) $allocation->diabetes) === 'yes')>Yes</option>
                                                            <option value="no" @selected(strtolower((string) $allocation->diabetes) === 'no')>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label mb-1">Family History</label>
                                                        <div class="family-history-chips">
                                                            @foreach($familyHistoryOptions as $fhOption)
                                                                <label class="family-history-chip">
                                                                    <input
                                                                        type="checkbox"
                                                                        name="family_history[]"
                                                                        value="{{ $fhOption }}"
                                                                        @checked($familyHistorySelected->contains(strtolower($fhOption)))
                                                                    >
                                                                    <span>{{ ucwords($fhOption) }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm mt-2"
                                                            name="family_history_other"
                                                            placeholder="Other family history"
                                                            value="{{ collect(explode(',', (string) $allocation->family_history))->map(fn ($item) => trim($item))->reject(fn ($item) => in_array(strtolower($item), $familyHistoryOptions, true))->implode(', ') }}"
                                                        >
                                                    </div>
                                                    <div class="col-12 d-flex justify-content-end gap-2 mt-1">
                                                        <button type="button" class="btn btn-light btn-sm ipd-clinical-cancel">Cancel</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Save Clinical Details</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                    @endcan

                                    <hr class="my-2">
                                    <div class="mb-1 ipd-text-block"><strong>Admission Notes:</strong><br>{{ $allocation->admission_notes ?: '-' }}</div>
                                    @if($allocation->discharge_notes)
                                        <div class="mb-0"><strong>Discharge Notes:</strong><br>{{ $allocation->discharge_notes }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card ipd-notes-card">
                                <div class="card-header bg-info d-flex align-items-center justify-content-between flex-wrap gap-2 ipd-overview-card-header">
                                    <strong>Doctor / Nursing Notes</strong>
                                    <small class="text-white-50">Quick progress updates</small>
                                </div>
                                <div class="card-body p-2">
                                    @can('edit-ipd-patient')
                                        @if(!$allocation->discharge_date)
                                            <form id="ipd-note-form" data-submit-url="{{ route('hospital.ipd-patient.notes.store', ['allocation' => $allocation->id]) }}" class="mb-2 ipd-note-form">
                                                <div class="row g-2 align-items-start">
                                                    <div class="col-lg-2 col-md-3">
                                                        <select class="form-select form-select-sm" name="note_type">
                                                            <option value="doctor">Doctor</option>
                                                            <option value="nursing">Nursing</option>
                                                            <option value="progress" selected>Progress</option>
                                                            <option value="discharge_plan">Discharge Plan</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3">
                                                        <input type="text" class="form-control form-control-sm" id="ipd-note-datetime" name="noted_at" value="{{ now()->format('d-m-Y H:i') }}">
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <textarea class="form-control form-control-sm" name="note" rows="2" placeholder="Enter clinical update, nursing observation, treatment plan, or discharge advice"></textarea>
                                                    </div>
                                                    <div class="col-lg-2 col-md-12">
                                                        <button type="submit" class="btn btn-primary btn-sm w-100 ipd-note-save-btn">Save Note</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                    @endcan

                                    <div id="ipd-notes-list">
                                        @forelse($progressNotes as $note)
                                            <div class="border rounded p-2 mb-2 ipd-note-item">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                                    <span class="badge bg-light text-dark">{{ strtoupper($note->note_type) }}</span>
                                                    <small class="text-muted">{{ optional($note->noted_at)->format('d-m-Y H:i') }}</small>
                                                </div>
                                                <div class="small mt-1">{{ $note->note }}</div>
                                                <small class="text-muted">By: {{ $note->creator?->name ?? 'System' }}</small>
                                            </div>
                                        @empty
                                            <div class="text-muted">No progress note added yet.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="tab-pane fade" id="ipd-orders" role="tabpanel">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @can('create-pathology-order')
                            <button
                                class="btn btn-outline-primary open-ipd-diagnostic-order"
                                data-order-type="pathology"
                                data-show-url="{{ route('hospital.ipd-patient.diagnostics.showform', ['allocation' => $allocation->id]) }}"
                                data-store-url="{{ route('hospital.ipd-patient.diagnostics.store', ['allocation' => $allocation->id]) }}"
                                {{ $allocation->discharge_date ? 'disabled' : '' }}
                                title="{{ $allocation->discharge_date ? 'Patient discharged. New orders are locked.' : 'Create pathology order' }}"
                            >Order Pathology</button>
                        @endcan
                        @can('create-radiology-order')
                            <button
                                class="btn btn-outline-primary open-ipd-diagnostic-order"
                                data-order-type="radiology"
                                data-show-url="{{ route('hospital.ipd-patient.diagnostics.showform', ['allocation' => $allocation->id]) }}"
                                data-store-url="{{ route('hospital.ipd-patient.diagnostics.store', ['allocation' => $allocation->id]) }}"
                                {{ $allocation->discharge_date ? 'disabled' : '' }}
                                title="{{ $allocation->discharge_date ? 'Patient discharged. New orders are locked.' : 'Create radiology order' }}"
                            >Order Radiology</button>
                        @endcan
                    </div>

                    <div class="row g-3">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header bg-info"><strong>Pathology Orders</strong></div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Test</th>
                                                    <th>Status</th>
                                                    <th>Payment</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pathologyOrders as $item)
                                                        @php
                                                            $pathologyStatusKey = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
                                                            $canDeletePathology = !$allocation->discharge_date && !in_array($pathologyStatusKey, ['in_progress', 'completed'], true);
                                                            $canPrintPathology = $pathologyStatusKey === 'completed';
                                                        @endphp
                                                    <tr>
                                                        <td>{{ $item->order?->order_no ?? '-' }}</td>
                                                        <td>{{ $item->test_name }}</td>
                                                        <td>{{ ucfirst($item->status) }}</td>
                                                        <td>{{ ucfirst($item->patientCharge?->payment_status ?? 'unpaid') }}</td>
                                                        <td>
                                                                @can('view-pathology-report')
                                                                    @if($canPrintPathology)
                                                                        <a href="{{ route('hospital.pathology.worklist.print',  ['item' => $item->id]) }}" target="_blank" class="me-2" data-bs-toggle="tooltip" title="Print Pathology Report">
                                                                            <i class="text-success fa-solid fa-print"></i>
                                                                        </a>
                                                                    @endif
                                                                @endcan
                                                            @can('delete-pathology-order')
                                                                    @if($canDeletePathology)
                                                                    <a
                                                                        class="delete-ipd-diagnostic-item"
                                                                        data-delete-url="{{ route('hospital.ipd-patient.diagnostics.destroy', ['allocation' => $allocation->id, 'item' => $item->id]) }}"
                                                                        data-test-name="{{ $item->test_name }}"
                                                                    ><i class="text-danger fa-solid fa-trash-can"></i></a>
                                                                    @else
                                                                        <span class="text-muted" data-bs-toggle="tooltip" title="{{ $allocation->discharge_date ? 'Discharged admission orders cannot be deleted.' : 'In-progress or completed test cannot be deleted.' }}">
                                                                            <i class="fa-solid fa-lock"></i>
                                                                        </span>
                                                                    @endif
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @empty
                                                    @if($opdPathologyOrders->isEmpty())
                                                        <tr><td colspan="5" class="text-center text-muted">No pathology order yet.</td></tr>
                                                    @endif
                                                @endforelse
                                                @if($opdPathologyOrders->isNotEmpty())
                                                    @if($pathologyOrders->isNotEmpty())
                                                        <tr><td colspan="5" class="bg-light text-muted small py-1 px-2"><i class="fa-solid fa-circle-info me-1 text-warning"></i>Orders placed from OPD visit</td></tr>
                                                    @endif
                                                    @foreach($opdPathologyOrders as $item)
                                                        @php $canPrintOpd = strtolower(str_replace([' ', '-'], '_', (string) $item->status)) === 'completed'; @endphp
                                                        <tr class="table-warning table-sm">
                                                            <td>{{ $item->order?->order_no ?? '-' }} <span class="badge bg-warning text-dark ms-1">OPD</span></td>
                                                            <td>{{ $item->test_name }}</td>
                                                            <td>{{ ucfirst($item->status) }}</td>
                                                            <td>{{ ucfirst($item->patientCharge?->payment_status ?? 'unpaid') }}</td>
                                                            <td>
                                                                @can('view-pathology-report')
                                                                    @if($canPrintOpd)
                                                                        <a href="{{ route('hospital.pathology.worklist.print', ['item' => $item->id]) }}" target="_blank" class="me-2" data-bs-toggle="tooltip" title="Print Pathology Report">
                                                                            <i class="text-success fa-solid fa-print"></i>
                                                                        </a>
                                                                    @endif
                                                                @endcan
                                                                <span class="text-muted" data-bs-toggle="tooltip" title="OPD order — cannot be deleted from IPD panel">
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header bg-info"><strong>Radiology Orders</strong></div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Test</th>
                                                    <th>Status</th>
                                                    <th>Payment</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($radiologyOrders as $item)
                                                        @php
                                                            $radiologyStatusKey = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
                                                            $canDeleteRadiology = !$allocation->discharge_date && !in_array($radiologyStatusKey, ['in_progress', 'completed'], true);
                                                            $canPrintRadiology = $radiologyStatusKey === 'completed';
                                                        @endphp
                                                    <tr>
                                                        <td>{{ $item->order?->order_no ?? '-' }}</td>
                                                        <td>{{ $item->test_name }}</td>
                                                        <td>{{ ucfirst($item->status) }}</td>
                                                        <td>{{ ucfirst($item->patientCharge?->payment_status ?? 'unpaid') }}</td>
                                                        <td>
                                                                @can('view-radiology-report')
                                                                    @if($canPrintRadiology)
                                                                        <a href="{{ route('hospital.radiology.worklist.print',  ['item' => $item->id]) }}" target="_blank" class="me-2" data-bs-toggle="tooltip" title="Print Radiology Report">
                                                                            <i class="text-success fa-solid fa-print"></i>
                                                                        </a>
                                                                    @endif
                                                                @endcan
                                                            @can('delete-radiology-order')
                                                                    @if($canDeleteRadiology)
                                                                    <a
                                                                        class="delete-ipd-diagnostic-item"
                                                                        data-delete-url="{{ route('hospital.ipd-patient.diagnostics.destroy', ['allocation' => $allocation->id, 'item' => $item->id]) }}"
                                                                        data-test-name="{{ $item->test_name }}"
                                                                    ><i class="text-danger fa-solid fa-trash-can"></i></a>
                                                                    @else
                                                                        <span class="text-muted" data-bs-toggle="tooltip" title="{{ $allocation->discharge_date ? 'Discharged admission orders cannot be deleted.' : 'In-progress or completed test cannot be deleted.' }}">
                                                                            <i class="fa-solid fa-lock"></i>
                                                                        </span>
                                                                    @endif
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @empty
                                                    @if($opdRadiologyOrders->isEmpty())
                                                        <tr><td colspan="5" class="text-center text-muted">No radiology order yet.</td></tr>
                                                    @endif
                                                @endforelse
                                                @if($opdRadiologyOrders->isNotEmpty())
                                                    @if($radiologyOrders->isNotEmpty())
                                                        <tr><td colspan="5" class="bg-light text-muted small py-1 px-2"><i class="fa-solid fa-circle-info me-1 text-warning"></i>Orders placed from OPD visit</td></tr>
                                                    @endif
                                                    @foreach($opdRadiologyOrders as $item)
                                                        @php $canPrintOpdRad = strtolower(str_replace([' ', '-'], '_', (string) $item->status)) === 'completed'; @endphp
                                                        <tr class="table-warning table-sm">
                                                            <td>{{ $item->order?->order_no ?? '-' }} <span class="badge bg-warning text-dark ms-1">OPD</span></td>
                                                            <td>{{ $item->test_name }}</td>
                                                            <td>{{ ucfirst($item->status) }}</td>
                                                            <td>{{ ucfirst($item->patientCharge?->payment_status ?? 'unpaid') }}</td>
                                                            <td>
                                                                @can('view-radiology-report')
                                                                    @if($canPrintOpdRad)
                                                                        <a href="{{ route('hospital.radiology.worklist.print', ['item' => $item->id]) }}" target="_blank" class="me-2" data-bs-toggle="tooltip" title="Print Radiology Report">
                                                                            <i class="text-success fa-solid fa-print"></i>
                                                                        </a>
                                                                    @endif
                                                                @endcan
                                                                <span class="text-muted" data-bs-toggle="tooltip" title="OPD order — cannot be deleted from IPD panel">
                                                                    <i class="fa-solid fa-lock"></i>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade ipd-billing-pane" id="ipd-billing" role="tabpanel">
                    <div class="d-flex justify-content-between flex-wrap gap-1 mb-1 ipd-billing-toolbar">
                        <div class="d-flex flex-wrap gap-1">
                            @can('edit-ipd-patient')
                                @if(!$allocation->discharge_date)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary open-ipd-add-charge-form"
                                        data-url="{{ route('hospital.ipd-patient.charges.show-add-form', ['allocation' => $allocation->id]) }}"
                                    >Add Charge</button>
                                @endif
                            @endcan
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary open-charge-payment-form"
                                data-url="{{ route('hospital.opd-patient.charges.show-payment-form', ['patient' => $allocation->patient_id]) }}"
                                data-charge-ids="{{ implode(',', $pendingEpisodeChargeIds->all()) }}"
                                @if($pendingEpisodeChargeIds->isEmpty()) disabled @endif
                            >Payment / Discount</button>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary open-charge-payment-form"
                                data-url="{{ route('hospital.opd-patient.charges.show-refund-form', ['patient' => $allocation->patient_id]) }}"
                            >Refund Advance</button>
                            <a href="{{ route('hospital.ipd-patient.final-bill.print', ['allocation' => $allocation->id]) }}" target="_blank" class="btn btn-sm btn-outline-dark">Print Final Bill</a>
                        </div>
                    </div>

                    <div class="row g-1 mb-1 ipd-billing-stats">
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="alert alert-light border mb-0 compact-stat"><span>Total Charges</span><strong>{{ number_format((float) ($episodeTotalCharges ?? 0), 2) }}</strong></div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="alert alert-light border mb-0 compact-stat"><span>Paid (Adjusted)</span><strong>{{ number_format((float) ($episodeTotalPaid ?? 0), 2) }}</strong></div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="alert alert-light border mb-0 compact-stat"><span>Total Due</span><strong>{{ number_format((float) ($episodeTotalDue ?? 0), 2) }}</strong></div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="alert alert-light border mb-0 compact-stat"><span>Advance/Credit</span><strong>{{ number_format((float) ($advanceCredit ?? 0), 2) }}</strong></div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="alert alert-light border mb-0 compact-stat"><span>Discount</span><strong>{{ number_format((float) ($episodeTotalDiscount ?? 0), 2) }}</strong></div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="alert alert-light border mb-0 compact-stat"><span>Tax</span><strong>{{ number_format((float) ($episodeTotalTax ?? 0), 2) }}</strong></div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-1" id="ipd-charges-ledger-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ipd-charges-ledger-tab" data-bs-toggle="tab" data-bs-target="#ipd-charges-ledger-panel" type="button" role="tab" aria-controls="ipd-charges-ledger-panel" aria-selected="true">Charge Ledger</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ipd-payments-ledger-tab" data-bs-toggle="tab" data-bs-target="#ipd-payments-ledger-panel" type="button" role="tab" aria-controls="ipd-payments-ledger-panel" aria-selected="false">Payment Ledger</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="ipd-charges-ledger-tab-content">
                        <div class="tab-pane fade show active" id="ipd-charges-ledger-panel" role="tabpanel" aria-labelledby="ipd-charges-ledger-tab">
                            <div class="table-responsive ipd-ledger-scroll">
                                <table class="table table-bordered table-sm align-middle mb-0 ipd-billing-table">
                                    <thead>
                                        <tr>
                                            <th>SN.</th>
                                            <th>Date</th>
                                            <th>Module</th>
                                            <th>Code</th>
                                            <th class="particular-col">Particular</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th>Paid</th>
                                            <th>Due</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($episodeCharges as $charge)
                                            @php $due = max(0, (float) $charge->amount - (float) $charge->paid_amount); @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ optional($charge->charged_at)->format('d-m-Y H:i') ?? '-' }}</td>
                                                <td>{{ strtoupper($charge->module ?? '-') }}</td>
                                                <td>{{ $charge->charge_code ?? '-' }}</td>
                                                <td class="particular-col">{{ $charge->particular }}</td>
                                                <td>{{ number_format((float) $charge->quantity, 2) }}</td>
                                                <td>{{ number_format((float) $charge->unit_rate, 2) }}</td>
                                                <td>{{ number_format((float) $charge->amount, 2) }}</td>
                                                <td>{{ number_format((float) $charge->paid_amount, 2) }}</td>
                                                <td>{{ number_format($due, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusKey = strtolower((string) ($charge->payment_status ?? 'unpaid'));
                                                        $statusClass = $statusKey === 'paid'
                                                            ? 'bg-success'
                                                            : ($statusKey === 'partial' ? 'bg-warning text-dark' : 'bg-danger');
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">{{ ucfirst($statusKey) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="11" class="text-center text-muted">No bill items yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="ipd-payments-ledger-panel" role="tabpanel" aria-labelledby="ipd-payments-ledger-tab">
                            <div class="table-responsive ipd-ledger-scroll">
                                <table class="table table-bordered table-sm align-middle mb-0 ipd-billing-table">
                                    <thead>
                                        <tr>
                                            <th>SN.</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Mode</th>
                                            <th>Reference</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                            @php
                                                $isRefund = (float) $payment->amount < 0;
                                                $displayAmount = abs((float) $payment->amount);
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ optional($payment->paid_at)->format('d-m-Y H:i') ?? '-' }}</td>
                                                <td>
                                                    @if($isRefund)
                                                        <span class="badge bg-warning text-dark">REFUND</span>
                                                    @else
                                                        <span class="badge bg-success">PAYMENT</span>
                                                    @endif
                                                </td>
                                                <td>{{ $payment->payment_mode ?? '-' }}</td>
                                                <td>{{ $payment->reference ?? '-' }}</td>
                                                <td class="{{ $isRefund ? 'text-danger' : 'text-success' }}">{{ $isRefund ? '-' : '' }}{{ number_format($displayAmount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted">No payment data found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="ipd-history" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Admission No</th>
                                    <th>Bed</th>
                                    <th>Consultant</th>
                                    <th>Admission</th>
                                    <th>Discharge</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $entry)
                                    <tr>
                                        <td>{{ $entry->admission_no }}</td>
                                        <td>{{ $entry->bed?->bed_code ?? '-' }}</td>
                                        <td>{{ $entry->consultantDoctor?->full_name ?? '-' }}</td>
                                        <td>{{ optional($entry->admission_date)->format('d-m-Y H:i') ?? '-' }}</td>
                                        <td>{{ optional($entry->discharge_date)->format('d-m-Y H:i') ?? '-' }}</td>
                                        <td>{{ $entry->discharge_date ? 'Discharged' : 'Active' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="ipd-prescriptions" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-info d-flex align-items-center justify-content-between">
                            <strong>IPD Prescriptions</strong>
                            @can('edit-ipd-patient')
                                @if(!$allocation->discharge_date)
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm open-ipd-prescription-form"
                                        data-form-url="{{ route('hospital.ipd-patient.prescription.form', ['allocation' => $allocation->id]) }}"
                                    >
                                        Add Prescription
                                    </button>
                                @endif
                            @endcan
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Consultant</th>
                                            <th>Medicine Items</th>
                                            <th>Valid Till</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ipd-prescriptions-table-body">
                                        @forelse($prescriptions as $prescription)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ optional($prescription->created_at)->format('d-m-Y h:i A') ?? '-' }}</td>
                                                <td>{{ $prescription->doctor?->full_name ?? '-' }}</td>
                                                <td>{{ $prescription->items->count() }}</td>
                                                <td>{{ optional($prescription->valid_till)->format('d-m-Y') ?? '-' }}</td>
                                                <td class="text-end">
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 me-2 open-ipd-prescription-view"
                                                        data-view-url="{{ route('hospital.ipd-patient.prescription.view', ['allocation' => $allocation->id, 'prescription' => $prescription->id]) }}"
                                                        data-bs-toggle="tooltip"
                                                        title="View Prescription"
                                                    >
                                                        <i class="text-primary fa-solid fa-eye"></i>
                                                    </button>
                                                    @if(!$allocation->discharge_date)
                                                        @can('edit-ipd-patient')
                                                            <button
                                                                type="button"
                                                                class="btn btn-link p-0 me-2 open-ipd-prescription-form"
                                                                data-form-url="{{ route('hospital.ipd-patient.prescription.edit-form', ['allocation' => $allocation->id, 'prescription' => $prescription->id]) }}"
                                                                data-bs-toggle="tooltip"
                                                                title="Edit Prescription"
                                                            >
                                                                <i class="text-info fa-solid fa-pen"></i>
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="btn btn-link p-0 me-2 delete-ipd-prescription-btn"
                                                                data-delete-url="{{ route('hospital.ipd-patient.prescription.destroy', ['allocation' => $allocation->id, 'prescription' => $prescription->id]) }}"
                                                                data-bs-toggle="tooltip"
                                                                title="Delete Prescription"
                                                            >
                                                                <i class="text-danger fa-solid fa-trash-can"></i>
                                                            </button>
                                                        @endcan
                                                    @endif
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 print-ipd-prescription-btn"
                                                        data-print-url="{{ route('hospital.ipd-patient.prescription.print', ['allocation' => $allocation->id, 'prescription' => $prescription->id]) }}"
                                                        data-bs-toggle="tooltip"
                                                        title="Print Prescription"
                                                    >
                                                        <i class="text-success fa-solid fa-print"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No prescription added yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="ipd-timeline" role="tabpanel">
                    <div class="notification">
                        <div class="card">
                            <div class="card-header">
                                <h5>Patient Timeline</h5>
                            </div>
                            <div class="card-body dark-timeline">
                                <ul>
                                    @forelse($timeline as $entry)
                                        @php
                                            $eventKey = (string) ($entry->event_key ?? '');
                                            $dotClass = str_contains($eventKey, 'deleted')
                                                ? 'activity-dot-warning'
                                                : 'activity-dot-primary';
                                            $logTime = $entry->logged_at ?? $entry->created_at;
                                        @endphp
                                        <li class="d-flex">
                                            <div class="{{ $dotClass }}"></div>
                                            <div class="w-100 ms-3">
                                                <p class="d-flex justify-content-between mb-2">
                                                    <span class="date-content light-background">{{ optional($logTime)->format('d M, Y') }}</span>
                                                    <span>{{ optional($logTime)->format('h:i A') }}</span>
                                                </p>
                                                <h6>{{ $entry->title }}<span class="dot-notification"></span></h6>
                                                @if($entry->description)
                                                    <span class="c-o-light d-block">{{ $entry->description }}</span>
                                                @endif
                                                <span class="badge badge-light-info mt-1 text-uppercase">{{ strtoupper((string) ($entry->encounter_type ?? 'IPD')) }}</span>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-center text-muted">No timeline activity found for this patient.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="ipd-discharge" role="tabpanel">

                    {{-- Patient / Admission Info Strip --}}
                    <div class="discharge-info-strip mb-3">
                        <div class="row g-0">
                            <div class="col-auto dis-cell">
                                <span class="dis-label">Patient</span>
                                <span class="dis-val fw-bold">{{ $allocation->patient?->name }}</span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">UHID</span>
                                <span class="dis-val">{{ $allocation->patient?->patient_id ?? '-' }}</span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">IPD No</span>
                                <span class="dis-val">{{ $allocation->admission_no ?? '-' }}</span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">Bed / Ward</span>
                                <span class="dis-val">{{ $allocation->bed?->bed_code ?? '-' }} / {{ $allocation->bed?->room?->ward?->ward_name ?? '-' }}</span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">Consultant</span>
                                <span class="dis-val">{{ $allocation->consultantDoctor ? trim($allocation->consultantDoctor->first_name . ' ' . $allocation->consultantDoctor->last_name) : '-' }}</span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">Admitted</span>
                                <span class="dis-val">{{ optional($allocation->admission_date)->format('d-m-Y') }}</span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">Discharge</span>
                                <span class="dis-val">
                                    @if($allocation->discharge_date)
                                        {{ optional($allocation->discharge_date)->format('d-m-Y') }}
                                    @else
                                        <span class="text-muted">Not discharged</span>
                                    @endif
                                </span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">LOS</span>
                                <span class="dis-val">{{ $stayDays }} day(s)</span>
                            </div>
                            <div class="col-auto dis-cell">
                                <span class="dis-label">Payer</span>
                                <span class="dis-val">{{ $allocation->tpa?->name ?: 'Self' }}</span>
                            </div>
                            <div class="col-auto dis-cell ms-auto">
                                <span class="dis-label">Status</span>
                                <span class="dis-val fw-bold {{ $allocation->discharge_date ? 'text-secondary' : ($canDischargeNow ? 'text-success' : 'text-danger') }}">
                                    {{ $allocation->discharge_date ? 'Discharged' : ($canDischargeNow ? 'Cleared' : 'Pending Clearance') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 justify-content-center">

                        {{-- Bill Summary + Actions --}}
                        <div class="col-xl-5 col-lg-6">

                            {{-- Financial Summary --}}
                            <div class="dis-section mb-3">
                                <div class="dis-section-head">Bill Summary</div>
                                <table class="table table-sm dis-summary-table mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Total Charges</td>
                                            <td class="text-end fw-semibold">{{ number_format((float)$episodeTotalCharges, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td class="text-end text-warning-emphasis">- {{ number_format((float)$episodeTotalDiscount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tax</td>
                                            <td class="text-end">+ {{ number_format((float)$episodeTotalTax, 2) }}</td>
                                        </tr>
                                        <tr class="dis-summary-divider">
                                            <td>Net Amount</td>
                                            <td class="text-end fw-semibold">{{ number_format(max(0, (float)$episodeTotalCharges - (float)$episodeTotalDiscount + (float)$episodeTotalTax), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Amount Paid</td>
                                            <td class="text-end text-success fw-semibold">{{ number_format((float)$episodeTotalPaid, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Advance / Credit</td>
                                            <td class="text-end text-primary">{{ number_format((float)$advanceCredit, 2) }}</td>
                                        </tr>
                                        <tr class="dis-summary-due {{ $episodeTotalDue > 0 ? 'due-pending' : 'due-clear' }}">
                                            <td class="fw-bold">Balance Due</td>
                                            <td class="text-end fw-bold fs-6">{{ number_format((float)$episodeTotalDue, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        {{-- Readiness + Actions --}}
                        <div class="col-xl-4 col-lg-6">

                            {{-- Discharge Readiness --}}
                            <div class="dis-section mb-3">
                                <div class="dis-section-head">Discharge Readiness</div>
                                <div class="p-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        @if($canDischargeNow)
                                            <span class="dis-ready-dot bg-success"></span>
                                            <span class="fw-semibold text-success">Bill Cleared — Ready for Discharge</span>
                                        @else
                                            <span class="dis-ready-dot bg-danger"></span>
                                            <span class="fw-semibold text-danger">Pending Financial Clearance</span>
                                        @endif
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="dis-mini-stat">
                                                <div class="dis-mini-label">Total Items</div>
                                                <div class="dis-mini-val">{{ $episodeCharges->count() }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="dis-mini-stat {{ $pendingEpisodeChargeIds->count() > 0 ? 'border-danger' : '' }}">
                                                <div class="dis-mini-label">Pending Items</div>
                                                <div class="dis-mini-val {{ $pendingEpisodeChargeIds->count() > 0 ? 'text-danger' : 'text-success' }}">{{ $pendingEpisodeChargeIds->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="dis-section">
                                <div class="dis-section-head">Actions</div>
                                <div class="p-3 d-grid gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-primary open-charge-payment-form"
                                        data-url="{{ route('hospital.opd-patient.charges.show-payment-form', ['patient' => $allocation->patient_id]) }}"
                                        data-charge-ids="{{ implode(',', $pendingEpisodeChargeIds->all()) }}"
                                        @if($pendingEpisodeChargeIds->isEmpty()) disabled @endif
                                    >
                                        <i class="ti ti-credit-card me-1"></i>
                                        {{ $pendingEpisodeChargeIds->isEmpty() ? 'No Pending Charges' : 'Receive Payment / Apply Discount' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline-warning open-charge-payment-form"
                                        data-url="{{ route('hospital.opd-patient.charges.show-refund-form', ['patient' => $allocation->patient_id]) }}"
                                    >
                                        <i class="ti ti-arrow-back-up me-1"></i> Refund Advance
                                    </button>
                                    <a href="{{ route('hospital.ipd-patient.final-bill.print', ['allocation' => $allocation->id]) }}" target="_blank" class="btn btn-outline-dark">
                                        <i class="ti ti-printer me-1"></i> Print Final Bill
                                    </a>
                                    @can('edit-ipd-patient')
                                        @if(!$allocation->discharge_date)
                                            <button
                                                type="button"
                                                class="btn {{ $canDischargeNow ? 'btn-success discharge-ipd-btn' : 'btn-secondary' }} mt-1"
                                                data-id="{{ $allocation->id }}"
                                                data-url="{{ route('hospital.ipd-patient.discharge.showform', ['allocation' => $allocation->id]) }}"
                                                {{ $canDischargeNow ? '' : 'disabled' }}
                                            >
                                                <i class="ti ti-logout me-1"></i>
                                                {{ $canDischargeNow ? 'Proceed to Discharge' : 'Clear Bill to Enable Discharge' }}
                                            </button>
                                        @else
                                            <div class="alert alert-success py-2 mb-0 text-center">
                                                <strong>Patient Discharged</strong><br>
                                                <small>{{ optional($allocation->discharge_date)->format('d-m-Y H:i') }}</small>
                                            </div>
                                        @endif
                                    @endcan
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="chargePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" id="chargePaymentContent"></div>
    </div>
</div>

<div class="modal fade" id="ipdDiagnosticOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" id="ipdDiagnosticOrderContent"></div>
    </div>
</div>

<div class="modal fade" id="ipdPrescriptionModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" id="ipdPrescriptionContent"></div>
    </div>
</div>
@endsection

@push('styles')
@include('layouts.partials.flatpickr-css')
<style>
    .ipd-profile-v2 .ipd-tabs .nav-link {
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 600;
        font-size: 0.92rem;
    }

    .ipd-profile-v2 .metric-card {
        background: linear-gradient(145deg, #ffffff, #f7fbff);
    }

    .ipd-profile-v2 .ipd-overview-card {
        border-radius: 14px;
        box-shadow: 0 0.35rem 0.9rem rgba(15, 23, 42, 0.08);
    }

    .ipd-profile-v2 .ipd-overview-card-header {
        padding: 0.72rem 1rem;
        border-bottom: 0;
    }

    .ipd-profile-v2 .ipd-overview-card-body {
        padding: 0.95rem 1rem;
        line-height: 1.45;
    }

    .ipd-profile-v2 .ipd-summary-grid > div {
        margin-bottom: 0.1rem;
        font-size: 0.84rem;
    }

    .ipd-profile-v2 .ipd-text-block {
        line-height: 1.45;
    }

    .ipd-profile-v2 .clinical-header-actions {
        justify-content: flex-end;
    }

    .ipd-profile-v2 .clinical-source-badge {
        font-size: 0.72rem;
    }

    .ipd-profile-v2 .clinical-edit-btn {
        min-width: 70px;
        font-weight: 600;
        border-width: 1px;
        background: rgba(255, 255, 255, 0.08);
    }

    .ipd-profile-v2 .clinical-edit-btn:hover,
    .ipd-profile-v2 .clinical-edit-btn:focus {
        color: #0d6efd;
        background: #ffffff;
        border-color: #ffffff;
    }

    .ipd-profile-v2 #ipd-clinical-form {
        border: 1px dashed #c7d2e3;
        border-radius: 8px;
        background: #f8fbff;
        padding: 10px;
    }

    .ipd-profile-v2 #ipd-clinical-form .form-label {
        font-size: 0.73rem;
        font-weight: 600;
        color: #495057;
    }

    .ipd-profile-v2 #ipd-clinical-form .family-history-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .ipd-profile-v2 #ipd-clinical-form .family-history-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        border: 1px solid #d5dde8;
        border-radius: 999px;
        padding: 0.2rem 0.55rem;
        font-size: 0.74rem;
        background: #fff;
    }

    .ipd-profile-v2 .ipd-notes-card {
        border-radius: 14px;
        overflow: hidden;
    }

    .ipd-profile-v2 .ipd-note-form .form-select,
    .ipd-profile-v2 .ipd-note-form .form-control {
        min-height: calc(1.5em + 0.5rem + 2px);
    }

    .ipd-profile-v2 .ipd-note-form textarea.form-control {
        min-height: 70px;
        resize: vertical;
    }

    .ipd-profile-v2 .ipd-note-save-btn {
        min-height: 70px;
        font-weight: 600;
    }

    .ipd-profile-v2 .ipd-note-item {
        background: #fbfdff;
        border-color: #e3ebf3 !important;
    }

    .ipd-profile-v2 .ipd-billing-pane .ipd-billing-toolbar {
        margin-bottom: 0.35rem !important;
    }

    .ipd-profile-v2 .ipd-billing-pane .ipd-billing-toolbar .btn {
        padding: 0.28rem 0.55rem;
        font-size: 0.76rem;
        line-height: 1.15;
    }

    .ipd-profile-v2 .ipd-billing-pane .compact-stat {
        padding: 0.38rem 0.5rem;
        font-size: 0.74rem;
        line-height: 1.1;
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }

    .ipd-profile-v2 .ipd-billing-pane .compact-stat span {
        color: #6c757d;
        font-weight: 600;
    }

    .ipd-profile-v2 .ipd-billing-pane .compact-stat strong {
        font-size: 0.9rem;
        color: #212529;
    }

    .ipd-profile-v2 .ipd-billing-pane .ipd-ledger-scroll {
        max-height: 62vh;
        overflow: auto;
        border: 1px solid #e9ecef;
        border-radius: 0.35rem;
    }

    .ipd-profile-v2 .ipd-billing-pane .ipd-billing-table {
        margin-bottom: 0;
    }

    .ipd-profile-v2 .ipd-billing-pane .ipd-billing-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8f9fa;
        white-space: nowrap;
        font-size: 0.72rem;
        padding: 0.34rem 0.32rem;
    }

    .ipd-profile-v2 .ipd-billing-pane .ipd-billing-table tbody td {
        font-size: 0.74rem;
        padding: 0.32rem 0.32rem;
        white-space: nowrap;
    }

    .ipd-profile-v2 .ipd-billing-pane .ipd-billing-table .particular-col {
        min-width: 210px;
        max-width: 300px;
        white-space: normal;
    }

    .ipd-profile-v2 .ipd-billing-pane #ipd-charges-ledger-tabs .nav-link {
        padding: 0.28rem 0.55rem;
        font-size: 0.76rem;
        line-height: 1.2;
    }

    /* ── Discharge Tab ─────────────────────────────────────── */
    .discharge-info-strip {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 0;
    }
    .discharge-info-strip .row { width: 100%; row-gap: 6px; }
    .dis-cell {
        display: flex;
        flex-direction: column;
        padding: 4px 12px 4px 0;
        border-right: 1px solid #dee2e6;
        margin-right: 12px;
        min-width: 125px;
    }
    .dis-cell:last-child { border-right: none; margin-right: 0; }
    .dis-label {
        font-size: 0.68rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .dis-val {
        font-size: 0.82rem;
        color: #212529;
        line-height: 1.25;
        white-space: normal;
    }
    .dis-section {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
    }
    .dis-section-head {
        background: #f1f3f5;
        border-bottom: 1px solid #dee2e6;
        padding: 7px 12px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #495057;
    }
    .dis-table { font-size: 0.78rem; }
    .dis-table thead th {
        background: #f8f9fa;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
        padding: 5px 6px;
        border-bottom: 2px solid #dee2e6;
    }
    .dis-table tbody td { padding: 4px 6px; vertical-align: middle; }
    .dis-table tfoot .dis-foot td {
        background: #f8f9fa;
        font-size: 0.77rem;
        padding: 5px 6px;
        border-top: 2px solid #dee2e6;
    }
    .dis-row-due { background: #fff9f9; }
    .dis-module-badge {
        display: inline-block;
        font-size: 0.65rem;
        padding: 1px 6px;
        border-radius: 3px;
        font-weight: 600;
        background: #e9ecef;
        color: #495057;
        text-transform: capitalize;
        border: 1px solid #dee2e6;
    }
    .dis-mod-bed { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
    .dis-mod-procedure { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
    .dis-mod-medicine { background: #ede9fe; color: #5b21b6; border-color: #ddd6fe; }
    .dis-mod-service { background: #fef3c7; color: #92400e; border-color: #fde68a; }
    .dis-mod-pathology, .dis-mod-radiology { background: #fce7f3; color: #9d174d; border-color: #fbcfe8; }
    .dis-summary-table { font-size: 0.82rem; }
    .dis-summary-table td { padding: 6px 10px; }
    .dis-summary-divider td { border-top: 2px solid #dee2e6 !important; }
    .dis-summary-due td {
        padding: 8px 10px;
        font-size: 0.9rem;
    }
    .dis-summary-due.due-pending td { background: #fff5f5; color: #dc3545; border-top: 2px solid #f5c6cb !important; }
    .dis-summary-due.due-clear td { background: #f0fff4; color: #198754; border-top: 2px solid #b2dfdb !important; }
    .dis-ready-dot {
        display: inline-block;
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .dis-mini-stat {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 8px 10px;
        text-align: center;
    }
    .dis-mini-label { font-size: 0.68rem; color: #6c757d; font-weight: 600; text-transform: uppercase; }
    .dis-mini-val { font-size: 1.1rem; font-weight: 700; }

    #ipd-discharge .btn.btn-primary:disabled {
        background-color: #d9dde1;
        border-color: #d9dde1;
        color: #495057;
        opacity: 1;
    }

    @media (max-width: 1199.98px) {
        .dis-cell {
            border-right: none;
            margin-right: 0;
            min-width: calc(33.333% - 4px);
        }
    }

    @media (max-width: 767.98px) {
        .dis-cell {
            min-width: calc(50% - 4px);
        }
    }

    @media (max-width: 575.98px) {
        .dis-cell {
            min-width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
@include('layouts.partials.flatpickr-js')
<script src="{{ asset('public/modules/sa/opd-care-shared.js') }}"></script>
<script>
    if (window.flatpickr && document.getElementById('ipd-note-datetime')) {
        flatpickr('#ipd-note-datetime', { enableTime: true, dateFormat: 'd-m-Y H:i' });
    }
</script>
@endpush
