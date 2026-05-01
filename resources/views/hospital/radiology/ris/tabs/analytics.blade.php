<div id="rad-ris-panel-reports" class="rad-ris-panel">
    <div class="rad-ris-grid-2 mb-4">
        <div class="rad-ris-card">
            <div class="rad-ris-card-header">
                <h2 class="rad-ris-card-title"><i class="fa-solid fa-chart-column" style="color:#1565c0"></i> Daily exams by modality</h2>
                <span class="rad-ris-text-sm rad-ris-text-muted">Rolling 7 days (stacked)</span>
            </div>
            <div class="rad-ris-card-body">
                <canvas id="rad-ris-exams-chart" height="240"></canvas>
            </div>
        </div>
        <div class="rad-ris-card">
            <div class="rad-ris-card-header">
                <h2 class="rad-ris-card-title"><i class="fa-solid fa-chart-line" style="color:#2e7d32"></i> Report TAT (weekly)</h2>
                <span class="rad-ris-text-sm rad-ris-text-muted">Avg hours by day (completed studies)</span>
            </div>
            <div class="rad-ris-card-body">
                <canvas id="rad-ris-tat-chart" height="240"></canvas>
            </div>
        </div>
    </div>
    <div class="rad-ris-grid-2">
        <div class="rad-ris-card">
            <div class="rad-ris-card-header">
                <h2 class="rad-ris-card-title"><i class="fa-solid fa-chart-pie" style="color:#6a1b9a"></i> Order sources</h2>
            </div>
            <div class="rad-ris-card-body">
                <canvas id="rad-ris-source-chart" height="240"></canvas>
            </div>
        </div>
        <div class="rad-ris-card">
            <div class="rad-ris-card-header">
                <h2 class="rad-ris-card-title"><i class="fa-solid fa-table" style="color:#e65100"></i> Monthly summary</h2>
                <span class="rad-ris-text-sm rad-ris-text-muted" id="rad-ris-analytics-month-label"></span>
            </div>
            <div class="rad-ris-card-body p-0">
                <div class="table-responsive">
                    <table class="w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Modality</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Orders</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Completed</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Pending</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Avg TAT</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="rad-ris-monthly-summary-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
