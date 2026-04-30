/**
 * ParaCare+ HMIS — Shared Navigation & Utilities v2.0
 * Government of Uttarakhand | Department of Health & Family Welfare
 */

'use strict';

/* ═══════════════════════════════════════════════════════════
   NAVIGATION CONFIGURATION — role-based menus
   ═══════════════════════════════════════════════════════════ */
const NAV_CONFIG = {

  admin: [
    { section: 'Dashboard' },
    { href: 'admin.html',      icon: '🏠', label: 'Executive Dashboard',   badge: 'LIVE', badgeType: 'live' },
    { href: 'admin.html',      icon: '📊', label: 'State KPI Centre',       badge: '' },

    { section: 'Clinical Modules' },
    { href: 'doctor.html',     icon: '🩺', label: 'OPD Management',         badge: '' },
    { href: 'doctor.html',     icon: '🛏️', label: 'IPD / ADT',              badge: '' },
    { href: 'patient.html',    icon: '👤', label: 'Patient 360 / EMR',      badge: '' },
    { href: 'lab.html',        icon: '🧪', label: 'Lab / Pathology',        badge: 'New', badgeType: 'new' },
    { href: 'radiology.html',  icon: '🩻', label: 'Radiology / RIS',        badge: 'New', badgeType: 'new' },
    { href: 'bloodbank.html',  icon: '🩸', label: 'Blood Bank',             badge: 'New', badgeType: 'new' },

    { section: 'Operations' },
    { href: 'pharmacy.html',   icon: '💊', label: 'Pharmacy',               badge: '' },
    { href: 'billing.html',    icon: '💳', label: 'Billing & Payments',     badge: '' },
    { href: 'nurse.html',      icon: '👩‍⚕️', label: 'Nursing / Wards',      badge: '' },
    { href: 'ambulance.html',  icon: '🚑', label: 'Ambulance',              badge: 'New', badgeType: 'new' },
    { href: 'inventory.html',  icon: '📦', label: 'Inventory / Store',      badge: 'New', badgeType: 'new' },
    { href: 'hr.html',         icon: '👨‍💼', label: 'Human Resources',       badge: 'New', badgeType: 'new' },

    { section: 'Administration' },
    { href: 'admin.html',      icon: '🏥', label: 'Institution Registry',   badge: '' },
    { href: 'admin.html',      icon: '🔐', label: 'Role & Access (RBAC)',   badge: '' },
    { href: 'admin.html',      icon: '⚙️', label: 'Master Setup',           badge: '' },
    { href: 'admin.html',      icon: '🔔', label: 'Alerts & Notifications', badge: '3', badgeType: 'count' },
  ],

  doctor: [
    { section: 'My Workspace' },
    { href: 'doctor.html',    icon: '🏠', label: 'My Dashboard',            badge: 'LIVE', badgeType: 'live' },
    { href: 'patient.html',   icon: '👤', label: 'Patient 360 / EMR',       badge: '' },

    { section: 'Clinical' },
    { href: 'doctor.html',    icon: '📋', label: 'OPD Queue',               badge: '14', badgeType: 'count' },
    { href: 'doctor.html',    icon: '🛏️', label: 'My IPD Patients',         badge: '6',  badgeType: 'count' },
    { href: 'lab.html',       icon: '🧪', label: 'Lab Orders / Results',    badge: '' },
    { href: 'radiology.html', icon: '🩻', label: 'Radiology Orders',        badge: '' },
    { href: 'pharmacy.html',  icon: '💊', label: 'e-Prescriptions',         badge: '' },

    { section: 'Tools' },
    { href: '#',              icon: '📝', label: 'Clinical Templates',      badge: '' },
    { href: '#',              icon: '📅', label: 'My Schedule',             badge: '' },
    { href: 'billing.html',   icon: '💳', label: 'Patient Bills',           badge: '' },
    { href: 'bloodbank.html', icon: '🩸', label: 'Blood Requests',          badge: '' },
  ],

  nurse: [
    { section: 'Ward Station' },
    { href: 'nurse.html',     icon: '🏠', label: 'Ward Dashboard',          badge: 'LIVE', badgeType: 'live' },
    { href: 'patient.html',   icon: '👤', label: 'Patient Records',         badge: '' },

    { section: 'Clinical Tasks' },
    { href: 'nurse.html',     icon: '✅', label: 'Task Checklist',          badge: '8', badgeType: 'count' },
    { href: 'nurse.html',     icon: '❤️', label: 'Record Vitals',           badge: '' },
    { href: 'nurse.html',     icon: '🛏️', label: 'Bed Map',                 badge: '' },
    { href: 'pharmacy.html',  icon: '💊', label: 'Medication Admin (MAR)',  badge: '' },

    { section: 'Support' },
    { href: 'lab.html',       icon: '🧪', label: 'Lab Samples',             badge: '' },
    { href: 'doctor.html',    icon: '🩺', label: 'Doctor Consult Request',  badge: '' },
    { href: 'billing.html',   icon: '💳', label: 'Billing Assist',          badge: '' },
  ],

  billing: [
    { section: 'Billing' },
    { href: 'billing.html',   icon: '🏠', label: 'Billing Dashboard',       badge: 'LIVE', badgeType: 'live' },
    { href: 'patient.html',   icon: '👤', label: 'Patient Lookup',          badge: '' },

    { section: 'Operations' },
    { href: 'billing.html',   icon: '🧾', label: 'Create Bill',             badge: '' },
    { href: 'billing.html',   icon: '💳', label: 'Payments',                badge: '' },
    { href: 'billing.html',   icon: '📋', label: 'IPD Estimates',           badge: '' },
    { href: 'billing.html',   icon: '📑', label: 'Insurance / TPA',         badge: '' },

    { section: 'Reports' },
    { href: 'billing.html',   icon: '📊', label: 'Revenue Reports',         badge: '' },
    { href: 'billing.html',   icon: '📉', label: 'Dues & Refunds',          badge: '' },
  ],

  pharmacy: [
    { section: 'Pharmacy' },
    { href: 'pharmacy.html',  icon: '🏠', label: 'Pharmacy Dashboard',      badge: 'LIVE', badgeType: 'live' },

    { section: 'Dispensing' },
    { href: 'pharmacy.html',  icon: '💊', label: 'Dispense Queue',          badge: '12', badgeType: 'count' },
    { href: 'pharmacy.html',  icon: '🚨', label: 'STAT / Urgent Orders',    badge: '3',  badgeType: 'count' },
    { href: 'pharmacy.html',  icon: '📋', label: 'Prescription Validation', badge: '' },

    { section: 'Stock' },
    { href: 'pharmacy.html',  icon: '📦', label: 'Drug Inventory',          badge: '' },
    { href: 'pharmacy.html',  icon: '⚠️', label: 'Expiry Alerts',           badge: '7', badgeType: 'warn' },
    { href: 'inventory.html', icon: '🏪', label: 'Central Store',           badge: '' },
  ],

  lab: [
    { section: 'Laboratory' },
    { href: 'lab.html',       icon: '🏠', label: 'Lab Dashboard',           badge: 'LIVE', badgeType: 'live' },

    { section: 'Operations' },
    { href: 'lab.html',       icon: '🧪', label: 'Sample Queue',            badge: '18', badgeType: 'count' },
    { href: 'lab.html',       icon: '🔬', label: 'Test Processing',         badge: '' },
    { href: 'lab.html',       icon: '🚨', label: 'Critical Values',         badge: '2',  badgeType: 'count' },
    { href: 'lab.html',       icon: '📄', label: 'Report Dispatch',         badge: '' },

    { section: 'Management' },
    { href: 'lab.html',       icon: '📊', label: 'TAT Analytics',           badge: '' },
    { href: 'lab.html',       icon: '⚙️', label: 'Analyzer Config',         badge: '' },
  ],

  radiology: [
    { section: 'Radiology' },
    { href: 'radiology.html', icon: '🏠', label: 'RIS Dashboard',           badge: 'LIVE', badgeType: 'live' },

    { section: 'Worklist' },
    { href: 'radiology.html', icon: '🩻', label: 'Imaging Worklist',        badge: '11', badgeType: 'count' },
    { href: 'radiology.html', icon: '🔬', label: 'Modality Status',         badge: '' },
    { href: 'radiology.html', icon: '📄', label: 'Report Sign-off',         badge: '5',  badgeType: 'count' },

    { section: 'AI Tools' },
    { href: 'radiology.html', icon: '🤖', label: 'AI Findings',             badge: 'Beta', badgeType: 'new' },
    { href: 'radiology.html', icon: '📊', label: 'Turnaround Time',         badge: '' },
  ],

  bloodbank: [
    { section: 'Blood Bank' },
    { href: 'bloodbank.html', icon: '🏠', label: 'Blood Bank Dashboard',    badge: 'LIVE', badgeType: 'live' },

    { section: 'Operations' },
    { href: 'bloodbank.html', icon: '🩸', label: 'Inventory',               badge: '' },
    { href: 'bloodbank.html', icon: '💉', label: 'Issue Blood',             badge: '' },
    { href: 'bloodbank.html', icon: '👥', label: 'Donor Registry',          badge: '' },
    { href: 'bloodbank.html', icon: '🔄', label: 'Cross-Match',             badge: '' },
    { href: 'bloodbank.html', icon: '🚨', label: 'Critical Stock Alerts',   badge: '3', badgeType: 'count' },
  ],

  ambulance: [
    { section: 'Ambulance' },
    { href: 'ambulance.html', icon: '🏠', label: 'Dispatch Centre',         badge: 'LIVE', badgeType: 'live' },

    { section: 'Operations' },
    { href: 'ambulance.html', icon: '🚑', label: 'Active Dispatches',       badge: '4', badgeType: 'count' },
    { href: 'ambulance.html', icon: '🗺️', label: 'Fleet Tracking',          badge: '' },
    { href: 'ambulance.html', icon: '📋', label: 'Dispatch History',        badge: '' },
    { href: 'ambulance.html', icon: '⚙️', label: 'Fleet Management',        badge: '' },
    { href: 'ambulance.html', icon: '📊', label: 'Response Analytics',      badge: '' },
  ],

  inventory: [
    { section: 'Inventory' },
    { href: 'inventory.html', icon: '🏠', label: 'Store Dashboard',         badge: 'LIVE', badgeType: 'live' },

    { section: 'Stock' },
    { href: 'inventory.html', icon: '📦', label: 'Stock Register',          badge: '' },
    { href: 'inventory.html', icon: '⚠️', label: 'Low Stock Alerts',        badge: '12', badgeType: 'warn' },
    { href: 'inventory.html', icon: '📥', label: 'Goods Receipt (GRN)',     badge: '' },
    { href: 'inventory.html', icon: '📤', label: 'Issue to Departments',    badge: '' },
    { href: 'inventory.html', icon: '📋', label: 'Purchase Orders',         badge: '' },
    { href: 'inventory.html', icon: '🔧', label: 'Equipment Register',      badge: '' },
  ],

  hr: [
    { section: 'Human Resources' },
    { href: 'hr.html',        icon: '🏠', label: 'HR Dashboard',            badge: 'LIVE', badgeType: 'live' },

    { section: 'Staff' },
    { href: 'hr.html',        icon: '👨‍💼', label: 'Staff Directory',        badge: '' },
    { href: 'hr.html',        icon: '📅', label: 'Attendance',              badge: '' },
    { href: 'hr.html',        icon: '🗓️', label: 'Duty Roster',             badge: '' },
    { href: 'hr.html',        icon: '🏖️', label: 'Leave Management',        badge: '5', badgeType: 'count' },
    { href: 'hr.html',        icon: '💰', label: 'Payroll',                 badge: '' },
    { href: 'hr.html',        icon: '🎓', label: 'Training & CPD',          badge: '' },
  ],
};

