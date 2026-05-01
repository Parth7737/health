<div id="rad-ris-panel-modalities" class="rad-ris-panel">
    <div class="rad-ris-card-header" style="background:#fff;border-radius:12px 12px 0 0;border:1px solid var(--rad-border, #ccd8e8);border-bottom:none;padding:14px 18px;margin-bottom:0">
        <h2 class="rad-ris-card-title"><i class="fa-solid fa-x-ray" style="color:#1565c0"></i> Modality status board</h2>
        <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="rad-ris-refresh-modalities"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>
    <div class="rad-ris-modality-grid" style="padding:16px;background:#fff;border:1px solid var(--rad-border, #ccd8e8);border-top:none;border-radius:0 0 12px 12px" id="rad-ris-modality-grid"></div>
    <div class="rad-ris-card rad-ris-mt-4">
        <div class="rad-ris-card-header">
            <h2 class="rad-ris-card-title"><i class="fa-solid fa-chart-line" style="color:#1565c0"></i> Modality utilisation (selected date)</h2>
        </div>
        <div class="rad-ris-card-body">
            <canvas id="rad-ris-modality-util-chart" height="220"></canvas>
        </div>
    </div>
</div>
