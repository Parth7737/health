<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Doctor Dashboard — ParaCare+ HMIS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('public/front/assets/css/hims.css') }}"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-shell">
  <header class="gov-header">
    <div class="gov-header-left">
      <div class="gov-emblem"><svg viewBox="0 0 100 100" fill="none"><circle cx="50" cy="50" r="46" fill="#fff8ec" stroke="#003580" stroke-width="3"/><circle cx="50" cy="50" r="34" fill="#e6f0fb" stroke="#003580" stroke-width="1.5"/><path d="M50 16 L57 35 L78 35 L62 47 L68 66 L50 54 L32 66 L38 47 L22 35 L43 35 Z" fill="#003580" opacity=".9"/><circle cx="50" cy="50" r="9" fill="#e65100"/><circle cx="50" cy="50" r="5" fill="#f9a825"/><text x="50" y="89" text-anchor="middle" font-size="7.5" fill="#003580" font-family="Arial" font-weight="700">उत्तराखण्ड</text></svg></div>
      <div class="gov-title-block"><div class="state-name">Government of Uttarakhand</div><div class="dept-name">Dept. of Health &amp; Family Welfare</div></div>
      <div class="gov-divider"></div>
      <div class="gov-title-block"><div class="dept-name" style="color:#7eaac8">ParaCare+ HMIS v2.4</div></div>
    </div>
    <div class="gov-header-center"><div class="sys-name">Doctor Dashboard — OPD &amp; IPD Workspace</div><div class="sys-sub" id="liveDateStr" data-prefix="Dr. Rajesh Sharma · Medicine"></div></div>
    <div class="gov-header-right">
      <div class="paracare-brand"><div class="paracare-logo-mark">+</div><div class="paracare-brand-text"><div class="name">ParaCare+</div><div class="tagline">AI-Enabled HMIS</div></div></div>
      <div class="header-icon-btn">🔔<span class="notif-dot"></span></div>
      <div class="flex items-center gap-2"><div class="header-avatar">RS</div><div class="header-user-text"><div class="role-name">Dr. Rajesh Sharma</div><div class="inst-name">Senior Physician · OPD-3</div></div></div>
      <button class="btn btn-ghost btn-sm" onclick="if(confirm('Logout?'))window.location.href='index.html'" style="color:#ef5350;border-color:rgba(239,83,80,.4)">⏻</button>
    </div>
  </header>

  <nav class="gov-sidebar">
    <div class="sidebar-facility-tag"><div class="facility-name">🏥 Doon Govt. Hospital</div><div class="facility-type">OPD Counter 3 · Medicine</div></div>
    <div class="nav-section-title">My Workspace</div>
    <a href="doctor.html" class="nav-item active"><span class="nav-icon">🏠</span><span class="nav-label">My Dashboard</span><span class="nav-badge live">LIVE</span></a>
    <a href="patient.html" class="nav-item"><span class="nav-icon">👤</span><span class="nav-label">Patient 360 / EMR</span></a>
    <div class="nav-section-title">Clinical</div>
    <a href="doctor.html" class="nav-item"><span class="nav-icon">📋</span><span class="nav-label">OPD Queue</span><span class="nav-badge count">14</span></a>
    <a href="doctor.html" class="nav-item"><span class="nav-icon">🛏️</span><span class="nav-label">My IPD Patients</span><span class="nav-badge count">6</span></a>
    <a href="lab.html" class="nav-item"><span class="nav-icon">🧪</span><span class="nav-label">Lab Orders / Results</span></a>
    <a href="radiology.html" class="nav-item"><span class="nav-icon">🩻</span><span class="nav-label">Radiology Orders</span></a>
    <a href="pharmacy.html" class="nav-item"><span class="nav-icon">💊</span><span class="nav-label">e-Prescriptions</span></a>
    <div class="nav-section-title">Tools</div>
    <a href="#" class="nav-item"><span class="nav-icon">📝</span><span class="nav-label">Clinical Templates</span></a>
    <a href="#" class="nav-item"><span class="nav-icon">📅</span><span class="nav-label">My Schedule</span></a>
    <a href="billing.html" class="nav-item"><span class="nav-icon">💳</span><span class="nav-label">Patient Bills</span></a>
    <a href="bloodbank.html" class="nav-item"><span class="nav-icon">🩸</span><span class="nav-label">Blood Requests</span></a>
    <a href="admin.html" class="nav-item"><span class="nav-icon">🏠</span><span class="nav-label">Admin Dashboard</span></a>
    <div class="sidebar-footer"><div class="ver-text">ParaCare+ HMIS v2.4.1<br>© 2024 Govt. of Uttarakhand</div></div>
  </nav>

  <main class="main-content">
    <div class="page-header">
      <div class="page-header-left">
        <div class="page-header-icon" style="background:#e3f2fd;font-size:20px">🩺</div>
        <div class="page-header-text"><div class="pg-title">Doctor Dashboard</div><div class="pg-sub">OPD Queue · IPD Patients · Orders · AI Copilot</div></div>
      </div>
      <div class="page-header-right">
        <div class="breadcrumb"><span>Home</span><span class="sep">›</span><span class="active">Doctor</span></div>
        <span class="badge badge-live">● Live</span>
        <button class="btn btn-primary btn-sm" onclick="openModal('newRxModal')">+ New Prescription</button>
      </div>
    </div>

    <div class="content-area">

      <!-- KPIs -->
      <div class="kpi-grid mb-4" style="grid-template-columns:repeat(5,1fr)">
        <div class="kpi-card blue"><div class="kpi-icon">📋</div><div class="kpi-label">OPD Queue</div><div class="kpi-value" id="queueCount">14</div><div class="kpi-sub">3 waiting &gt;15 min</div></div>
        <div class="kpi-card green"><div class="kpi-icon">✅</div><div class="kpi-label">Seen Today</div><div class="kpi-value">28</div><div class="kpi-sub">Avg consult: 7.2 min</div></div>
        <div class="kpi-card teal"><div class="kpi-icon">🛏️</div><div class="kpi-label">IPD Patients</div><div class="kpi-value">6</div><div class="kpi-sub">1 critical review due</div></div>
        <div class="kpi-card orange"><div class="kpi-icon">🧪</div><div class="kpi-label">Pending Results</div><div class="kpi-value">9</div><div class="kpi-sub">2 critical values</div></div>
        <div class="kpi-card purple"><div class="kpi-icon">💊</div><div class="kpi-label">Rx Today</div><div class="kpi-value">24</div><div class="kpi-sub">All validated</div></div>
      </div>

      <div class="grid-21 mb-5">
        <!-- OPD Queue -->
        <div class="card">
          <div class="card-header">
            <div class="card-title"><div class="card-icon" style="background:#e3f2fd">📋</div>OPD Queue — Counter 3</div>
            <div class="card-actions">
              <span class="badge badge-live">● Live</span>
              <button class="btn btn-primary btn-sm" onclick="callNext()">📢 Call Next</button>
            </div>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Token</th><th>Patient</th><th>Age/Sex</th><th>Complaint</th><th>Wait</th><th>Priority</th><th>Action</th></tr></thead>
              <tbody id="opdQueue">
                <tr style="background:#fff8e1"><td class="font-600" style="color:#e65100">▶ T-029</td><td><div class="cell-primary">Smt. Kamla Devi</div><div class="cell-secondary">ABHA: UK-40291-D</div></td><td>54F</td><td>Chest pain, SOB</td><td style="color:#c62828">22 min</td><td><span class="badge badge-danger">Urgent</span></td><td><button class="btn btn-primary btn-sm" onclick="openConsult('Kamla Devi')">Consult</button></td></tr>
                <tr><td>T-030</td><td><div class="cell-primary">Rajan Verma</div><div class="cell-secondary">ABHA: UK-18841-H</div></td><td>38M</td><td>Fever, cough 5 days</td><td>14 min</td><td><span class="badge badge-warning">Medium</span></td><td><button class="btn btn-ghost btn-sm" onclick="openConsult('Rajan Verma')">Consult</button></td></tr>
                <tr><td>T-031</td><td><div class="cell-primary">Priya Negi</div><div class="cell-secondary">ABHA: UK-29012-N</div></td><td>26F</td><td>Abdominal pain</td><td>8 min</td><td><span class="badge badge-success">Routine</span></td><td><button class="btn btn-ghost btn-sm" onclick="openConsult('Priya Negi')">Consult</button></td></tr>
                <tr><td>T-032</td><td><div class="cell-primary">Govind Rawat</div><div class="cell-secondary">ABHA: UK-77341-P</div></td><td>67M</td><td>Diabetes follow-up</td><td>5 min</td><td><span class="badge badge-success">Routine</span></td><td><button class="btn btn-ghost btn-sm" onclick="openConsult('Govind Rawat')">Consult</button></td></tr>
                <tr><td>T-033</td><td><div class="cell-primary">Baby Ananya (5yr)</div><div class="cell-secondary">Guardian: S. Bisht</div></td><td>5F</td><td>Convulsions</td><td>3 min</td><td><span class="badge badge-danger">Critical</span></td><td><button class="btn btn-danger btn-sm" onclick="openConsult('Baby Ananya')">URGENT</button></td></tr>
                <tr><td>T-034</td><td><div class="cell-primary">Mohan Lal Shah</div></td><td>48M</td><td>Hypertension check</td><td>1 min</td><td><span class="badge badge-success">Routine</span></td><td><button class="btn btn-ghost btn-sm">Consult</button></td></tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer"><span id="queueStatus">14 in queue · Next: T-029 Kamla Devi</span><button class="btn btn-ghost btn-sm">View All</button></div>
        </div>

        <!-- Right panel -->
        <div class="flex flex-col gap-4">
          <!-- Current patient -->
          <div class="card" id="currentPatientCard">
            <div class="card-header"><div class="card-title">👤 Current — T-028</div><span class="badge badge-live">In Consult</span></div>
            <div class="card-body-sm">
              <div class="flex items-center gap-3 mb-3">
                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#1565c0,#00695c);color:white;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0">HD</div>
                <div><div style="font-size:14px;font-weight:700">Hari Das Gupta</div><div class="text-muted text-sm">62M · ABHA: UK-22041-D · Dr. Sharma</div></div>
              </div>
              <div class="info-row cols-3 mb-3">
                <div class="info-item"><div class="i-label">BP</div><div class="i-value" style="color:#c62828">148/94</div></div>
                <div class="info-item"><div class="i-label">Pulse</div><div class="i-value">88 bpm</div></div>
                <div class="info-item"><div class="i-label">SpO2</div><div class="i-value" style="color:#2e7d32">97%</div></div>
                <div class="info-item"><div class="i-label">Temp</div><div class="i-value">99.2°F</div></div>
                <div class="info-item"><div class="i-label">RBS</div><div class="i-value" style="color:#e65100">212 mg/dL</div></div>
                <div class="info-item"><div class="i-label">Wt</div><div class="i-value">74 kg</div></div>
              </div>
              <div class="flex gap-2 flex-wrap">
                <a href="patient.html" class="btn btn-primary btn-sm">📋 Full EMR</a>
                <button class="btn btn-soft-primary btn-sm" onclick="openModal('labOrderModal')">🧪 Lab Order</button>
                <button class="btn btn-soft-primary btn-sm" onclick="openModal('newRxModal')">💊 Prescribe</button>
                <button class="btn btn-ghost btn-sm">🩻 Radiology</button>
              </div>
            </div>
          </div>

          <!-- AI Copilot -->
          <div class="ai-insight-block">
            <div class="ai-header">🤖 AI Clinical Copilot</div>
            <div class="ai-body">
              <div class="ai-item"><div class="ai-dot" style="background:#e65100"></div><div><strong>Hari Das Gupta:</strong> Uncontrolled T2DM + HTN. HbA1c due (last 4 months ago). Consider ARB addition per JNC-8.</div></div>
              <div class="ai-item"><div class="ai-dot"></div><div><strong>Drug alert:</strong> Metformin + contrast — hold 48h before CT if ordered.</div></div>
              <div class="ai-item"><div class="ai-dot" style="background:#2e7d32"></div><div><strong>Kamla Devi (T-029):</strong> ECG pattern suggests LBBB. Cardiology consult recommended urgently.</div></div>
            </div>
          </div>
        </div>
      </div>

      <!-- IPD Patients -->
      <div class="card mb-5">
        <div class="card-header">
          <div class="card-title"><div class="card-icon" style="background:#e8f5e9">🛏️</div>My IPD Patients</div>
          <div class="card-actions"><button class="btn btn-primary btn-sm">+ Admit Patient</button></div>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>MRN</th><th>Patient</th><th>Ward / Bed</th><th>Diagnosis</th><th>Day</th><th>Vitals Alert</th><th>Action</th></tr></thead>
            <tbody>
              <tr><td class="font-600">UK-IPD-4812</td><td><div class="cell-primary">Deepak Kumar</div><div class="cell-secondary">58M · ABHA linked</div></td><td>Ward 4 / Bed 12</td><td>Acute MI — post PCI</td><td>Day 3</td><td><span class="badge badge-success">Stable</span></td><td><a href="patient.html" class="btn btn-ghost btn-sm">EMR</a></td></tr>
              <tr><td class="font-600">UK-IPD-4798</td><td><div class="cell-primary">Sunita Rawat</div><div class="cell-secondary">44F</div></td><td>ICU / Bed 3</td><td>Sepsis — ARDS</td><td>Day 5</td><td><span class="badge badge-danger">⚡ Review Now</span></td><td><a href="patient.html" class="btn btn-danger btn-sm">Review</a></td></tr>
              <tr><td class="font-600">UK-IPD-4823</td><td><div class="cell-primary">Ram Prasad Joshi</div><div class="cell-secondary">71M</div></td><td>Ward 2 / Bed 7</td><td>COPD Exacerbation</td><td>Day 2</td><td><span class="badge badge-warning">SpO2 91%</span></td><td><a href="patient.html" class="btn btn-ghost btn-sm">EMR</a></td></tr>
              <tr><td class="font-600">UK-IPD-4829</td><td><div class="cell-primary">Meera Bisht</div><div class="cell-secondary">35F</div></td><td>Ward 6 / Bed 4</td><td>GI Bleed — variceal</td><td>Day 1</td><td><span class="badge badge-warning">Hb 6.8</span></td><td><a href="patient.html" class="btn btn-ghost btn-sm">EMR</a></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Lab results + Vitals chart -->
      <div class="grid-12 mb-4">
        <div class="card">
          <div class="card-header"><div class="card-title"><div class="card-icon" style="background:#ede7f6">🧪</div>Pending Lab Results</div></div>
          <div class="card-body-sm">
            <div class="list-item"><div class="list-item-icon" style="background:#ffebee">🚨</div><div class="list-item-body"><div class="li-title">Potassium — Sunita Rawat</div><div class="li-sub">Reported: 6.2 mEq/L · Critical high</div></div><span class="badge badge-danger">Critical</span></div>
            <div class="list-item"><div class="list-item-icon" style="background:#fff3e0">⚠️</div><div class="list-item-body"><div class="li-title">Hb — Meera Bisht</div><div class="li-sub">Reported: 6.8 g/dL · Low</div></div><span class="badge badge-warning">Abnormal</span></div>
            <div class="list-item"><div class="list-item-icon" style="background:#e8f5e9">✅</div><div class="list-item-body"><div class="li-title">HbA1c — Govind Rawat</div><div class="li-sub">Reported: 7.9% · Elevated</div></div><span class="badge badge-warning">Review</span></div>
            <div class="list-item"><div class="list-item-icon" style="background:#e3f2fd">🧫</div><div class="list-item-body"><div class="li-title">Blood Culture — Deepak Kumar</div><div class="li-sub">Pending 48h · No growth yet</div></div><span class="badge badge-primary">Pending</span></div>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title"><div class="card-icon" style="background:#e3f2fd">📈</div>My OPD — Today's Flow</div></div>
          <div class="card-body">
            <div class="chart-container" style="height:180px"><canvas id="opdFlowChart"></canvas></div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- Modals -->
