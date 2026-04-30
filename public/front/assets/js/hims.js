/**
 * ParaCare+ HIMS — Enterprise Core JS v3.0
 * Government of Uttarakhand | Health & Family Welfare
 * Shared utilities, navigation, session, toast notifications
 */

'use strict';

/* ═══════════════════════════════════════════════════════════════
   NAVIGATION CONFIGURATION
   ═══════════════════════════════════════════════════════════════ */
const HIMS_NAV = {

  admin: [
    { section: '🏠 Dashboard' },
    { href: 'admin.html',      icon: '📊', label: 'Executive Dashboard',      badge: 'LIVE', btype: 'live' },
    { href: 'admin.html#kpi',  icon: '📈', label: 'State KPI Centre',         badge: '' },
    { href: 'admin.html#alerts', icon: '🔔', label: 'Alerts & Notifications', badge: '5', btype: 'warn' },

    { section: '🏥 Clinical' },
    { href: 'patient.html',    icon: '👤', label: 'Patient Registration',     badge: '' },
    { href: 'patient.html#opd', icon: '🩺', label: 'OPD Management',         badge: '32', btype: 'count' },
    { href: 'patient.html#ipd', icon: '🛏️', label: 'IPD / Bed Management',   badge: '18', btype: 'count' },
    { href: 'doctor.html',     icon: '👨‍⚕️', label: 'Doctor Workspace',       badge: '' },
    { href: 'nurse.html',      icon: '👩‍⚕️', label: 'Nursing Station',        badge: '' },
    { href: 'lab.html',        icon: '🧪', label: 'Laboratory / LIS',         badge: '12', btype: 'count' },
    { href: 'radiology.html',  icon: '🩻', label: 'Radiology / RIS',          badge: '8', btype: 'count' },
    { href: 'bloodbank.html',  icon: '🩸', label: 'Blood Bank',               badge: '⚠', btype: 'warn' },

    { section: '⚙️ Operations' },
    { href: 'pharmacy.html',   icon: '💊', label: 'Pharmacy',                 badge: '14', btype: 'count' },
    { href: 'billing.html',    icon: '💳', label: 'Billing & Finance',        badge: '' },
    { href: 'ambulance.html',  icon: '🚑', label: 'Ambulance Dispatch',       badge: 'LIVE', btype: 'live' },
    { href: 'inventory.html',  icon: '📦', label: 'Inventory & Store',        badge: '⚠', btype: 'warn' },

    { section: '👥 Administration' },
    { href: 'hr.html',         icon: '🧑‍💼', label: 'HR & Payroll',           badge: '' },
    { href: 'certificate.html',icon: '📜', label: 'Certificates & Docs',      badge: '' },
    { href: 'admin.html#rbac', icon: '🔐', label: 'RBAC & Access Control',   badge: '' },
    { href: 'admin.html#setup',icon: '⚙️', label: 'System Configuration',    badge: '' },
    { href: 'admin.html#audit',icon: '📋', label: 'Audit Trail',             badge: '' },
  ],

  doctor: [
    { section: '🏠 My Workspace' },
    { href: 'doctor.html',     icon: '🏠', label: 'My Dashboard',             badge: 'LIVE', btype: 'live' },
    { href: 'patient.html',    icon: '🔍', label: 'Patient Search / 360°',   badge: '' },

    { section: '🩺 Clinical Work' },
    { href: 'doctor.html#opd', icon: '📋', label: 'OPD Queue',                badge: '14', btype: 'count' },
    { href: 'doctor.html#ipd', icon: '🛏️', label: 'My IPD Patients',         badge: '6', btype: 'count' },
    { href: 'doctor.html#rx',  icon: '💊', label: 'e-Prescriptions',          badge: '' },
    { href: 'doctor.html#notes',icon: '📝', label: 'Clinical Notes / SOAP',   badge: '' },

    { section: '🔬 Orders' },
    { href: 'lab.html',        icon: '🧪', label: 'Lab Orders / Results',     badge: '3', btype: 'count' },
    { href: 'radiology.html',  icon: '🩻', label: 'Radiology Orders',         badge: '2', btype: 'count' },
    { href: 'bloodbank.html',  icon: '🩸', label: 'Blood Requests',           badge: '' },
    { href: 'billing.html',    icon: '💳', label: 'Patient Bills',            badge: '' },

    { section: '📅 Schedule' },
    { href: 'doctor.html#schedule', icon: '📅', label: 'My Schedule',        badge: '' },
    { href: 'doctor.html#templates', icon: '📄', label: 'Clinical Templates',badge: '' },
  ],

  nurse: [
    { section: '🏠 Ward Station' },
    { href: 'nurse.html',      icon: '🏠', label: 'Ward Dashboard',           badge: 'LIVE', btype: 'live' },
    { href: 'nurse.html#beds', icon: '🛏️', label: 'Bed Map & Status',        badge: '' },
    { href: 'patient.html',    icon: '👤', label: 'Patient Records',          badge: '' },

    { section: '✅ Clinical Tasks' },
    { href: 'nurse.html#tasks', icon: '✅', label: 'Task Checklist',          badge: '8', btype: 'warn' },
    { href: 'nurse.html#vitals', icon: '❤️', label: 'Record Vitals',          badge: '' },
    { href: 'nurse.html#mar',  icon: '💊', label: 'Medication Admin (MAR)',   badge: '6', btype: 'count' },
    { href: 'nurse.html#intake', icon: '💧', label: 'I/O Chart',              badge: '' },
    { href: 'nurse.html#wounds', icon: '🩹', label: 'Wound Care',             badge: '' },

    { section: '🤝 Support' },
    { href: 'lab.html',        icon: '🧪', label: 'Lab Samples',              badge: '' },
    { href: 'nurse.html#consult', icon: '🩺', label: 'Consult Requests',     badge: '2', btype: 'count' },
    { href: 'billing.html',    icon: '💳', label: 'Billing Assist',           badge: '' },
    { href: 'nurse.html#shift', icon: '🔄', label: 'Shift Handover',          badge: '' },
  ],

  billing: [
    { section: '💳 Billing' },
    { href: 'billing.html',    icon: '🏠', label: 'Billing Dashboard',        badge: 'LIVE', btype: 'live' },
    { href: 'patient.html',    icon: '🔍', label: 'Patient Lookup',           badge: '' },
    { href: 'billing.html#opd', icon: '🩺', label: 'OPD Billing',            badge: '' },
    { href: 'billing.html#ipd', icon: '🛏️', label: 'IPD Billing',            badge: '' },
    { href: 'billing.html#insurance', icon: '🏛️', label: 'Insurance / AB-PMJAY', badge: '' },
    { href: 'billing.html#payments', icon: '💰', label: 'Payments & Receipts', badge: '' },
    { href: 'billing.html#reports', icon: '📊', label: 'Revenue Reports',     badge: '' },
    { href: 'billing.html#advance', icon: '📋', label: 'Advance Deposits',    badge: '' },
    { href: 'billing.html#refund', icon: '↩️', label: 'Refunds & Adjustments',badge: '' },
  ],

  pharmacy: [
    { section: '💊 Pharmacy' },
    { href: 'pharmacy.html',   icon: '🏠', label: 'Pharmacy Dashboard',       badge: 'LIVE', btype: 'live' },
    { href: 'pharmacy.html#queue', icon: '📋', label: 'Dispense Queue',       badge: '12', btype: 'count' },
    { href: 'pharmacy.html#stat', icon: '🚨', label: 'STAT Orders',           badge: '3', btype: 'warn' },
    { href: 'pharmacy.html#validate', icon: '✅', label: 'Rx Validation',     badge: '' },
    { href: 'pharmacy.html#returns', icon: '↩️', label: 'Drug Returns',       badge: '' },
    { href: 'pharmacy.html#inventory', icon: '📦', label: 'Drug Inventory',   badge: '' },
    { href: 'pharmacy.html#expiry', icon: '⚠️', label: 'Expiry Alerts',       badge: '7', btype: 'warn' },
    { href: 'pharmacy.html#grn', icon: '📥', label: 'GRN / Receive Stock',    badge: '' },
    { href: 'pharmacy.html#po', icon: '📤', label: 'Purchase Orders',         badge: '' },
    { href: 'pharmacy.html#supplier', icon: '🏭', label: 'Supplier Registry', badge: '' },
  ],

  lab: [
    { section: '🧪 Laboratory' },
    { href: 'lab.html',        icon: '🏠', label: 'LIS Dashboard',            badge: 'LIVE', btype: 'live' },
    { href: 'lab.html#samples', icon: '🧫', label: 'Sample Queue',            badge: '18', btype: 'count' },
    { href: 'lab.html#urgent', icon: '🚨', label: 'Urgent / STAT Tests',      badge: '4', btype: 'warn' },
    { href: 'lab.html#results', icon: '📊', label: 'Result Entry',            badge: '' },
    { href: 'lab.html#critical', icon: '⚠️', label: 'Critical Values',        badge: '2', btype: 'warn' },
    { href: 'lab.html#reports', icon: '📄', label: 'Report Dispatch',         badge: '' },
    { href: 'lab.html#tat', icon: '⏱️', label: 'TAT Analytics',               badge: '' },
    { href: 'lab.html#analyzers', icon: '🔬', label: 'Analyzer Config',       badge: '' },
  ],

  radiology: [
    { section: '🩻 Radiology' },
    { href: 'radiology.html',  icon: '🏠', label: 'RIS Dashboard',            badge: 'LIVE', btype: 'live' },
    { href: 'radiology.html#worklist', icon: '📋', label: 'Imaging Worklist', badge: '11', btype: 'count' },
    { href: 'radiology.html#modalities', icon: '🖥️', label: 'Modality Status', badge: '' },
    { href: 'radiology.html#reporting', icon: '📝', label: 'Report Editor',   badge: '' },
    { href: 'radiology.html#signoff', icon: '✅', label: 'Pending Sign-off',   badge: '5', btype: 'warn' },
    { href: 'radiology.html#ai', icon: '🤖', label: 'AI Findings (Beta)',      badge: 'AI', btype: 'new' },
    { href: 'radiology.html#tat', icon: '⏱️', label: 'TAT Reports',            badge: '' },
  ],

  bloodbank: [
    { section: '🩸 Blood Bank' },
    { href: 'bloodbank.html',  icon: '🏠', label: 'Blood Bank Dashboard',     badge: 'LIVE', btype: 'live' },
    { href: 'bloodbank.html#inventory', icon: '📦', label: 'Blood Inventory', badge: '' },
    { href: 'bloodbank.html#issue', icon: '💉', label: 'Issue Blood',         badge: '' },
    { href: 'bloodbank.html#crossmatch', icon: '🔬', label: 'Cross Match',    badge: '3', btype: 'count' },
    { href: 'bloodbank.html#donors', icon: '👥', label: 'Donor Registry',     badge: '' },
    { href: 'bloodbank.html#camp', icon: '🏕️', label: 'Donation Camps',       badge: '' },
    { href: 'bloodbank.html#critical', icon: '⚠️', label: 'Critical Stock',   badge: '3', btype: 'warn' },
  ],

  ambulance: [
    { section: '🚑 Ambulance' },
    { href: 'ambulance.html',  icon: '🏠', label: 'Dispatch Centre',          badge: 'LIVE', btype: 'live' },
    { href: 'ambulance.html#map', icon: '🗺️', label: 'Live Fleet Map',        badge: '' },
    { href: 'ambulance.html#dispatch', icon: '📡', label: 'Active Dispatches', badge: '4', btype: 'count' },
    { href: 'ambulance.html#history', icon: '📋', label: 'Dispatch History',  badge: '' },
    { href: 'ambulance.html#fleet', icon: '🚑', label: 'Fleet Management',    badge: '' },
    { href: 'ambulance.html#analytics', icon: '📊', label: 'Response Analytics', badge: '' },
  ],

  hr: [
    { section: '👥 Human Resources' },
    { href: 'hr.html',         icon: '🏠', label: 'HR Dashboard',             badge: 'LIVE', btype: 'live' },
    { href: 'hr.html#staff',   icon: '👤', label: 'Staff Directory',          badge: '' },
    { href: 'hr.html#attendance', icon: '✅', label: 'Attendance',            badge: '' },
    { href: 'hr.html#roster',  icon: '📅', label: 'Duty Roster',              badge: '' },
    { href: 'hr.html#leave',   icon: '🏖️', label: 'Leave Management',         badge: '5', btype: 'warn' },
    { href: 'hr.html#payroll', icon: '💰', label: 'Payroll',                  badge: '' },
    { href: 'hr.html#training',icon: '📚', label: 'Training & CPD',           badge: '' },
    { href: 'hr.html#recruitment', icon: '📝', label: 'Recruitment',          badge: '' },
  ],

  inventory: [
    { section: '📦 Inventory' },
    { href: 'inventory.html',  icon: '🏠', label: 'Store Dashboard',          badge: 'LIVE', btype: 'live' },
    { href: 'inventory.html#stock', icon: '📊', label: 'Stock Register',      badge: '' },
    { href: 'inventory.html#lowstock', icon: '⚠️', label: 'Low-Stock Alerts', badge: '12', btype: 'warn' },
    { href: 'inventory.html#grn', icon: '📥', label: 'GRN / Receive Goods',   badge: '' },
    { href: 'inventory.html#issue', icon: '📤', label: 'Department Issues',   badge: '' },
    { href: 'inventory.html#po', icon: '📋', label: 'Purchase Orders',        badge: '' },
    { href: 'inventory.html#assets', icon: '🏷️', label: 'Asset Register',     badge: '' },
    { href: 'inventory.html#amc', icon: '🔧', label: 'AMC / Maintenance',     badge: '' },
  ],

  certificate: [
    { section: '📜 Documents' },
    { href: 'certificate.html',icon: '🏠', label: 'Certificate Centre',       badge: '' },
    { href: 'certificate.html#birth', icon: '👶', label: 'Birth Certificates', badge: '' },
    { href: 'certificate.html#death', icon: '⚫', label: 'Death Certificates', badge: '' },
    { href: 'certificate.html#discharge', icon: '🏥', label: 'Discharge Summary', badge: '' },
    { href: 'certificate.html#fitness', icon: '💪', label: 'Fitness Certificates', badge: '' },
    { href: 'certificate.html#mlc', icon: '⚖️', label: 'MLC / Legal Docs',    badge: '' },
    { href: 'certificate.html#disability', icon: '♿', label: 'Disability Certs', badge: '' },
    { href: 'certificate.html#vaccination', icon: '💉', label: 'Vaccination Certs', badge: '' },
    { href: 'certificate.html#sickleave', icon: '🤒', label: 'Sick Leave Certs', badge: '' },
  ],

};

