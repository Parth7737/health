@extends('layouts.hospital.app')
@section('title', 'Human Resource Management')
@section('page_subtitle', 'Integrated HR dashboard with tab-wise live data')

@section('page_header_actions')
    <button class="btn btn-primary btn-sm hr-open-modal" data-modal-type="add-staff">
        <i class="fa fa-plus me-1"></i>Add Staff
    </button>
    <button class="btn btn-outline-primary btn-sm hr-open-modal" data-modal-type="leave-request-ajax">
        <i class="fa fa-calendar me-1"></i>Apply Leave
    </button>
@endsection

@section('content')
    <div class="hrx-page" id="hrxPage">
        <div class="hrx-stats-grid">
            <div class="hrx-stat-card purple">
                <div class="hrx-label">Total Staff</div>
                <div class="hrx-value">{{ number_format($stats['totalStaff']) }}</div>
                <div class="hrx-meta">Across departments</div>
            </div>
            <div class="hrx-stat-card green">
                <div class="hrx-label">Present Today</div>
                <div class="hrx-value">{{ number_format($stats['presentToday']) }}</div>
                <div class="hrx-meta">Attendance captured live</div>
            </div>
            <div class="hrx-stat-card orange">
                <div class="hrx-label">On Leave</div>
                <div class="hrx-value">{{ number_format($stats['onLeave']) }}</div>
                <div class="hrx-meta">Approved active leaves</div>
            </div>
            <div class="hrx-stat-card blue">
                <div class="hrx-label">Monthly Payroll</div>
                <div class="hrx-value">INR {{ number_format((float) $stats['monthlyPayroll']) }}</div>
                <div class="hrx-meta">Current month net payout</div>
            </div>
            <div class="hrx-stat-card red">
                <div class="hrx-label">Vacancies</div>
                <div class="hrx-value">{{ number_format($stats['vacancies']) }}</div>
                <div class="hrx-meta">Open positions</div>
            </div>
        </div>

        <div class="hrx-tabbar" id="hrxTabbar">
            <button type="button" class="hrx-tab-btn active" data-tab="dashboard"><i class="fa fa-chart-pie"></i>Dashboard</button>
            <button type="button" class="hrx-tab-btn" data-tab="directory"><i class="fa fa-id-card"></i>Directory</button>
            @if($canViewAttendance ?? false)
                <button type="button" class="hrx-tab-btn" data-tab="attendance"><i class="fas fa-calendar-check"></i>Attendance</button>
            @endif
            <button type="button" class="hrx-tab-btn" data-tab="payroll"><i class="fas fa-money-check-alt"></i>Payroll</button>
            <button type="button" class="hrx-tab-btn" data-tab="leave"><i class="fas fa-calendar-times"></i>Leave</button>
            <button type="button" class="hrx-tab-btn" data-tab="recruitment"><i class="fa fa-user-plus"></i>Recruitment</button>
            <button type="button" class="hrx-tab-btn" data-tab="training"><i class="fa fa-graduation-cap"></i>Training</button>
            <button type="button" class="hrx-tab-btn" data-tab="reports"><i class="fa fa-bar-chart"></i>Reports</button>
        </div>

        <div id="hrxTabContainer" class="hrx-tab-container">
            <div class="hrx-loading">Loading HR tab...</div>
        </div>
    </div>
@endsection