<div class="modal-overlay" id="newRxModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">💊 New e-Prescription</div>
      <button class="modal-close" onclick="closeModal('newRxModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row cols-2 mb-3">
        <div class="form-group"><label class="form-label">Patient Name</label><input class="form-control" value="Hari Das Gupta"/></div>
        <div class="form-group"><label class="form-label">MRN / ABHA</label><input class="form-control" value="UK-22041-D"/></div>
      </div>
      <div class="form-group">
        <label class="form-label">Drug Name <span class="req">*</span></label>
        <select class="form-control"><option>Metformin 500mg</option><option>Amlodipine 5mg</option><option>Atorvastatin 40mg</option><option>Losartan 50mg</option><option>Aspirin 75mg</option></select>
      </div>
      <div class="form-row cols-3">
        <div class="form-group"><label class="form-label">Dose</label><input class="form-control" value="500mg"/></div>
        <div class="form-group"><label class="form-label">Frequency</label><select class="form-control"><option>Twice daily</option><option>Once daily</option><option>Three times daily</option></select></div>
        <div class="form-group"><label class="form-label">Duration</label><input class="form-control" value="30 days"/></div>
      </div>
      <div class="form-group"><label class="form-label">Instructions</label><textarea class="form-control" rows="2">Take after food. Avoid alcohol. Monitor RBS weekly.</textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('newRxModal')">Cancel</button>
      <button class="btn btn-primary" onclick="closeModal('newRxModal');showToastDoc('Prescription sent to pharmacy','success')">Send to Pharmacy →</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="labOrderModal">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">🧪 Lab Order</div><button class="modal-close" onclick="closeModal('labOrderModal')">✕</button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Test Category</label>
        <select class="form-control"><option>Biochemistry</option><option>Haematology</option><option>Microbiology</option><option>Serology</option></select>
      </div>
      <div class="form-group"><label class="form-label">Tests Ordered</label>
        <div class="flex flex-wrap gap-2">
          <label class="checkbox-wrap"><input type="checkbox" checked/> CBC</label>
          <label class="checkbox-wrap"><input type="checkbox" checked/> LFT</label>
          <label class="checkbox-wrap"><input type="checkbox" checked/> KFT</label>
          <label class="checkbox-wrap"><input type="checkbox"/> HbA1c</label>
          <label class="checkbox-wrap"><input type="checkbox"/> Lipid Profile</label>
          <label class="checkbox-wrap"><input type="checkbox" checked/> Blood Culture</label>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Priority</label>
        <select class="form-control"><option>Routine</option><option>Urgent</option><option>STAT</option></select>
      </div>
      <div class="form-group"><label class="form-label">Clinical Note</label><textarea class="form-control" rows="2">Suspected sepsis. STAT blood culture + sensitivity.</textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('labOrderModal')">Cancel</button>
      <button class="btn btn-primary" onclick="closeModal('labOrderModal');showToastDoc('Lab order sent — STAT','success')">Send to Lab →</button>
    </div>
  </div>