/* ═══════════════════════════════════════════════════════════════
   SESSION / USER CONTEXT
   ═══════════════════════════════════════════════════════════════ */
const HIMS_SESSION = {
  currentUser: {
    id: 'U-001',
    name: 'Dr. Ananya Sharma',
    role: 'admin',
    roleLabel: 'System Administrator',
    avatar: 'AS',
    facility: 'District Hospital Dehradun',
    facilityCode: 'DHD-01',
    loginTime: new Date().toISOString(),
    shift: 'Morning (08:00–14:00)',
  },

  getUser()  { return this.currentUser; },
  getRole()  { return this.currentUser.role; },
  isAdmin()  { return this.currentUser.role === 'admin'; },
  isDoctor() { return this.currentUser.role === 'doctor'; },
  isNurse()  { return this.currentUser.role === 'nurse'; },

  login(roleKey) {
    const roles = {
      admin:     { name:'Admin User',          roleLabel:'System Administrator',     avatar:'AU', role:'admin'     },
      doctor:    { name:'Dr. Rajesh Negi',      roleLabel:'Senior Physician (Gen.Med)',avatar:'RN', role:'doctor'    },
      nurse:     { name:'Nurse Priya Rawat',    roleLabel:'Head Nurse, Ward 3',       avatar:'PR', role:'nurse'     },
      billing:   { name:'Kavita Bisht',         roleLabel:'Billing Officer',          avatar:'KB', role:'billing'   },
      pharmacy:  { name:'Suresh Pharmacist',    roleLabel:'Chief Pharmacist',         avatar:'SP', role:'pharmacy'  },
      lab:       { name:'Dr. Pooja Lab',        roleLabel:'Lab Technician',           avatar:'PL', role:'lab'       },
      radiology: { name:'Dr. Amit Rad',         roleLabel:'Radiologist',              avatar:'AR', role:'radiology' },
    };
    Object.assign(this.currentUser, roles[roleKey] || roles.admin);
    localStorage.setItem('hims_role', roleKey);
    localStorage.setItem('hims_user', JSON.stringify(this.currentUser));
  },

  loadFromStorage() {
    const saved = localStorage.getItem('hims_user');
    if (saved) {
      try { Object.assign(this.currentUser, JSON.parse(saved)); } catch(e){}
    }
  }
};

