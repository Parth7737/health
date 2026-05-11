<div id="hrx-panel-dashboard" data-headcount='@json($departmentSummary)' data-category='@json($categoryMix)' data-category-by-department='@json($categoryByDepartment)'>
    <div class="hrx-toolbar">
        <div class="hrx-filters">
            <select id="hrxDashboardDepartmentFilter" class="hrx-select">
                <option value="">All Departments</option>
                @foreach($departmentSummary as $row)
                    <option value="{{ $row['department'] }}">{{ $row['department'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="hrx-actions">
            <button type="button" class="hrx-btn-lite" id="hrxDashboardRefresh"><i class="fa fa-refresh"></i>Refresh</button>
            <!-- <button type="button" class="hrx-btn-lite" id="hrxDashboardExport"><i class="fa fa-download"></i>Export Snapshot</button> -->
        </div>
    </div>
    <div class="hrx-grid-two">
        <div>
            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title"><i class="fa fa-bar-chart" style="color:#4a148c"></i>Department Headcount</div>
                </div>
                <div class="hrx-card-body">
                    <canvas id="hrxHeadcountChart" height="200"></canvas>
                </div>
            </div>

            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title"><i class="fa fa-calendar-check-o" style="color:#2e7d32"></i>Today's Attendance Summary</div>
                    <button type="button" class="hrx-btn-lite" id="hrxDashboardAttendanceDetails"><i class="fa fa-external-link"></i>Details</button>
                </div>
                <div class="hrx-table-wrap">
                    <table class="hrx-table">
                        <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>On Leave</th>
                            <th>Rate</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($attendanceSummary as $row)
                            <tr class="hrx-attendance-summary-row" data-department="{{ strtolower($row['department']) }}">
                                <td>{{ $row['department'] }}</td>
                                <td>{{ $row['total'] }}</td>
                                <td><span class="hrx-badge green">{{ $row['present'] }}</span></td>
                                <td><span class="hrx-badge red">{{ $row['absent'] }}</span></td>
                                <td><span class="hrx-badge orange">{{ $row['on_leave'] }}</span></td>
                                <td><strong>{{ number_format((float) $row['rate'], 1) }}%</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No department attendance data available.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title"><i class="fa fa-pie-chart" style="color:#1565c0"></i>Staff Category Mix</div>
                </div>
                <div class="hrx-card-body">
                    <canvas id="hrxCategoryChart" height="220"></canvas>
                </div>
            </div>
            <div class="hrx-card">
                <div class="hrx-card-header">
                    <div class="hrx-card-title"><i class="fa fa-bell" style="color:#e65100"></i>HR Alerts</div>
                </div>
                <div class="hrx-card-body">
                    @foreach($alerts as $alert)
                        <div class="hrx-badge {{ $alert['type'] === 'warning' ? 'orange' : ($alert['type'] === 'success' ? 'green' : 'blue') }}" style="display:flex;margin-bottom:8px;white-space:normal">{{ $alert['text'] }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
