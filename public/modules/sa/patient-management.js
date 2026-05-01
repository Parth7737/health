const PM_ROUTES = window.PM_ROUTES || {};
const PM_BOOT = window.PM_BOOT || {};

const TAB_IDS = {
  all: 'tc-all',
  opd: 'tc-opd',
  booking: 'tc-book',
  ipd: 'tc-ipd',
  emergency: 'tc-em',
  discharged: 'tc-dis',
};

const TAB_LOADERS = {
  all: (page) => loadPatientsPage(page),
  opd: (page) => loadOPDCards(page),
  booking: (page) => loadBookingAppointments(page),
  ipd: (page) => loadIPDList(page),
  emergency: (page) => loadEmergencyList(page),
  discharged: (page) => loadDischargedList(page),
};

const pmState = {
  activeTab: 'all',
  all: { page: 1, perPage: 20, total: 0, lastPage: 1 },
  opd: { page: 1, perPage: 12, total: 0, lastPage: 1 },
  booking: { page: 1, perPage: 12, total: 0, lastPage: 1 },
  ipd: { page: 1, perPage: 10, total: 0, lastPage: 1 },
  emergency: { page: 1, perPage: 10, total: 0, lastPage: 1 },
  discharged: { page: 1, perPage: 10, total: 0, lastPage: 1 },
};

/** Lazy tab loads: list AJAX runs when the user opens a tab (not all at once on init). */
const tabDataLoaded = {
  all: false,
  opd: false,
  booking: false,
  ipd: false,
  emergency: false,
  discharged: false,
};

async function pmFetch(url, options = {}) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    || window.Laravel?.csrfToken
    || document.querySelector('input[name="_token"]')?.value
    || '';

  const headers = {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    ...options.headers,
  };
  const method = String(options.method || 'GET').toUpperCase();
  if (csrfToken && method !== 'GET' && method !== 'HEAD') {
    headers['X-CSRF-TOKEN'] = csrfToken;
  }
  if (options.body && !(options.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
  }

  const response = await fetch(url, {
    credentials: 'same-origin',
    ...options,
    headers,
    body: options.body && !(options.body instanceof FormData) ? JSON.stringify(options.body) : options.body,
  });

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    const errorMessage = data?.message || data?.errors?.[0]?.message || 'Request failed';
    const error = new Error(errorMessage);
    error.status = response.status;
    error.responseData = data;
    throw error;
  }

  return data;
}

function notify(title, message, type = 'info') {
  if (typeof showToast === 'function') {
    showToast(title, message, type);
    return;
  }
  alert(`${title}: ${message}`);
}

function renderOptions(select, items, config = {}) {
  if (!select) return;
  const placeholder = config.placeholder || 'Select';
  const rows = Array.isArray(items) ? items : [];
  select.innerHTML = `<option value="">${placeholder}</option>` + rows.map((item) => {
    const value = item[config.valueKey || 'id'];
    const label = item[config.labelKey || 'name'];
    return `<option value="${escapeHtml(value)}">${escapeHtml(label)}</option>`;
  }).join('');
}

function debounce(fn, delay = 350) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function ucwords(str) {
  return String(str || '')
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ');
}

function parseLocalDateOnly(value) {
  if (!value) return null;

  if (value instanceof Date) {
    return new Date(value.getFullYear(), value.getMonth(), value.getDate());
  }

  const raw = String(value).trim();
  const dateOnly = raw.split('T')[0].split(' ')[0];
  const ymdMatch = dateOnly.match(/^(\d{4})-(\d{2})-(\d{2})$/);

  if (ymdMatch) {
    return new Date(Number(ymdMatch[1]), Number(ymdMatch[2]) - 1, Number(ymdMatch[3]));
  }

  const parsed = new Date(raw);
  if (Number.isNaN(parsed.getTime())) return null;
  return new Date(parsed.getFullYear(), parsed.getMonth(), parsed.getDate());
}

function encodeInline(value) {
  return encodeURIComponent(String(value ?? ''));
}

function switchPtTab(targetPaneId, triggerButton) {
  const panes = ['ptListPane', 'opdPane', 'bookingPane', 'ipdPane', 'emergencyPane', 'dischargePane'];
  panes.forEach((paneId) => {
    const pane = document.getElementById(paneId);
    if (pane) {
      pane.style.display = paneId === targetPaneId ? '' : 'none';
    }
  });

  document.querySelectorAll('#ptTabBar .tab-btn').forEach((button) => {
    button.classList.remove('active');
  });

  if (triggerButton?.classList) {
    triggerButton.classList.add('active');
  }

  const tabKey = {
    ptListPane: 'all',
    opdPane: 'opd',
    bookingPane: 'booking',
    ipdPane: 'ipd',
    emergencyPane: 'emergency',
    dischargePane: 'discharged',
  }[targetPaneId] || 'all';

  pmState.activeTab = tabKey;
  void ensureTabDataLoaded(tabKey).catch((error) => notify('Load Failed', error.message, 'error'));
}

function updateBadgeCount(tabKey, total) {
  const badge = document.getElementById(TAB_IDS[tabKey]);
  if (badge) {
    badge.textContent = total || 0;
  }
}

function updateStatsCards(data) {
  const mappings = {
    kpiOpdToday: data.opd_today,
    kpiIpdActive: data.ipd_active,
    kpiEmergency: data.emergency,
    kpiDischargedToday: data.discharged_today,
    kpiTransferred: data.transferred_today,
    kpiTotalActive: data.total_active,
  };

  Object.entries(mappings).forEach(([id, value]) => {
    const node = document.getElementById(id);
    if (node) {
      node.textContent = Number(value || 0);
    }
  });
}

/** Tab pill counts from stats (so badges stay correct before a tab’s list is loaded). */
function updateTabBadgesFromStats(data) {
  const map = {
    all: data.tab_all_patients,
    opd: data.tab_opd_queue,
    booking: data.tab_booking,
    ipd: data.tab_ipd,
    emergency: data.tab_emergency,
    discharged: data.tab_discharged,
  };
  Object.entries(map).forEach(([tabKey, value]) => {
    if (value !== undefined && value !== null) {
      updateBadgeCount(tabKey, value);
    }
  });
}

