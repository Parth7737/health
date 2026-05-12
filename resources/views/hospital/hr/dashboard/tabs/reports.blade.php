<div id="hrx-panel-reports" data-reports='@json($reports)'>
<style>
.hrx-reports-wrap{position:relative;min-height:280px;}
.hrx-reports-loader{display:none;position:absolute;inset:0;z-index:20;flex-direction:column;align-items:center;justify-content:center;background:rgba(255,255,255,.9);border-radius:12px;}
.hrx-reports-loader.hrx-reports-loader--show{display:flex;}
</style>
<div class="hrx-reports-wrap">
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
        <div class="hrx-card" style="margin-bottom:16px">
            <div class="hrx-card-header">
                <div class="hrx-card-title"><i class="fa fa-line-chart" style="color:#4a148c"></i>Attendance Trend</div>
            </div>
            <div class="hrx-card-body" style="min-height:240px">
                <canvas id="hrxAttTrendChart" height="220"></canvas>
            </div>
        </div>
        <div class="hrx-card" style="margin-bottom:16px">
            <div class="hrx-card-header">
                <div class="hrx-card-title"><i class="fa fa-bar-chart" style="color:#e65100"></i>Leave Utilisation by Type</div>
            </div>
            <div class="hrx-card-body" style="min-height:240px">
                <canvas id="hrxLeaveTypeChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <div class="hrx-grid-two-even">
        <div class="hrx-card" style="margin-bottom:16px">
            <div class="hrx-card-header">
                <div class="hrx-card-title"><i class="fa fa-money" style="color:#2e7d32"></i>Monthly Payroll Trend</div>
            </div>
            <div class="hrx-card-body" style="min-height:240px">
                <canvas id="hrxPayrollTrendChart" height="220"></canvas>
            </div>
        </div>
        <div class="hrx-card" style="margin-bottom:16px">
            <div class="hrx-card-header">
                <div class="hrx-card-title"><i class="fa fa-table" style="color:#1565c0"></i>Department-wise Staff</div>
            </div>
            <div class="hrx-card-body" style="padding:0">
                <div class="hrx-table-wrap">
                    <table class="hrx-table">
                        <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total</th>
                            <th>Doctors</th>
                            <th>Nurses</th>
                            <th>Support</th>
                        </tr>
                        </thead>
                        <tbody id="hrxDeptStaffBody">
                        <tr><td colspan="5" class="text-muted" style="padding:14px">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="hrxReportsLoader" class="hrx-reports-loader" role="status" aria-live="polite" aria-busy="false">
        <i class="fa fa-spinner fa-spin fa-2x" style="color:#4a148c"></i>
        <div style="margin-top:12px;font-weight:700;color:#0d1b2a;font-size:14px">Loading reports…</div>
    </div>
</div>
</div>