/* ═══════════════════════════════════════════════════════════
   SHARED HEADER TEMPLATE
   ═══════════════════════════════════════════════════════════ */
function buildGovEmblem() {
  return `<svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="50" r="46" fill="#fff8ec" stroke="#003580" stroke-width="3"/>
    <circle cx="50" cy="50" r="34" fill="#e6f0fb" stroke="#003580" stroke-width="1.5"/>
    <path d="M50 16 L57 35 L78 35 L62 47 L68 66 L50 54 L32 66 L38 47 L22 35 L43 35 Z" fill="#003580" opacity="0.9"/>
    <circle cx="50" cy="50" r="9" fill="#e65100"/>
    <circle cx="50" cy="50" r="5" fill="#f9a825"/>
    <text x="50" y="89" text-anchor="middle" font-size="7.5" fill="#003580"
      font-family="'Noto Sans Devanagari',Arial" font-weight="700">उत्तराखण्ड</text>
  </svg>`;
}

function buildHeader(opts = {}) {
  const {
    title    = 'ParaCare+ HMIS',
    subtitle = '',
    role     = 'User',
    initials = 'U',
    facility = 'Directorate of Health, Uttarakhand',
    page     = '',
  } = opts;

  return `
  <header class="gov-header">
    <div class="gov-header-left">
      <div class="gov-emblem">${buildGovEmblem()}</div>
      <div class="gov-title-block">
        <div class="state-name">Government of Uttarakhand</div>
        <div class="dept-name">Dept. of Health &amp; Family Welfare</div>
        <div class="hi-text hindi">स्वास्थ्य एवं परिवार कल्याण विभाग</div>
      </div>
      <div class="gov-divider"></div>
      <div class="gov-title-block">
        <div class="state-name" style="font-size:10.5px;color:#6a8fa8;letter-spacing:.06em">NHM | ABDM INTEGRATED</div>
        <div class="dept-name" style="color:#94b8cc">ParaCare+ HMIS v2.4</div>
        <div class="hi-text">State Health Ecosystem</div>
      </div>
    </div>
    <div class="gov-header-center">
      <div class="sys-name">${title}</div>
      <div class="sys-sub" id="liveDateStr">${subtitle || page}</div>
    </div>
    <div class="gov-header-right">
      <div class="paracare-brand">
        <div class="paracare-logo-mark">+</div>
        <div class="paracare-brand-text">
          <div class="name">ParaCare+</div>
          <div class="tagline">AI-Enabled HMIS</div>
        </div>
      </div>
      <div class="header-icon-btn" title="Notifications" onclick="toggleNotifPanel()">🔔<span class="notif-dot"></span></div>
      <div class="header-icon-btn" title="Help">❓</div>
      <div class="flex items-center gap-2">
        <div class="header-avatar">${initials}</div>
        <div class="header-user-text">
          <div class="role-name">${role}</div>
          <div class="inst-name truncate" style="max-width:160px">${facility}</div>
        </div>
      </div>
      <button class="btn btn-ghost btn-sm" onclick="confirmLogout()" style="color:#ef5350;border-color:#ef5350;padding:5px 10px">⏻</button>
    </div>
  </header>`;
}

