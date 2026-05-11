<style>
  #hrx-panel-attendance {
    display:grid;
    gap:12px;
  }
  #hrx-panel-attendance .card {
    border:1px solid #cfd8e6;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 1px 2px rgba(10,22,40,.06);
  }
  #hrx-panel-attendance .card-header {
    padding:10px 12px;
    border-bottom:1px solid #d2dbe8;
    background:#f8fbff;
  }
  #hrx-panel-attendance .card-title {
    font-size:14px;
    font-weight:700;
    color:#12253b;
  }
  #hrx-panel-attendance .card-title i {
    font-size:12px;
    margin-right:2px;
  }
  #hrx-panel-attendance #hrxRegisterTable {
    width:100% !important;
    border-collapse:collapse;
  }
  #hrx-panel-attendance #hrxRegisterTable thead th {
    white-space:nowrap;
    background:#edf2f8;
    color:#58738d;
    font-size:11px;
    border-bottom:1px solid #d8e0ec;
    border-right:1px solid #d8e0ec;
    padding:8px 10px;
    text-align:center;
  }
  #hrx-panel-attendance #hrxRegisterTable thead th:first-child {
    text-align:left;
  }
  #hrx-panel-attendance #hrxRegisterTable tbody td {
    vertical-align:middle;
    font-size:12px;
    border-bottom:1px solid #edf2f8;
    border-right:1px solid #edf2f8;
    padding:8px 10px;
    text-align:center;
  }
  #hrx-panel-attendance #hrxRegisterTable tbody td:first-child {
    text-align:left;
    background:#f7faff;
    color:#10253c;
    font-weight:600;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  #hrx-panel-attendance .att-p,
  #hrx-panel-attendance .att-a,
  #hrx-panel-attendance .att-l,
  #hrx-panel-attendance .att-h {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:20px;
    height:20px;
    border-radius:6px;
    font-size:10px;
    font-weight:700;
  }
  #hrx-panel-attendance .att-p { background:#e8f5e9; color:#2e7d32; }
  #hrx-panel-attendance .att-a { background:#ffebee; color:#c62828; }
  #hrx-panel-attendance .att-l { background:#fff3e0; color:#e65100; }
  #hrx-panel-attendance .att-h { background:#e3f0ff; color:#1565c0; }
  /* Register badges: scoped to table so DataTables wrapper / font issues never hide codes */
  #hrxRegisterTable .hrx-att-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:22px;
    height:22px;
    padding:0 4px;
    border-radius:6px;
    font-size:11px;
    font-weight:800;
    line-height:1;
    letter-spacing:0.02em;
    font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
    box-sizing:border-box;
  }
  #hrxRegisterTable .hrx-att-badge--present { background:#e8f5e9; color:#1b5e20; }
  #hrxRegisterTable .hrx-att-badge--absent { background:#ffebee; color:#b71c1c; }
  #hrxRegisterTable .hrx-att-badge--leave { background:#fff3e0; color:#bf360c; border:1px solid rgba(191,54,12,.25); }
  #hrxRegisterTable .hrx-att-badge--holiday { background:#e3f0ff; color:#0d47a1; }
  #hrxRegisterWrap {
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
  }
  #hrx-panel-attendance .hrx-att-pager {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:8px;
    gap:8px;
    flex-wrap:wrap;
  }
  #hrx-panel-attendance .hrx-att-page-info {
    font-size:11px;
    color:#617a93;
    font-weight:600;
  }
  #hrx-panel-attendance .hrx-att-pager-btn:disabled {
    opacity:.5;
    cursor:not-allowed;
  }
  #hrx-panel-attendance .hrx-att-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    width:100%;
    flex-wrap:wrap;
  }
  #hrx-panel-attendance .hrx-att-controls {
    display:flex;
    align-items:center;
    gap:6px;
    flex-wrap:wrap;
    justify-content:flex-end;
  }
  #hrx-panel-attendance .hrx-att-control {
    min-width:136px;
    height:30px;
    background:#fff;
    border:1px solid #c8d4e5;
    border-radius:7px;
    font-size:11px;
    font-weight:600;
    color:#20344b;
    padding:4px 9px;
    outline:none;
  }
  #hrx-panel-attendance .hrx-att-control:focus {
    border-color:#1565c0;
    box-shadow:0 0 0 2px rgba(21,101,192,.12);
  }
  #hrx-panel-attendance .hrx-att-icon-btn {
    width:30px;
    height:30px;
    padding:0;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:7px;
    border:1px solid #c8d4e5;
    color:#3b4f67;
    background:#fff;
  }
  #hrx-panel-attendance .hrx-att-action-btn {
    height:30px;
    padding:0 10px;
    border-radius:7px;
    font-size:11px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:5px;
    border:1px solid #7f57cf;
    color:#5f35b2;
    background:#fff;
  }
  #hrx-panel-attendance .hrx-att-action-btn:hover,
  #hrx-panel-attendance .hrx-att-icon-btn:hover {
    background:#f5f1ff;
    border-color:#7f57cf;
    color:#5f35b2;
  }
  #hrx-panel-attendance .hrx-att-action-btn .fa-download {
    font-size:11px;
  }
  #hrx-panel-attendance .badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:3px 8px;
    border-radius:999px;
    font-size:10px;
    font-weight:700;
    line-height:1;
  }
  #hrx-panel-attendance .badge-gray { background:#eef2f5; color:#6a7280; }
  #hrx-panel-attendance .badge-green { background:#e8f6ea; color:#2e7d32; }
  #hrx-panel-attendance .badge-red { background:#fdecec; color:#c62828; }
  #hrx-panel-attendance .badge-orange { background:#fff3e3; color:#e65100; }
  #hrx-panel-attendance .badge-blue { background:#e5f0ff; color:#1565c0; }
  #hrx-panel-attendance .fw-700 { font-weight:700; color:#0f2033; }
  #hrx-panel-attendance .text-sm { font-size:12px; }
  #hrx-panel-attendance .text-muted { color:#6f86a0; }
  #hrx-panel-attendance .hrx-daily-att-edit {
    width:26px;
    height:22px;
    padding:0;
    border-radius:7px;
    border:1px solid #8558d6;
    color:#6f3fc7;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#fff;
  }
  #hrx-panel-attendance .hrx-daily-att-edit:hover {
    background:#f5f1ff;
    border-color:#7042c1;
    color:#5c2fb0;
  }
  #hrx-panel-attendance .hrx-daily-table-wrap {
    overflow-x:visible;
    border:1px solid #d8e0ec;
    border-radius:8px;
    background:#fff;
    padding:8px;
  }
  #hrx-panel-attendance .hrx-daily-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    width:100%;
    flex-wrap:wrap;
  }
  #hrx-panel-attendance .hrx-daily-filters {
    display:flex;
    align-items:center;
    gap:7px;
    flex-wrap:nowrap;
  }
  #hrx-panel-attendance .hrx-daily-field {
    position:relative;
    display:flex;
    align-items:center;
  }
  #hrx-panel-attendance .hrx-daily-field i {
    position:absolute;
    left:9px;
    font-size:11px;
    color:#7a90a8;
    pointer-events:none;
  }
  #hrx-panel-attendance .hrx-daily-filter {
    height:31px;
    border:1px solid #c9d5e6;
    border-radius:7px;
    background:#fcfeff;
    font-size:11px;
    font-weight:600;
    color:#20344b;
    padding:5px 9px;
    outline:none;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.8);
  }
  #hrx-panel-attendance #hrxDailySearch {
    width:188px;
    min-width:188px;
    padding-left:28px;
  }
  #hrx-panel-attendance #hrxDailyDeptFilter {
    width:146px;
    min-width:146px;
    padding-left:28px;
    padding-right:24px;
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;
  }
  #hrx-panel-attendance #hrxDailyDate {
    width:132px;
    min-width:132px;
    padding-left:28px;
    padding-right:6px;
  }
  #hrx-panel-attendance .hrx-daily-field.hrx-daily-select::after {
    content:'\f107';
    font-family:'FontAwesome';
    position:absolute;
    right:9px;
    top:50%;
    transform:translateY(-50%);
    font-size:12px;
    color:#7a90a8;
    pointer-events:none;
  }
  #hrx-panel-attendance .hrx-daily-filter:focus {
    border-color:#1565c0;
    box-shadow:0 0 0 2px rgba(21,101,192,.12);
  }
  #hrx-panel-attendance #hrxDailyAttendanceTable {
    width:100% !important;
    border-collapse:collapse;
  }
  #hrx-panel-attendance #hrxDailyAttendanceTable thead th {
    white-space:nowrap;
    background:#edf2f8;
    color:#58738d;
    font-size:11px;
    border-bottom:1px solid #d8e0ec;
    padding:8px 10px;
  }
  #hrx-panel-attendance #hrxDailyAttendanceTable tbody td {
    vertical-align:middle;
    font-size:12px;
    border-bottom:1px solid #edf2f8;
    padding:8px 10px;
  }
  #hrx-panel-attendance .dataTables_wrapper {
    padding:0;
  }
  #hrx-panel-attendance .dataTables_length,
  #hrx-panel-attendance .dataTables_filter {
    display:none !important;
  }
  #hrx-panel-attendance .hrx-dt-footer {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:8px;
    padding:6px 4px 0;
    flex-wrap:wrap;
  }
  #hrx-panel-attendance .dataTables_info {
    font-size:11px;
    color:#617a93;
    padding:10px 12px;
  }
  #hrx-panel-attendance .dataTables_paginate {
    padding:8px 12px !important;
  }
  #hrx-panel-attendance .dataTables_paginate .paginate_button {
    border:1px solid #c8d4e5 !important;
    border-radius:7px !important;
    background:#fff !important;
    color:#1c324a !important;
    font-size:11px !important;
    font-weight:600;
    min-width:30px;
    height:30px;
    line-height:17px;
    margin-left:4px;
  }
  #hrx-panel-attendance .dataTables_paginate .paginate_button.current {
    border-color:#1565c0 !important;
    color:#1565c0 !important;
    background:#e3f0ff !important;
  }
  #hrx-panel-attendance .dataTables_paginate .paginate_button.disabled {
    opacity:.5;
    cursor:not-allowed !important;
  }
  @media (max-width: 900px) {
    #hrx-panel-attendance .hrx-att-toolbar {
      align-items:flex-start;
    }
    #hrx-panel-attendance .hrx-att-controls {
      justify-content:flex-start;
      width:100%;
    }
    #hrx-panel-attendance .hrx-att-control {
      min-width:130px;
      flex:1 1 130px;
    }
    #hrx-panel-attendance .hrx-daily-filters {
      width:100%;
      flex-wrap:wrap;
    }
    #hrx-panel-attendance .hrx-daily-field {
      flex:1 1 130px;
    }
    #hrx-panel-attendance #hrxDailySearch,
    #hrx-panel-attendance #hrxDailyDeptFilter,
    #hrx-panel-attendance #hrxDailyDate {
      min-width:130px;
      width:100%;
    }
  }
