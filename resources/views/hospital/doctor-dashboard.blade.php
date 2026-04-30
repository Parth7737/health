@extends('layouts.hospital.app',['is_dashbaord' => true])

@section('title', 'Doctor Dashboard')
@section('sidebar_variant', 'auto')
@section('header_system_title', 'Doctor Dashboard - OPD and IPD Workspace')
@section('header_system_prefix', 'Doctor Console - Real-time')
@section('page_header_icon', 'DR')
@section('page_subtitle', 'OPD queue, IPD patients and clinical orders in one workspace')
@section('breadcrumb_title', 'Doctor Workspace')
@section('critical_ticker', 'Critical potassium alert (Sunita Rawat) - Low hemoglobin alert (Meera Bisht) - 3 OPD patients waiting >15 min - Cardiology review advised for Kamla Devi')
@section('hide_page_header', 'true')


@push('styles')
<link rel="stylesheet" href="{{ asset('public/modules/sa/doctor-dashboard.css') }}">
<style>
  #doctorCareModal .modal-dialog.modal-xxl {
    max-width: none;
    width: calc(100vw - 16px);
    height: calc(100vh - 48px);
    margin: 24px 8px;
  }

  #doctorCareModal .modal-content {
    border-radius: 12px;
    border: 2px solid #2c6db6;
    background: #ffffff;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 48px);
    box-shadow: 0 18px 48px rgba(18, 49, 80, 0.28);
  }

  #doctorCareModal .modal-header {
    border-bottom: 1px solid #e3edf8;
    background: #ffffff;
    color: #0d1b2a;
    padding: 12px 16px;
  }

  #doctorCareModal .modal-title {
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.01em;
  }

  #doctorCareModal .modal-body {
    overflow-y: auto;
    background: #ffffff;
    padding: 12px;
    flex: 1 1 auto;
    min-height: 0;
  }

  #doctorCareModal #doctor-care-modal-content {
    min-height: 100%;
  }

  #doctorCareModal {
    padding-right: 0 !important;
  }

  #doctorCareModal .btn-close {
    width: 22px;
    height: 22px;
    border: 1px solid #d3e1ef;
    border-radius: 4px;
    opacity: 1;
    background-size: 9px;
  }
</style>
@endpush

@section('content')
@php
  $snapshot = $dashboardSnapshot ?? [];
  $doctor = $snapshot['doctor'] ?? [];
  $stats = $snapshot['stats'] ?? [];
  $ipdPatients = $snapshot['ipdPatients'] ?? [];
  $prescriptions = $snapshot['prescriptions'] ?? [];
  $labOrders = $snapshot['labOrders'] ?? [];
  $radiologyOrders = $snapshot['radiologyOrders'] ?? [];
  $clinicalFeed = $snapshot['clinicalFeed'] ?? [];
  $workboard = $snapshot['workboard'] ?? [];
  $alerts = $snapshot['alerts'] ?? [];