/* ═══════════════════════════════════════════════════════════
   SIDEBAR BUILDER
   ═══════════════════════════════════════════════════════════ */
function buildSidebar(role = 'admin', activePage = '') {
  const items = NAV_CONFIG[role] || NAV_CONFIG.admin;
  const currentFile = activePage || window.location.pathname.split('/').pop() || 'index.html';

  let html = '<nav class="gov-sidebar" id="govSidebar">';

  // Facility tag
  const facilityMap = {
    admin:    { name: 'State Admin — Directorate', type: 'All Facilities' },
    doctor:   { name: 'AIIMS Rishikesh Satellite', type: 'District Hospital' },
    nurse:    { name: 'Ward 4 — General Medicine',  type: 'Nursing Station' },
    billing:  { name: 'Registration & Billing',     type: 'Counter 3' },
    pharmacy: { name: 'Central Pharmacy',            type: 'Dispensing Unit' },
    lab:      { name: 'Central Laboratory',          type: 'Pathology & Biochem' },
    radiology:{ name: 'Radiology Dept.',             type: 'RIS / PACS Interface' },
    bloodbank:{ name: 'Blood Bank Unit',             type: 'Transfusion Services' },
    ambulance:{ name: 'Emergency Dispatch Centre',   type: '108 Ambulance Service' },
    inventory:{ name: 'Central Medical Store',       type: 'Inventory Management' },
    hr:       { name: 'HR & Administration',         type: 'Human Resources Dept.' },
  };
  const f = facilityMap[role] || facilityMap.admin;
  html += `<div class="sidebar-facility-tag">
    <div class="facility-name">🏥 ${f.name}</div>
    <div class="facility-type">${f.type}</div>
  </div>`;

  items.forEach(item => {
    if (item.section) {
      html += `<div class="nav-section-title">${item.section}</div>`;
      return;
    }
    const isActive = item.href && item.href !== '#' &&
      (item.href === currentFile ||
       (currentFile === '' && item.href === 'admin.html'));

    let badgeHtml = '';
    if (item.badge) {
      badgeHtml = `<span class="nav-badge ${item.badgeType || ''}">${item.badge}</span>`;
    }

    html += `<a href="${item.href || '#'}" class="nav-item ${isActive ? 'active' : ''}" title="${item.label}">
      <span class="nav-icon">${item.icon}</span>
      <span class="nav-label">${item.label}</span>
      ${badgeHtml}
    </a>`;
  });

  html += `<div class="sidebar-footer">
    <div class="ver-text">
      ParaCare+ HMIS v2.4.1<br>
      NHM | ABDM Integrated<br>
      © 2024 Govt. of Uttarakhand
    </div>
  </div>`;

  html += '</nav>';
  return html;
}