async function ensureTabDataLoaded(tabKey) {
  if (!tabKey || tabDataLoaded[tabKey]) {
    return;
  }
  const loader = TAB_LOADERS[tabKey];
  if (!loader) {
    return;
  }
  await loader(pmState[tabKey]?.page);
  tabDataLoaded[tabKey] = true;
}

function resetTabLoadedFlags() {
  Object.keys(tabDataLoaded).forEach((k) => {
    tabDataLoaded[k] = false;
  });
}

function setTabMeta(tabKey, payload) {
  pmState[tabKey].total = Number(payload?.total || 0);
  pmState[tabKey].page = Number(payload?.page || 1);
  pmState[tabKey].perPage = Number(payload?.per_page || pmState[tabKey].perPage || 10);
  pmState[tabKey].lastPage = Math.max(1, Number(payload?.last_page || 1));
  updateBadgeCount(tabKey, pmState[tabKey].total);
}

function formatPaginationInfo(page, perPage, total, count, label = 'records') {
  if (!total || !count) {
    return `Showing 0-0 of 0 ${label}`;
  }
  const start = ((page - 1) * perPage) + 1;
  const end = start + count - 1;
  return `Showing ${start}-${end} of ${total} ${label}`;
}

function buildPaginationButtons(tabKey, page, lastPage) {
  const pages = [];
  const start = Math.max(1, page - 2);
  const end = Math.min(lastPage, page + 2);

  pages.push(`
    <button class="pg-btn${page <= 1 ? ' disabled' : ''}" ${page <= 1 ? 'disabled' : ''} onclick="pmGoToTabPage('${tabKey}', ${page - 1})">‹</button>
  `);

  for (let cursor = start; cursor <= end; cursor += 1) {
    pages.push(`
      <button class="pg-btn${cursor === page ? ' active' : ''}" onclick="pmGoToTabPage('${tabKey}', ${cursor})">${cursor}</button>
    `);
  }

  pages.push(`
    <button class="pg-btn${page >= lastPage ? ' disabled' : ''}" ${page >= lastPage ? 'disabled' : ''} onclick="pmGoToTabPage('${tabKey}', ${page + 1})">›</button>
  `);

  return pages.join('');
}

function renderPagination(tabKey, infoId, buttonsId, count, label) {
  const state = pmState[tabKey];
  const infoNode = document.getElementById(infoId);
  const buttonsNode = document.getElementById(buttonsId);

  if (infoNode) {
    infoNode.textContent = formatPaginationInfo(state.page, state.perPage, state.total, count, label);
  }

  if (buttonsNode) {
    buttonsNode.innerHTML = buildPaginationButtons(tabKey, state.page, state.lastPage);
  }
}

function renderAllPatientsPagination(count) {
  renderPagination('all', 'ptPagInfo', 'ptPagBtns', count, 'patients');
}

function resetAllPages() {
  ['all', 'opd', 'booking', 'ipd', 'emergency', 'discharged'].forEach((tabKey) => {
    pmState[tabKey].page = 1;
  });
}

function buildUrl(baseUrl, params) {
  const searchParams = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null || value === '') {
      return;
    }
    searchParams.append(key, value);
  });
  return `${baseUrl}?${searchParams.toString()}`;
}

function currentValue(id) {
  return document.getElementById(id)?.value || '';
}

async function initPatientManagementDynamic() {
  renderOptions(document.getElementById('tok_dept'), PM_BOOT.departments, { placeholder: 'Select Department' });
  renderOptions(document.getElementById('deptFilter'), PM_BOOT.departments, { placeholder: 'All Departments' });
  renderOptions(document.getElementById('opdDeptFilter'), PM_BOOT.departments, { placeholder: 'All Departments' });
  renderOptions(document.getElementById('bookingDeptFilter'), PM_BOOT.departments, { placeholder: 'All Departments' });
  renderOptions(document.getElementById('ipdDeptFilter'), PM_BOOT.departments, { placeholder: 'All Departments' });
  renderOptions(document.getElementById('emDeptFilter'), PM_BOOT.departments, { placeholder: 'All Departments' });
  renderOptions(document.getElementById('disDeptFilter'), PM_BOOT.departments, { placeholder: 'All Departments' });
  renderOptions(document.getElementById('admit_dept'), PM_BOOT.departments, { placeholder: 'Select Department' });

  window.PatientRegistrationForm?.init({ routes: PM_ROUTES, boot: PM_BOOT });
  window.PatientVisitModals?.init({ routes: PM_ROUTES, boot: PM_BOOT });

  bindPmEvents();
  await initPatientManagementFirstPaint();
}

/** Initial load: KPI + tab badges + “All patients” list only; other tabs load on first open. */
async function initPatientManagementFirstPaint() {
  resetAllPages();
  resetTabLoadedFlags();
  await Promise.all([loadDashboardStats(), loadPatientsPage(1)]);
  tabDataLoaded.all = true;
}

function bindPmEvents() {
  document.getElementById('ptSearch')?.addEventListener('input', debounce(() => loadPatientsPage(1)));
  document.getElementById('ptFilter')?.addEventListener('change', () => loadPatientsPage(1));
  document.getElementById('deptFilter')?.addEventListener('change', () => loadPatientsPage(1));
  document.getElementById('ptResetBtn')?.addEventListener('click', () => {
    const search = document.getElementById('ptSearch');
    const filter = document.getElementById('ptFilter');
    const dept = document.getElementById('deptFilter');
    if (search) search.value = '';
    if (filter) filter.value = '';
    if (dept) dept.value = '';
    loadPatientsPage(1);
  });

  document.getElementById('opdSearch')?.addEventListener('input', debounce(() => loadOPDCards(1)));
  document.getElementById('opdDeptFilter')?.addEventListener('change', () => loadOPDCards(1));

  document.getElementById('bookingSearch')?.addEventListener('input', debounce(() => loadBookingAppointments(1)));
  document.getElementById('bookingDeptFilter')?.addEventListener('change', () => loadBookingAppointments(1));

  document.getElementById('ipdSearch')?.addEventListener('input', debounce(() => loadIPDList(1)));
  document.getElementById('ipdDeptFilter')?.addEventListener('change', () => loadIPDList(1));

  document.getElementById('emSearch')?.addEventListener('input', debounce(() => loadEmergencyList(1)));
  document.getElementById('emDeptFilter')?.addEventListener('change', () => loadEmergencyList(1));

  document.getElementById('disSearch')?.addEventListener('input', debounce(() => loadDischargedList(1)));
  document.getElementById('disDeptFilter')?.addEventListener('change', () => loadDischargedList(1));
}

