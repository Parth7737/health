<div id="rad-ris-panel-protocols" class="rad-ris-panel">
    <div class="rad-ris-card">
        <div class="rad-ris-card-header">
            <h2 class="rad-ris-card-title"><i class="fa-solid fa-book-medical" style="color:#1565c0"></i> Imaging protocol library</h2>
            <div class="d-flex gap-2 flex-wrap">
                <input type="search" id="rad-ris-protocol-search" placeholder="Search protocols…" class="form-control" style="max-width:220px;padding:7px 12px;border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px">
                <button type="button" class="rad-ris-btn rad-ris-btn-primary rad-ris-btn-sm" id="rad-ris-protocol-settings"><i class="fa-solid fa-gear"></i> Manage in settings</button>
            </div>
        </div>
        <div class="rad-ris-card-body">
            <p class="rad-ris-text-muted rad-ris-text-sm mb-3">Protocols are loaded from your <strong>Radiology tests</strong> master (name, code, modality category, method / turnaround). Use Settings to add or edit tests.</p>
            <div class="rad-ris-protocol-grid" id="rad-ris-protocol-grid"></div>
        </div>
    </div>
</div>
