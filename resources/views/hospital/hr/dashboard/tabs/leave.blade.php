<div id="hrx-panel-leave"
     data-status-chart='@json($leaveByStatus)'
     data-balance-chart='@json($leaveBalance)'
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
        {{-- ── LEFT : Leave Requests table ────────────────────────────── --}}
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
                    <table class="hrx-table">
                        <thead>
                            <tr>
                                <th>Req No</th>
                                <th>Staff</th>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($leaveRows as $row)
                            <tr class="hrx-leave-row"
                                data-request="{{ strtolower($row->request_no) }}"
                                data-name="{{ strtolower(trim(($row->staff->first_name ?? '') . ' ' . ($row->staff->last_name ?? ''))) }}"
                                data-status="{{ strtolower($row->status) }}"
                                data-note="{{ e($row->status_note ?? '') }}"
                                data-reason="{{ e($row->reason ?? '') }}"
                                data-type="{{ e($row->leaveType->name ?? 'General') }}"
                                data-display-name="{{ e(trim(($row->staff->first_name ?? '') . ' ' . ($row->staff->last_name ?? ''))) }}">
                                <td style="font-family:monospace;font-size:12px">{{ $row->request_no }}</td>
                                <td style="font-weight:700">{{ trim(($row->staff->first_name ?? '') . ' ' . ($row->staff->last_name ?? '')) }}</td>
                                <td><span class="hrx-badge blue">{{ $row->leaveType->name ?? 'General' }}</span></td>
                                <td style="color:#5a7894;font-size:12px">{{ optional($row->from_date)->format('d/m') }}</td>
                                <td style="color:#5a7894;font-size:12px">{{ optional($row->to_date)->format('d/m') }}</td>
                                <td style="font-weight:700">{{ number_format((float)$row->total_days, 1) }}</td>
                                <td style="font-size:12px;color:#5a7894;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $row->reason ?? '' }}">{{ $row->reason ?? '—' }}</td>
                                <td>
                                    <span class="hrx-badge {{ $row->status === 'Approved' ? 'green' : ($row->status === 'Rejected' ? 'red' : 'orange') }}">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="hrx-actions">
                                        @if($row->status === 'Pending')
                                            <button type="button" class="hrx-btn-lite hrx-leave-approve hrx-leave-approve-btn" data-request="{{ $row->request_no }}" title="Approve">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button type="button" class="hrx-btn-lite hrx-leave-reject hrx-leave-reject-btn" data-request="{{ $row->request_no }}" title="Reject">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        @else
                                            <button type="button" class="hrx-btn-lite hrx-leave-view hrx-leave-view-btn" data-request="{{ $row->request_no }}" title="View">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" style="text-align:center;color:#5a7894;padding:20px">No leave requests found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── RIGHT : Charts + Calendar ───────────────────────────────── --}}
        <div>
            {{-- Leave Balance Summary (stacked bar) --}}
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

            {{-- Leave Status Mix (doughnut) --}}
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

            {{-- Leave Calendar --}}
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