@endphp
<div class="doctor-dashboard">
  <div class="page-header">
    <div>
      <div class="page-title">🩺 My Clinical Workspace</div>
      <div class="page-subtitle">Morning Shift — <span id="liveClock" class="live-clock"></span> | {{ $doctor['name'] ?? auth()->user()->name }} | {{ $doctor['department'] ?? 'General Medicine' }}</div>
    </div>
    <div class="page-actions">
      <button class="btn btn-outline-primary btn-sm" type="button" onclick="window.DoctorDashboardApp?.openDoctorQuickPrescription?.()">➕ Start Consultation</button>
      <a href="{{ route('hospital.patient-management.index') }}" class="btn btn-primary btn-sm">👤 New Patient</a>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card stat-blue">
      <div class="stat-icon">📋</div>
      <div class="stat-info">
        <div class="stat-value" id="myOpd">{{ $stats['total_opd_today'] ?? 0 }}</div>
        <div class="stat-label">OPD Queue Today</div>
        <div class="stat-change neutral" id="myOpdMeta">{{ $stats['completed_count'] ?? 0 }} seen, {{ $stats['waiting_count'] ?? 0 }} waiting</div>
      </div>
    </div>
    <div class="stat-card stat-teal">
      <div class="stat-icon">🛏️</div>
      <div class="stat-info">
        <div class="stat-value">{{ $stats['active_ipd_count'] ?? 0 }}</div>
        <div class="stat-label">My IPD Patients</div>
        <div class="stat-change neutral">Active under your care</div>
      </div>
    </div>
    <div class="stat-card stat-orange">
      <div class="stat-icon">🧪</div>
      <div class="stat-info">
        <div class="stat-value">{{ $stats['pending_diagnostics_count'] ?? 0 }}</div>
        <div class="stat-label">Pending Lab Results</div>
        <div class="stat-change neutral">Critical tracking enabled</div>
      </div>
    </div>
    <div class="stat-card stat-purple">
      <div class="stat-icon">💊</div>
      <div class="stat-info">
        <div class="stat-value">{{ $stats['rx_today_count'] ?? 0 }}</div>
        <div class="stat-label">Prescriptions Today</div>
        <div class="stat-change up">↑ All sent to pharmacy</div>
      </div>
    </div>
    <div class="stat-card stat-red">
      <div class="stat-icon">📝</div>
      <div class="stat-info">
        <div class="stat-value">{{ $stats['overdue_queue_count'] ?? 0 }}</div>
        <div class="stat-label">Discharge Pending</div>
        <div class="stat-change neutral">Summaries needed</div>
      </div>
    </div>
    <div class="stat-card stat-green">
      <div class="stat-icon">✅</div>
      <div class="stat-info">
        <div class="stat-value">{{ $stats['completed_count'] ?? 0 }}</div>
        <div class="stat-label">Completed Today</div>
        <div class="stat-change up">↑ Good progress</div>
      </div>
    </div>
  </div>

  <div class="dash-grid cols-2 mb-4" style="grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title" id="queueCardTitle"><span class="ct-icon">📋</span> OPD Queue</div>
          <div class="card-subtitle" id="queueCardSubtitle">Patients waiting for consultation</div>
        </div>
        <div class="card-actions">
          <span class="badge badge-orange" id="queueCount">{{ $stats['waiting_count'] ?? 0 }} Waiting</span>
          <button class="btn btn-primary btn-xs queue-call-next-btn" type="button">📣 Call Next</button>
          <button class="btn btn-success btn-xs" type="button" id="queueViewCompletedBtn">Completed OPD (<span id="queueCompletedCount">0</span>)</button>
          <button class="btn btn-outline-primary btn-xs" type="button" id="queueViewWaitingBtn" style="display:none">← OPD Queue</button>
        </div>
      </div>
      <div class="table-wrap">
        <table class="hims-table" id="opdQueueTable">
          <thead>
            <tr>
              <th>Token</th>
              <th>Patient</th>
              <th>Age/Sex</th>
              <th>Complaint</th>
              <th>Wait</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="opdQueueBody">
            <tr><td colspan="6" class="text-center text-muted">Loading queue...</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title"><span class="ct-icon">🛏️</span> My IPD Patients</div>
          <div class="card-subtitle">Current ward admissions under my care</div>
        </div>
        <a href="{{ route('hospital.ipd-patient.index') }}" class="btn btn-secondary btn-xs">View All</a>
      </div>
      <div id="ipdPatientsList" class="list-wrap">
        @forelse ($ipdPatients as $patient)
          <div class="list-item">
            <div class="li-icon" style="background:{{ ($patient['status_color'] ?? '#2e7d32') . '18' }};color:{{ $patient['status_color'] ?? '#2e7d32' }};font-weight:700;font-size:12px">{{ strtoupper(substr($patient['name'] ?? 'P', 0, 1)) }}</div>
            <div class="li-content">
              <div class="li-title">{{ $patient['name'] }}</div>
              <div class="li-sub">{{ $patient['bed'] ?: 'Bed not mapped' }} | {{ $patient['department'] }}</div>
            </div>
            <div class="li-right">
              <span class="badge {{ $patient['status_badge_class'] ?? 'badge-green' }}" style="font-size:9.5px">{{ strtoupper($patient['status'] ?? 'stable') }}</span>
              @if (!empty($patient['days']))
                <div class="li-time">Day {{ $patient['days'] }}</div>
              @endif
              <a href="{{ $patient['profile_url'] }}" class="btn btn-primary btn-xs" style="margin-top:4px">Round</a>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-3">No active IPD patients assigned.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="card mb-4" style="margin-bottom:16px">
    <div class="card-header">
      <div class="card-title"><span class="ct-icon">📝</span> Clinical Orders & Prescriptions</div>
      <button class="btn btn-primary btn-xs" type="button" onclick="window.DoctorDashboardApp?.openDoctorQuickPrescription?.()">+ New Order</button>
    </div>
    <div class="card-body p-0">
      <div class="tabs-bar" id="orderTabsBar" style="padding:0 18px;margin-bottom:0">
        <button class="tab-btn active" type="button" data-target="rxPane">💊 e-Prescriptions <span class="tab-count">{{ count($prescriptions) }}</span></button>
        <button class="tab-btn" type="button" data-target="labPane">🧪 Lab Orders <span class="tab-count">{{ count($labOrders) }}</span></button>
        <button class="tab-btn" type="button" data-target="radPane">🩻 Radiology Orders <span class="tab-count">{{ count($radiologyOrders) }}</span></button>
        <button class="tab-btn" type="button" data-target="notesPane">📝 Clinical Notes</button>
      </div>

      <div id="rxPane" class="tab-pane-content active" style="padding:16px">
        <div class="table-wrap">
          <table class="hims-table">
          <thead>
            <tr>
              <th>Patient</th>
              <th>Drug</th>
              <th>Dose / Freq</th>
              <th>Days</th>
              <th>Status</th>
              <th>Time</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($prescriptions as $prescription)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $prescription['patient'] }}</div>
                  <div class="text-muted small">{{ $prescription['context'] }}</div>
                </td>
                <td>{{ $prescription['drug'] }}</td>
                <td>{{ trim(($prescription['dose'] ?? '-') . ' / ' . ($prescription['frequency'] ?? '-'), ' /') }}</td>
                <td>{{ $prescription['days'] }}</td>
                <td><span class="badge {{ ($prescription['status'] ?? '') === 'dispensed' ? 'badge-green' : 'badge-blue' }}">{{ strtoupper($prescription['status'] ?? 'sent') }}</span></td>
                <td>{{ $prescription['time'] ?: '-' }}</td>
                <td>
                  @if (!empty($prescription['action_url']))
                    <a href="{{ $prescription['action_url'] }}" class="btn btn-secondary btn-xs">{{ $prescription['action_label'] ?? 'View' }}</a>
                  @else
                    <span class="text-muted small">Ready</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted">No prescriptions created today.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      </div>

      <div id="labPane" class="tab-pane-content" style="padding:16px">
        <div class="table-wrap">
          <table class="hims-table">
          <thead>
            <tr>
              <th>Patient</th>
              <th>Test</th>
              <th>Status</th>
              <th>Result</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($labOrders as $order)
              <tr>
                <td class="fw-semibold">{{ $order['patient'] }}</td>
                <td>{{ $order['test'] }}</td>
                <td><span class="badge badge-orange">{{ strtoupper($order['status']) }}</span></td>
                <td>{{ $order['result'] }}</td>
                <td>{{ $order['time'] ?: '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">No pathology orders available.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      </div>

      <div id="radPane" class="tab-pane-content" style="padding:16px">
        <div class="table-wrap">
          <table class="hims-table">
          <thead>
            <tr>
              <th>Patient</th>
              <th>Test</th>
              <th>Modality</th>
              <th>Status</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($radiologyOrders as $order)
              <tr>
                <td class="fw-semibold">{{ $order['patient'] }}</td>
                <td>{{ $order['test'] }}</td>
                <td><span class="badge badge-indigo">{{ $order['modality'] }}</span></td>
                <td><span class="badge badge-blue">{{ strtoupper($order['status']) }}</span></td>
                <td>{{ $order['time'] ?: '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">No radiology orders available.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      </div>

      <div id="notesPane" class="tab-pane-content" style="padding:16px">
        <div class="timeline">
        @forelse ($clinicalFeed as $entry)
          <div class="tl-item">
            <div class="tl-dot"></div>
            <div class="tl-content">
              <div class="tl-time">{{ $entry['time'] ?? 'Just now' }} - {{ $entry['author'] ?? 'Care team' }}</div>
              <div class="tl-title">{{ $entry['patient'] }}</div>
              <div class="tl-sub">{{ $entry['title'] }}</div>
              <div class="tl-note">{{ \Illuminate\Support\Str::limit($entry['note'] ?? '', 160) }}</div>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-3">No recent clinical notes.</div>
        @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="dash-grid cols-2 mb-4" style="grid-template-columns:1fr 1fr;gap:16px">
    <div class="card">
      <div class="card-header">
        <div class="card-title"><span class="ct-icon">📅</span> Today's Schedule</div>
      </div>
      <div id="scheduleList" class="list-wrap">
        @forelse ($workboard as $item)
          <div class="list-item">
            <div class="li-icon">{{ $item['slot'] }}</div>
            <div class="li-content">
              <div class="li-title">{{ $item['label'] }}</div>
              <div class="li-sub">{{ $item['type'] }}</div>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-3">No schedule items.</div>
        @endforelse
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <div class="card-title"><span class="ct-icon">🚨</span> Clinical Alerts</div>
        <span class="badge badge-red">{{ count($alerts) }} Active</span>
      </div>
      <div id="clinicalAlerts" class="list-wrap">
        @forelse ($alerts as $alert)
          <div class="list-item alert-{{ $alert['type'] ?? 'info' }}">
            <div class="li-icon">{{ $alert['icon'] ?? 'AL' }}</div>
            <div class="li-content">
              <div class="li-title">{{ $alert['message'] }}</div>
              <div class="li-sub">{{ $alert['sub'] }}</div>
            </div>
            <div class="li-right">
              <div class="li-time">{{ $alert['time'] ?? 'Live' }}</div>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-3">No active alerts.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="modal fade" id="doctorCareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xxl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="doctorCareModalTitle">Doctor Care</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="doctor-care-modal-body">
          <div id="doctor-care-modal-content">
            <div class="p-4 text-muted">Select an action to continue.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- Doctor Dashboard Config & Initialization -->
<script>
  // Dashboard configuration - passed to doctor-dashboard.js
  window.DoctorDashboardConfig = {
    routes: {
      queueStatus: "{{ route('hospital.opd-patient.queue-status') }}",
      callNext: "{{ route('hospital.opd-patient.queue-call-next') }}",
      skipPatient: "{{ route('hospital.opd-patient.queue-skip', '__ID__') }}",
      undoSkip: "{{ route('hospital.opd-patient.queue-undo-skip', '__ID__') }}",
      queueList: "{{ route('hospital.opd-patient.doctor-queue-list') }}",
      careUnifiedForm: "{{ route('hospital.opd-patient.doctor-care.unified', ['opdPatient' => '__ID__']) }}",
      visitSummaryView: "{{ route('hospital.opd-patient.visit-summary.view', ['opdPatient' => '__ID__']) }}",
      prescriptionForm: "{{ route('hospital.opd-patient.prescription.form', ['opdPatient' => '__ID__']) }}",
      prescriptionStore: "{{ route('hospital.opd-patient.prescription.store', ['opdPatient' => '__ID__']) }}",
      prescriptionDestroy: "{{ route('hospital.opd-patient.prescription.destroy', ['opdPatient' => '__ID__']) }}",
      prescriptionLoadDosages: "{{ route('hospital.opd-patient.prescription.load-dosages') }}",
      diagnosticShow: "{{ route('hospital.opd-patient.diagnostics.showform', ['opdPatient' => '__ID__']) }}",
      diagnosticStore: "{{ route('hospital.opd-patient.diagnostics.store', ['opdPatient' => '__ID__']) }}",
      diagnosticDestroy: "{{ route('hospital.opd-patient.diagnostics.destroy', ['opdPatient' => '__ID__', 'item' => '__ITEM__']) }}",
      updateVitalsSocial: "{{ route('hospital.opd-patient.vitals-social.update', ['opdPatient' => '__ID__']) }}",
      visits: "{{ route('hospital.opd-patient.visits', ['patient' => '__ID__']) }}",
      visitSummaryPrint: "{{ route('hospital.opd-patient.visit-summary.print', ['opdPatient' => '__ID__']) }}",
      prescriptionPrint: "{{ route('hospital.opd-patient.prescription.print', ['opdPatient' => '__ID__']) }}"
    },
    csrf: "{{ csrf_token() }}",
    queuePreviewLimit: 20,
    permissions: {
      canPathology: @json(auth()->user()->can('create-pathology-order')),
      canRadiology: @json(auth()->user()->can('create-radiology-order'))
    },
    snapshot: @json($snapshot)
  };
</script>

<!-- External Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('public/front/assets/js/editor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('public/modules/sa/opd-care-shared.js') }}"></script>

<!-- Doctor Dashboard Module -->
<script src="{{ asset('public/modules/sa/doctor-dashboard.js') }}"></script>
@endpush