async function loadDashboardStats() {
  const data = await pmFetch(PM_ROUTES.stats);
  updateStatsCards(data || {});
  updateTabBadgesFromStats(data || {});
}

async function loadPatientsPage(page = pmState.all.page) {
  pmState.all.page = page;
  const url = buildUrl(PM_ROUTES.patients, {
    page,
    per_page: pmState.all.perPage,
    q: currentValue('ptSearch'),
    filter: currentValue('ptFilter'),
    dept_id: currentValue('deptFilter'),
  });

  const data = await pmFetch(url);
  const rows = Array.isArray(data?.data) ? data.data : [];
  setTabMeta('all', data);
  renderPatientTableDynamic(rows);
  renderAllPatientsPagination(rows.length);
}

function renderPatientTableDynamic(patients) {
  const statusMap = {
    waiting: 'badge-blue',
    in_room: 'badge-orange',
    completed: 'badge-green',
    admitted: 'badge-teal',
    critical: 'badge-red',
    discharged: 'badge-gray',
    registered: 'badge-gray',
  };
  const visitMap = {
    OPD: 'badge-blue',
    IPD: 'badge-teal',
    Emergency: 'badge-red',
    Daycare: 'badge-purple',
    '-': 'badge-gray',
  };

  document.getElementById('patientTableBody').innerHTML = patients.map((patient) => {
    const mrn = encodeInline(patient.mrn || '');
    const patientId = Number(patient.id || patient.patient_id || 0);
    const isOPD = patient.visit_type === 'OPD';
    const statusText = ucwords(patient.status || '-');
    const apptText = isOPD && patient.appointment_date ? `<div style="font-size:9px;margin-top:3px;opacity:.7">${escapeHtml(patient.appointment_date)} ${escapeHtml(patient.slot || '')}</div>` : '';
    const statusDisplay = `${statusText}${apptText}`;
    return `
      <tr>
        <td><span class="text-primary fw-700" style="cursor:pointer" onclick="viewPatient360(decodeURIComponent('${mrn}'))">${escapeHtml(patient.mrn || '-')}</span></td>
        <td><div class="fw-700 fs-12">${escapeHtml(patient.name || '-')}</div></td>
        <td>${escapeHtml(patient.age_sex || '-')}</td>
        <td>${escapeHtml(patient.phone || '-')}</td>
        <td><span class="badge badge-gray" style="font-size:10px">${escapeHtml(patient.blood_group || '-')}</span></td>
        <td><span class="badge ${visitMap[patient.visit_type] || 'badge-gray'}">${escapeHtml(patient.visit_type || '-')}</span></td>
        <td style="font-size:11.5px">${escapeHtml(patient.dept || '-')}</td>
        <td><span class="badge ${statusMap[patient.status] || 'badge-gray'}">${statusDisplay}</span></td>
        <td style="font-size:11px;color:var(--text-muted)">${escapeHtml(patient.registered || '-')}</td>
        <td>
          <div style="display:flex;gap:3px">
            <button class="btn btn-primary btn-xs" onclick="openPatientDetails(${patientId}, decodeURIComponent('${mrn}'))" title="View Details">👁️</button>
          </div>
        </td>
      </tr>`;
  }).join('') || '<tr><td colspan="11" class="text-center text-muted">No patients found</td></tr>';
}

async function searchPatients() {
  await loadPatientsPage(1);
}

async function issueNextTokenForBooking(opdPatientId) {
  if (!PM_ROUTES.issueNextToken) {
    notify('Config Missing', 'Issue next token route is not configured.', 'error');
    return;
  }

  try {
    const response = await pmFetch(PM_ROUTES.issueNextToken, {
      method: 'POST',
      body: {
        opd_patient_id: opdPatientId,
        force: true,
      },
    });

    notify('Success', response?.message || 'Token assigned successfully.', 'success');
    await loadDashboardStats();
    const tasks = [loadOPDCards(pmState.opd.page)];
    if (tabDataLoaded.booking) {
      tasks.push(loadBookingAppointments(pmState.booking.page));
    }
    await Promise.all(tasks);
  } catch (error) {
    notify('Token Issue Failed', error.message || 'Unable to issue token right now.', 'error');
  }
}

function pmOpdQueueActionUrl(routeTemplate, opdPatientId) {
  if (!routeTemplate || String(routeTemplate).indexOf('__ID__') === -1) {
    return '';
  }
  return String(routeTemplate).replace('__ID__', String(opdPatientId));
}

function renderOpdQueueCardFooter(patient) {
  const id = Number(patient.id);
  const st = String(patient.status || '').toLowerCase();
  const isWaiting = st === 'waiting';
  const parts = [];

  if (!patient.token_no) {
    parts.push(`<button type="button" class="btn btn-primary btn-sm" style="width:100%" onclick="pmIssueNextToken(${id})">🎫 Issue Token</button>`);
  }

  if (isWaiting) {
    if (patient.absent) {
      parts.push(`<button type="button" class="btn btn-primary btn-sm" style="width:100%" onclick="pmMarkOpdPresent(${id})">✓ Mark Present</button>`);
    } else {
      parts.push(`<button type="button" class="btn btn-danger btn-sm" style="width:100%" onclick="pmMarkOpdAbsent(${id})">🙅 Not Present</button>`);
    }
  }

  if (parts.length === 0) {
    return '<div class="fs-11 text-muted">—</div>';
  }

  return `<div style="display:flex;flex-direction:column;gap:8px;width:100%">${parts.join('')}</div>`;
}

