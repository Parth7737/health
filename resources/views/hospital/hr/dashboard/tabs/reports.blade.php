<div id="hrx-panel-reports" data-staff-trend='@json($monthlyStaff)' data-payroll-trend='@json($payrollTrend)'>
    <div class="hrx-toolbar">
        <div class="hrx-filters">
            <select id="hrxReportsPeriod" class="hrx-select">
                <option value="6">Last 6 Months</option>
                <option value="3">Last 3 Months</option>
                <option value="12">Last 12 Months</option>
            </select>
        </div>
        <div class="hrx-actions">
            <button type="button" class="hrx-btn-lite" id="hrxReportsRefresh"><i class="fa fa-refresh"></i>Refresh</button>
            <button type="button" class="hrx-btn-lite" id="hrxReportsExport"><i class="fa fa-download"></i>Export</button>
        </div>
    </div>
    <div class="hrx-grid-two-even">
        <div class="hrx-card">
            <div class="hrx-card-header"><div class="hrx-card-title"><i class="fa fa-line-chart" style="color:#4a148c"></i>Staff Joining Trend</div></div>
            <div class="hrx-card-body"><canvas id="hrxStaffTrendChart" height="220"></canvas></div>
        </div>
        <div class="hrx-card">
            <div class="hrx-card-header"><div class="hrx-card-title"><i class="fa fa-area-chart" style="color:#2e7d32"></i>Payroll Trend</div></div>
            <div class="hrx-card-body"><canvas id="hrxPayrollTrendChart" height="220"></canvas></div>
        </div>
    </div>

    <div class="hrx-card">
        <div class="hrx-card-header"><div class="hrx-card-title"><i class="fa fa-table" style="color:#1565c0"></i>Department-wise Staff</div></div>
        <div class="hrx-table-wrap">
            <table class="hrx-table">
                <thead>
                <tr>
                    <th>Department</th>
                    <th>Staff Count</th>
                </tr>
                </thead>
                <tbody>
                @forelse($departmentBreakup as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->staff_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No department data found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