/* ═══════════════════════════════════════════════════════════════
   SIDEBAR BUILDER
   ═══════════════════════════════════════════════════════════════ */
function buildSidebar(activeHref, role) {
  role = role || HIMS_SESSION.getRole() || 'admin';
  const navItems = HIMS_NAV[role] || HIMS_NAV.admin;
  const user     = HIMS_SESSION.getUser();
  const currentPage = activeHref || (window.location.pathname.split('/').pop() || 'index.html');

  let html = `
  <div class="hims-sidebar" id="himsSidebar">
    <div class="sidebar-brand">
      <div class="brand-logo">🏥</div>
      <div class="brand-text">
        <div class="brand-name">ParaCare+ HIMS</div>
        <div class="brand-sub">Govt. Uttarakhand · ${user.facilityCode}</div>
      </div>
    </div>
    <div class="sidebar-role-badge">
      <div class="role-avatar">${user.avatar}</div>
      <div class="role-info">
        <div class="role-name">${user.name}</div>
        <div class="role-type">${user.roleLabel}</div>
      </div>
      <div class="role-dot"></div>
    </div>
    <nav class="sidebar-nav">`;

  navItems.forEach(item => {
    if (item.section) {
      html += `<div class="nav-section-title">${item.section}</div>`;
      return;
    }
    const isActive = item.href && (item.href === currentPage || currentPage.startsWith(item.href.split('#')[0]));
    let badge = '';
    if (item.badge) {
      badge = `<span class="nav-badge ${item.btype || ''}">${item.badge}</span>`;
    }
    html += `
    <a href="${item.href||'#'}" class="nav-item${isActive?' active':''}">
      <span class="nav-icon">${item.icon}</span>
      <span class="nav-label">${item.label}</span>
      ${badge}
    </a>`;
  });

  html += `</nav>
    <div class="sidebar-footer">
      <div class="sidebar-footer-links">
        <a href="index.html" class="sf-btn">🚪 Logout</a>
        <a href="admin.html#help" class="sf-btn">❓ Help</a>
      </div>
    </div>
  </div>`;

  return html;
}