async function pmMarkOpdAbsent(opdPatientId) {
  const url = pmOpdQueueActionUrl(PM_ROUTES.opdQueueSkip, opdPatientId);
  if (!url) {
    notify('Config Missing', 'Mark not present route is not configured.', 'error');
    return;
  }
  try {
    await pmFetch(url, { method: 'POST', body: {} });
    notify('Updated', 'Patient marked as not present.', 'success');
    await loadDashboardStats();
    await loadOPDCards(pmState.opd.page);
  } catch (error) {
    notify('Not Present', error.message || 'Unable to update.', 'error');
  }
}

async function pmMarkOpdPresent(opdPatientId) {
  const url = pmOpdQueueActionUrl(PM_ROUTES.opdQueueUndoSkip, opdPatientId);
  if (!url) {
    notify('Config Missing', 'Mark present route is not configured.', 'error');
    return;
  }
  try {
    await pmFetch(url, { method: 'POST', body: {} });
    notify('Updated', 'Patient marked as present.', 'success');
    await loadDashboardStats();
    await loadOPDCards(pmState.opd.page);
  } catch (error) {
    notify('Mark Present', error.message || 'Unable to update.', 'error');
  }
}

async function cancelFutureBookingAppointment(opdPatientId) {
  if (!PM_ROUTES.cancelBookingAppointment) {
    notify('Config Missing', 'Cancel booking route is not configured.', 'error');
    return;
  }

  const result = await Swal.fire({
    title: 'Cancel Booking Appointment?',
    text: 'This will permanently delete this future booking record.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Cancel Booking',
    cancelButtonText: 'No',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
  });

  if (!result.isConfirmed) {
    return;
  }

  try {
    const response = await pmFetch(PM_ROUTES.cancelBookingAppointment, {
      method: 'POST',
      body: {
        opd_patient_id: opdPatientId,
      },
    });

    notify('Success', response?.message || 'Booking appointment cancelled successfully.', 'success');
    await loadBookingAppointments(pmState.booking.page);
  } catch (error) {
    notify('Cancel Failed', error.message || 'Unable to cancel this booking right now.', 'error');
  }
}

async function loadOPDCards(page = pmState.opd.page) {
  pmState.opd.page = page;
  const url = buildUrl(PM_ROUTES.opdQueue, {
    page,
    per_page: pmState.opd.perPage,
    q: currentValue('opdSearch'),
    dept_id: currentValue('opdDeptFilter'),
  });

  const data = await pmFetch(url);
  const rows = Array.isArray(data?.data) ? data.data : [];
  setTabMeta('opd', data);

  document.getElementById('opdQueueCards').innerHTML = `
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px">
      ${rows.map((patient) => {
        const isBooking = patient.queue_type === 'booking';
        const statusBadge = isBooking ? 'purple' : 'blue';
        const statusLabel = isBooking ? '📘 Booking' : '⏳ Waiting';
        const tokenDisplay = patient.token_no ? escapeHtml(patient.token || '-') : escapeHtml(patient.booking_number || '-');
        const chipDisplay = patient.token_no ? `${tokenDisplay}` : tokenDisplay;

        return `
        <div style="background:var(--surface);border:1.5px solid var(--border-light);border-radius:12px;padding:14px;transition:.2s;opacity:${patient.absent && String(patient.status || '').toLowerCase() === 'waiting' ? '0.72' : '1'}" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-light)'">
          <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;align-items:flex-start">
            <span class="badge badge-${patient.token_no ? 'blue' : 'gray'}">${chipDisplay}</span>
            <span class="badge badge-${statusBadge}">${statusLabel}</span>
          </div>
          <div class="fw-700 fs-13">${escapeHtml(patient.patient || '-')}${patient.token_no && patient.booking_number ? ` <span class="fs-11 text-muted fw-400">(${escapeHtml(patient.booking_number)})</span>` : ''}</div>
          <div class="fs-11 text-muted">${escapeHtml(patient.age_sex || '-')} | ${escapeHtml(patient.dept || '-')}</div>
          <div class="fs-11 text-muted mt-2">🏥 ${escapeHtml(patient.visit_type || '-')}</div>
          <div class="fs-11 text-muted mt-2">👨‍⚕️ ${escapeHtml(patient.doctor || '-')}</div>
          <div class="fs-11 text-muted mt-2">📅 ${escapeHtml(patient.appointment_date || '-')} ${escapeHtml(patient.time || '')}</div>
          <div class="fs-11 text-muted mt-2">⏱️ ${escapeHtml(patient.slot || '-')}</div>

          <div style="margin-top:8px;padding:8px;border:1px solid var(--border-light);border-radius:8px;background:var(--surface-2)">
            ${renderOpdQueueCardFooter(patient)}
          </div>
        </div>`;
      }).join('') || '<div class="empty-state"><div class="empty-title">No waiting/booking OPD patients for current filter</div></div>'}
    </div>`;

  renderPagination('opd', 'opdPagInfo', 'opdPagBtns', rows.length, 'records');
}