/* ═══════════════════════════════════════════════════════════
   INIT — called on DOMContentLoaded
   ═══════════════════════════════════════════════════════════ */
function initNav(opts = {}) {
  const {
    role     = 'admin',
    title    = 'ParaCare+ HMIS',
    subtitle = '',
    initials = 'SA',
    userRole = 'State Administrator',
    facility = 'Directorate of Health, Uttarakhand',
  } = opts;

  const shell = document.querySelector('.app-shell');
  if (!shell) return;

  // Inject header
  const headerPlaceholder = shell.querySelector('.gov-header');
  if (!headerPlaceholder) {
    shell.insertAdjacentHTML('afterbegin',
      buildHeader({ title, subtitle, role: userRole, initials, facility }));
  }

  // Inject sidebar after header
  const existingSidebar = shell.querySelector('.gov-sidebar');
  if (!existingSidebar) {
    const header = shell.querySelector('.gov-header');
    if (header) {
      header.insertAdjacentHTML('afterend', buildSidebar(role));
    }
  }

  // Live clock
  updateClock();
  setInterval(updateClock, 30000);
}

/* ═══════════════════════════════════════════════════════════
   CLOCK / DATE
   ═══════════════════════════════════════════════════════════ */
function updateClock() {
  const el = document.getElementById('liveDateStr');
  if (!el) return;
  const now = new Date();
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  const hh = String(now.getHours()).padStart(2,'0');
  const mm = String(now.getMinutes()).padStart(2,'0');
  const existing = el.dataset.prefix || '';
  el.textContent = `${existing ? existing + ' · ' : ''}${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} — ${hh}:${mm}`;
}