/* ═══════════════════════════════════════════════════════════════
   HEADER BUILDER
   ═══════════════════════════════════════════════════════════════ */
function buildHeader(moduleIcon, moduleName, subTitle, actions) {
  const user = HIMS_SESSION.getUser();
  const now  = new Date();
  const timeStr = now.toLocaleTimeString('en-IN', {hour:'2-digit', minute:'2-digit'});
  const dateStr = now.toLocaleDateString('en-IN', {day:'2-digit', month:'short', year:'numeric'});

  return `
  <header class="hims-header" id="himsHeader">
    <div class="header-breadcrumb">
      <div class="breadcrumb-module">
        <span class="mod-icon">${moduleIcon}</span>
        <span>${moduleName}</span>
      </div>
      ${subTitle ? `<span class="breadcrumb-sep">›</span><span class="breadcrumb-sub">${subTitle}</span>` : ''}
    </div>
    <div class="header-actions">
      <div class="header-search">
        <span class="search-icon">🔍</span>
        <input type="text" placeholder="Search patient, MRN, bill…" id="globalSearch" />
      </div>
      <div class="hdr-icon-btn" title="Notifications" onclick="toggleNotifPanel()">
        🔔<span class="notif-badge">5</span>
      </div>
      <div class="hdr-icon-btn" title="Quick Add" onclick="showQuickAdd()">➕</div>
      <div class="hdr-icon-btn" title="Settings" onclick="location.href='admin.html#settings'">⚙️</div>
      <div class="header-user" onclick="showUserMenu()">
        <div class="user-avatar">${user.avatar}</div>
        <div>
          <div class="user-name">${user.name.split(' ').slice(0,2).join(' ')}</div>
          <div class="user-role">${user.roleLabel.slice(0,22)}</div>
        </div>
        <span style="font-size:10px;color:var(--text-muted)">▾</span>
      </div>
    </div>
  </header>`;
}