async function loadBookingAppointments(page = pmState.booking.page) {
  pmState.booking.page = page;
  const url = buildUrl(PM_ROUTES.bookingAppointments, {
    page,
    per_page: pmState.booking.perPage,
    q: currentValue('bookingSearch'),
    dept_id: currentValue('bookingDeptFilter'),
  });

  const data = await pmFetch(url);
  const rows = Array.isArray(data?.data) ? data.data : [];
  setTabMeta('booking', data);

  document.getElementById('bookingQueueCards').innerHTML = `
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px">
      ${rows.map((patient) => {
        const isBooking = patient.queue_type === 'booking';
        const statusBadge = isBooking ? 'purple' : 'blue';
        const statusLabel = isBooking ? '📘 Booking' : '⏳ Waiting';
        const tokenDisplay = patient.token_no ? escapeHtml(patient.token || '-') : escapeHtml(patient.booking_number || '-');
        const chipDisplay = patient.token_no ? `${tokenDisplay}` : tokenDisplay;

        return `
        <div style="background:var(--surface);border:1.5px solid var(--border-light);border-radius:12px;padding:14px;transition:.2s" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-light)'">
          <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;align-items:flex-start">
            <span class="badge badge-${patient.token_no ? 'blue' : 'gray'}">${chipDisplay}</span>
            <span class="badge badge-${statusBadge}">${statusLabel}</span>
          </div>
          <div class="fw-700 fs-13">${escapeHtml(patient.patient || '-')}${patient.token_no && patient.booking_number ? ` <span class="fs-11 text-muted fw-400">(${escapeHtml(patient.booking_number)})</span>` : ''}</div>
          <div class="fs-11 text-muted">${escapeHtml(patient.age_sex || '-')} | ${escapeHtml(patient.dept || '-')}</div>
          <div class="fs-11 text-muted mt-2">🏥 ${escapeHtml(patient.visit_type || '-')}</div>
          <div class="fs-11 text-muted mt-2">👨‍⚕️ ${escapeHtml(patient.doctor || '-')}</div>
          <div class="fs-11 text-muted mt-2">📅 ${escapeHtml(patient.appointment_date || '-')} ${escapeHtml(patient.time || '')}</div>
          <div class="fs-11 text-muted mt-2">⏱️ ${escapeHtml(patient.slot || '-')}</div>

          <div style="margin-top:8px;padding:8px;border:1px solid var(--border-light);border-radius:8px;background:var(--surface-2)">
            ${patient.token_no
    ? '<div class="fs-11 text-muted">✓ Token assigned</div>'
    : `<button type="button" class="btn btn-primary btn-sm" style="width:100%" onclick="pmIssueNextToken(${Number(patient.id)})">🎫 Issue Token</button>
            <button type="button" class="btn btn-danger btn-xs" style="margin-top:6px;width:100%" onclick="pmCancelFutureBooking(${Number(patient.id)})" title="Cancel this future booking">🗑️ Cancel Booking</button>`}
          </div>
        </div>`;
      }).join('') || '<div class="empty-state"><div class="empty-title">No future booking appointments for current filter</div></div>'}
    </div>`;

  renderPagination('booking', 'bookingPagInfo', 'bookingPagBtns', rows.length, 'records');
}

async function loadIPDList(page = pmState.ipd.page) {
  pmState.ipd.page = page;
  const url = buildUrl(PM_ROUTES.ipdAdmissions, {
    page,
    per_page: pmState.ipd.perPage,
    q: currentValue('ipdSearch'),
    dept_id: currentValue('ipdDeptFilter'),
  });

  const data = await pmFetch(url);
  const rows = Array.isArray(data?.data) ? data.data : [];
  setTabMeta('ipd', data);

  const visitMap = {
    OPD: 'badge-blue',
    IPD: 'badge-teal',
    Emergency: 'badge-red',
    Daycare: 'badge-purple',
    '-': 'badge-gray',
  };

  document.getElementById('ipdAdmissionsList').innerHTML = `
    <div class="table-wrap">
      <table class="hims-table">
        <thead><tr><th>MRN</th><th>Patient</th><th>Age/Sex</th><th>Visit Type</th><th>Department</th><th>Bed</th><th>Ward</th><th>Day</th><th>Actions</th></tr></thead>
        <tbody>${rows.map((patient) => {
          const mrn = encodeInline(patient.mrn || '');
          const patientId = Number(patient.patient_id || 0);
          return `
            <tr>
              <td class="text-primary fw-700" style="cursor:pointer" onclick="viewPatient360(decodeURIComponent('${mrn}'))">${escapeHtml(patient.mrn || '-')}</td>
              <td class="fw-700 fs-12">${escapeHtml(patient.patient || '-')}</td>
              <td>${escapeHtml(patient.age_sex || '-')}</td>
              <td><span class="badge ${visitMap[patient.visit_type] || 'badge-gray'}">${escapeHtml(patient.visit_type || '-')}</span></td>
              <td class="fs-11">${escapeHtml(patient.dept || '-')}</td>
              <td><span class="badge badge-teal fs-10">${escapeHtml(patient.bed || '-')}</span></td>
              <td><span class="badge badge-gray fs-10">${escapeHtml(patient.ward || '-')}</span></td>
              <td class="text-muted">Day ${escapeHtml(patient.days || '-')}</td>
              <td>
                <div style="display:flex;gap:3px">
                  <button class="btn btn-primary btn-xs" onclick="openPatientDetails(${patientId}, decodeURIComponent('${mrn}'))" title="View Details">👁️</button>
                </div>
              </td>
            </tr>`;
        }).join('') || '<tr><td colspan="9" class="text-center text-muted">No active IPD admissions</td></tr>'}
        </tbody>
      </table>
    </div>`;

  renderPagination('ipd', 'ipdPagInfo', 'ipdPagBtns', rows.length, 'records');
}