</div>

<script src="{{ asset('public/front/assets/js/nav.js') }}"></script>
<script>
updateClock();
setInterval(updateClock, 30000);

new Chart(document.getElementById('opdFlowChart'), {
  type: 'bar',
  data: {
    labels: ['9am','10am','11am','12pm','1pm','2pm','3pm','4pm'],
    datasets: [{
      label: 'Patients',
      data: [4,7,6,5,3,2,2,0],
      backgroundColor: '#1565c0', borderRadius: 5
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});

let queueN = 14;
function callNext() {
  if (queueN > 0) {
    queueN--;
    document.getElementById('queueCount').textContent = queueN;
    document.getElementById('queueStatus').textContent = queueN + ' in queue · Next: T-0' + (30 + (14 - queueN));
    showToastDoc('Patient T-0' + (29 + (14 - queueN)) + ' called to Counter 3', 'success');
  }
}

function openConsult(name) { showToastDoc('Opening EMR for ' + name, 'info'); window.location.href='patient.html'; }

function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
}
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
}

function showToastDoc(msg, type) {
  const c = document.getElementById('toastCont') || (() => {
    const d = document.createElement('div'); d.id = 'toastCont';
    d.style = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px';
    document.body.appendChild(d); return d;
  })();
  const cols = {success:'#2e7d32',info:'#1565c0',warning:'#e65100',danger:'#c62828'};
  const icons = {success:'✅',info:'ℹ️',warning:'⚠️',danger:'❌'};
  const t = document.createElement('div');
  t.style = `background:#fff;border:1px solid #e0e0e0;border-left:4px solid ${cols[type]||cols.info};border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;font-size:13px;color:#1a2a3a;font-family:Inter,sans-serif;box-shadow:0 8px 28px rgba(0,0,0,.15);max-width:340px`;
  t.innerHTML = `<span style="font-size:16px">${icons[type]||'ℹ️'}</span><span>${msg}</span>`;
  c.appendChild(t); setTimeout(() => t.remove(), 3200);
}

document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('open'); document.body.style.overflow = '';
  }
});
</script>
</body>
</html>
