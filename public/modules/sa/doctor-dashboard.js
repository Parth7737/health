/**
 * Doctor Dashboard - Queue Management & Care Modal Module
 * 
 * Manages OPD queue, patient care modal, and prescription/diagnostic workflows
 * on the doctor dashboard. Coordinates API calls, UI rendering, and form submissions.
 * 
 * Dependencies:
 *   - Bootstrap 5 Modal API
 *   - jQuery (for event delegation and Select2)
 *   - Chart.js (for queue flow chart)
 *   - opd-care-shared.js (shared care utilities)
 * 
 * Config (expects window.DoctorDashboardConfig):
 *   - routes: { queueStatus, callNext, skipPatient, undoSkip, visitSummaryView, ... }
 *   - csrf: CSRF token for POST requests
 *   - permissions: { canPathology, canRadiology }
 */

(function (window) {
  'use strict';

  // Destructure config from global namespace
  const config = window.DoctorDashboardConfig || {};
  const ROUTES = config.routes || {};
  const CSRF = config.csrf || '';
  const SNAPSHOT = config.snapshot || {};
  const QUEUE_PREVIEW_LIMIT = Number(config.queuePreviewLimit || 20);
  const CAN_PATHOLOGY = config.permissions?.canPathology || false;
  const CAN_RADIOLOGY = config.permissions?.canRadiology || false;

  // ===== UTILITIES =====

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function routeWithId(template, id) {
    return template.replace('__ID__', id);
  }

  function getInitials(name) {
    const source = String(name || '').trim();
    if (!source) {
      return 'PT';
    }

    const parts = source.split(/\s+/).filter(Boolean);
    if (parts.length === 1) {
      return parts[0].slice(0, 2).toUpperCase();
    }

    return (parts[0][0] + parts[1][0]).toUpperCase();
  }

  function getPriorityMeta(row) {
    if (row.absent) {
      return { label: 'Pending', cls: 'medium' };
    }

    const waitMinutes = Number(row.wait_minutes || 0);
    if (waitMinutes >= 15) {
      return { label: 'Urgent', cls: 'urgent' };
    }

    if (waitMinutes >= 8) {
      return { label: 'Soon', cls: 'medium' };
    }

    return { label: 'Routine', cls: 'routine' };
  }

  function formatWaitLabel(row) {
    const fromMinutes = Number(row.wait_minutes);
    const fallbackMinutes = Number.parseFloat(String(row.wait_time || '').replace(/[^0-9.]/g, ''));
    const waitMinutes = Number.isFinite(fromMinutes) && fromMinutes > 0
      ? fromMinutes
      : (Number.isFinite(fallbackMinutes) && fallbackMinutes > 0 ? fallbackMinutes : 0);

    if (waitMinutes <= 0) {
      return row.wait_time || '-';
    }

    const minutes = Math.max(1, Math.round(waitMinutes));
    if (minutes >= 60) {
      const hours = Math.floor(minutes / 60);
      const remainingMins = minutes % 60;
      return remainingMins > 0 ? `${hours}h ${remainingMins}min` : `${hours}h`;
    }
    return `${minutes} min`;
  }

  function buildCareDataAttributes(row) {
    return `data-opd-id="${row.id}" data-patient-id="${row.patient_id || ''}" data-doctor-id="${row.doctor_id || ''}" data-doctor="${escapeHtml(row.doctor || '')}" data-dept="${escapeHtml(row.dept || '')}" data-token="${escapeHtml(row.token || '')}" data-name="${escapeHtml(row.name || '')}" data-case="${escapeHtml(row.case_no || '')}"`;
  }

  function buildVisitHistoryUrl(row) {
    const visitId = (row && row.id) ? String(row.id) : '1';
    return `/hospital/opd-patient/visits/${encodeURIComponent(visitId)}`;
  }

  function createFlowChart() {
    const chartEl = document.getElementById('opdFlowChart');
    if (!chartEl || !window.Chart) {
      return;
    }

    const flowChart = SNAPSHOT.flowChart || {};
    new Chart(chartEl, {
      type: 'bar',
      data: {
        labels: flowChart.labels || ['9am', '10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm'],
        datasets: [{
          label: 'Patients',
          data: flowChart.data || [0, 0, 0, 0, 0, 0, 0, 0],
          backgroundColor: '#1565c0',
          hoverBackgroundColor: '#0d47a1',
          borderRadius: 10,
          borderSkipped: false,
          maxBarThickness: 34,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: '#5d7692', font: { weight: '600' } }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(21, 101, 192, 0.08)' },
            ticks: { precision: 0, color: '#5d7692' }
          }
        }
      }
    });
  }

  function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    if (!tabButtons.length) {
      return;
    }

    tabButtons.forEach((button) => {
      button.addEventListener('click', function () {
        const targetId = this.dataset.target;
        if (!targetId) {
          return;
        }

        document.querySelectorAll('.tab-btn').forEach((item) => item.classList.remove('active'));
        document.querySelectorAll('.tab-pane-content').forEach((pane) => pane.classList.remove('active'));

        this.classList.add('active');
        const targetPane = document.getElementById(targetId);
        if (targetPane) {
          targetPane.classList.add('active');
        }
      });
    });
  }

  function notify(type, message) {
    if (typeof sendmsg === 'function') {
      sendmsg(type, message);
      return;
    }
    alert(message);
  }

  function showLoader() {
    if (typeof loader === 'function') {
      loader('show');
    }
  }

  function hideLoader() {
    if (typeof loader === 'function') {
      loader('hide');
    }
  }

  async function postJson(url, body = {}) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF,
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(body)
    });

    return response.json();
  }

  // ===== DOCTOR CARE MODAL MODULE =====

  const DoctorCareModal = (() => {
    const state = {
      opdPatientId: null,
      patientId: null,
      doctorId: null,
      token: null,
      patientName: null,
      caseNo: null,
      activeSection: 'summary',
      prescriptionStoreUrl: null,
      hasExistingPrescription: false,
      diagnosticPriorityByType: {
        pathology: 'Routine',
        radiology: 'Routine'
      }
    };

    const modalEl = document.getElementById('doctorCareModal');
    const modal = (window.bootstrap && modalEl) ? new bootstrap.Modal(modalEl) : null;
    let prescriptionComposer = null;

    function debugUnifiedSave(message, payload) {
      if (!(window.console && typeof window.console.log === 'function')) {
        return;
      }

      if (typeof payload === 'undefined') {
        window.console.log('[DoctorCare][UnifiedSave]', message);
        return;
      }

      window.console.log('[DoctorCare][UnifiedSave]', message, payload);
    }

    function getActivePrescriptionForm() {
      const host = document.getElementById('doctor-care-modal-content');
      if (host) {
        const scopedForm = host.querySelector('#opdPrescriptionForm');
        if (scopedForm) {
          return scopedForm;
        }
      }

      return document.getElementById('opdPrescriptionForm');
    }

    function getPrescriptionScopeElement() {
      return getActivePrescriptionForm() || document.getElementById('doctorUnifiedPrescriptionSlot');
    }

    function openUnifiedPrescriptionComposer() {
      if (state.activeSection !== 'unified') {
        return;
      }

      const entryGrid = document.querySelector('#doctorUnifiedPrescriptionSlot .prescription-entry-grid');
      if (!entryGrid) {
        return;
      }

      entryGrid.classList.add('is-open');
      entryGrid.style.display = 'grid';
      entryGrid.style.marginBottom = '8px';

      if (window.matchMedia('(max-width: 991.98px)').matches) {
        entryGrid.style.gridTemplateColumns = '1fr';
        entryGrid.style.minWidth = '0';
      }

      entryGrid.querySelectorAll(':scope > div').forEach((col) => {
        col.style.minWidth = '0';
        col.style.gridColumn = 'auto';
      });

      entryGrid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function buildFormDataFromScope(scopeEl) {
      if (!scopeEl) {
        return new FormData();
      }

      if (scopeEl.tagName === 'FORM') {
        return new FormData(scopeEl);
      }

      const formData = new FormData();
      scopeEl.querySelectorAll('input[name], select[name], textarea[name]').forEach((field) => {
        if (!field.name || field.disabled) {
          return;
        }

        const tagName = (field.tagName || '').toUpperCase();
        const type = String(field.type || '').toLowerCase();

        if ((type === 'checkbox' || type === 'radio') && !field.checked) {
          return;
        }

        if (tagName === 'SELECT' && field.multiple) {
          Array.from(field.selectedOptions || []).forEach((option) => {
            formData.append(field.name, option.value);
          });
          return;
        }

        formData.append(field.name, field.value || '');
      });

      return formData;
    }

    function getPrescriptionComposer() {
      if (!(window.OPDCareShared && typeof window.OPDCareShared.createPrescriptionComposer === 'function')) {
        return null;
      }

      if (!prescriptionComposer) {
        prescriptionComposer = window.OPDCareShared.createPrescriptionComposer({
          getLoadDosagesUrl: function () {
            if (ROUTES.prescriptionLoadDosages) {
              return ROUTES.prescriptionLoadDosages;
            }

            const form = getActivePrescriptionForm();
            return form ? (form.dataset.loadDosagesUrl || '') : '';
          },
          getCsrfToken: function () {
            return CSRF;
          }
        });
      }

      return prescriptionComposer;
    }

    function initSelect2(scopeSelector) {
      if (!(window.jQuery && jQuery.fn && jQuery.fn.select2)) {
        return;
      }

      const $scope = jQuery(scopeSelector);
      $scope.find('.select2-modal').each(function () {
        const $select = jQuery(this);
        if ($select.hasClass('select2-hidden-accessible')) {
          $select.select2('destroy');
        }

        $select.select2({ dropdownParent: jQuery('#doctorCareModal'), width: '100%' });
      });
    }

    function refreshDiagnosticPreview() {
      if (!(window.OPDCareShared && typeof window.OPDCareShared.refreshDiagnosticPreview === 'function')) {
        return;
      }

      window.OPDCareShared.refreshDiagnosticPreview('#diagnostic_test_ids', '#diagnostic-test-preview-body');
    }

    function initSectionPlugins() {
      initSelect2('#doctor-care-modal-content');

      const prescriptionScope = getPrescriptionScopeElement();
      if (prescriptionScope) {
        const composer = getPrescriptionComposer();
        if (composer) {
          composer.initialize();
          if (state.activeSection === 'prescription' || state.activeSection === 'unified') {
            composer.focusStart();
          }
        }
      }

      if (state.activeSection === 'pathology' || state.activeSection === 'radiology') {
        refreshDiagnosticPreview();
      }

      document.querySelectorAll('.diagnostic-test-select').forEach((selectEl) => {
        const previewSelector = selectEl.dataset.previewTarget || '#diagnostic-test-preview-body';
        if (!(window.OPDCareShared && typeof window.OPDCareShared.refreshDiagnosticPreview === 'function')) {
          return;
        }

        window.OPDCareShared.refreshDiagnosticPreview(`#${selectEl.id}`, previewSelector);
      });
    }

    function getContentHost() {
      return document.getElementById('doctor-care-modal-content');
    }

    function setHeader(section) {
      const title = document.getElementById('doctorCareModalTitle');
      if (!title) {
        return;
      }

      const sectionLabels = {
        summary: 'Visit Summary',
        prescription: 'Prescription',
        pathology: 'Pathology Order',
        radiology: 'Radiology Order',
        unified: '🩺 New Consultation - OPD Visit'
      };

      title.textContent = sectionLabels[section] || 'Doctor Care';
    }

    async function fetchHtml(url) {
      const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!response.ok) {
        throw new Error('Unable to load content.');
      }

      return response.text();
    }

    function extractPrescriptionForm(html) {
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const form = doc.querySelector('#opdPrescriptionForm');
      return form ? form.outerHTML : '<div class="text-muted">Prescription form unavailable.</div>';
    }

    function extractDiagnosticForm(html, orderType) {
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const form = doc.querySelector('form#saveDiagnosticOrderForm');
      if (!form) {
        return '<div class="text-muted">Diagnostic form unavailable.</div>';
      }

      form.id = `saveDiagnosticOrderForm_${orderType}`;
      form.classList.add('doctor-unified-diagnostic-form');
      form.dataset.orderType = orderType;
      form.dataset.storeUrl = routeWithId(ROUTES.diagnosticStore, state.opdPatientId);

      const selectEl = form.querySelector('#diagnostic_test_ids');
      if (selectEl) {
        selectEl.id = `diagnostic_test_ids_${orderType}`;
        selectEl.classList.add('diagnostic-test-select');
        selectEl.dataset.previewTarget = `#diagnostic-test-preview-body_${orderType}`;
      }

      const previewBody = form.querySelector('#diagnostic-test-preview-body');
      if (previewBody) {
        previewBody.id = `diagnostic-test-preview-body_${orderType}`;
      }

      const footer = form.querySelector('.modal-footer');
      if (footer) {
        footer.remove();
      }

      const closeBtn = form.querySelector('[data-bs-dismiss="modal"]');
      if (closeBtn) {
        closeBtn.remove();
      }

      return form.outerHTML;
    }

    function collectDiagnosticOptions(orderType) {
      const selectEl = document.getElementById(`diagnostic_test_ids_${orderType}`);
      if (!selectEl) {
        return [];
      }

      return Array.from(selectEl.options)
        .filter((option) => String(option.value || '').trim() !== '')
        .map((option) => ({
          id: String(option.value),
          label: option.textContent.trim()
        }));
    }

    function setDiagnosticOptionSelected(orderType, testId, shouldSelect) {
      const selectEl = document.getElementById(`diagnostic_test_ids_${orderType}`);
      if (!selectEl) {
        return;
      }

      const option = Array.from(selectEl.options).find((item) => String(item.value) === String(testId));
      if (!option) {
        return;
      }

      option.selected = !!shouldSelect;
      if (window.jQuery && jQuery.fn && jQuery.fn.select2 && jQuery(selectEl).hasClass('select2-hidden-accessible')) {
        jQuery(selectEl).trigger('change');
      } else {
        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }

    function readSelectedDiagnosticTests() {
      const tests = [];

      ['pathology', 'radiology'].forEach((orderType) => {
        const selectEl = document.getElementById(`diagnostic_test_ids_${orderType}`);
        if (!selectEl) {
          return;
        }

        Array.from(selectEl.selectedOptions || []).forEach((option) => {
          if (!String(option.value || '').trim()) {
            return;
          }

          tests.push({
            type: orderType,
            id: String(option.value),
            label: option.textContent.trim(),
            priority: String(option.dataset.selectedPriority || state.diagnosticPriorityByType[orderType] || 'Routine')
          });
        });
      });

      return tests;
    }

    function renderUnifiedDiagnosticBadges() {
      const listEl = document.getElementById('labTestsAdded');
      if (!listEl) {
        return;
      }

      const selectedTests = readSelectedDiagnosticTests();
      if (!selectedTests.length) {
        listEl.innerHTML = '<span class="doctor-care-empty-text">No tests added</span>';
        return;
      }

      listEl.innerHTML = selectedTests.map((test) => {
        const icon = test.type === 'radiology' ? '🩻' : '🧪';
        const priorityText = ` [${escapeHtml(test.priority)}]`;
        return `<span class="doctor-care-test-badge">${icon} ${escapeHtml(test.label)}${priorityText} <button type="button" class="doctor-care-test-remove" data-type="${test.type}" data-test-id="${escapeHtml(test.id)}">✕</button></span>`;
      }).join('');
    }

    function bindUnifiedDiagnosticPicker() {
      const addBtn = document.getElementById('doctorUnifiedAddTestBtn');
      const blockEl = document.getElementById('doctorUnifiedTestBlock');
      const typeEl = document.getElementById('doctorUnifiedTestType');
      const testEl = document.getElementById('doctorUnifiedTestSelect');
      const priorityWrapEl = document.getElementById('doctorUnifiedPriorityWrap');
      const priorityEl = document.getElementById('doctorUnifiedTestPriority');
      const listEl = document.getElementById('labTestsAdded');

      if (!addBtn || !blockEl || !typeEl || !testEl || !listEl) {
        return;
      }

      function syncPriorityUi() {
        const orderType = typeEl.value;
        if (!priorityWrapEl || !priorityEl) {
          return;
        }

        priorityWrapEl.style.display = '';
        priorityEl.value = state.diagnosticPriorityByType[orderType] || 'Routine';
      }

      const typeOptions = Array.from(typeEl.options)
        .map((option) => String(option.value || '').trim())
        .filter((value) => value !== '');

      function populateTests(orderType) {
        const options = collectDiagnosticOptions(orderType);
        let html = '<option value="">Select test</option>';
        html += options.map((item) => `<option value="${escapeHtml(item.id)}">${escapeHtml(item.label)}</option>`).join('');
        testEl.innerHTML = html;
      }

      addBtn.onclick = function () {
        blockEl.classList.toggle('is-open');

        if (!typeEl.value && typeOptions.length === 1) {
          typeEl.value = typeOptions[0];
        }

        if (typeEl.value) {
          populateTests(typeEl.value);
        }

        syncPriorityUi();

        if (blockEl.classList.contains('is-open')) {
          (typeEl.value ? testEl : typeEl).focus();
        }
      };

      typeEl.onchange = function () {
        populateTests(typeEl.value);
        syncPriorityUi();
      };

      if (priorityEl) {
        priorityEl.onchange = function () {
          const ot = typeEl.value;
          if (ot) {
            state.diagnosticPriorityByType[ot] = priorityEl.value || 'Routine';
          }
          renderUnifiedDiagnosticBadges();
        };
      }

      testEl.onchange = function () {
        if (!typeEl.value || !testEl.value) {
          return;
        }

        setDiagnosticOptionSelected(typeEl.value, testEl.value, true);
        const orderType = typeEl.value;
        const hiddenSelectEl = document.getElementById(`diagnostic_test_ids_${orderType}`);
        const selectedHiddenOption = hiddenSelectEl
          ? Array.from(hiddenSelectEl.options || []).find((option) => String(option.value) === String(testEl.value))
          : null;
        if (selectedHiddenOption) {
          selectedHiddenOption.dataset.selectedPriority = priorityEl ? (priorityEl.value || 'Routine') : 'Routine';
        }
        if (orderType) {
          state.diagnosticPriorityByType[orderType] = priorityEl ? (priorityEl.value || 'Routine') : 'Routine';
        }
        renderUnifiedDiagnosticBadges();

        testEl.value = '';
      };

      listEl.onclick = function (event) {
        const removeBtn = event.target.closest('.doctor-care-test-remove');
        if (!removeBtn) {
          return;
        }

        const orderType = removeBtn.dataset.type;
        const testId = removeBtn.dataset.testId;
        if (!orderType || !testId) {
          return;
        }

        setDiagnosticOptionSelected(orderType, testId, false);
        renderUnifiedDiagnosticBadges();
      };

      if (!typeEl.value && typeOptions.length === 1) {
        typeEl.value = typeOptions[0];
      }

      if (typeEl.value) {
        populateTests(typeEl.value);
      }

      syncPriorityUi();

      renderUnifiedDiagnosticBadges();
    }

    function buildUnifiedDiagnosticSyncPayload(orderType) {
      const selectEl = document.getElementById(`diagnostic_test_ids_${orderType}`);
      if (!selectEl) {
        return null;
      }

      const options = Array.from(selectEl.options || []).filter((option) => String(option.value || '').trim() !== '');
      const selectedOptions = options.filter((option) => option.selected);

      const selectedIds = new Set(selectedOptions.map((option) => String(option.value)));
      const existingItems = options
        .filter((option) => String(option.dataset.itemId || '').trim() !== '')
        .map((option) => ({
          testId: String(option.value),
          itemId: String(option.dataset.itemId)
        }));

      const removedItemIds = existingItems
        .filter((item) => !selectedIds.has(item.testId))
        .map((item) => item.itemId);

      const newTestIds = selectedOptions
        .filter((option) => String(option.dataset.itemId || '').trim() === '')
        .map((option) => String(option.value));

      const priority = String(state.diagnosticPriorityByType[orderType] || 'Routine');

      return {
        selectEl,
        removedItemIds,
        newTestIds,
        priority
      };
    }

    async function loadUnifiedWorkspace() {
      if (!state.opdPatientId) {
        return;
      }

      state.diagnosticPriorityByType = {
        pathology: 'Routine',
        radiology: 'Routine'
      };

      function bindUnifiedActionButtons() {
        const saveAllBtn = document.getElementById('doctorCareSaveAllBtn');
        if (saveAllBtn && saveAllBtn.dataset.boundUnifiedSave !== '1') {
          saveAllBtn.dataset.boundUnifiedSave = '1';
          saveAllBtn.addEventListener('click', function (event) {
            event.preventDefault();
            debugUnifiedSave('Direct click handler fired: doctorCareSaveAllBtn');
            submitUnifiedSaveAll(true);
          });
        }

        const saveDraftBtn = document.getElementById('doctorCareSaveDraftBtn');
        if (saveDraftBtn && saveDraftBtn.dataset.boundUnifiedSave !== '1') {
          saveDraftBtn.dataset.boundUnifiedSave = '1';
          saveDraftBtn.addEventListener('click', function (event) {
            event.preventDefault();
            debugUnifiedSave('Direct click handler fired: doctorCareSaveDraftBtn');
            submitUnifiedSaveAll(false);
          });
        }
      }

      function applyUnifiedPrescriptionEntryLayout(entryGrid, isOpen) {
        if (!entryGrid) {
          return;
        }

        const isMobile = window.matchMedia('(max-width: 991.98px)').matches;
        entryGrid.style.display = isOpen ? 'grid !important' : 'none';
        // entryGrid.style.gap = '6px';
        // entryGrid.style.alignItems = 'end';
        entryGrid.style.marginBottom = '8px';

        if (isMobile) {
          entryGrid.style.gridTemplateColumns = '1fr';
          entryGrid.style.minWidth = '0';
        } else {
          // entryGrid.style.gridTemplateColumns = 'minmax(180px, 2.2fr) minmax(110px, 1.2fr) minmax(120px, 1.3fr) minmax(110px, 1.2fr) minmax(70px, 0.8fr) auto';
          // entryGrid.style.minWidth = '740px';
        }

        entryGrid.querySelectorAll(':scope > div').forEach((col) => {
          col.style.minWidth = '0';
          col.style.gridColumn = 'auto';
        });
      }

      state.activeSection = 'unified';
      setHeader('unified');

      const content = getContentHost();
      if (content) {
        content.innerHTML = '<div class="text-center text-muted py-4">Loading workspace...</div>';
      }

      try {
        const shellHtml = await fetchHtml(routeWithId(ROUTES.careUnifiedForm, state.opdPatientId));
        if (content) {
          content.innerHTML = shellHtml;
        }

        bindUnifiedActionButtons();

        const [prescriptionHtml, pathologyHtml, radiologyHtml] = await Promise.all([
          fetchHtml(routeWithId(ROUTES.prescriptionForm, state.opdPatientId)),
          CAN_PATHOLOGY ? postHtml(routeWithId(ROUTES.diagnosticShow, state.opdPatientId), { order_type: 'pathology', _token: CSRF }) : Promise.resolve(''),
          CAN_RADIOLOGY ? postHtml(routeWithId(ROUTES.diagnosticShow, state.opdPatientId), { order_type: 'radiology', _token: CSRF }) : Promise.resolve('')
        ]);

        const prescriptionSlot = document.getElementById('doctorUnifiedPrescriptionSlot');
        if (prescriptionSlot) {
          prescriptionSlot.innerHTML = extractPrescriptionForm(prescriptionHtml);

          const prescriptionDoc = new DOMParser().parseFromString(prescriptionHtml, 'text/html');
          const prescriptionSourceForm = prescriptionDoc.querySelector('#opdPrescriptionForm');
          state.prescriptionStoreUrl = prescriptionSourceForm?.dataset?.storeUrl || routeWithId(ROUTES.prescriptionStore, state.opdPatientId);
          prescriptionSlot.dataset.storeUrl = state.prescriptionStoreUrl;
          state.hasExistingPrescription = prescriptionDoc.querySelectorAll('tbody#prescriptionItemsTbody tr.prescription-item-row').length > 0;

          const initialEntryGrid = prescriptionSlot.querySelector('.prescription-entry-grid');
          if (initialEntryGrid) {
            initialEntryGrid.classList.remove('is-open');
            applyUnifiedPrescriptionEntryLayout(initialEntryGrid, false);
          }
        }

        const pathologySlot = document.getElementById('doctorUnifiedPathologySlot');
        if (pathologySlot) {
          pathologySlot.innerHTML = extractDiagnosticForm(pathologyHtml, 'pathology');
        }

        const radiologySlot = document.getElementById('doctorUnifiedRadiologySlot');
        if (radiologySlot) {
          radiologySlot.innerHTML = extractDiagnosticForm(radiologyHtml, 'radiology');
        }

        bindUnifiedDiagnosticPicker();
        renderUnifiedDiagnosticBadges();

        const addDrugBtn = document.getElementById('doctorUnifiedAddDrugBtn');
        if (addDrugBtn) {
          addDrugBtn.addEventListener('click', function () {
            const entryGrid = document.querySelector('#doctorUnifiedPrescriptionSlot .prescription-entry-grid');
            const entryShell = document.querySelector('#doctorUnifiedPrescriptionSlot .prescription-items-shell');

            if (entryShell) {
              entryShell.scrollLeft = 0;
            }

            if (entryGrid) {
              entryGrid.classList.add('is-open');
              applyUnifiedPrescriptionEntryLayout(entryGrid, true);
              entryGrid.scrollIntoView({ behavior: 'smooth', block: 'center' });
              entryGrid.classList.remove('doctor-care-entry-flash');
              // Restart animation so each click gives visual feedback.
              void entryGrid.offsetWidth;
              entryGrid.classList.add('doctor-care-entry-flash');
            }

            const medicineField = document.getElementById('prescription_entry_medicine');
            if (medicineField) {
              medicineField.focus();
              if (window.jQuery) {
                const $el = jQuery(medicineField);
                if ($el.hasClass('select2-hidden-accessible')) {
                  $el.select2('open');
                }
              }
            }
          });
        }

        initSectionPlugins();
      } catch (error) {
        if (content) {
          content.innerHTML = '<div class="alert alert-danger mb-0">Unable to load workspace. Please try again.</div>';
        }
      }
    }

    async function submitUnifiedSaveAll(isFinal) {
      const saveBtn = document.getElementById('doctorCareSaveAllBtn');
      const originalLabel = saveBtn ? saveBtn.textContent : '';

      debugUnifiedSave('submitUnifiedSaveAll invoked', {
        opdPatientId: state.opdPatientId,
        activeSection: state.activeSection
      });

      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
      }

      showLoader();

      try {
        const composer = getPrescriptionComposer();
        const prescriptionForm = getActivePrescriptionForm();
        const prescriptionScope = getPrescriptionScopeElement() || getContentHost() || modalEl || document;
        const hasPendingPrescriptionDraft = !!(
          prescriptionScope.querySelector('#prescription_entry_medicine')?.value ||
          prescriptionScope.querySelector('#prescription_entry_dosage')?.value ||
          prescriptionScope.querySelector('#prescription_entry_instruction')?.value ||
          prescriptionScope.querySelector('#prescription_entry_route')?.value ||
          prescriptionScope.querySelector('#prescription_entry_frequency')?.value ||
          prescriptionScope.querySelector('#prescription_entry_days')?.value
        );

        debugUnifiedSave('Draft/form state', {
          hasPrescriptionForm: !!prescriptionForm,
          hasPendingPrescriptionDraft: hasPendingPrescriptionDraft
        });

        if (composer && hasPendingPrescriptionDraft) {
          const committed = composer.addOrUpdateFromComposer((message, focusSelector) => {
            notify('error', message);
            const focusEl = document.querySelector(focusSelector);
            if (focusEl) {
              focusEl.focus();
            }
          });

          if (!committed) {
            throw new Error('Please complete prescription row before saving.');
          }
        }

        const vitalsForm = document.getElementById('doctorUnifiedVitalsForm');
        if (vitalsForm) {
          const vitalsResponse = await fetch(vitalsForm.dataset.storeUrl || routeWithId(ROUTES.updateVitalsSocial, state.opdPatientId), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(vitalsForm)
          });

          const vitalsData = await vitalsResponse.json();
          if (!vitalsResponse.ok || vitalsData.status === false || vitalsData.errors) {
            throw new Error(vitalsData?.errors?.[0]?.message || vitalsData?.message || 'Unable to save vitals.');
          }
        }

        const hasPrescriptionRows = !!(
          prescriptionScope && (
            prescriptionScope.querySelector('tbody#prescriptionItemsTbody tr.prescription-item-row input[name="medicine_id[]"]') ||
            prescriptionScope.querySelector('input[name="medicine_id[]"]')
          )
        );

        debugUnifiedSave('Prescription row presence', {
          hasPrescriptionRows: hasPrescriptionRows
        });

        if (hasPrescriptionRows) {
          const prescriptionSlot = document.getElementById('doctorUnifiedPrescriptionSlot');
          const prescriptionStoreUrl =
            prescriptionForm?.dataset?.storeUrl ||
            prescriptionSlot?.dataset?.storeUrl ||
            state.prescriptionStoreUrl ||
            routeWithId(ROUTES.prescriptionStore, state.opdPatientId);

          const prescriptionResponse = await fetch(prescriptionStoreUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: buildFormDataFromScope(prescriptionScope)
          });

          const prescriptionData = await prescriptionResponse.json();
          if (!prescriptionResponse.ok || prescriptionData.status === false || prescriptionData.errors) {
            throw new Error(prescriptionData?.errors?.[0]?.message || prescriptionData?.message || 'Unable to save prescription.');
          }

          state.hasExistingPrescription = true;
        } else if (state.hasExistingPrescription) {
          const destroyUrl = ROUTES.prescriptionDestroy
            ? routeWithId(ROUTES.prescriptionDestroy, state.opdPatientId)
            : null;

          if (!destroyUrl) {
            throw new Error('Prescription remove route is not configured.');
          }

          const deleteResponse = await fetch(destroyUrl, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
          });

          const deleteData = await deleteResponse.json();
          if (!deleteResponse.ok || deleteData.status === false || deleteData.errors) {
            throw new Error(deleteData?.errors?.[0]?.message || deleteData?.message || 'Unable to delete prescription.');
          }

          state.hasExistingPrescription = false;
        }

        const diagnosticTypes = ['pathology', 'radiology'];
        for (let idx = 0; idx < diagnosticTypes.length; idx++) {
          const orderType = diagnosticTypes[idx];
          const payload = buildUnifiedDiagnosticSyncPayload(orderType);
          if (!payload) {
            continue;
          }

          for (let removeIdx = 0; removeIdx < payload.removedItemIds.length; removeIdx++) {
            const itemId = payload.removedItemIds[removeIdx];
            if (!itemId || !ROUTES.diagnosticDestroy) {
              continue;
            }

            const destroyUrl = routeWithId(ROUTES.diagnosticDestroy, state.opdPatientId).replace('__ITEM__', itemId);
            const diagnosticDeleteResponse = await fetch(destroyUrl, {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
            });

            const diagnosticDeleteData = await diagnosticDeleteResponse.json();
            if (!diagnosticDeleteResponse.ok || diagnosticDeleteData.status === false || diagnosticDeleteData.errors) {
              throw new Error(diagnosticDeleteData?.errors?.[0]?.message || diagnosticDeleteData?.message || 'Unable to remove diagnostic test.');
            }
          }

          if (!payload.newTestIds.length) {
            continue;
          }

          const diagnosticBody = new FormData();
          diagnosticBody.append('order_type', orderType);
          diagnosticBody.append('opd_patient_id', String(state.opdPatientId || ''));

          const notesInput = payload.selectEl.closest('form')?.querySelector('textarea[name="notes"]');
          if (notesInput && String(notesInput.value || '').trim() !== '') {
            diagnosticBody.append('notes', notesInput.value);
          }

          payload.newTestIds.forEach((testId) => {
            diagnosticBody.append('test_ids[]', testId);
          });

          if (orderType === 'pathology') {
            diagnosticBody.append('priority', payload.priority || 'Routine');
          }

          const diagnosticResponse = await fetch(routeWithId(ROUTES.diagnosticStore, state.opdPatientId), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: diagnosticBody
          });

          const diagnosticData = await diagnosticResponse.json();
          if (!diagnosticResponse.ok || diagnosticData.status === false || diagnosticData.errors) {
            throw new Error(diagnosticData?.errors?.[0]?.message || diagnosticData?.message || 'Unable to create diagnostic order.');
          }
        }

        hideLoader();
        notify('success', 'Clinical workspace saved successfully.');

        if (isFinal) {
          close();
          window.location.reload();
          return;
        }

        await loadUnifiedWorkspace();
        await QueueDesk.fetchAndRender();
      } catch (error) {
        hideLoader();
        notify('error', error?.message || 'Unable to save workspace.');
      } finally {
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.textContent = originalLabel || 'Save All';
        }
      }
    }

    async function postHtml(url, payload) {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': CSRF,
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams(payload)
      });

      if (!response.ok) {
        throw new Error('Unable to load content.');
      }

      return response.text();
    }

    async function loadSection(section) {
      if (!state.opdPatientId) {
        return;
      }

      state.activeSection = section;
      setHeader(section);
      const content = getContentHost();
      if (content) {
        content.innerHTML = '<div class="text-center text-muted py-4">Loading...</div>';
      }

      try {
        let html = '';
        if (section === 'summary') {
          html = await fetchHtml(routeWithId(ROUTES.visitSummaryView, state.opdPatientId));
        } else if (section === 'prescription') {
          html = await fetchHtml(routeWithId(ROUTES.prescriptionForm, state.opdPatientId));
        } else if (section === 'pathology' || section === 'radiology') {
          html = await postHtml(routeWithId(ROUTES.diagnosticShow, state.opdPatientId), { order_type: section, _token: CSRF });
        }

        if (content) {
          content.innerHTML = html || '<div class="text-muted">No content.</div>';
        }

        const diagnosticForm = document.getElementById('saveDiagnosticOrderForm');
        if (diagnosticForm) {
          diagnosticForm.dataset.storeUrl = routeWithId(ROUTES.diagnosticStore, state.opdPatientId);
        }

        initSectionPlugins();
      } catch (error) {
        if (content) {
          content.innerHTML = '<div class="alert alert-danger mb-0">Unable to load section. Please try again.</div>';
        }
      }
    }

    function open(meta, section = 'summary') {
      state.opdPatientId = meta.opdPatientId;
      state.patientId = meta.patientId;
      state.doctorId = meta.doctorId;
      state.token = meta.token || null;
      state.patientName = meta.patientName || null;
      state.caseNo = meta.caseNo || null;
      state.activeSection = section;

      if (modal) {
        modal.show();
      }
      loadUnifiedWorkspace();
    }

    function close() {
      if (modal) {
        modal.hide();
      }
    }

    function reset() {
      state.opdPatientId = null;
      state.patientId = null;
      state.doctorId = null;
      state.token = null;
      state.patientName = null;
      state.caseNo = null;
      state.activeSection = 'summary';
      prescriptionComposer = null;

      const content = getContentHost();
      if (content) {
        content.innerHTML = '<div class="text-muted">Select an action to continue.</div>';
      }
    }

    async function submitPrescription(form) {
      showLoader();
      try {
        const storeUrl = form.dataset.storeUrl || routeWithId(ROUTES.prescriptionStore, state.opdPatientId);
        const response = await fetch(storeUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form)
        });

        const data = await response.json();
        hideLoader();

        if (!response.ok || data.status === false || data.errors) {
          notify('error', data?.errors?.[0]?.message || data?.message || 'Unable to save prescription.');
          return;
        }

        notify('success', data.message || 'Prescription saved successfully.');
        await loadSection('summary');
        await QueueDesk.fetchAndRender();
      } catch (error) {
        hideLoader();
        notify('error', 'Unable to save prescription.');
      }
    }

    async function submitDiagnostic(form) {
      showLoader();
      try {
        const storeUrl = form.dataset.storeUrl || routeWithId(ROUTES.diagnosticStore, state.opdPatientId);
        const response = await fetch(storeUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form)
        });

        const data = await response.json();
        hideLoader();

        if (!response.ok || data.status === false || data.errors) {
          notify('error', data?.errors?.[0]?.message || data?.message || 'Unable to create diagnostic order.');
          return;
        }

        notify('success', data.message || 'Diagnostic order created successfully.');
        await loadSection(state.activeSection);
        await QueueDesk.fetchAndRender();
      } catch (error) {
        hideLoader();
        notify('error', 'Unable to create diagnostic order.');
      }
    }

    function bindEvents() {
      if (!window.jQuery) {
        return;
      }

      jQuery(document)
        .off('click.doctorCare', '#addPrescriptionItemRow')
        .on('click.doctorCare', '#addPrescriptionItemRow', function (event) {
          event.preventDefault();
          const composer = getPrescriptionComposer();
          if (!composer) {
            return;
          }

          composer.addOrUpdateFromComposer((message, focusSelector) => {
            notify('error', message);
            const focusEl = document.querySelector(focusSelector);
            if (focusEl) {
              focusEl.focus();
            }
          });
        })
        .off('click.doctorCare', '#cancelPrescriptionItemEdit')
        .on('click.doctorCare', '#cancelPrescriptionItemEdit', function (event) {
          event.preventDefault();
          const composer = getPrescriptionComposer();
          if (composer) {
            composer.clearComposer();
          }
        })
        .off('click.doctorCare', '.edit-prescription-item-row')
        .on('click.doctorCare', '.edit-prescription-item-row', function (event) {
          event.preventDefault();
          const composer = getPrescriptionComposer();
          if (composer) {
            openUnifiedPrescriptionComposer();
            composer.loadFromRow(jQuery(this).closest('tr.prescription-item-row'));
          }
        })
        .off('click.doctorCare', '.remove-prescription-item-row')
        .on('click.doctorCare', '.remove-prescription-item-row', function (event) {
          event.preventDefault();
          const composer = getPrescriptionComposer();
          if (composer) {
            composer.removeRow(jQuery(this).closest('tr.prescription-item-row'));
          }
        })
        .off('change.doctorCare', '#prescription_entry_medicine')
        .on('change.doctorCare', '#prescription_entry_medicine', function () {
          const composer = getPrescriptionComposer();
          if (composer) {
            composer.onMedicineChanged(true);
          }
        })
        .off('select2:select.doctorCare', '#prescription_entry_medicine')
        .on('select2:select.doctorCare', '#prescription_entry_medicine', function () {
          const composer = getPrescriptionComposer();
          if (composer) {
            composer.onMedicineChanged(true);
          }
        })
        .off('select2:select.doctorCare', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_route, #prescription_entry_frequency')
        .on('select2:select.doctorCare', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_route, #prescription_entry_frequency', function () {
          const composer = getPrescriptionComposer();
          if (composer) {
            composer.focusNextField(this.id);
          }
        })
        .off('keydown.doctorCare', '#prescription_entry_days')
        .on('keydown.doctorCare', '#prescription_entry_days', function (event) {
          if (event.key !== 'Tab' || event.shiftKey) {
            return;
          }

          event.preventDefault();
          const addBtn = document.getElementById('addPrescriptionItemRow');
          if (addBtn) {
            addBtn.focus();
          }
        })
        .off('keydown.doctorCare', '#opdPrescriptionForm')
        .on('keydown.doctorCare', '#opdPrescriptionForm', function (event) {
          const targetId = event.target && event.target.id ? event.target.id : '';
          if (event.key === 'Enter' && /^prescription_entry_/.test(targetId)) {
            event.preventDefault();
            const addBtn = document.getElementById('addPrescriptionItemRow');
            if (addBtn) {
              addBtn.click();
            }
            return;
          }

          if (event.altKey && (event.key === 'n' || event.key === 'N')) {
            event.preventDefault();
            const addBtn = document.getElementById('addPrescriptionItemRow');
            if (addBtn) {
              addBtn.click();
            }
          }
        })
        .off('change.doctorCare', '#diagnostic_test_ids')
        .on('change.doctorCare', '#diagnostic_test_ids', function () {
          refreshDiagnosticPreview();
        })
        .off('select2:select.doctorCare select2:unselect.doctorCare', '#diagnostic_test_ids')
        .on('select2:select.doctorCare select2:unselect.doctorCare', '#diagnostic_test_ids', function () {
          refreshDiagnosticPreview();
        })
        .off('change.doctorCare', '.diagnostic-test-select')
        .on('change.doctorCare', '.diagnostic-test-select', function () {
          if (!(window.OPDCareShared && typeof window.OPDCareShared.refreshDiagnosticPreview === 'function')) {
            return;
          }

          const previewSelector = this.dataset.previewTarget || '#diagnostic-test-preview-body';
          window.OPDCareShared.refreshDiagnosticPreview(`#${this.id}`, previewSelector);
        })
        .off('select2:select.doctorCare select2:unselect.doctorCare', '.diagnostic-test-select')
        .on('select2:select.doctorCare select2:unselect.doctorCare', '.diagnostic-test-select', function () {
          if (!(window.OPDCareShared && typeof window.OPDCareShared.refreshDiagnosticPreview === 'function')) {
            return;
          }

          const previewSelector = this.dataset.previewTarget || '#diagnostic-test-preview-body';
          window.OPDCareShared.refreshDiagnosticPreview(`#${this.id}`, previewSelector);
        })
        .off('submit.doctorCare', '#opdPrescriptionForm')
        .on('submit.doctorCare', '#opdPrescriptionForm', function (event) {
          event.preventDefault();
          if (state.activeSection === 'unified') {
            submitUnifiedSaveAll();
            return;
          }
          submitPrescription(this);
        })
        .off('submit.doctorCare', '#saveDiagnosticOrderForm')
        .on('submit.doctorCare', '#saveDiagnosticOrderForm', function (event) {
          event.preventDefault();
          if (state.activeSection === 'unified') {
            submitUnifiedSaveAll();
            return;
          }
          submitDiagnostic(this);
        })
        .off('submit.doctorCare', '.doctor-unified-diagnostic-form')
        .on('submit.doctorCare', '.doctor-unified-diagnostic-form', function (event) {
          event.preventDefault();
          submitUnifiedSaveAll();
        })
        .off('click.doctorCare', '#doctorCareSaveAllBtn')
        .on('click.doctorCare', '#doctorCareSaveAllBtn', function (event) {
          event.preventDefault();
          debugUnifiedSave('Delegated click handler fired: doctorCareSaveAllBtn');
          submitUnifiedSaveAll(true);
        })
        .off('click.doctorCare', '#doctorCareSaveDraftBtn')
        .on('click.doctorCare', '#doctorCareSaveDraftBtn', function (event) {
          event.preventDefault();
          debugUnifiedSave('Delegated click handler fired: doctorCareSaveDraftBtn');
          submitUnifiedSaveAll(false);
        });

      if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', reset);
      }
    }

    bindEvents();

    return {
      open,
      close,
      loadSection,
      getState: () => ({ ...state })
    };
  })();

  // ===== QUEUE DESK MODULE =====

  const QueueDesk = (() => {
    let lastQueueData = null;
    let currentInRoom = null;
    let activeQueueView = 'waiting';

    function setQueueView(view) {
      activeQueueView = view === 'completed' ? 'completed' : 'waiting';
      syncQueueViewControls();

      if (lastQueueData) {
        renderFromData(lastQueueData);
      }
    }

    function syncQueueViewControls(completedCount) {
      const completedBtn = document.getElementById('queueViewCompletedBtn');
      const waitingBtn = document.getElementById('queueViewWaitingBtn');
      const completedCountEl = document.getElementById('queueCompletedCount');
      const queueBadge = document.getElementById('queueCount');
      const queueCallBtn = document.querySelector('.queue-call-next-btn');
      const queueTitle = document.getElementById('queueCardTitle');
      const queueSubtitle = document.getElementById('queueCardSubtitle');

      if (completedCountEl && Number.isFinite(completedCount)) {
        completedCountEl.textContent = String(completedCount);
      }

      const isCompletedView = activeQueueView === 'completed';

      if (completedBtn) {
        completedBtn.style.display = isCompletedView ? 'none' : '';
      }

      if (waitingBtn) {
        waitingBtn.style.display = isCompletedView ? '' : 'none';
      }

      if (queueBadge) {
        queueBadge.style.display = isCompletedView ? 'none' : '';
      }

      if (queueCallBtn) {
        queueCallBtn.style.display = isCompletedView ? 'none' : '';
      }

      if (queueTitle) {
        queueTitle.innerHTML = isCompletedView
          ? '<span class="ct-icon">✅</span> OPD Completed'
          : '<span class="ct-icon">📋</span> OPD Queue';
      }

      if (queueSubtitle) {
        queueSubtitle.textContent = isCompletedView
          ? 'Patients completed from today\'s OPD'
          : 'Patients waiting for consultation';
      }
    }

    async function fetchAndRender() {
      const response = await fetch(ROUTES.queueStatus, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      lastQueueData = await response.json();
      renderFromData(lastQueueData);
      return lastQueueData;
    }

    function renderFromData(data) {
      if (!data) {
        return;
      }

      const queueBody = document.getElementById('opdQueueBody');
      const queueBadge = document.getElementById('queueCount');
      const totalOpdStat = document.getElementById('myOpd');
      const totalOpdSub = document.getElementById('myOpdMeta');
      const newRxBtn = document.getElementById('doctor-new-rx-btn');

      if (!queueBody || !queueBadge) {
        return;
      }

      const currentList = data.current_list || [];
      const waiting = data.queue || [];
      const completed = data.completed || [];
      const waitingPreview = waiting.slice(0, QUEUE_PREVIEW_LIMIT);
      const completedPreview = completed.slice(0, QUEUE_PREVIEW_LIMIT);
      currentInRoom = currentList[0] || null;
      const firstCallable = waiting.find((row) => {
        const st = String(row.status || 'waiting').toLowerCase();
        return !row.absent && st === 'waiting';
      }) || null;
      const completedCount = Number(SNAPSHOT.stats?.completed_count || 0);

      if (newRxBtn) {
        newRxBtn.disabled = !currentInRoom;
      }

      if (totalOpdStat) {
        totalOpdStat.textContent = completedCount + waiting.length + currentList.length;
      }

      if (totalOpdSub) {
        totalOpdSub.textContent = waiting.length > 0 ? waiting.length + ' waiting right now' : 'Queue is clear right now';
      }

      syncQueueViewControls(completed.length);

      queueBadge.textContent = waiting.length + ' Waiting';

      const myCurrent = currentInRoom;
      const myDoctorId = myCurrent ? myCurrent.doctor_id : (waiting[0] ? waiting[0].doctor_id : null);

      if (activeQueueView === 'completed') {
        if (completedPreview.length === 0) {
          queueBody.innerHTML = `
            <tr>
              <td colspan="6" class="text-center text-muted">No completed OPD patients for today.</td>
            </tr>
          `;
          return;
        }

        queueBody.innerHTML = `
          ${completedPreview.map((q) => {
            return `
              <tr>
                <td><span class="badge badge-green">${escapeHtml(q.token)}</span></td>
                <td>
                  <div style="font-weight:700">${escapeHtml(q.name)}</div>
                  <div style="font-size:11px;color:#7a8ea5">${escapeHtml(q.case_no || '-')}</div>
                </td>
                <td>${escapeHtml(q.age_gender || '-')}</td>
                <td>${escapeHtml(q.complaint || 'General consultation')}</td>
                <td style="color:#2e7d32">Completed ${escapeHtml(q.completed_at || '-')}</td>
                <td>
                  <div style="display:flex;gap:4px;justify-content:flex-end">
                    <button class="btn btn-primary btn-xs open-care-section" data-section="summary" ${buildCareDataAttributes(q)}>🩺 See</button>
                    <a class="btn btn-secondary btn-xs" href="${buildVisitHistoryUrl(q)}" title="Patient History">📋</a>
                    <span class="badge badge-green">Done</span>
                  </div>
                </td>
              </tr>
            `;
          }).join('')}
        `;
        return;
      }

      if (waiting.length === 0 && !myCurrent) {
        queueBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center text-muted">No patients in your waiting queue.</td>
          </tr>
        `;
        return;
      }

      const currentRow = myCurrent ? `
        <tr style="background:#e8f5e9">
          <td><span class="badge badge-green">IN-${escapeHtml(myCurrent.token)}</span></td>
          <td><div style="font-weight:700">${escapeHtml(myCurrent.name)}</div></td>
          <td>${escapeHtml(myCurrent.age_gender || '-')}</td>
          <td>${escapeHtml(myCurrent.complaint || 'General consultation')}</td>
          <td>In Room</td>
          <td>
            <div style="display:flex;gap:4px;justify-content:flex-end">
              <button class="btn btn-primary btn-xs open-care-section" data-section="summary" ${buildCareDataAttributes(myCurrent)}>🩺 See</button>
              <a class="btn btn-secondary btn-xs" href="${buildVisitHistoryUrl(myCurrent)}" title="Patient History">📋</a>
              <button class="btn btn-success btn-xs queue-call-next-btn" data-doctor-id="${myDoctorId || ''}">Complete</button>
            </div>
          </td>
        </tr>
      ` : '';

      queueBody.innerHTML = `
        ${currentRow}
        ${waitingPreview.map((q, index) => {
                const priority = getPriorityMeta(q);
                const waitText = formatWaitLabel(q);
                const canCall = !currentInRoom && !q.absent && firstCallable && q.id === firstCallable.id;

                const qst = String(q.status || 'waiting').toLowerCase();
                const showAbsentControls = qst === 'waiting';
                return `
                  <tr class="${q.absent && showAbsentControls ? 'table-secondary opacity-75' : ''}">
                    <td><span class="badge badge-blue">${escapeHtml(q.token)}</span></td>
                    <td>
                      <div style="font-weight:700">${escapeHtml(q.name)}</div>
                      <div style="font-size:11px;color:#7a8ea5">${escapeHtml(q.case_no || '-')}</div>
                    </td>
                    <td>${escapeHtml(q.age_gender || '-')}</td>
                    <td>${escapeHtml(q.complaint || 'General consultation')}</td>
                    <td style="color:${priority.cls === 'urgent' ? 'var(--danger)' : '#6f839a'}">${escapeHtml(waitText)}</td>
                    <td>
                      <div style="display:flex;gap:4px;justify-content:flex-end;align-items:center">
                        ${canCall
                          ? `<button class="btn btn-secondary btn-xs queue-call-next-btn" data-doctor-id="${q.doctor_id || ''}">Call</button>`
                          : `<span class="badge badge-orange">Waiting</span>`}
                        ${showAbsentControls ? (q.absent
                          ? `<button type="button" class="btn btn-outline-success btn-xs py-0 px-2 queue-undo-skip-btn" data-id="${q.id}" title="Mark present"><i class="fa-solid fa-user-check"></i></button>`
                          : `<button type="button" class="btn btn-outline-danger btn-xs py-0 px-2 queue-skip-btn" data-id="${q.id}" title="Not present"><i class="fa-solid fa-user-slash"></i></button>`) : ''}
                      </div>
                    </td>
                  </tr>
                `;
              }).join('')}
      `;
    }

    async function callNext(doctorId, btnEl) {
      const btn = btnEl || null;
      const originalHtml = btn ? btn.innerHTML : '';

      if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Please wait...';
      }

      try {
        const body = doctorId ? { doctor_id: doctorId } : {};
        const result = await postJson(ROUTES.callNext, body);

        if (!result.status) {
          notify('error', result.message || 'Unable to call next patient.');
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
          }
          return;
        }

        notify('success', result.message || 'Next patient called.');
        await fetchAndRender();
      } catch (error) {
        notify('error', 'Unable to call next patient.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = originalHtml;
        }
      }
    }

    async function skipPatient(id) {
      const result = await postJson(routeWithId(ROUTES.skipPatient, id), {});
      if (!result.status) {
        notify('error', result.message || 'Unable to mark absent.');
        return;
      }
      await fetchAndRender();
    }

    async function undoSkip(id) {
      const result = await postJson(routeWithId(ROUTES.undoSkip, id), {});
      if (!result.status) {
        notify('error', result.message || 'Unable to mark present.');
        return;
      }
      await fetchAndRender();
    }

    function bindEvents() {
      if (!window.jQuery) {
        return;
      }

      jQuery(document)
        .off('click.queueDesk', '.open-care-section')
        .on('click.queueDesk', '.open-care-section', function (event) {
          event.preventDefault();
          DoctorCareModal.open({
            opdPatientId: this.dataset.opdId,
            patientId: this.dataset.patientId,
            doctorId: this.dataset.doctorId,
            doctor: this.dataset.doctor,
            dept: this.dataset.dept,
            token: this.dataset.token,
            patientName: this.dataset.name,
            caseNo: this.dataset.case
          }, this.dataset.section || 'summary');
        })
        .off('click.queueDesk', '.queue-call-next-btn')
        .on('click.queueDesk', '.queue-call-next-btn', function (event) {
          event.preventDefault();
          callNext(this.dataset.doctorId || null, this);
        })
        .off('click.queueDesk', '.queue-skip-btn')
        .on('click.queueDesk', '.queue-skip-btn', function () {
          skipPatient(this.dataset.id);
        })
        .off('click.queueDesk', '.queue-undo-skip-btn')
        .on('click.queueDesk', '.queue-undo-skip-btn', function () {
          undoSkip(this.dataset.id);
        })
        .off('click.queueDesk', '#queueViewCompletedBtn')
        .on('click.queueDesk', '#queueViewCompletedBtn', function (event) {
          event.preventDefault();
          setQueueView('completed');
        })
        .off('click.queueDesk', '#queueViewWaitingBtn')
        .on('click.queueDesk', '#queueViewWaitingBtn', function (event) {
          event.preventDefault();
          setQueueView('waiting');
        });
    }

    bindEvents();

    return { fetchAndRender, getData: () => lastQueueData, getCurrentInRoom: () => currentInRoom };
  })();

  // ===== PUBLIC API =====

  async function openDoctorQuickPrescription() {
    try {
      const data = QueueDesk.getData() || await QueueDesk.fetchAndRender();
      const current = (data.current_list || [])[0] || null;

      if (!current) {
        notify('warning', 'New prescription can be created only when a patient is in room.');
        return;
      }

      DoctorCareModal.open({
        opdPatientId: current.id,
        patientId: current.patient_id,
        doctorId: current.doctor_id,
        doctor: current.doctor,
        dept: current.dept,
        token: current.token,
        patientName: current.name,
        caseNo: current.case_no
      }, 'prescription');
    } catch (error) {
      notify('error', 'Unable to open prescription right now.');
    }
  }

  initializeTabs();
  createFlowChart();

  function startLiveClock() {
    const clock = document.getElementById('liveClock');
    if (!clock) {
      return;
    }

    const render = () => {
      clock.textContent = new Date().toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    };

    render();
    setInterval(render, 1000);
  }

  startLiveClock();

  // Initialize on page load
  QueueDesk.fetchAndRender();
  setInterval(() => QueueDesk.fetchAndRender(), 6000);

  // Export public API to window
  window.DoctorDashboardApp = {
    QueueDesk,
    DoctorCareModal,
    openDoctorQuickPrescription
  };

})(window);