async function loadEmergencyList(page = pmState.emergency.page) {
  pmState.emergency.page = page;
  const url = buildUrl(PM_ROUTES.opdQueue, {
    page,
    per_page: pmState.emergency.perPage,
    q: currentValue('emSearch'),
    dept_id: currentValue('emDeptFilter'),
    emergency_only: 1,
  });

  const data = await pmFetch(url);
  const rows = Array.isArray(data?.data) ? data.data : [];
  setTabMeta('emergency', data);

  document.getElementById('emergencyList').innerHTML = `
    <div class="alert alert-red mb-12"><span class="alert-icon">🚨</span><div><b>${pmState.emergency.total} Emergency Cases Active</b> - Priority triage system active</div></div>
    <div class="table-wrap">
      <table class="hims-table">
        <thead><tr><th>MRN</th><th>Patient</th><th>Triage</th><th>Chief Slot</th><th>Assigned Doctor</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>${rows.map((patient) => {
          const mrn = encodeInline(patient.mrn || '');
          const patientId = Number(patient.patient_id || 0);
          return `
            <tr style="border-left:3px solid var(--danger)">
              <td><span class="text-primary fw-700" style="cursor:pointer" onclick="viewPatient360(decodeURIComponent('${mrn}'))">${escapeHtml(patient.mrn || '-')}</span></td>
              <td><div class="fw-700 fs-12">${escapeHtml(patient.patient || '-')}</div><div class="fs-11 text-muted">${escapeHtml(patient.age_sex || '-')}</div></td>
              <td><span class="badge badge-red">🔴 Red</span></td>
              <td class="fs-11">${escapeHtml(patient.slot || patient.time || '-')}</td>
              <td>${escapeHtml(patient.doctor || '-')}</td>
              <td><span class="status-dot critical">${escapeHtml(patient.status || '-')}</span></td>
              <td>
                <div style="display:flex;gap:3px">
                  <button class="btn btn-primary btn-xs" onclick="openPatientDetails(${patientId}, decodeURIComponent('${mrn}'))" title="View Details">👁️</button>
                </div>
              </td>
            </tr>`;
        }).join('') || '<tr><td colspan="7" class="text-center text-muted">No emergency cases</td></tr>'}
        </tbody>
      </table>
    </div>`;

  renderPagination('emergency', 'emPagInfo', 'emPagBtns', rows.length, 'records');
}

async function loadDischargedList(page = pmState.discharged.page) {
  pmState.discharged.page = page;
  const url = buildUrl(PM_ROUTES.patients, {
    page,
    per_page: pmState.discharged.perPage,
    filter: 'discharged',
    q: currentValue('disSearch'),
    dept_id: currentValue('disDeptFilter'),
  });

  const data = await pmFetch(url);
  const rows = Array.isArray(data?.data) ? data.data : [];
  setTabMeta('discharged', data);

  document.getElementById('dischargedList').innerHTML = rows.length > 0 ? `
    <div class="table-wrap">
      <table class="hims-table">
        <thead><tr><th>MRN</th><th>Patient</th><th>Department</th><th>Discharge Status</th><th>Actions</th></tr></thead>
        <tbody>${rows.map((patient) => {
          const mrn = encodeInline(patient.mrn || '');
          const patientId = Number(patient.id || patient.patient_id || 0);
          return `
            <tr>
              <td><span class="text-primary fw-700" style="cursor:pointer" onclick="viewPatient360(decodeURIComponent('${mrn}'))">${escapeHtml(patient.mrn || '-')}</span></td>
              <td>${escapeHtml(patient.name || '-')}</td>
              <td>${escapeHtml(patient.dept || '-')}</td>
              <td class="text-muted">${escapeHtml(patient.status || '-')}</td>
              <td>
                <div style="display:flex;gap:3px">
                  <button class="btn btn-primary btn-xs" onclick="openPatientDetails(${patientId}, decodeURIComponent('${mrn}'))" title="View Details">👁️</button>
                </div>
              </td>
            </tr>`;
        }).join('')}
        </tbody>
      </table>
    </div>` : '<div class="empty-state"><div class="empty-icon">✅</div><div class="empty-title">No discharges for current filter</div></div>';

  renderPagination('discharged', 'disPagInfo', 'disPagBtns', rows.length, 'records');
}

function convertSlotTo24Hour(slotLabel) {
  const start = String(slotLabel).split(' - ')[0]?.trim();
  if (!start) return null;
  const temp = new Date(`2000-01-01 ${start}`);
  if (Number.isNaN(temp.getTime())) return null;
  return `${String(temp.getHours()).padStart(2, '0')}:${String(temp.getMinutes()).padStart(2, '0')}`;
}

async function refreshAllTabData({ resetPages = false } = {}) {
  if (resetPages) {
    resetAllPages();
  }

  resetTabLoadedFlags();
  await loadDashboardStats();

  const active = pmState.activeTab || 'all';
  if (active === 'all') {
    await loadPatientsPage(pmState.all.page);
    tabDataLoaded.all = true;
  } else {
    await TAB_LOADERS[active](pmState[active].page);
    tabDataLoaded[active] = true;
  }
}

function pmGoToTabPage(tabKey, page) {
  const safePage = Math.max(1, Number(page || 1));
  const loader = TAB_LOADERS[tabKey];
  if (!loader) {
    return;
  }
  loader(safePage).catch((error) => notify('Load Failed', error.message, 'error'));
}

window.pmFetch = pmFetch;
window.pmNotify = notify;
window.pmRenderOptions = renderOptions;
window.pmConvertSlotTo24Hour = convertSlotTo24Hour;
window.pmRefreshPatientDashboard = () => refreshAllTabData({ resetPages: true });
window.pmGoToTabPage = pmGoToTabPage;
window.pmIssueNextToken = issueNextTokenForBooking;
window.pmCancelFutureBooking = cancelFutureBookingAppointment;
window.pmForceIssueToken = issueNextTokenForBooking;
window.pmMarkOpdAbsent = pmMarkOpdAbsent;
window.pmMarkOpdPresent = pmMarkOpdPresent;
window.switchPtTab = switchPtTab;
window.searchPatients = searchPatients;
window.viewPatient360 = viewPatient360;
window.openPatientDetails = openPatientDetails;

function openPatientDetails(patientId, mrn) {
  if (!PM_ROUTES.patientDetails) {
    notify('Config Missing', 'Patient details route is not configured.', 'error');
    return;
  }

  const url = new URL(PM_ROUTES.patientDetails, window.location.origin);
  if (Number(patientId) > 0) {
    url.searchParams.set('id', String(patientId));
  } else if (mrn) {
    url.searchParams.set('mrn', mrn);
  }

  window.location.href = url.toString();
}