/* ═══════════════════════════════════════════════════════════
   MODALS
   ═══════════════════════════════════════════════════════════ */
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
}
// Close on overlay click
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('open');
    document.body.style.overflow = '';
  }
});
// Close on Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(m => {
      m.classList.remove('open');
      document.body.style.overflow = '';
    });
  }
});

/* ═══════════════════════════════════════════════════════════
   TABS
   ═══════════════════════════════════════════════════════════ */
function initTabs(barSelector = '.tab-bar', paneSelector = '.tab-pane') {
  document.querySelectorAll(barSelector).forEach(bar => {
    const btns  = bar.querySelectorAll('.tab-btn');
    const group = bar.dataset.group;
    btns.forEach(btn => {
      btn.addEventListener('click', () => {
        btns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const target = btn.dataset.tab;
        const scope = group
          ? document.querySelectorAll(`${paneSelector}[data-group="${group}"]`)
          : document.querySelectorAll(paneSelector);
        scope.forEach(p => p.classList.toggle('active', p.id === target));
      });
    });
  });
}

function switchTab(tabId, btnEl) {
  const pane = document.getElementById(tabId);
  if (!pane) return;
  const bar = btnEl.closest('.tab-bar, .pill-tabs');
  if (bar) {
    bar.querySelectorAll('.tab-btn, .pill-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');
  }
  const parentPane = pane.parentElement;
  parentPane.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  pane.classList.add('active');
}