@push('styles')
@include('layouts.partials.datatable-css')
<link rel="stylesheet" href="{{ asset('public/modules/sa/hr-dashboard/tabs/payroll.css') }}">
<style>
    .hrx-page { --hr-primary:#4a148c; --hr-blue:#1565c0; --hr-green:#2e7d32; --hr-orange:#e65100; --hr-red:#c62828; }
    .hrx-stats-grid { display:grid; grid-template-columns:repeat(5, minmax(0, 1fr)); gap:14px; margin-bottom:20px; }
    .hrx-stat-card { background:#fff; border:1px solid #ccd8e8; border-radius:12px; padding:16px; position:relative; overflow:hidden; }
    .hrx-stat-card::before { content:''; position:absolute; left:0; top:0; right:0; height:3px; }
    .hrx-stat-card.purple::before { background:linear-gradient(90deg, #4a148c, #7b1fa2); }
    .hrx-stat-card.blue::before { background:linear-gradient(90deg, #1565c0, #42a5f5); }
    .hrx-stat-card.green::before { background:linear-gradient(90deg, #2e7d32, #66bb6a); }
    .hrx-stat-card.orange::before { background:linear-gradient(90deg, #e65100, #ff9800); }
    .hrx-stat-card.red::before { background:linear-gradient(90deg, #c62828, #ef5350); }
    .hrx-label { font-size:11px; font-weight:600; color:#5a7894; margin-bottom:6px; }
    .hrx-value { font-size:24px; font-weight:800; line-height:1.1; color:#0d1b2a; }
    .hrx-meta { margin-top:4px; font-size:11px; color:#5a7894; }
    .hrx-tabbar { display:flex; gap:6px; flex-wrap:wrap; background:#fff; border:1px solid #ccd8e8; border-radius:12px; padding:6px; margin-bottom:16px; }
    .hrx-tab-btn { border:none; background:transparent; border-radius:8px; padding:8px 14px; font-size:13px; font-weight:600; color:#5a7894; display:flex; gap:7px; align-items:center; }
    .hrx-tab-btn.active { background:var(--hr-primary); color:#fff; box-shadow:0 2px 8px rgba(74,20,140,.3); }
    .hrx-tab-btn:not(.active):hover { background:#eef2f7; color:#0d1b2a; }
    .hrx-tab-container { min-height:380px; }
    .hrx-loading { background:#fff; border:1px solid #ccd8e8; border-radius:12px; padding:18px; color:#5a7894; }
    .hrx-card { background:#fff; border:1px solid #ccd8e8; border-radius:12px; overflow:hidden; margin-bottom:16px; }
    .hrx-card-header { padding:14px 18px; border-bottom:1px solid #ccd8e8; display:flex; justify-content:space-between; align-items:center; gap:10px; }
    .hrx-card-title { font-size:14px; font-weight:700; color:#0d1b2a; display:flex; gap:8px; align-items:center; }
    .hrx-card-body { padding:16px 18px; }
    .hrx-toolbar { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
    .hrx-filters { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .hrx-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .hrx-input, .hrx-select {
        height:34px;
        border:1px solid #ccd8e8;
        border-radius:8px;
        background:#fff;
        color:#0d1b2a;
        font-size:12px;
        padding:0 10px;
        min-width:150px;
    }
    .hrx-btn-lite {
        border:1px solid #ccd8e8;
        border-radius:8px;
        background:#fff;
        color:#5a7894;
        font-size:12px;
        font-weight:600;
        padding:7px 10px;
        display:inline-flex;
        align-items:center;
        gap:6px;
    }
    .hrx-btn-lite:hover { color:#0d1b2a; border-color:#1565c0; }

    .hrx-actions .hr-open-modal {
        background: linear-gradient(135deg, #4a148c, #7b1fa2);
        border-color: #4a148c;
        color: #fff;
        box-shadow: 0 4px 10px rgba(74, 20, 140, 0.24);
    }
    .hrx-actions .hr-open-modal:hover {
        background: linear-gradient(135deg, #3b0070, #6a1b9a);
        border-color: #3b0070;
        color: #fff;
    }

    .hrx-btn-lite.hrx-staff-view {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }
    .hrx-btn-lite.hrx-staff-view:hover {
        border-color: #93c5fd;
        background: #dbeafe;
        color: #1e40af;
    }

    .hrx-btn-lite.hrx-staff-edit {
        border-color: #fde68a;
        background: #fffbeb;
        color: #b45309;
    }
    .hrx-btn-lite.hrx-staff-edit:hover {
        border-color: #fcd34d;
        background: #fef3c7;
        color: #92400e;
    }

    .hrx-btn-lite.hrx-staff-delete {
        border-color: #fecaca;
        background: #fef2f2;
        color: #dc2626;
    }
    .hrx-btn-lite.hrx-staff-delete:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #b91c1c;
    }

    .hrx-btn-lite.hrx-leave-approve-btn {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
        box-shadow: 0 2px 6px rgba(21, 128, 61, 0.10);
    }
    .hrx-btn-lite.hrx-leave-approve-btn:hover {
        border-color: #86efac;
        background: #dcfce7;
        color: #166534;
        transform: translateY(-1px);
    }

    .hrx-btn-lite.hrx-leave-reject-btn {
        border-color: #fecaca;
        background: #fef2f2;
        color: #dc2626;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.10);
    }
    .hrx-btn-lite.hrx-leave-reject-btn:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #b91c1c;
        transform: translateY(-1px);
    }

    .hrx-btn-lite.hrx-leave-view-btn {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.10);
    }
    .hrx-btn-lite.hrx-leave-view-btn:hover {
        border-color: #93c5fd;
        background: #dbeafe;
        color: #1e40af;
        transform: translateY(-1px);
    }

    .hrx-directory-view-toggle {
        width:auto;
        min-width:unset;
        justify-content:center;
        transition:all .18s ease;
    }
    .hrx-directory-view-toggle.active {
        background:linear-gradient(90deg, #1565c0, #1e88e5);
        border-color:#1565c0;
        color:#fff;
        box-shadow:0 3px 10px rgba(21,101,192,.28);
    }
    .hrx-directory-view-toggle.active:hover {
        color:#fff;
        border-color:#1565c0;
        background:linear-gradient(90deg, #0d47a1, #1565c0);
    }
    .hrx-table-wrap { overflow:auto; }
    .hrx-table { width:100%; border-collapse:collapse; }
    .hrx-table th { background:#f5f7fb; font-size:12px; color:#5a7894; text-align:left; padding:10px 12px; border-bottom:1px solid #ccd8e8; white-space:nowrap; }
    .hrx-table td { font-size:13px; color:#0d1b2a; padding:10px 12px; border-bottom:1px solid #eef3f8; }
    .hrx-badge { display:inline-flex; padding:3px 9px; border-radius:999px; font-size:11px; font-weight:700; }
    .hrx-badge.green { background:#e8f5e9; color:#2e7d32; }
    .hrx-badge.red { background:#ffebee; color:#c62828; }
    .hrx-badge.orange { background:#fff3e0; color:#e65100; }
    .hrx-badge.blue { background:#e3f0ff; color:#1565c0; }
    .hrx-badge.purple { background:#f3e5f5; color:#4a148c; }
    .hrx-grid-two { display:grid; grid-template-columns:2fr 1fr; gap:16px; }
    .hrx-grid-two-even { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .hrx-staff-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:12px; }
    .hrx-staff-card { border:1px solid #ccd8e8; border-radius:12px; padding:14px; background:#fff; text-align:center; }
    .hrx-avatar { width:56px; height:56px; border-radius:50%; margin:0 auto 10px; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; color:#fff; background:#1565c0; }
    #hrxDirectoryListTable_wrapper .dt-buttons { display:flex !important; gap:6px; flex-wrap:wrap; margin-bottom:8px; }
    #hrxDirectoryListTable_wrapper .dataTables_paginate,
    #hrxDirectoryListTable_wrapper .dt-paging {
        float:none !important;
        text-align:left !important;
        margin-left:0 !important;
    }
    #hrxDirectoryListTable_wrapper .dt-layout-end {
        justify-content:flex-start !important;
    }
    @media (max-width: 1200px) { .hrx-stats-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 900px) { .hrx-grid-two, .hrx-grid-two-even { grid-template-columns:1fr; } }
</style>
@endpush

@push('scripts')
@include('layouts.partials.datatable-js')
<script>
    window.HRDashboardConfig = {
        tabUrl: @json(route('hospital.hr.dashboard.tab', ['tab' => '__TAB__'])),
        directoryLoadUrl: @json(route('hospital.hr.dashboard.directory-load')),
        directoryListDataUrl: @json(route('hospital.hr.dashboard.directory-list-data')),
        payrollListDataUrl: @json(route('hospital.hr.dashboard.payroll-list-data')),
        payrollPayslipCardUrl: @json(route('hospital.hr.dashboard.payroll-payslip-card')),
        payrollPayslipPdfUrl: @json(route('hospital.hr.dashboard.payroll-payslip-pdf', ['record' => '__RECORD__'])),
        payrollMarkPaidUrl: @json(route('hospital.hr.dashboard.payroll-mark-paid', ['record' => '__RECORD__'])),
        payrollExportCsvUrl: @json(route('hospital.hr.dashboard.payroll-export-csv')),
        payrollSendSlipUrl: @json(route('hospital.hr.dashboard.payroll-send-slip')),
        payrollSendSelectedUrl: @json(route('hospital.hr.dashboard.payroll-send-selected')),
        payrollSendBulkUrl: @json(route('hospital.hr.dashboard.payroll-send-bulk')),
        payrollMarkPaidBulkUrl: @json(route('hospital.hr.dashboard.payroll-mark-paid-bulk')),
        payrollProcessUrl: @json(route('hospital.hr.dashboard.process-payroll')),
        payrollDefaultMonth: @json($payrollDefaultMonth ?? now()->subMonth()->format('Y-m')),
        attendanceRegisterDataUrl: @json(route('hospital.hr.dashboard.attendance-register-data')),
        attendanceDailyDataUrl: @json(route('hospital.hr.dashboard.attendance-daily-data')),
        attendanceExportUrl: @json(route('hospital.hr.dashboard.attendance-export')),
        showModalUrl: @json(route('hospital.hr.dashboard.show-modal')),
        staffShowFormUrl: @json(route('hospital.hr.staff.showform')),
        staffStoreUrl: @json(route('hospital.hr.staff.store')),
        staffViewUrl: @json(route('hospital.hr.staff.view', ['staff' => '__STAFF__'])),
        staffDestroyUrl: @json(route('hospital.hr.staff.destroy', ['staff' => '__STAFF__'])),
        loadUnitsUrl: @json(route('hospital.load-units')),
        storeStaffUrl: @json(route('hospital.hr.dashboard.store-staff')),
        storeAttendanceUrl: @json(route('hospital.hr.dashboard.store-attendance')),
        storeLeaveUrl: @json(route('hospital.hr.dashboard.store-leave')),
        updateLeaveStatusUrl: @json(route('hospital.hr.dashboard.update-leave-status')),
    };
</script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/dashboard.js') }}"></script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/directory.js') }}"></script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/attendance.js') }}"></script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/payroll.js') }}"></script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/leave.js') }}"></script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/recruitment.js') }}"></script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/training.js') }}"></script>
<script src="{{ asset('public/modules/sa/hr-dashboard/tabs/reports.js') }}"></script>
@endpush
