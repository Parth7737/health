<div id="hrx-panel-leave"
     data-status-chart='@json($leaveByStatus)'
     data-balance-chart='@json($leaveBalance ?? [])'
     data-calendar='@json($leaveCalendar)'>

    <div class="hrx-toolbar">
        <div class="hrx-filters">
            <input type="text" id="hrxLeaveSearch" class="hrx-input" placeholder="Search request or staff">
            <select id="hrxLeaveStatus" class="hrx-select">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="hrx-actions">
            <button type="button" class="hrx-btn-lite" id="hrxLeaveRefresh"><i class="fa fa-refresh"></i>Refresh</button>
            <button type="button" class="hrx-btn-lite" id="hrxLeaveExport"><i class="fa fa-download"></i>Export</button>
        </div>
    </div>

    <div class="hrx-grid-two">
        <div>
            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title">
                        <i class="fa fa-calendar-times-o" style="color:#e65100"></i>Leave Requests
                    </div>
                    <button type="button" class="btn btn-warning btn-sm hr-open-modal" data-modal-type="leave-request-ajax">
                        <i class="fa fa-plus me-1"></i>Apply Leave
                    </button>
                </div>
                <div class="hrx-table-wrap">
                    <table class="hrx-table display table-striped" id="hrxLeaveRequestsTable" style="width:100%;">
                        <thead>
                        <tr>
                            <th style="display:none">ID</th>
                            <th>Req No</th>
                            <th>Staff</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title">
                        <i class="fa fa-bar-chart" style="color:#4a148c"></i>Leave Balance Summary
                    </div>
                </div>
                <div class="hrx-card-body" style="height:260px">
                    <canvas id="hrxLeaveBalanceChart"></canvas>
                </div>
            </div>

            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title">
                        <i class="fa fa-pie-chart" style="color:#1565c0"></i>Leave Status Mix
                    </div>
                </div>
                <div class="hrx-card-body" style="height:220px">
                    <canvas id="hrxLeaveStatusChart"></canvas>
                </div>
            </div>

            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title">
                        <i class="fa fa-calendar" style="color:#1565c0"></i>Leave Calendar
                    </div>
                </div>
                <div class="hrx-card-body" style="font-size:12px;line-height:1.8">
                    <div style="display:flex;gap:8px;flex-wrap:wrap" id="hrxLeaveCalendarBadges">
                        @forelse($leaveCalendar as $cal)
                            @php
                                $badgeCls = $cal['status'] === 'Approved' ? 'orange' : ($cal['status'] === 'Rejected' ? 'red' : 'blue');
                                $dateStr  = $cal['from'] === $cal['to'] ? $cal['from'] : $cal['from'].' – '.$cal['to'];
                            @endphp
                            <span class="hrx-badge {{ $badgeCls }}">
                                {{ $dateStr }} – {{ $cal['name'] }} – {{ $cal['type'] }}
                            </span>
                        @empty
                            <span style="color:#5a7894;font-size:12px">No upcoming leaves.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="hrxStaffLeaveBalanceModal" tabindex="-1" aria-labelledby="hrxStaffLeaveBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hrxStaffLeaveBalanceModalLabel">Leave balance summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <label for="hrxStaffLeaveBalanceYear" class="mb-0" style="font-size:13px;font-weight:600;">Year</label>
                    <select id="hrxStaffLeaveBalanceYear" class="form-select form-select-sm" style="max-width:140px;"></select>
                    <button type="button" class="btn btn-sm btn-primary" id="hrxStaffLeaveBalanceReload">Load</button>
                </div>
                <div id="hrxStaffLeaveBalanceBody" style="min-height:80px;">
                    <span class="text-muted" style="font-size:13px;">Select year and load.</span>
                </div>
            </div>
        </div>
    </div>
</div>