/* ═══════════════════════════════════════════════════════════════
   GOV TOP BAR
   ═══════════════════════════════════════════════════════════════ */
function buildGovBar() {
  const now = new Date();
  const dateStr = now.toLocaleDateString('en-IN',{weekday:'long',day:'2-digit',month:'long',year:'numeric'});
  return `
  <div class="gov-topbar">
    <div class="govbar-left">
      <div class="govbar-seal">
        <svg viewBox="0 0 40 40" width="40" height="40">
          <circle cx="20" cy="20" r="18" fill="none" stroke="#c8a84b" stroke-width="1.5"/>
          <circle cx="20" cy="20" r="14" fill="#1a3a6b"/>
          <text x="20" y="24" font-size="13" fill="#c8a84b" text-anchor="middle" font-weight="700">अ</text>
        </svg>
      </div>
      <div class="govbar-text">
        <div class="gt1">Government of Uttarakhand — Health &amp; Family Welfare</div>
        <div class="gt2">District Hospital Dehradun | ParaCare+ HIMS v3.0</div>
        <div class="gt3">उत्तराखण्ड शासन · स्वास्थ्य एवं परिवार कल्याण विभाग</div>
      </div>
    </div>
    <div class="govbar-right">
      <div class="govbar-badge green">● System Online</div>
      <div class="govbar-badge blue">📅 ${dateStr}</div>
      <div class="govbar-badge saffron">🔒 ABDM Synced</div>
    </div>
  </div>`;
}

/* ═══════════════════════════════════════════════════════════════
   PAGE INITIALIZER
   ═══════════════════════════════════════════════════════════════ */
function initHIMSPage(config) {
  config = config || {};
  const role        = config.role || HIMS_SESSION.getRole();
  const moduleIcon  = config.icon || '🏥';
  const moduleName  = config.module || 'Dashboard';
  const subTitle    = config.subtitle || '';
  const currentHref = config.activeHref || (window.location.pathname.split('/').pop() || '');

  // Load user from storage
  HIMS_SESSION.loadFromStorage();

  // Build & inject sidebar
  const sidebarEl = document.getElementById('sidebar-mount');
  if (sidebarEl) sidebarEl.innerHTML = buildSidebar(currentHref, role);

  // Build & inject header
  const headerEl = document.getElementById('header-mount');
  if (headerEl) headerEl.innerHTML = buildHeader(moduleIcon, moduleName, subTitle);

  // Build & inject gov bar
  const govEl = document.getElementById('govbar-mount');
  if (govEl) govEl.innerHTML = buildGovBar();

  // Live clock
  startLiveClock();

  // Init toast container
  if (!document.getElementById('toastContainer')) {
    const tc = document.createElement('div');
    tc.id = 'toastContainer';
    tc.className = 'toast-container';
    document.body.appendChild(tc);
  }
}

/* ═══════════════════════════════════════════════════════════════
   LIVE CLOCK
   ═══════════════════════════════════════════════════════════════ */
function startLiveClock() {
  function tick() {
    const el = document.getElementById('liveClock');
    if (el) {
      const now = new Date();
      el.textContent = now.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
    }
  }
  tick();
  setInterval(tick, 1000);
}

/* ═══════════════════════════════════════════════════════════════
   TOAST NOTIFICATIONS
   ═══════════════════════════════════════════════════════════════ */