/* ═══════════════════════════════════════════════════════════
   TOAST NOTIFICATIONS
   ═══════════════════════════════════════════════════════════ */
let _toastTimer;
function showToast(msg, type = 'success', duration = 3200) {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = `
      position:fixed; bottom:24px; right:24px; z-index:9999;
      display:flex; flex-direction:column; gap:8px; align-items:flex-end;
    `;
    document.body.appendChild(container);
  }

  const icons = { success:'✅', danger:'❌', warning:'⚠️', info:'ℹ️' };
  const colors = {
    success: '#2e7d32', danger: '#c62828',
    warning: '#e65100', info: '#1565c0'
  };

  const toast = document.createElement('div');
  toast.style.cssText = `
    background:#fff; border:1px solid #e0e0e0;
    border-left:4px solid ${colors[type]||colors.info};
    border-radius:10px; padding:12px 16px;
    display:flex; align-items:center; gap:10px;
    font-size:13px; color:#1a2a3a; font-family:'Inter',sans-serif;
    box-shadow:0 8px 28px rgba(0,0,0,0.15);
    max-width:340px;
    animation: toastIn .25s ease;
  `;
  toast.innerHTML = `<span style="font-size:16px">${icons[type]||'ℹ️'}</span><span>${msg}</span>`;

  if (!document.getElementById('toastStyle')) {
    const s = document.createElement('style');
    s.id = 'toastStyle';
    s.textContent = `@keyframes toastIn{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:none}}`;
    document.head.appendChild(s);
  }

  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(24px)';
    toast.style.transition = 'all .25s ease';
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

/* ═══════════════════════════════════════════════════════════
   LOGOUT CONFIRM
   ═══════════════════════════════════════════════════════════ */
function confirmLogout() {
  if (confirm('Log out of ParaCare+ HMIS?')) {
    window.location.href = 'index.html';
  }
}

function toggleNotifPanel() {
  showToast('No new critical notifications', 'info');
}

/* ═══════════════════════════════════════════════════════════
   CHART DEFAULTS
   ═══════════════════════════════════════════════════════════ */
if (typeof Chart !== 'undefined') {
  Chart.defaults.font.family = "'Inter', sans-serif";
  Chart.defaults.font.size   = 11;
  Chart.defaults.color       = '#5a7894';
  Chart.defaults.plugins.legend.position = 'bottom';
  Chart.defaults.plugins.legend.labels.padding = 16;
  Chart.defaults.plugins.legend.labels.usePointStyle = true;
  Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(10,22,40,0.92)';
  Chart.defaults.plugins.tooltip.titleColor = '#e8f2fb';
  Chart.defaults.plugins.tooltip.bodyColor  = '#a8c8e0';
  Chart.defaults.plugins.tooltip.borderColor = 'rgba(255,255,255,0.08)';
  Chart.defaults.plugins.tooltip.borderWidth = 1;
  Chart.defaults.plugins.tooltip.padding    = 10;
  Chart.defaults.plugins.tooltip.cornerRadius = 8;
  Chart.defaults.scale.grid.color = 'rgba(0,0,0,0.05)';
  Chart.defaults.scale.border.display = false;
}

/* ═══════════════════════════════════════════════════════════
   UTILITY HELPERS
   ═══════════════════════════════════════════════════════════ */
const Utils = {
  formatDate(d) {
    if (!d) return '—';
    const dt = new Date(d);
    return dt.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' });
  },
  formatTime(d) {
    if (!d) return '—';
    return new Date(d).toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
  },
  formatCurrency(n) {
    return '₹' + Number(n||0).toLocaleString('en-IN');
  },
  random(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; },
  randomChoice(arr) { return arr[Math.floor(Math.random() * arr.length)]; },
  padStart(n, len = 2) { return String(n).padStart(len, '0'); },
};

/* ═══════════════════════════════════════════════════════════
   DOM READY
   ═══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  initTabs();
  updateClock();
});