</style>

@if(!($canViewAttendance ?? false))
  <div class="hrx-loading">You do not have permission to view attendance.</div>
@else
<div id="hrx-panel-attendance" data-week-start="{{ $weekStart ?? now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString() }}">
  <div class="card">
    <div class="card-header">
      <div class="hrx-att-toolbar">
        <span class="card-title">
          <i class="fas fa-calendar-check" style="color:#2e7d32"></i>
          Attendance Register
          <small id="hrxWeekLabel" style="margin-left:6px;color:var(--muted);font-weight:600"></small>
        </span>
        <div class="hrx-att-controls">
            @if($canCreateAttendance ?? false)
              <button type="button" class="btn btn-outline btn-sm hrx-att-action-btn hr-open-modal" data-modal-type="mark-attendance">
                  <i class="fa fa-plus"></i> Add Attendance
              </button>
            @endif
          <button class="btn btn-outline btn-sm hrx-att-icon-btn" id="hrxWeekPrev" type="button" title="Previous week"><i class="fas fa-chevron-left"></i></button>
          <input id="hrxWeekPicker" type="week" class="hrx-att-control">
          <select id="hrxDeptFilter" class="hrx-att-control">
          <option value="">All Departments</option>
          @foreach(($departments ?? collect()) as $department)
            <option value="{{ $department->id }}">{{ $department->name }}</option>
          @endforeach
          </select>
          <button class="btn btn-outline btn-sm hrx-att-icon-btn" id="hrxWeekNext" type="button" title="Next week"><i class="fas fa-chevron-right"></i></button>
          <button class="btn btn-outline btn-sm hrx-att-action-btn" id="hrxAttendanceRefresh" type="button"><i class="fas fa-refresh"></i> Refresh</button>
          <button class="btn btn-outline btn-sm hrx-att-action-btn" id="hrxAttendanceExport" type="button"><i class="fas fa-download"></i> Export</button>
        </div>
      </div>
    </div>
    <div class="card-body" style="padding:10px">
      <div id="hrxAttGridLoading" style="display:none;color:var(--muted);font-size:12px;padding:6px 0">
        <i class="fa fa-spinner fa-spin"></i> Loading attendance...
      </div>
      <div class="hrx-daily-table-wrap" id="hrxRegisterWrap">
        <table id="hrxRegisterTable" class="hrx-register-table">
          <thead></thead>
          <tbody></tbody>
        </table>
        <div id="hrxRegisterPager" class="hrx-att-pager" style="display:none;margin-top:8px;">
          <span id="hrxRegisterPageInfo" class="hrx-att-page-info"></span>
          <span style="display:flex;gap:6px;align-items:center">
            <button type="button" class="btn btn-outline btn-sm hrx-att-pager-btn" id="hrxRegisterPrev">Previous</button>
            <button type="button" class="btn btn-outline btn-sm hrx-att-pager-btn" id="hrxRegisterNext">Next</button>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="hrx-daily-toolbar">
        <span class="card-title"><i class="fas fa-clock" style="color:#1565c0"></i> Daily Attendance Entry</span>
        <div class="hrx-daily-filters">
         
          <div class="hrx-daily-field">
            <i class="fa fa-search"></i>
            <input type="text" id="hrxDailySearch" class="hrx-daily-filter" placeholder="Search staff...">
          </div>
          <div class="hrx-daily-field hrx-daily-select">
            <i class="fa fa-building-o"></i>
            <select id="hrxDailyDeptFilter" class="hrx-daily-filter">
              <option value="">All Departments</option>
              @foreach(($departments ?? collect()) as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="hrx-daily-field">
            <i class="fa fa-calendar"></i>
            <input type="date" id="hrxDailyDate" class="hrx-daily-filter" max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}">
          </div>
        </div>
      </div>
    </div>
    <div class="card-body" style="padding:10px">
      <div class="hrx-daily-table-wrap">
        <table id="hrxDailyAttendanceTable">
          <thead>
            <tr><th>Name</th><th>Designation</th><th>Shift</th><th>In Time</th><th>Out Time</th><th>Status</th><th>OT Hrs</th><th>Action</th></tr>
          </thead>
          <tbody id="dailyAttBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endif