function showToast(title, desc, type, duration) {
  type     = type     || 'info';
  duration = duration || 4000;

  const icons = { success:'✅', error:'❌', warning:'⚠️', info:'ℹ️', critical:'🚨' };
  const icon  = icons[type] || icons.info;

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <span class="toast-icon">${icon}</span>
    <div class="toast-msg">
      <div class="toast-title">${title}</div>
      ${desc ? `<div class="toast-desc">${desc}</div>` : ''}
    </div>
    <button onclick="this.closest('.toast').remove()" style="background:none;border:none;color:inherit;opacity:.5;cursor:pointer;font-size:16px;padding:0 0 0 8px">✕</button>`;

  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity='0'; toast.style.transform='translateX(100%)'; toast.style.transition='.3s'; setTimeout(()=>toast.remove(),300); }, duration);
}

/* ═══════════════════════════════════════════════════════════════
   MODAL HELPERS
   ═══════════════════════════════════════════════════════════════ */
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('hidden'); document.body.style.overflow='hidden'; }
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('hidden'); document.body.style.overflow=''; }
}
function confirmDialog(msg, onConfirm) {
  if (confirm(msg)) onConfirm();
}

/* ═══════════════════════════════════════════════════════════════
   TABS
   ═══════════════════════════════════════════════════════════════ */
function initTabs(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const btns  = container.querySelectorAll('.tab-btn');
  const panes = container.querySelectorAll('.tab-pane');

  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      btns.forEach(b => b.classList.remove('active'));
      panes.forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      const target = btn.dataset.tab;
      const pane = container.querySelector(`#${target}`);
      if (pane) pane.classList.add('active');
    });
  });
  // Activate first tab
  if (btns[0]) btns[0].click();
}

/* ═══════════════════════════════════════════════════════════════
   DATA HELPERS
   ═══════════════════════════════════════════════════════════════ */
const HIMS_DATA = {
  // Patient number generator
  genMRN: () => 'MRN-' + String(Math.floor(Math.random()*90000)+10000),
  genOpd: () => 'OPD-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random()*9000)+1000),
  genIpd: () => 'IPD-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random()*9000)+1000),
  genBill:() => 'BILL-' + String(Math.floor(Math.random()*90000)+10000),
  genToken:(dept) => (dept||'OPD').toUpperCase().slice(0,3) + String(Math.floor(Math.random()*99)+1).padStart(3,'0'),
  genLab:  () => 'LAB-' + String(Math.floor(Math.random()*900000)+100000),
  genRad:  () => 'RAD-' + String(Math.floor(Math.random()*900000)+100000),

  // Blood groups
  bloodGroups: ['A+', 'A−', 'B+', 'B−', 'AB+', 'AB−', 'O+', 'O−'],

  // Department list
  departments: [
    'General Medicine','General Surgery','Orthopaedics','Gynaecology & Obstetrics',
    'Paediatrics','ENT','Ophthalmology','Dermatology','Psychiatry','Cardiology',
    'Neurology','Oncology','Urology','Nephrology','Emergency','ICU','NICU','PICU',
    'Dental','Pulmonology','Gastroenterology','Endocrinology','Rheumatology',
    'Haematology','Anaesthesia','Pathology','Radiology','Pharmacy','Blood Bank'
  ],

  // Ward list
  wards: [
    'Medical Ward A','Medical Ward B','Surgical Ward','Ortho Ward','Maternity Ward',
    'Paediatric Ward','Burns Ward','Psychiatric Ward','Isolation Ward','ICU','NICU',
    'HDU','Daycare','Emergency Ward'
  ],

  // Test categories
  labCategories: [
    'Haematology','Biochemistry','Microbiology','Serology/Immunology',
    'Histopathology','Cytology','Endocrinology','Toxicology','Blood Gas'
  ],

  // Common lab tests
  labTests: [
    {name:'Complete Blood Count (CBC)', cat:'Haematology', price:150, tat:'4h'},
    {name:'Blood Glucose (Fasting)', cat:'Biochemistry', price:60, tat:'2h'},
    {name:'Blood Glucose (Random)', cat:'Biochemistry', price:60, tat:'2h'},
    {name:'HbA1c', cat:'Biochemistry', price:350, tat:'6h'},
    {name:'Lipid Profile', cat:'Biochemistry', price:400, tat:'4h'},
    {name:'Liver Function Test (LFT)', cat:'Biochemistry', price:500, tat:'4h'},
    {name:'Kidney Function Test (KFT)', cat:'Biochemistry', price:500, tat:'4h'},
    {name:'Thyroid Profile (T3, T4, TSH)', cat:'Endocrinology', price:700, tat:'8h'},
    {name:'Urine Routine & Microscopy', cat:'Biochemistry', price:80, tat:'2h'},
    {name:'Blood Culture & Sensitivity', cat:'Microbiology', price:800, tat:'48h'},
    {name:'Urine Culture & Sensitivity', cat:'Microbiology', price:600, tat:'48h'},
    {name:'Widal Test', cat:'Serology/Immunology', price:120, tat:'4h'},
    {name:'HIV 1 & 2 Ab', cat:'Serology/Immunology', price:200, tat:'4h'},
    {name:'HBsAg', cat:'Serology/Immunology', price:150, tat:'4h'},
    {name:'Dengue NS1 / IgM / IgG', cat:'Serology/Immunology', price:500, tat:'6h'},
    {name:'Malaria Rapid Test', cat:'Serology/Immunology', price:100, tat:'1h'},
    {name:'PT/INR', cat:'Haematology', price:180, tat:'2h'},
    {name:'ESR', cat:'Haematology', price:50, tat:'2h'},
    {name:'CRP (Quantitative)', cat:'Biochemistry', price:350, tat:'4h'},
  ],

  // Radiology modalities
  radModalities: ['X-Ray','CT Scan','MRI','Ultrasound','Mammography','Fluoroscopy','PET-CT','Bone Scan'],

  // Insurance schemes
  insuranceSchemes: [
    'AB-PMJAY (Ayushman Bharat)','CGHS','ECHS','State Health Scheme',
    'ESI','RSBY','Private Insurance','Cash'
  ],

  // Sample patients for demo
  samplePatients: [
    {mrn:'MRN-10021',name:'Ramesh Kumar Singh',age:45,gender:'M',dob:'1979-03-15',phone:'9456781234',bg:'B+',visit:'OPD',dept:'General Medicine',status:'active'},
    {mrn:'MRN-10022',name:'Sunita Devi Rawat',age:32,gender:'F',dob:'1992-06-08',phone:'9876543210',bg:'O+',visit:'IPD',dept:'Gynaecology & Obstetrics',status:'admitted'},
    {mrn:'MRN-10023',name:'Ajay Bisht',age:58,gender:'M',dob:'1966-11-22',phone:'9567890123',bg:'A+',visit:'OPD',dept:'Cardiology',status:'active'},
    {mrn:'MRN-10024',name:'Priya Negi',age:24,gender:'F',dob:'2000-01-30',phone:'9341567890',bg:'AB−',visit:'OPD',dept:'Dermatology',status:'active'},
    {mrn:'MRN-10025',name:'Mohan Lal Gupta',age:70,gender:'M',dob:'1954-09-12',phone:'9012345678',bg:'B−',visit:'Emergency',dept:'Neurology',status:'critical'},
    {mrn:'MRN-10026',name:'Kavita Sharma',age:38,gender:'F',dob:'1986-07-04',phone:'9678901234',bg:'O−',visit:'IPD',dept:'Orthopaedics',status:'admitted'},
  ],
};

