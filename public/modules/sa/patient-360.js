/**
 * Patient 360 — New Order Modal
 *
 * Single-view unified layout (same UI as doctor-dashboard New Consultation).
 * OPD: Loads careUnifiedForm shell (SOAP + Vitals + Prescription + Lab Tests + Advice)
 * IPD: Builds inline 2-column shell (Prescription + Lab Tests)
 *
 * Requires: window.Patient360Config (injected by Blade)
 * Requires: opd-care-shared.js (prescription composer)
 */
;(function (win, doc) {
    'use strict';

    var cfg = win.Patient360Config;
    if (!cfg) { return; }

    // --- State ---
    var state = {
        mode:                    'opd',
        openContext:             'order',
        opdPatientId:            '',
        allocationId:            '',
        prescriptionStoreUrl:    '',
        hasExistingPrescription: false,
        diagnosticPriority: {
            pathology: 'Routine',
            radiology: 'Routine'
        }
    };

    var bsModal       = null;
    var prescComposer = null;
    var els           = {};

    // --- Init ---
    function init() {
        initIpdBedActions();

        var modalEl = doc.getElementById('p360Modal');
        if (!modalEl) { return; }

        els = {
            modal:   modalEl,
            body:    doc.getElementById('p360ModalBody'),
            title:   doc.getElementById('p360ModalTitle'),
            saveBtn: doc.getElementById('p360SaveBtn')
        };

        bsModal = (win.bootstrap && win.bootstrap.Modal)
            ? new win.bootstrap.Modal(modalEl)
            : null;

        var openButtons = doc.querySelectorAll('#patient360NewOrderBtn, #patient360PrescribeBtn, #patient360AddNoteBtn');
        openButtons.forEach(function (openBtn) {
            openBtn.addEventListener('click', function () {
                if (this.disabled || this.getAttribute('data-can-new-order') === '0') {
                    var reason = this.getAttribute('data-block-reason') || 'New orders are not allowed for this patient in the current visit state.';
                    if (win.notify && typeof win.notify === 'function') {
                        win.notify('New order', reason, 'info');
                    } else if (win.alert) {
                        win.alert(reason);
                    }
                    return;
                }
                state.mode         = this.dataset.mode || 'opd';
                state.openContext  = this.dataset.openContext || 'order';
                state.opdPatientId = this.dataset.opdId || '';
                state.allocationId = this.dataset.allocationId || '';
                openModal();
            });
        });

        if (els.saveBtn) {
            els.saveBtn.addEventListener('click', saveAll);
        }

        modalEl.addEventListener('hidden.bs.modal', onModalClose);
    }

    // --- Open modal ---
    function openModal() {
        prescComposer                 = null;
        state.prescriptionStoreUrl    = '';
        state.hasExistingPrescription = false;
        state.diagnosticPriority      = { pathology: 'Routine', radiology: 'Routine' };

        ensureUnifiedLayoutStyles();

        if (els.title) {
            els.title.textContent = state.mode === 'ipd' ? 'IPD — New Order' : 'OPD Consultation';
        }
        if (els.body) {
            els.body.innerHTML = '<div class="p-4 text-center text-muted">'
                + '<div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading workspace...</div>';
        }
        if (els.saveBtn) {
            els.saveBtn.style.display = 'none';
            els.saveBtn.disabled      = true;
        }

        if (bsModal) { bsModal.show(); }

        if (state.mode === 'opd') {
            loadOpdUnified();
        } else {
            loadIpdUnified();
        }
    }

    function ensureUnifiedLayoutStyles() {
        var styleEl = doc.getElementById('p360UnifiedLayoutStyle');
        var css = ''
            + '.doctor-care-unified-modal{background:#fff;min-height:100%;padding:0;}'
            + '.doctor-care-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;}'
            + '.doctor-care-col-stack{display:grid;gap:14px;}'
            + '.doctor-care-card{border:1px solid #dbe8f5;border-radius:11px;overflow:hidden;background:#fff;}'
            + '.doctor-care-card-head{border-bottom:1px solid #e3edf8;padding:10px 14px;font-size:14px;font-weight:700;color:#0d1b2a;background:#f8fbff;min-height:44px;display:flex;align-items:center;justify-content:space-between;gap:8px;}'
            + '.doctor-care-card-body{padding:12px 14px;}'
            + '.doctor-care-form-group{margin-bottom:10px;}'
            + '.doctor-care-form-group:last-child{margin-bottom:0;}'
            + '.doctor-care-form-group label{font-size:12px;color:#2c4460;font-weight:600;margin-bottom:4px;display:block;}'
            + '.doctor-care-section-title{display:inline-flex;align-items:center;gap:5px;}'
            + '.doctor-care-head-action{font-size:11px;font-weight:700;border-radius:4px;line-height:1;padding:5px 9px;height:24px;}'
            + '.doctor-care-unified-modal .prescription-workspace,.doctor-care-unified-modal .prescription-meta-card{background:transparent;border:0;border-radius:0;padding:0;}'
            + '.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid:not(.is-open){display:none !important;}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open{display:grid !important;grid-template-columns:3fr 1.6fr 2fr 2fr 2fr 1fr auto !important;grid-auto-flow:row !important;gap:10px !important;align-items:start !important;margin-bottom:12px;}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open > div{grid-column:auto !important;min-width:0;}'
            + '@media (max-width:1399.98px) and (min-width:992px){#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open{grid-template-columns:3fr 1.6fr 2fr 2fr 2fr 1fr auto !important;grid-auto-flow:row !important;}#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open > div{grid-column:auto !important;}}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open .form-control,#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open .form-select{height:42px !important;min-height:42px !important;font-size:13px !important;box-sizing:border-box !important;}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open .select2-container .select2-selection--single{height:42px !important;min-height:42px !important;display:flex;align-items:center;box-sizing:border-box !important;}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open .select2-container .select2-selection__rendered{line-height:40px !important;padding-left:10px;padding-right:20px;}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open .prescription-entry-actions .d-flex{align-items:center;min-height:42px;}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open #addPrescriptionItemRow{width:42px !important;height:42px !important;min-width:42px !important;min-height:42px !important;border-radius:8px !important;display:inline-flex !important;align-items:center !important;justify-content:center !important;padding:0 !important;line-height:1 !important;}'
            + '#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open #cancelPrescriptionItemEdit:not(.d-none){width:42px !important;height:42px !important;min-width:42px !important;min-height:42px !important;border-radius:8px !important;display:inline-flex !important;align-items:center !important;justify-content:center !important;padding:0 !important;line-height:1 !important;}'
            + '.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-items-shell{display:grid !important;gap:12px !important;overflow-x:auto;-webkit-overflow-scrolling:touch;}'
            + '.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable{border:1px solid #d6e4f1;border-radius:7px;overflow:hidden;font-size:11px;}'
            + '.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable thead th{background:#edf3f9;color:#56718d;border-color:#d6e4f1;text-transform:uppercase;font-size:10px;font-weight:700;letter-spacing:.02em;padding:7px 8px;}'
            + '.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable tbody td{border-color:#e4edf6;padding:7px 8px;font-size:11px;}'
            + '.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-notes-grid{display:none;}'
            + '.doctor-care-test-block{border:1px solid #d6e4f1;border-radius:8px;background:#f8fbff;padding:10px;margin-bottom:10px;display:none;}'
            + '.doctor-care-test-block.is-open{display:block;}'
            + '.doctor-care-unified-modal .doctor-care-test-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;align-items:end;}'
            + '.doctor-care-unified-modal .doctor-care-test-grid .doctor-care-form-group{margin-bottom:0;}'
            + '.doctor-care-test-added{padding:8px 10px;min-height:40px;font-size:12px;color:#6a84a0;border:1px solid #d6e4f1;border-radius:6px;background:#fff;display:flex;flex-wrap:wrap;gap:6px;}'
            + '.doctor-care-empty-text{color:#7a93ad;}'
            + '.doctor-care-test-badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;background:#e7f1ff;color:#0f56a5;border:1px solid #bfd7f5;font-size:11px;font-weight:600;padding:3px 10px;}'
            + '.doctor-care-test-remove{border:0;background:transparent;color:#0f56a5;font-size:11px;line-height:1;padding:0;cursor:pointer;font-weight:700;}'
            + '.doctor-care-hidden-slot{display:none;}'
            + '@media (max-width:991.98px){.doctor-care-grid{grid-template-columns:1fr;}#p360Modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open{min-width:880px;grid-template-columns:3fr 1.6fr 2fr 2fr 2fr 1fr auto !important;grid-auto-flow:row !important;}.doctor-care-unified-modal .doctor-care-test-grid{grid-template-columns:1fr;}}';

        if (styleEl) {
            styleEl.textContent = css;
            return;
        }
        styleEl = doc.createElement('style');
        styleEl.id = 'p360UnifiedLayoutStyle';
        styleEl.textContent = css;
        doc.head.appendChild(styleEl);
    }

    // --- OPD: use careUnifiedForm shell + fill slots ---
    async function loadOpdUnified() {
        try {
            var shellUrl  = rpl(cfg.routes.opd.careUnifiedForm, state.opdPatientId);
            var shellHtml = await fetchHtml(shellUrl);

            // Remove built-in action buttons from shell (we use our own Save button)
            var shellDoc  = new DOMParser().parseFromString(shellHtml, 'text/html');
            var actionsEl = shellDoc.querySelector('.doctor-care-actions');
            if (actionsEl) { actionsEl.remove(); }

            var root = shellDoc.querySelector('.doctor-care-unified-modal');
            if (els.body) {
                els.body.innerHTML = root ? root.outerHTML : shellHtml;
            }

            // Fetch prescription + pathology + radiology simultaneously
            var results = await Promise.all([
                fetchHtml(rpl(cfg.routes.opd.prescriptionForm, state.opdPatientId)),
                cfg.permissions.canPathology
                    ? postHtml(rpl(cfg.routes.opd.diagnosticShow, state.opdPatientId), { order_type: 'pathology' })
                    : Promise.resolve(''),
                cfg.permissions.canRadiology
                    ? postHtml(rpl(cfg.routes.opd.diagnosticShow, state.opdPatientId), { order_type: 'radiology' })
                    : Promise.resolve('')
            ]);

            fillPrescriptionSlot(results[0], false);
            fillDiagnosticSlot('pathology', results[1]);
            fillDiagnosticSlot('radiology', results[2]);
            wirePlugins();
            showSaveBtn();
            applyOpenContextFocus();
        } catch (err) {
            if (els.body) {
                els.body.innerHTML = '<div class="alert alert-danger m-3">Unable to load workspace. Please try again.</div>';
            }
        }
    }

    // --- IPD: build inline 2-column shell + fill slots ---
    async function loadIpdUnified() {
        var canPath = cfg.permissions.canPathology;
        var canRadi = cfg.permissions.canRadiology;

        try {
            var shellUrl  = rpl(cfg.routes.ipd.careUnifiedForm, state.allocationId, '__ALLOCATION__');
            var shellHtml = await fetchHtml(shellUrl);
            if (els.body) { els.body.innerHTML = shellHtml; }

            var results = await Promise.all([
                fetchHtml(rpl(cfg.routes.ipd.prescriptionForm, state.allocationId, '__ALLOCATION__')),
                canPath ? postHtml(rpl(cfg.routes.ipd.diagnosticShow, state.allocationId, '__ALLOCATION__'), { order_type: 'pathology' }) : Promise.resolve(''),
                canRadi ? postHtml(rpl(cfg.routes.ipd.diagnosticShow, state.allocationId, '__ALLOCATION__'), { order_type: 'radiology' }) : Promise.resolve('')
            ]);
            fillPrescriptionSlot(results[0], true);
            fillDiagnosticSlot('pathology', results[1]);
            fillDiagnosticSlot('radiology', results[2]);
            wirePlugins();
            showSaveBtn();
            applyOpenContextFocus();
        } catch (err) {
            if (els.body) {
                els.body.innerHTML = '<div class="alert alert-danger m-3">Unable to load form. Please try again.</div>';
            }
        }
    }

    // --- Fill prescription slot ---
    function fillPrescriptionSlot(html, isIpd) {
        var slot = doc.getElementById('doctorUnifiedPrescriptionSlot');
        if (!slot) { return; }

        var formId = isIpd ? '#ipdPrescriptionForm' : '#opdPrescriptionForm';
        var parsed = new DOMParser().parseFromString(html, 'text/html');
        var formEl = parsed.querySelector(formId) || parsed.querySelector('form');

        if (!formEl) {
            slot.innerHTML = '<div class="text-muted small p-2">Prescription form unavailable.</div>';
            return;
        }

        // Strip modal-footer (Complete & Print etc.)
        var footer = formEl.querySelector('.modal-footer');
        if (footer) { footer.remove(); }

        // Keep entry grid hidden initially — revealed via + Add Drug button
        var entryGrid = formEl.querySelector('.prescription-entry-grid');
        if (entryGrid) { entryGrid.style.display = 'none'; }

        slot.innerHTML = formEl.outerHTML;

        state.prescriptionStoreUrl = formEl.dataset.storeUrl || (
            isIpd
                ? rpl(cfg.routes.ipd.prescriptionStore, state.allocationId, '__ALLOCATION__')
                : rpl(cfg.routes.opd.prescriptionStore, state.opdPatientId)
        );
        state.hasExistingPrescription = !!(slot.querySelector('tbody#prescriptionItemsTbody tr.prescription-item-row'));

        var formInSlot = slot.querySelector('form');
        if (formInSlot) {
            if (!formInSlot.id) {
                formInSlot.id = isIpd ? 'ipdPrescriptionForm' : 'opdPrescriptionForm';
            }
            formInSlot.addEventListener('submit', function (e) { e.preventDefault(); });
        }

        // Wire + Add Drug button
        var addDrugBtn = doc.getElementById('doctorUnifiedAddDrugBtn');
        if (addDrugBtn) {
            addDrugBtn.onclick = function () {
                var grid = slot.querySelector('.prescription-entry-grid');
                if (grid) {
                    grid.style.display = '';
                    grid.classList.add('is-open');
                    grid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    grid.classList.remove('doctor-care-entry-flash');
                    void grid.offsetWidth;
                    grid.classList.add('doctor-care-entry-flash');
                }
                var medField = doc.getElementById('prescription_entry_medicine');
                if (medField) {
                    medField.focus();
                    if (win.jQuery) {
                        var $el = win.jQuery(medField);
                        if ($el.hasClass('select2-hidden-accessible')) { $el.select2('open'); }
                    }
                }
            };
        }
    }

    // --- Fill diagnostic slot ---
    function fillDiagnosticSlot(orderType, html) {
        var cap    = orderType.charAt(0).toUpperCase() + orderType.slice(1);
        var slot   = doc.getElementById('doctorUnified' + cap + 'Slot');
        if (!slot) { return; }
        if (!html) { slot.innerHTML = ''; return; }

        var parsed = new DOMParser().parseFromString(html, 'text/html');
        var form   = parsed.querySelector('form#saveDiagnosticOrderForm') || parsed.querySelector('form');
        if (!form) { slot.innerHTML = ''; return; }

        form.id               = 'saveDiagnosticOrderForm_' + orderType;
        form.dataset.orderType = orderType;
        form.dataset.storeUrl  = state.mode === 'ipd'
            ? rpl(cfg.routes.ipd.diagnosticStore, state.allocationId, '__ALLOCATION__')
            : rpl(cfg.routes.opd.diagnosticStore, state.opdPatientId);

        var selEl = form.querySelector('#diagnostic_test_ids, #ipd_diagnostic_test_ids');
        if (selEl) {
            selEl.id                    = 'diagnostic_test_ids_' + orderType;
            selEl.dataset.previewTarget = '#diagnostic-test-preview-body_' + orderType;
            selEl.classList.add('diagnostic-test-select');
        }

        var previewBody = form.querySelector('#diagnostic-test-preview-body, #ipd-diagnostic-test-preview-body');
        if (previewBody) { previewBody.id = 'diagnostic-test-preview-body_' + orderType; }

        var mFooter = form.querySelector('.modal-footer');
        if (mFooter) { mFooter.remove(); }

        slot.innerHTML = form.outerHTML;

        var formPriorityEl = slot.querySelector('select[name="priority"]');
        if (formPriorityEl) {
            formPriorityEl.value = state.diagnosticPriority[orderType] || 'Routine';
        }
    }

    /** Parse weight as kg from input (digits and one decimal). */
    function parseWeightKgForBmi(raw) {
        if (raw == null || String(raw).trim() === '') {
            return null;
        }
        var w = parseFloat(String(raw).replace(/,/g, '.').replace(/[^\d.]/g, ''));
        if (isNaN(w) || w < 1 || w > 500) {
            return null;
        }
        return w;
    }

    /**
     * Parse height: 50–300 treated as cm; 0.5–2.5 as metres.
     * Avoids treating "5.5" as 5.5 m (common typo vs cm / feet).
     */
    function parseHeightMetersForBmi(raw) {
        if (raw == null || String(raw).trim() === '') {
            return null;
        }
        var h = parseFloat(String(raw).replace(/,/g, '.').replace(/[^\d.]/g, ''));
        if (isNaN(h) || h <= 0) {
            return null;
        }
        if (h >= 50 && h <= 300) {
            return h / 100;
        }
        if (h >= 0.5 && h <= 2.5) {
            return h;
        }
        return null;
    }

    /** Wire height/weight → BMI for IPD or OPD unified vitals inside the modal. */
    function wireVitalsBmiAutoCalc() {
        if (!els.body) {
            return;
        }
        var form = els.body.querySelector('#doctorUnifiedIpdVitalsForm')
            || els.body.querySelector('#doctorUnifiedVitalsForm');
        if (!form || form.dataset.p360BmiWired === '1') {
            return;
        }
        var hEl = form.querySelector('input[name="height"]');
        var wEl = form.querySelector('input[name="weight"]');
        var bEl = form.querySelector('input[name="bmi"]');
        if (!hEl || !wEl || !bEl) {
            return;
        }
        form.dataset.p360BmiWired = '1';

        function recalc() {
            var hm = parseHeightMetersForBmi(hEl.value);
            var wkg = parseWeightKgForBmi(wEl.value);
            if (hm == null || wkg == null) {
                return;
            }
            var bmi = wkg / (hm * hm);
            if (!isFinite(bmi) || bmi < 5 || bmi > 90) {
                return;
            }
            bEl.value = String(Math.round(bmi * 10) / 10);
        }

        hEl.addEventListener('input', recalc);
        hEl.addEventListener('change', recalc);
        wEl.addEventListener('input', recalc);
        wEl.addEventListener('change', recalc);
        recalc();
    }

    // --- Wire plugins after slots filled ---
    function wirePlugins() {
        if (els.body) {
            els.body.querySelectorAll('form').forEach(function (f) {
                if (!f.dataset.p360Bound) {
                    f.dataset.p360Bound = '1';
                    f.addEventListener('submit', function (e) { e.preventDefault(); });
                }
            });
        }

        initSelect2();
        initPrescComposer();
        bindPrescriptionEvents();
        wireVitalsBmiAutoCalc();
        ensureDiagnosticPriorityControl();
        bindDiagnosticPicker();

        if (win.OPDCareShared && typeof win.OPDCareShared.refreshDiagnosticPreview === 'function') {
            if (els.body) {
                els.body.querySelectorAll('.diagnostic-test-select').forEach(function (sel) {
                    win.OPDCareShared.refreshDiagnosticPreview('#' + sel.id, sel.dataset.previewTarget || '#diagnostic-test-preview-body');
                });
            }
        }
    }

    // --- Prescription events (required for dosage loading and row actions) ---
    function bindPrescriptionEvents() {
        if (!(win.jQuery && prescComposer)) { return; }

        function ensureComposerPanelVisible() {
            var slot = doc.getElementById('doctorUnifiedPrescriptionSlot');
            if (!slot) { return; }
            var grid = slot.querySelector('.prescription-entry-grid');
            if (grid) {
                grid.style.display = '';
                grid.classList.add('is-open');
            }
        }

        win.jQuery(doc)
            .off('click.p360Rx', '#addPrescriptionItemRow')
            .on('click.p360Rx', '#addPrescriptionItemRow', function (event) {
                event.preventDefault();
                prescComposer.addOrUpdateFromComposer(function (message, focusSelector) {
                    notify('error', message);
                    var focusEl = doc.querySelector(focusSelector);
                    if (focusEl) { focusEl.focus(); }
                });
            })
            .off('click.p360Rx', '#cancelPrescriptionItemEdit')
            .on('click.p360Rx', '#cancelPrescriptionItemEdit', function (event) {
                event.preventDefault();
                prescComposer.clearComposer();
            })
            .off('click.p360Rx', '.edit-prescription-item-row')
            .on('click.p360Rx', '.edit-prescription-item-row', function (event) {
                event.preventDefault();
                ensureComposerPanelVisible();
                prescComposer.loadFromRow(win.jQuery(this).closest('tr.prescription-item-row'));
            })
            .off('click.p360Rx', '.remove-prescription-item-row')
            .on('click.p360Rx', '.remove-prescription-item-row', function (event) {
                event.preventDefault();
                prescComposer.removeRow(win.jQuery(this).closest('tr.prescription-item-row'));
            })
            .off('change.p360Rx', '#prescription_entry_medicine')
            .on('change.p360Rx', '#prescription_entry_medicine', function () {
                prescComposer.onMedicineChanged(true);
            })
            .off('select2:select.p360Rx', '#prescription_entry_medicine')
            .on('select2:select.p360Rx', '#prescription_entry_medicine', function () {
                prescComposer.onMedicineChanged(true);
            })
            .off('change.p360Rx', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_route, #prescription_entry_frequency')
            .on('change.p360Rx', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_route, #prescription_entry_frequency', function () {
                prescComposer.focusNextField(this.id);
            });
    }

    function showSaveBtn() {
        if (els.saveBtn) {
            els.saveBtn.style.display = '';
            els.saveBtn.disabled      = false;
            els.saveBtn.textContent   = 'Save';
        }
    }

    // --- Diagnostic picker ---
    function ensureDiagnosticPriorityControl() {
        var blockEl = doc.getElementById('doctorUnifiedTestBlock');
        if (!blockEl) { return; }

        var gridEl = blockEl.querySelector('.doctor-care-test-grid');
        if (!gridEl) { return; }

        var existing = doc.getElementById('doctorUnifiedTestPriority');
        if (!existing) {
            var wrapper = doc.createElement('div');
            wrapper.className = 'doctor-care-form-group';
            wrapper.innerHTML = '<label>Priority <span class="text-danger">*</span></label>'
                + '<select id="doctorUnifiedTestPriority" class="form-control">'
                + '<option value="Routine">Routine</option>'
                + '<option value="Urgent">Urgent</option>'
                + '<option value="STAT">STAT</option>'
                + '</select>';
            gridEl.appendChild(wrapper);
            existing = wrapper.querySelector('#doctorUnifiedTestPriority');
        }

        if (existing && !existing.value) {
            existing.value = 'Routine';
        }
    }

    function syncDiagnosticPriorityToForm(orderType, priority) {
        if (!orderType || !priority) { return; }
        state.diagnosticPriority[orderType] = priority;

        var formEl = doc.getElementById('saveDiagnosticOrderForm_' + orderType);
        var formPriorityEl = formEl ? formEl.querySelector('select[name="priority"]') : null;
        if (formPriorityEl) {
            formPriorityEl.value = priority;
        }
    }

    function getDiagnosticPriority(orderType) {
        var formEl = doc.getElementById('saveDiagnosticOrderForm_' + orderType);
        var formPriorityEl = formEl ? formEl.querySelector('select[name="priority"]') : null;
        return (formPriorityEl && formPriorityEl.value)
            ? formPriorityEl.value
            : (state.diagnosticPriority[orderType] || '');
    }

    function collectOptions(type) {
        var sel = doc.getElementById('diagnostic_test_ids_' + type);
        if (!sel) { return []; }
        return Array.from(sel.options)
            .filter(function (o) { return String(o.value || '').trim() !== ''; })
            .map(function (o) { return { id: String(o.value), label: o.textContent.trim() }; });
    }

    function setOptionSelected(type, testId, shouldSelect, prioritySnapshot) {
        var sel = doc.getElementById('diagnostic_test_ids_' + type);
        if (!sel) { return; }
        var opt = Array.from(sel.options).find(function (o) { return String(o.value) === String(testId); });
        if (!opt) { return; }
        opt.selected = !!shouldSelect;
        if (shouldSelect) {
            var pv = prioritySnapshot
                || (doc.getElementById('doctorUnifiedTestPriority') || {}).value
                || state.diagnosticPriority[type]
                || 'Routine';
            opt.dataset.selectedPriority = String(pv);
        } else if (opt.dataset) {
            delete opt.dataset.selectedPriority;
        }
        if (win.jQuery && win.jQuery.fn.select2 && win.jQuery(sel).hasClass('select2-hidden-accessible')) {
            win.jQuery(sel).trigger('change');
        } else {
            sel.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function readSelectedTests() {
        var tests = [];
        ['pathology', 'radiology'].forEach(function (type) {
            var sel = doc.getElementById('diagnostic_test_ids_' + type);
            if (!sel) { return; }
            Array.from(sel.selectedOptions || []).forEach(function (o) {
                if (String(o.value || '').trim()) {
                    tests.push({
                        type:     type,
                        id:       String(o.value),
                        label:    o.textContent.trim(),
                        priority: String(o.dataset.selectedPriority || state.diagnosticPriority[type] || 'Routine')
                    });
                }
            });
        });
        return tests;
    }

    function renderBadges() {
        var listEl = doc.getElementById('labTestsAdded');
        if (!listEl) { return; }
        var tests = readSelectedTests();
        if (!tests.length) {
            listEl.innerHTML = '<span class="doctor-care-empty-text">No tests added</span>';
            return;
        }
        listEl.innerHTML = tests.map(function (t) {
            var icon = t.type === 'radiology' ? '&#x1FA7B;' : '&#x1F9EA;';
            var pr = t.priority ? ' <span class="text-muted fw-normal">[' + esc(t.priority) + ']</span>' : '';
            return '<span class="doctor-care-test-badge">' + icon + ' ' + esc(t.label) + pr
                + ' <button type="button" class="doctor-care-test-remove" data-type="' + t.type
                + '" data-test-id="' + esc(t.id) + '">&#x2715;</button></span>';
        }).join('');
    }

    function bindDiagnosticPicker() {
        var addBtn  = doc.getElementById('doctorUnifiedAddTestBtn');
        var blockEl = doc.getElementById('doctorUnifiedTestBlock');
        var typeEl  = doc.getElementById('doctorUnifiedTestType');
        var testEl  = doc.getElementById('doctorUnifiedTestSelect');
        var prioEl  = doc.getElementById('doctorUnifiedTestPriority');
        var listEl   = doc.getElementById('labTestsAdded');
        var prioWrap = doc.getElementById('doctorUnifiedPriorityWrap');
        if (!addBtn || !blockEl || !typeEl || !testEl || !prioEl || !listEl) { return; }

        var typeValues = Array.from(typeEl.options)
            .map(function (o) { return String(o.value || '').trim(); })
            .filter(function (v) { return v !== ''; });

        function populateTests(type) {
            var opts = collectOptions(type);
            testEl.innerHTML = '<option value="">Select test</option>'
                + opts.map(function (o) { return '<option value="' + esc(o.id) + '">' + esc(o.label) + '</option>'; }).join('');
        }

        addBtn.onclick = function () {
            blockEl.classList.toggle('is-open');
            if (prioWrap) {
                prioWrap.style.display = '';
            }
            if (!typeEl.value && typeValues.length === 1) { typeEl.value = typeValues[0]; }
            if (typeEl.value) { populateTests(typeEl.value); }
            if (typeEl.value) {
                prioEl.value = state.diagnosticPriority[typeEl.value] || 'Routine';
            }
            if (blockEl.classList.contains('is-open')) { (typeEl.value ? testEl : typeEl).focus(); }
        };

        typeEl.onchange = function () {
            populateTests(typeEl.value);
            if (typeEl.value) {
                prioEl.value = state.diagnosticPriority[typeEl.value] || 'Routine';
            }
        };

        prioEl.onchange = function () {
            if (!typeEl.value) { return; }
            syncDiagnosticPriorityToForm(typeEl.value, prioEl.value);
        };

        testEl.onchange = function () {
            if (!typeEl.value || !testEl.value) { return; }

            if (!prioEl.value) {
                notify('error', 'Please select priority before adding test.');
                prioEl.focus();
                testEl.value = '';
                return;
            }

            syncDiagnosticPriorityToForm(typeEl.value, prioEl.value);
            setOptionSelected(typeEl.value, testEl.value, true, prioEl.value);
            renderBadges();
            testEl.value = '';
        };

        listEl.onclick = function (e) {
            var btn = e.target.closest('.doctor-care-test-remove');
            if (!btn) { return; }
            setOptionSelected(btn.dataset.type, btn.dataset.testId, false);
            renderBadges();
        };

        if (!typeEl.value && typeValues.length === 1) { typeEl.value = typeValues[0]; }
        if (typeEl.value) {
            populateTests(typeEl.value);
            prioEl.value = state.diagnosticPriority[typeEl.value] || 'Routine';
        }
        renderBadges();
    }

    // --- Save all ---
    async function saveAll() {
        var btn = els.saveBtn;
        if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
        showLoader();

        try {
            // 1. Vitals (OPD only — embedded in shell form)
            if (state.mode === 'opd') {
                var vitalsForm = doc.getElementById('doctorUnifiedVitalsForm');
                if (vitalsForm) {
                    var vUrl = vitalsForm.dataset.storeUrl || rpl(cfg.routes.opd.updateVitalsSocial, state.opdPatientId);
                    var vRes = await fetch(vUrl, {
                        method:  'POST',
                        headers: { 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                        body:    new FormData(vitalsForm)
                    });
                    var vData = await vRes.json();
                    if (vData.status === false || vData.errors) {
                        throw new Error(errMsg(vData, 'Unable to save vitals.'));
                    }
                }
            }

            // 1.25 IPD vitals / clinical snapshot
            if (state.mode === 'ipd' && cfg.routes.ipd.clinicalUpdate) {
                var ipdVitalsForm = doc.getElementById('doctorUnifiedIpdVitalsForm');
                if (ipdVitalsForm) {
                    var cvUrl = rpl(cfg.routes.ipd.clinicalUpdate, state.allocationId, '__ALLOCATION__');
                    var cvRes = await fetch(cvUrl, {
                        method:  'POST',
                        headers: { 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                        body:    new FormData(ipdVitalsForm)
                    });
                    var cvData = await cvRes.json();
                    if (cvData.status === false || cvData.errors) {
                        throw new Error(errMsg(cvData, 'Unable to save IPD vitals.'));
                    }
                }
            }

            // 1.5 IPD SOAP note (optional)
            if (state.mode === 'ipd' && cfg.routes.ipd.notesStore) {
                var soapS = (doc.getElementById('doctorUnifiedIpdSoapS') || {}).value || '';
                var soapO = (doc.getElementById('doctorUnifiedIpdSoapO') || {}).value || '';
                var soapA = (doc.getElementById('doctorUnifiedIpdSoapA') || {}).value || '';
                var soapP = (doc.getElementById('doctorUnifiedIpdSoapP') || {}).value || '';
                var noteTypeEl = doc.getElementById('doctorUnifiedIpdNoteType');
                var noteType = noteTypeEl ? noteTypeEl.value : 'progress';

                var soapParts = [];
                if (String(soapS).trim() !== '') { soapParts.push('S: ' + String(soapS).trim()); }
                if (String(soapO).trim() !== '') { soapParts.push('O: ' + String(soapO).trim()); }
                if (String(soapA).trim() !== '') { soapParts.push('A: ' + String(soapA).trim()); }
                if (String(soapP).trim() !== '') { soapParts.push('P: ' + String(soapP).trim()); }

                if (soapParts.length) {
                    var nBody = new FormData();
                    nBody.append('note_type', noteType || 'progress');
                    nBody.append('note', soapParts.join('\n'));
                    var nUrl = rpl(cfg.routes.ipd.notesStore, state.allocationId, '__ALLOCATION__');
                    var nRes = await fetch(nUrl, {
                        method:  'POST',
                        headers: { 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                        body:    nBody
                    });
                    var nData = await nRes.json();
                    if (nData.status === false || nData.errors) {
                        throw new Error(errMsg(nData, 'Unable to save IPD SOAP note.'));
                    }
                }
            }

            // 2. Prescription
            var prescSlot = doc.getElementById('doctorUnifiedPrescriptionSlot');
            var fSel      = state.mode === 'ipd' ? '#ipdPrescriptionForm' : '#opdPrescriptionForm';
            var prescForm = prescSlot && (
                prescSlot.querySelector(fSel)
                || prescSlot.querySelector('form#ipdPrescriptionForm, form#opdPrescriptionForm')
                || prescSlot.querySelector('form')
            );
            var prescScope = prescForm || prescSlot || els.body || doc;

            var hasPendingPrescriptionDraft = !!(
                prescScope.querySelector('#prescription_entry_medicine')?.value ||
                prescScope.querySelector('#prescription_entry_dosage')?.value ||
                prescScope.querySelector('#prescription_entry_instruction')?.value ||
                prescScope.querySelector('#prescription_entry_route')?.value ||
                prescScope.querySelector('#prescription_entry_frequency')?.value ||
                prescScope.querySelector('#prescription_entry_days')?.value
            );

            if (prescComposer && hasPendingPrescriptionDraft) {
                var committed = prescComposer.addOrUpdateFromComposer(function (message, focusSelector) {
                    notify('error', message);
                    var focusEl = doc.querySelector(focusSelector);
                    if (focusEl) { focusEl.focus(); }
                });

                if (!committed) {
                    throw new Error('Please complete prescription row before saving.');
                }
            }

            var hasRows = !!(prescScope && (
                prescScope.querySelector('tbody#prescriptionItemsTbody tr.prescription-item-row input[name="medicine_id[]"]') ||
                prescScope.querySelector('input[name="medicine_id[]"]')
            ));

            if (hasRows) {
                var pUrl = state.prescriptionStoreUrl || (
                    state.mode === 'ipd'
                        ? rpl(cfg.routes.ipd.prescriptionStore, state.allocationId, '__ALLOCATION__')
                        : rpl(cfg.routes.opd.prescriptionStore, state.opdPatientId)
                );
                var pRes  = await fetch(pUrl, {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                    body:    collectFormData(prescScope)
                });
                var pData = await pRes.json();
                if (pData.status === false || pData.errors) {
                    throw new Error(errMsg(pData, 'Unable to save prescription.'));
                }
                state.hasExistingPrescription = true;

            } else if (state.hasExistingPrescription && state.mode === 'opd' && cfg.routes.opd.prescriptionDestroy) {
                var dUrl  = rpl(cfg.routes.opd.prescriptionDestroy, state.opdPatientId);
                var dRes  = await fetch(dUrl, {
                    method:  'DELETE',
                    headers: { 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' }
                });
                var dData = await dRes.json();
                if (dData.status === false || dData.errors) {
                    throw new Error(errMsg(dData, 'Unable to update prescription.'));
                }
                state.hasExistingPrescription = false;
            }

            // 3. Diagnostics
            var diagTypes = ['pathology', 'radiology'];
            for (var i = 0; i < diagTypes.length; i++) {
                var orderType = diagTypes[i];
                var selEl     = doc.getElementById('diagnostic_test_ids_' + orderType);
                if (!selEl) { continue; }

                var newOpts = Array.from(selEl.selectedOptions || [])
                    .filter(function (o) { return String(o.value || '').trim() !== '' && String(o.dataset.itemId || '').trim() === ''; });

                if (!newOpts.length) { continue; }

                var byPriority = {};
                newOpts.forEach(function (o) {
                    var pr = String(o.dataset.selectedPriority || state.diagnosticPriority[orderType] || getDiagnosticPriority(orderType) || 'Routine');
                    if (!pr) {
                        return;
                    }
                    if (!byPriority[pr]) {
                        byPriority[pr] = [];
                    }
                    byPriority[pr].push(String(o.value));
                });

                var diagUrl  = state.mode === 'ipd'
                    ? rpl(cfg.routes.ipd.diagnosticStore, state.allocationId, '__ALLOCATION__')
                    : rpl(cfg.routes.opd.diagnosticStore, state.opdPatientId);

                var prKeys = Object.keys(byPriority);
                for (var pi = 0; pi < prKeys.length; pi++) {
                    var priority = prKeys[pi];
                    var newIds   = byPriority[priority];
                    if (!priority || !newIds.length) {
                        throw new Error('Please select priority for ' + orderType + ' tests.');
                    }

                    var diagBody = new FormData();
                    diagBody.append('order_type', orderType);
                    diagBody.append('priority', priority);
                    if (state.mode === 'opd') { diagBody.append('opd_patient_id', String(state.opdPatientId)); }
                    newIds.forEach(function (id) { diagBody.append('test_ids[]', id); });

                    var diagRes  = await fetch(diagUrl, {
                        method:  'POST',
                        headers: { 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                        body:    diagBody
                    });
                    var diagData = await diagRes.json();
                    if (diagData.status === false || diagData.errors) {
                        throw new Error(errMsg(diagData, 'Unable to save ' + orderType + ' order.'));
                    }
                }
            }

            hideLoader();
            notify('success', 'Saved successfully.');
            if (bsModal) { bsModal.hide(); }
            win.location.reload();

        } catch (err) {
            hideLoader();
            notify('error', err && err.message ? err.message : 'Unable to save. Please try again.');
            if (btn) { btn.disabled = false; btn.textContent = 'Save'; }
        }
    }

    // --- Prescription composer ---
    function initPrescComposer() {
        if (!(win.OPDCareShared && typeof win.OPDCareShared.createPrescriptionComposer === 'function')) { return; }
        prescComposer = win.OPDCareShared.createPrescriptionComposer({
            getLoadDosagesUrl: function () {
                return state.mode === 'ipd' ? cfg.routes.ipd.prescriptionLoadDosages : cfg.routes.opd.prescriptionLoadDosages;
            },
            getCsrfToken: function () { return cfg.csrf; }
        });
        prescComposer.initialize();
    }

    // --- Select2 scoped to modal ---
    function initSelect2() {
        if (!(win.jQuery && win.jQuery.fn && win.jQuery.fn.select2)) { return; }
        win.jQuery('#p360ModalBody .select2-modal').each(function () {
            var $el = win.jQuery(this);
            if ($el.hasClass('select2-hidden-accessible')) { $el.select2('destroy'); }
            $el.select2({ dropdownParent: win.jQuery('#p360Modal'), width: '100%' });
        });
    }

    // --- Modal cleanup ---
    function onModalClose() {
        prescComposer = null;
        state.openContext = 'order';
        if (win.jQuery) {
            win.jQuery('#p360ModalBody .select2-modal').each(function () {
                var $el = win.jQuery(this);
                if ($el.hasClass('select2-hidden-accessible')) { $el.select2('destroy'); }
            });
        }
        if (els.body) { els.body.innerHTML = '<div class="p-4 text-center text-muted">Loading...</div>'; }
        if (els.saveBtn) { els.saveBtn.style.display = 'none'; }
    }

    // --- Utilities ---
    function rpl(template, value, placeholder) {
        return String(template || '').replace(placeholder || '__ID__', value || '');
    }

    function collectFormData(scope) {
        var fd = new FormData();
        if (!scope) { return fd; }
        scope.querySelectorAll('input[name], select[name], textarea[name]').forEach(function (field) {
            if (!field.name || field.disabled) { return; }
            var type = String(field.type || '').toLowerCase();
            if ((type === 'checkbox' || type === 'radio') && !field.checked) { return; }
            if (field.tagName.toUpperCase() === 'SELECT' && field.multiple) {
                Array.from(field.selectedOptions || []).forEach(function (o) { fd.append(field.name, o.value); });
                return;
            }
            fd.append(field.name, field.value || '');
        });
        return fd;
    }

    function fetchHtml(url) {
        return win.fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { if (!res.ok) { throw new Error('HTTP ' + res.status); } return res.text(); });
    }

    function postHtml(url, data) {
        var body = new URLSearchParams(data);
        body.append('_token', cfg.csrf);
        return win.fetch(url, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    body.toString()
        }).then(function (res) { if (!res.ok) { throw new Error('HTTP ' + res.status); } return res.text(); });
    }

    function errMsg(data, fallback) {
        return (data.errors && data.errors[0] && data.errors[0].message) || data.message || fallback;
    }

    function esc(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function notify(type, message) {
        if (typeof win.sendmsg === 'function') { win.sendmsg(type, message); return; }
        win.alert(message);
    }

    function showLoader() { if (typeof win.loader === 'function') { win.loader('show'); } }
    function hideLoader() { if (typeof win.loader === 'function') { win.loader('hide'); } }

    /**
     * Transfer / discharge use the same IPD routes and partials as the legacy IPD profile;
     * content is shown in the global #add-datamodal (Bootstrap 5 + Select2 + Flatpickr).
     */
    function initIpdBedActions() {
        if (!win.jQuery) {
            return;
        }
        var $ = win.jQuery;

        function openIpdActionModal(html, sizeClass) {
            var $modal = $('.add-datamodal');
            var $ajax = $('#ajaxdata');
            if (!$modal.length || !$ajax.length) {
                return;
            }
            $ajax.html(html);
            var $dialog = $modal.find('.modal-dialog');
            $dialog
                .removeClass('modal-sm modal-lg modal-xl modal-fullscreen modal-dialog-centered modal-dialog-scrollable')
                .addClass((sizeClass || 'modal-lg') + ' modal-dialog-centered modal-dialog-scrollable');
            $modal.modal('show');
            wireIpdActionModal($modal);
        }

        function wireIpdActionModal($modalRoot) {
            var $wrap = $modalRoot.closest('.add-datamodal');
            if ($.fn.select2) {
                $modalRoot.find('.select2-modal').each(function () {
                    var $sel = $(this);
                    if ($sel.hasClass('select2-hidden-accessible')) {
                        $sel.select2('destroy');
                    }
                    $sel.select2({
                        dropdownParent: $wrap.length ? $wrap : $modalRoot,
                        width:            '100%'
                    });
                });
                $modalRoot.find('.modal-body').off('scroll.p360IpdSelect2').on('scroll.p360IpdSelect2', function () {
                    $modalRoot.find('.select2-hidden-accessible').each(function () {
                        $(this).select2('close');
                    });
                });
            }
            var dpEl = doc.getElementById('ipd-discharge-date');
            if (win.flatpickr && dpEl) {
                if (dpEl._flatpickr) {
                    dpEl._flatpickr.destroy();
                }
                win.flatpickr(dpEl, { enableTime: true, dateFormat: 'd-m-Y H:i' });
            }
        }

        function ajaxFailMessage(xhr) {
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }
            if (xhr && xhr.responseText && typeof xhr.responseText === 'string') {
                var t = xhr.responseText.trim();
                if (t && t.charAt(0) !== '{') {
                    return t.replace(/<[^>]+>/g, '').slice(0, 200);
                }
            }
            return 'Unable to complete the request.';
        }

        function handleIpdFormValidationErrors(xhr, $form) {
            if (xhr.status !== 422 || !xhr.responseJSON || !xhr.responseJSON.errors) {
                notify('error', ajaxFailMessage(xhr));
                return;
            }
            var errors = xhr.responseJSON.errors;
            $form.find('.err').remove();
            var msgs = [];
            Object.keys(errors).forEach(function (field) {
                var err = errors[field];
                var msg = (err && err.message) ? err.message : '';
                var code = (err && err.code) ? err.code : field;
                if (msg) {
                    msgs.push(msg);
                }
                var $field = $form.find('[name="' + code + '"]');
                if (!$field.length) {
                    $field = $form.find('[name="' + code + '[]"]');
                }
                if ($field.length) {
                    if ($field.hasClass('select2-hidden-accessible') || $field.hasClass('select2-modal')) {
                        $field.next('.select2-container').after('<div class="err text-danger small">' + esc(msg || 'Invalid') + '</div>');
                    } else {
                        $field.last().after('<div class="err text-danger small">' + esc(msg || 'Invalid') + '</div>');
                    }
                }
            });
            if (msgs.length) {
                notify('error', msgs.join(' '));
            }
        }

        $(doc).on('click', '.p360-transfer-ipd-btn, .p360-discharge-ipd-btn', function () {
            var url = this.getAttribute('data-url');
            if (!url) {
                return;
            }
            if (this.disabled || this.getAttribute('aria-disabled') === 'true') {
                return;
            }
            showLoader();
            $.ajax({
                url:     url,
                type:    'POST',
                data:    { _token: cfg.csrf },
                success: function (response) {
                    hideLoader();
                    openIpdActionModal(response, 'modal-lg');
                },
                error: function (xhr) {
                    hideLoader();
                    notify('error', ajaxFailMessage(xhr));
                }
            });
        });

        $(doc).on('submit', '#transfer-ipd-form, #discharge-ipd-form', function (ev) {
            var form = this;
            if (!$(form).closest('.add-datamodal').length) {
                return;
            }
            ev.preventDefault();
            showLoader();
            $(form).find('.err').remove();
            $.ajax({
                url:          $(form).attr('action'),
                type:         'POST',
                data:         $(form).serialize() + '&_token=' + encodeURIComponent(cfg.csrf),
                dataType:     'json',
                success:      function (response) {
                    hideLoader();
                    $('.add-datamodal').modal('hide');
                    notify('success', (response && response.message) ? response.message : 'Completed successfully.');
                    if (response && response.redirect_url) {
                        win.location.href = response.redirect_url;
                    }
                },
                error: function (xhr) {
                    hideLoader();
                    handleIpdFormValidationErrors(xhr, $(form));
                }
            });
        });
    }

    function applyOpenContextFocus() {
        if (state.openContext !== 'note') { return; }

        if (state.mode === 'opd') {
            var opdSubjective = (els.body && els.body.querySelector('textarea[name="subjective_notes"]')) || doc.querySelector('textarea[name="subjective_notes"]');
            if (opdSubjective) {
                opdSubjective.focus();
                opdSubjective.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        var ipdSubjective = doc.getElementById('doctorUnifiedIpdSoapS');
        if (ipdSubjective) {
            ipdSubjective.focus();
            ipdSubjective.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // --- Boot ---
    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

}(window, document));