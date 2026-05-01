<div id="rad-ris-panel-ai" class="rad-ris-panel">
    <div class="rad-ris-grid-2">
        <div>
            <div class="rad-ris-ai-box mb-4">
                <div class="rad-ris-ai-header">
                    <div class="rad-ris-ai-icon"><i class="fa-solid fa-robot"></i></div>
                    <div>
                        <h3 style="font-size:14px;font-weight:700;margin:0">AI-assisted findings</h3>
                        <div class="rad-ris-text-sm" style="opacity:.65">Optional module — not connected in this deployment.</div>
                    </div>
                </div>
                <div class="rad-ris-empty-ai" id="rad-ris-ai-placeholder">
                    No AI analyses on file. When an engine is connected, flagged studies will appear here.
                </div>
                <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" style="color:#fff;border-color:rgba(255,255,255,.35)" id="rad-ris-ai-run-demo"><i class="fa-solid fa-play"></i> Run demo toast</button>
            </div>
            <div id="rad-ris-ai-results-list"></div>
        </div>
        <div>
            <div class="rad-ris-card">
                <div class="rad-ris-card-header">
                    <h2 class="rad-ris-card-title"><i class="fa-solid fa-chart-pie" style="color:#6a1b9a"></i> AI detection summary</h2>
                </div>
                <div class="rad-ris-card-body">
                    <canvas id="rad-ris-ai-pie-chart" height="240"></canvas>
                </div>
            </div>
            <div class="rad-ris-card rad-ris-mt-4">
                <div class="rad-ris-card-header">
                    <h2 class="rad-ris-card-title"><i class="fa-solid fa-clock-rotate-left" style="color:#1565c0"></i> AI audit log</h2>
                </div>
                <div class="rad-ris-card-body p-0">
                    <table class="w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Patient</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Study</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Finding</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Conf.</th>
                                <th class="rad-ris-text-sm text-muted px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody id="rad-ris-ai-audit-body">
                            <tr><td colspan="5" class="px-3 py-3 rad-ris-text-muted rad-ris-text-sm">No audit entries.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