/* ═══════════════════════════════════════════════════════════════
   PRINT UTILITY
   ═══════════════════════════════════════════════════════════════ */
function printSection(sectionId, title) {
  const content = document.getElementById(sectionId);
  if (!content) return;
  const win = window.open('', '_blank', 'width=800,height=600');
  win.document.write(`
    <!DOCTYPE html><html><head>
    <title>${title || 'Print'}</title>
    <style>
      body{font-family:Arial,sans-serif;padding:20px;font-size:12px}
      table{width:100%;border-collapse:collapse}
      th,td{border:1px solid #ccc;padding:6px 10px;text-align:left}
      th{background:#f5f5f5;font-weight:bold}
      .no-print{display:none}
      @media print{.no-print{display:none}}
    </style></head>
    <body>${content.innerHTML}</body></html>`);
  win.document.close();
  win.focus();
  setTimeout(() => win.print(), 500);
}

/* ═══════════════════════════════════════════════════════════════
   QUICK-ADD PANEL (Global)
   ═══════════════════════════════════════════════════════════════ */
function showQuickAdd() {
  const items = [
    {icon:'👤',label:'New Patient',href:'patient.html#new'},
    {icon:'🩺',label:'OPD Token',href:'patient.html#opd'},
    {icon:'🛏️',label:'IPD Admission',href:'patient.html#ipd'},
    {icon:'💊',label:'Prescribe',href:'pharmacy.html#queue'},
    {icon:'🧪',label:'Lab Order',href:'lab.html#samples'},
    {icon:'🩻',label:'Radiology Order',href:'radiology.html#worklist'},
    {icon:'💳',label:'New Bill',href:'billing.html#opd'},
    {icon:'🩸',label:'Blood Request',href:'bloodbank.html#issue'},
    {icon:'🚑',label:'Dispatch Ambulance',href:'ambulance.html#dispatch'},
  ];
  let html = `<div class="quick-tiles" style="max-width:500px">`;
  items.forEach(i => {
    html += `<a href="${i.href}" class="quick-tile"><span class="qt-icon">${i.icon}</span><span class="qt-label">${i.label}</span></a>`;
  });
  html += `</div>`;
  showGenericModal('Quick Actions', html, 'modal-md');
}