async function viewPatient360(mrn) {
  try {
    const data = await pmFetch(`${PM_ROUTES.patient360}?mrn=${encodeURIComponent(mrn)}`);
    const patient = data.patient || {};
    const today = new Date();
    const todayOnly = parseLocalDateOnly(today);
    const isIpd = Boolean(data.active_ipd);
    const opdVisits = Array.isArray(data.opd_visits) ? data.opd_visits : [];
    const latestOpd = opdVisits.length ? opdVisits[0] : null;
    const activeIpd = data.active_ipd || null;
    const latestVisitType = latestOpd
      ? (latestOpd.casualty === 'Yes' ? 'Emergency' : (latestOpd.visit_type || 'OPD'))
      : '';
    const ipdSourceVisitType = activeIpd?.admission_source === 'emergency' ? 'Emergency' : '';

    const visitBadgeClassMap = {
      OPD: 'badge-blue',
      IPD: 'badge-teal',
      Emergency: 'badge-red',
      Daycare: 'badge-purple',
    };
    const visitType = String(patient.visit_type || latestVisitType || ipdSourceVisitType || (isIpd ? 'IPD' : 'OPD') || '-').trim();
    const statusBadgeClassMap = {
      waiting: 'badge-blue',
      in_room: 'badge-orange',
      completed: 'badge-green',
      admitted: 'badge-teal',
      critical: 'badge-red',
      discharged: 'badge-gray',
      registered: 'badge-gray',
      active: 'badge-green',
    };
    const currentStatusRaw = String(
      latestOpd?.status
      || (isIpd ? 'admitted' : '')
      || patient.status
      || 'active'
    ).trim();
    const currentStatusKey = currentStatusRaw.toLowerCase();
    const statusText = ucwords(currentStatusRaw);

    const visitBadgeClass = visitBadgeClassMap[visitType] || 'badge-gray';
    const statusBadgeClass = statusBadgeClassMap[currentStatusKey] || 'badge-gray';

    const admissionDate = activeIpd?.admission_date ? new Date(activeIpd.admission_date) : null;
    const ipdDays = admissionDate ? Math.max(1, Math.ceil((today.getTime() - admissionDate.getTime()) / (1000 * 60 * 60 * 24))) : 0;

    const billingSummary = data.billing_summary || {};
    const totalBilled = Number(billingSummary.total_billed || 0);
    const paidAmount = Number(billingSummary.total_paid || 0);
    const dueAmount = Number(billingSummary.total_due || 0);
    const discountAmount = Number(billingSummary.total_discount || 0);
    const advanceBalance = Number(billingSummary.advance_balance || 0);

    const formatInr = (value) => `Rs ${Number(value || 0).toLocaleString('en-IN')}`;

    const registeredDate = patient.created_at ? new Date(patient.created_at) : null;
    const registeredLabel = registeredDate
      ? `${registeredDate.toLocaleDateString('en-IN')} ${registeredDate.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' })}`
      : '-';

    const deptDisplay = latestOpd?.dept_name || patient.department_name || '-';

    const summaryBlock = isIpd ? `
      <div style="background:var(--teal-light);border-radius:10px;padding:12px">
        <div class="fs-20">🛏️</div>
        <div class="fw-700 fs-12">${escapeHtml(activeIpd?.admission_no || '-')}</div>
        <div class="fs-11 text-muted">Active IPD Admission</div>
      </div>
      <div style="background:var(--surface-2);border:1px solid var(--border-light);border-radius:10px;padding:12px;margin-top:10px">
        <div class="fw-700 fs-13 mb-8">IPD Bed Details</div>
        <div class="fs-12"><b>Bed:</b> ${escapeHtml(activeIpd?.bed_no || '-')}</div>
        <div class="fs-12"><b>Ward / Type:</b> ${escapeHtml(activeIpd?.ward_name || '-')}</div>
        <div class="fs-12"><b>Admission Date:</b> ${admissionDate ? admissionDate.toLocaleDateString('en-IN') : '-'}</div>
        <div class="fs-12"><b>Length of Stay:</b> ${ipdDays} day(s)</div>
      </div>` : `
      <div style="background:var(--success-light);border-radius:10px;padding:12px">
        <div class="fs-20">📋</div>
        <div class="fw-700 fs-12">${escapeHtml(latestOpd?.case_no || '-')}</div>
        <div class="fs-11 text-muted">Last OPD Visit</div>
      </div>
      <div style="background:var(--surface-2);border:1px solid var(--border-light);border-radius:10px;padding:12px;margin-top:10px">
        <div class="fw-700 fs-13 mb-8">Last OPD Details</div>
        <div class="fs-12"><b>Department:</b> ${escapeHtml(latestOpd?.dept_name || '-')}</div>
        <div class="fs-12"><b>Doctor:</b> ${escapeHtml(latestOpd?.doctor_name || '-')}</div>
        <div class="fs-12"><b>Slot:</b> ${escapeHtml(latestOpd?.slot || '-')}</div>
        <div class="fs-12"><b>Chief Complaint:</b> ${escapeHtml(latestOpd?.symptoms_description || '-')}</div>
      </div>`;

    const timelineRows = [];

    if (isIpd && activeIpd) {
      const ipdDate = parseLocalDateOnly(activeIpd.admission_date);
      timelineRows.push(`
        <div class="tl-item" style="border-left:3px solid var(--teal);padding-left:12px">
          <div class="tl-dot" style="background:var(--teal)"></div>
          <div class="tl-time">${ipdDate ? ipdDate.toLocaleDateString('en-IN') : '-'} | IPD</div>
          <div class="tl-content">Admitted with admission no <b>${escapeHtml(activeIpd.admission_no || '-')}</b></div>
          <div class="fs-11 text-muted" style="margin-top:4px">Bed ${escapeHtml(activeIpd.bed_no || '-')} | ${escapeHtml(activeIpd.ward_name || '-')}</div>
        </div>`);
    }

    opdVisits.forEach((visit) => {
      const apptDate = parseLocalDateOnly(visit.appointment_date);
      const isFuture = apptDate && apptDate.getTime() > todayOnly.getTime();
      const dateStr = apptDate ? apptDate.toLocaleDateString('en-IN') : '-';
      const slotStr = visit.slot ? `${visit.slot}` : '-';
      const rowVisitType = visit.casualty === 'Yes' ? 'Emergency' : (visit.visit_type || 'OPD');
      timelineRows.push(`
      <div class="tl-item" style="border-left:3px solid ${isFuture ? 'var(--success)' : 'var(--border-light)'};padding-left:12px">
        <div class="tl-dot" style="background:${isFuture ? 'var(--success)' : 'var(--primary)'}"></div>
        <div class="tl-time">${dateStr} | ${escapeHtml(rowVisitType)} ${isFuture ? 'upcoming' : 'done'}</div>
        <div class="tl-content">${escapeHtml(visit.case_no || '-')} | ${escapeHtml(visit.dept_name || '-')} | ${escapeHtml(visit.doctor_name || '-')}</div>
        <div class="fs-12 mt-4">Chief Complaint: <b>${escapeHtml(visit.symptoms_description || '-')}</b></div>
        <div class="fs-11 text-muted" style="margin-top:4px">⏱️ Slot: <b>${escapeHtml(slotStr)}</b></div>
      </div>`);
    });

    const timelineHtml = timelineRows.join('') || '<div class="fs-12 text-muted">No visit timeline found.</div>';

    document.getElementById('p360Content').innerHTML = `
      <div class="patient-chip mb-16">
        <div class="patient-chip-avatar" style="background:linear-gradient(135deg,#1565c0,#42a5f5)">${escapeHtml((patient.name || 'P').charAt(0))}</div>
        <div class="patient-chip-info">
          <div class="patient-chip-name">${escapeHtml(patient.name || '-')}</div>
          <div class="patient-chip-meta">${escapeHtml(patient.mrn || patient.patient_id || '-')} | ${escapeHtml(patient.age_years || '-')} Yrs / ${escapeHtml(patient.gender || '-')} | Blood Group: ${escapeHtml(patient.blood_group || '-')} | ${escapeHtml(patient.phone || '-')}</div>
        </div>
        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
          <span class="badge ${visitBadgeClass}">${escapeHtml(visitType)}</span>
          <span class="badge ${statusBadgeClass}">${escapeHtml(statusText)}</span>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px">
        <div style="background:var(--primary-light);border-radius:10px;padding:12px;text-align:center"><div class="fs-20">🩺</div><div class="fw-700 fs-12">${escapeHtml(deptDisplay)}</div><div class="fs-11 text-muted">Department</div></div>
        <div style="background:var(--success-light);border-radius:10px;padding:12px;text-align:center"><div class="fs-20">📋</div><div class="fw-700 fs-12">${escapeHtml(visitType || '-')}</div><div class="fs-11 text-muted">Visit Type</div></div>
        <div style="background:var(--warning-light);border-radius:10px;padding:12px;text-align:center"><div class="fs-20">📅</div><div class="fw-700 fs-12">${escapeHtml(registeredLabel)}</div><div class="fs-11 text-muted">Registered</div></div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="card">
          <div class="card-header"><div class="card-title">🧪 Recent Lab Results</div></div>
          <div class="card-body">
            <div class="fs-12 text-muted">CBC: WBC 11.2 ↑ | HB 10.8 ↓ | Platelets 1.8L</div>
            <div class="fs-12 text-muted mt-4">LFT: Bilirubin 1.2 | ALT 42 | AST 38</div>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title">💊 Current Medications</div></div>
          <div class="card-body">
            <div class="fs-12 text-muted">Tab. Metformin 500mg BD | Tab. Amlodipine 5mg OD | Inj. Ceftriaxone 1g IV BD</div>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title">📋 Visit Summary Timeline</div></div>
          <div class="card-body"><div class="timeline" style="padding-left:24px">${timelineHtml}</div></div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title">💳 Billing Summary</div></div>
          <div class="card-body">
            <div class="fs-12">Total Billed: <b>${formatInr(totalBilled)}</b></div>
            <div class="fs-12">Paid: <b class="text-success">${formatInr(paidAmount)}</b></div>
            <div class="fs-12">Due: <b class="text-danger">${formatInr(dueAmount)}</b></div>
            <div class="fs-12">Discount: <b>${formatInr(discountAmount)}</b></div>
            <div class="fs-12">Advance Balance: <b>${formatInr(advanceBalance)}</b></div>
            <div style="margin-top:10px">${summaryBlock}</div>
          </div>
        </div>
      </div>`;

    const actionRow = document.createElement('div');
    actionRow.className = 'd-flex gap-8 mt-16';
    actionRow.innerHTML = `
      <button class="btn btn-primary btn-sm" type="button" id="p360BtnBill">💳 View Bill</button>
      <button class="btn btn-secondary btn-sm" type="button" id="p360BtnLab">🧪 Lab Reports</button>
      <button class="btn btn-secondary btn-sm" type="button" id="p360BtnCert">📜 Certificates</button>
      <button class="btn btn-warning btn-sm" type="button" id="p360BtnDischarge">🏠 Discharge</button>`;
    document.getElementById('p360Content').appendChild(actionRow);

    document.getElementById('p360BtnBill')?.addEventListener('click', () => {
      window.location.href = 'billing.html';
    });
    document.getElementById('p360BtnLab')?.addEventListener('click', () => {
      window.location.href = 'lab.html';
    });
    document.getElementById('p360BtnCert')?.addEventListener('click', () => {
      window.location.href = 'certificate.html';
    });
    document.getElementById('p360BtnDischarge')?.addEventListener('click', () => {
      if (typeof window.dischargeWorkflow === 'function') {
        window.dischargeWorkflow(patient.mrn || mrn);
      } else {
        notify('Discharge', 'Discharge workflow is not configured yet.', 'info');
      }
    });

    openModal('patient360Modal');
  } catch (error) {
    notify('Patient View Failed', error.message, 'error');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  initPatientManagementDynamic().catch((error) => notify('Load Failed', error.message, 'error'));
});
