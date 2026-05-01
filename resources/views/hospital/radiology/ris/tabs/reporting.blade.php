<div id="rad-ris-panel-reporting" class="rad-ris-panel">
    {{-- .rad-ris-report-viewer is the 2-column grid (see radiology-ris.css); do not nest .rad-ris-grid-2 here. --}}
    <div class="rad-ris-report-viewer">
        {{-- Left: viewer stack + thumbnails --}}
        <aside class="rad-ris-reporting-sidebar">
            <div class="rad-ris-viewer-stack">
                <div class="rad-ris-image-viewer-box">
                    <div class="rad-ris-viewer-placeholder" id="rad-ris-dicom-placeholder">
                        <i class="fa-solid fa-x-ray" aria-hidden="true"></i>
                        <div class="rad-ris-text-sm rad-ris-mt-4">PACS / DICOM viewer placeholder</div>
                        <div class="rad-ris-text-sm rad-ris-mt-4" id="rad-ris-dicom-hint">
                            Configure <code>RADIOLOGY_PACS_WEB_VIEWER_URL</code> in <code>.env</code> and click <strong>Open viewer</strong>.
                            Supported placeholders: <code>{accession}</code>, <code>{order_no}</code>, <code>{patient_id}</code>.
                        </div>
                        <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm rad-ris-mt-4" id="rad-ris-dicom-open" disabled>
                            <i class="fa-solid fa-external-link-alt"></i> Open viewer
                        </button>
                    </div>
                </div>
                <div class="rad-ris-image-controls" role="toolbar" aria-label="Viewer tools">
                    <button type="button" class="rad-ris-ctrl-btn rad-ris-dicom-tool" data-msg="Zoom in"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                    <button type="button" class="rad-ris-ctrl-btn rad-ris-dicom-tool" data-msg="Zoom out"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                    <button type="button" class="rad-ris-ctrl-btn rad-ris-dicom-tool" data-msg="Pan"><i class="fa-solid fa-arrows-up-down-left-right"></i></button>
                    <button type="button" class="rad-ris-ctrl-btn rad-ris-dicom-tool" data-msg="Window / level"><i class="fa-solid fa-circle-half-stroke"></i></button>
                    <button type="button" class="rad-ris-ctrl-btn rad-ris-dicom-tool" data-msg="Measure"><i class="fa-solid fa-ruler"></i></button>
                    <button type="button" class="rad-ris-ctrl-btn rad-ris-dicom-tool" data-msg="Reset"><i class="fa-solid fa-rotate-left"></i></button>
                </div>
            </div>

            <div class="rad-ris-card rad-ris-mt-4">
                <div class="rad-ris-card-header">
                    <h2 class="rad-ris-card-title"><i class="fa-solid fa-images" style="color: #1565c0"></i> Study thumbnails</h2>
                </div>
                <div class="rad-ris-card-body">
                    <p class="rad-ris-text-muted rad-ris-text-sm mb-0">Thumbnails appear when PACS integration is connected.</p>
                </div>
            </div>
        </aside>

        {{-- Right: report editor + pending queue --}}
        <div class="rad-ris-report-form">
            <div class="rad-ris-card" id="rad-ris-rpt-editor-card">
                <div class="rad-ris-card-header">
                    <h2 class="rad-ris-card-title"><i class="fa-solid fa-file-medical" style="color: #1565c0"></i> Radiology report</h2>
                    <span class="rad-ris-badge rad-ris-badge-orange" id="rad-ris-rpt-status-badge">—</span>
                </div>
                <div class="rad-ris-card-body">
                    <div class="rad-ris-info-row"><span class="lbl">Patient</span><span class="rad-ris-fw-700" id="rad-ris-rpt-patient">—</span></div>
                    <div class="rad-ris-info-row"><span class="lbl">Accession</span><span id="rad-ris-rpt-accession">—</span></div>
                    <div class="rad-ris-info-row"><span class="lbl">Study</span><span id="rad-ris-rpt-study">—</span></div>
                    <div class="rad-ris-info-row"><span class="lbl">Referred by</span><span id="rad-ris-rpt-referred">—</span></div>
                    <hr class="rad-ris-divider">

                    <form id="radRisRptForm" class="rad-ris-rpt-form-inner">
                        <input type="hidden" name="item_id" id="rad-ris-rpt-item-id" value="">
                        <input type="hidden" name="save_action" id="radRisRptSaveAction" value="save">

                        <div class="form-group mb-3">
                            <label for="rad-ris-rpt-clinical">Clinical indication</label>
                            <textarea class="form-control" id="rad-ris-rpt-clinical" name="clinical_indication" rows="3" placeholder="Clinical history / indication…"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="rad-ris-rpt-technique">Technique</label>
                            <textarea class="form-control" id="rad-ris-rpt-technique" name="report_technique" rows="2" placeholder="Protocol / technique…"></textarea>
                        </div>

                        <div class="form-group mb-3 d-none" id="rad-ris-rpt-parameters-wrap">
                            <label class="d-flex align-items-center gap-2">
                                Study parameters / measurements
                                <span class="rad-ris-text-sm rad-ris-text-muted rad-ris-fw-400" id="rad-ris-rpt-parameters-count"></span>
                            </label>
                            <div class="table-responsive rad-ris-rpt-param-table-wrap">
                                <table class="table table-sm mb-0 rad-ris-rpt-param-table">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th class="text-center" style="width:72px;">Unit</th>
                                            <th class="text-center" style="width:120px;">Normal range</th>
                                            <th class="text-center" style="width:108px;">Result</th>
                                            <th class="text-center" style="width:112px;">Flag</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rad-ris-rpt-parameters-body"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rad-ris-rpt-findings">Findings</label>
                            <textarea class="form-control" id="rad-ris-rpt-findings" name="report_text" rows="6" placeholder="Findings…"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="rad-ris-rpt-impression">Impression / diagnosis</label>
                            <textarea class="form-control" id="rad-ris-rpt-impression" name="report_impression" rows="4" placeholder="Impression…"></textarea>
                        </div>
                        <input type="hidden" name="report_summary" id="rad-ris-rpt-summary" value="">

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="rad-ris-rpt-radiologist">Radiologist</label>
                                <select class="form-control" id="rad-ris-rpt-radiologist" name="report_radiologist_id">
                                    <option value="">— Select —</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="rad-ris-rpt-category">Report category</label>
                                <select class="form-control" id="rad-ris-rpt-category" name="report_category">
                                    <option value="Normal">Normal</option>
                                    <option value="Abnormal">Abnormal</option>
                                    <option value="Critical">Critical</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3 d-none" id="rad-ris-rpt-addendum-wrap">
                            <label for="rad-ris-rpt-addendum">Addendum (append to narrative)</label>
                            <textarea class="form-control" id="rad-ris-rpt-addendum" name="addendum_text" rows="2" placeholder="Addendum text…"></textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <button type="button" class="rad-ris-btn rad-ris-btn-success rad-ris-btn-sm" id="radRisRptFinalizeBtn"><i class="fa-solid fa-circle-check"></i> Finalize report</button>
                            <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="radRisRptDraftBtn"><i class="fa-solid fa-floppy-disk"></i> Save draft</button>
                            <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm d-none" id="radRisRptAddendumBtn" style="border-color: #e65100; color: #e65100"><i class="fa-solid fa-plus"></i> Addendum</button>
                            <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="radRisRptPrintBtn"><i class="fa-solid fa-print"></i> Print / PDF</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="rad-ris-card rad-ris-mt-4">
                <div class="rad-ris-card-header">
                    <h2 class="rad-ris-card-title"><i class="fa-solid fa-clock" style="color: #e65100"></i> Pending reports queue</h2>
                    <span class="rad-ris-text-sm rad-ris-text-muted">Examination / reporting</span>
                </div>
                <div class="rad-ris-card-body p-0">
                    <div class="table-responsive">
                        <table class="w-100 mb-0">
                            <thead>
                                <tr>
                                    <th class="rad-ris-text-sm text-muted px-3 py-2">Patient</th>
                                    <th class="rad-ris-text-sm text-muted px-3 py-2">Study</th>
                                    <th class="rad-ris-text-sm text-muted px-3 py-2">Priority</th>
                                    <th class="rad-ris-text-sm text-muted px-3 py-2">Waiting</th>
                                    <th class="rad-ris-text-sm text-muted px-3 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody id="rad-ris-pending-report-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