function showGenericModal(title, bodyHtml, sizeClass) {
  const id = 'genericModal';
  let el = document.getElementById(id);
  if (!el) {
    el = document.createElement('div');
    el.id = id;
    el.className = 'modal-overlay hidden';
    el.innerHTML = `
      <div class="modal modal-md" id="genericModalInner">
        <div class="modal-header">
          <div class="modal-title" id="genericModalTitle"></div>
          <button class="modal-close" onclick="closeModal('genericModal')">✕</button>
        </div>
        <div class="modal-body" id="genericModalBody"></div>
      </div>`;
    document.body.appendChild(el);
    el.addEventListener('click', e => { if(e.target===el) closeModal(id); });
  }
  document.getElementById('genericModalTitle').innerHTML = title;
  document.getElementById('genericModalBody').innerHTML = bodyHtml;
  const inner = document.getElementById('genericModalInner');
  inner.className = `modal ${sizeClass||'modal-md'}`;
  openModal(id);
}

function toggleNotifPanel() {
  showToast('Notifications', '5 unread alerts — Pending lab results, low blood stock, leave requests.', 'info');
}
function showUserMenu() {
  showGenericModal('User Profile', `
    <div class="d-flex align-center gap-16 mb-16">
      <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#1565c0,#42a5f5);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:white">${HIMS_SESSION.getUser().avatar}</div>
      <div>
        <div class="fw-700 fs-16">${HIMS_SESSION.getUser().name}</div>
        <div class="text-muted fs-12">${HIMS_SESSION.getUser().roleLabel}</div>
        <div class="text-muted fs-12">🏥 ${HIMS_SESSION.getUser().facility}</div>
        <div class="text-muted fs-12">🕐 Shift: ${HIMS_SESSION.getUser().shift}</div>
      </div>
    </div>
    <div class="d-flex gap-8">
      <a href="index.html" class="btn btn-danger btn-sm">🚪 Logout</a>
      <button class="btn btn-secondary btn-sm" onclick="closeModal('genericModal')">✕ Close</button>
    </div>`, 'modal-sm');
}

/* ═══════════════════════════════════════════════════════════════
   FORMAT HELPERS
   ═══════════════════════════════════════════════════════════════ */
const FMT = {
  date(d)   { return d ? new Date(d).toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'}) : '—'; },
  time(d)   { return d ? new Date(d).toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'}) : '—'; },
  datetime(d) { return d ? `${FMT.date(d)}, ${FMT.time(d)}` : '—'; },
  currency(n) { return '₹' + Number(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); },
  age(dob)  {
    const diff = Date.now() - new Date(dob).getTime();
    const y = Math.floor(diff / (1000*60*60*24*365.25));
    return y + ' yrs';
  },
  phone(p)  { return p ? p.replace(/(\d{5})(\d{5})/,'$1 $2') : '—'; },
  capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; },
};

/* ═══════════════════════════════════════════════════════════════
   SEARCH HIGHLIGHTER
   ═══════════════════════════════════════════════════════════════ */
function filterTable(inputId, tableId) {
  const inp = document.getElementById(inputId);
  const tbl = document.getElementById(tableId);
  if (!inp || !tbl) return;
  inp.addEventListener('input', () => {
    const q = inp.value.trim().toLowerCase();
    const rows = tbl.querySelectorAll('tbody tr');
    rows.forEach(row => {
      row.style.display = (!q || row.textContent.toLowerCase().includes(q)) ? '' : 'none';
    });
  });
}

/* ═══════════════════════════════════════════════════════════════
   CHART HELPERS (Chart.js wrappers)
   ═══════════════════════════════════════════════════════════════ */
const HIMS_CHARTS = {
  defaultFont: { family: 'Inter', size: 11 },

  line(canvasId, labels, datasets, opts) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !window.Chart) return null;
    return new Chart(ctx, {
      type: 'line',
      data: { labels, datasets },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { font: this.defaultFont } } },
        scales: {
          x: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: this.defaultFont } },
          y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: this.defaultFont } }
        },
        ...opts
      }
    });
  },

  bar(canvasId, labels, datasets, opts) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !window.Chart) return null;
    return new Chart(ctx, {
      type: 'bar',
      data: { labels, datasets },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { font: this.defaultFont } } },
        scales: {
          x: { grid: { display: false }, ticks: { font: this.defaultFont } },
          y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: this.defaultFont } }
        },
        ...opts
      }
    });
  },

  doughnut(canvasId, labels, data, colors, opts) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !window.Chart) return null;
    return new Chart(ctx, {
      type: 'doughnut',
      data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: { position: 'right', labels: { font: this.defaultFont, padding: 12 } }
        },
        cutout: '65%',
        ...opts
      }
    });
  },
};

/* Auto-init on DOMContentLoaded */
document.addEventListener('DOMContentLoaded', () => {
  HIMS_SESSION.loadFromStorage();
  // Auto-init tabs
  document.querySelectorAll('[data-tabs]').forEach(el => initTabs(el.id));
  // Escape key closes modals
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => {
        m.classList.add('hidden');
        document.body.style.overflow='';
      });
    }
  });
});
