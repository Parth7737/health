<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPD Token Display</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Arial', sans-serif;
            background: #0a1628;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #0d1f3c;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1e3a6e;
        }

        .hospital-name {
            font-size: 22px;
            font-weight: bold;
            color: #60a5fa;
        }

        .header-right {
            font-size: 16px;
            color: #94a3b8;
        }

        #clock {
            font-size: 18px;
            font-weight: 600;
            color: #e2e8f0;
        }

        .main-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .dept-tabs {
            padding: 8px 16px;
            background: #09162a;
            border-bottom: 1px solid #1e3a6e;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .dept-tab-btn {
            background: transparent;
            color: #93c5fd;
            border: 1px solid #2a4c87;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 12px;
            cursor: pointer;
        }

        .dept-tab-btn.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        /* LEFT: Current Token */
        .current-panel {
            background: #0f2248;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            padding: 28px;
            border-right: 2px solid #1e3a6e;
        }

        .now-label {
            font-size: 18px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 12px;
        }

        .current-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            overflow-y: auto;
        }

        .doctor-current-card {
            background: #132a57;
            border: 1px solid #2a4c87;
            border-radius: 12px;
            padding: 14px;
        }

        .doctor-current-name {
            font-size: 15px;
            font-weight: 700;
            color: #93c5fd;
            margin-bottom: 8px;
        }

        .doctor-current-token {
            font-size: 54px;
            font-weight: 900;
            color: #fbbf24;
            line-height: 1;
            text-shadow: 0 0 20px rgba(251, 191, 36, 0.35);
        }

        .doctor-current-patient {
            font-size: 20px;
            font-weight: 700;
            color: #e2e8f0;
            margin-top: 8px;
        }

        .doctor-current-meta {
            font-size: 12px;
            color: #cbd5e1;
            margin-top: 3px;
        }

        .no-patient-msg {
            font-size: 24px;
            color: #64748b;
        }

        /* RIGHT: Waiting Queue */
        .queue-panel {
            background: #0a1628;
            display: flex;
            flex-direction: column;
            padding: 32px;
        }

        .queue-title {
            font-size: 18px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 20px;
            border-bottom: 1px solid #1e3a6e;
            padding-bottom: 12px;
        }

        .queue-list {
            flex: 1;
            overflow: hidden;
        }

        .queue-item {
            display: flex;
            align-items: center;
            background: #0d1f3c;
            border: 1px solid #1e3a6e;
            border-radius: 10px;
            padding: 14px 20px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .queue-item.absent {
            opacity: 0.45;
            border-color: #374151;
        }

        .q-token {
            font-size: 32px;
            font-weight: 800;
            color: #60a5fa;
            min-width: 70px;
        }

        .q-info {
            flex: 1;
            margin-left: 16px;
        }

        .q-name {
            font-size: 18px;
            font-weight: 600;
            color: #e2e8f0;
        }

        .q-meta {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .absent-badge {
            background: #7f1d1d;
            color: #fca5a5;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 8px;
        }

        .empty-queue {
            text-align: center;
            color: #64748b;
            font-size: 18px;
            margin-top: 40px;
        }

        .footer-bar {
            background: #0d1f3c;
            padding: 10px 32px;
            border-top: 2px solid #1e3a6e;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #64748b;
        }

        .blink {
            animation: blinker 1.2s step-start infinite;
        }

        @keyframes blinker { 50% { opacity: 0; } }

        .pulse-dot {
            width: 10px; height: 10px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.8); }
        }

        @media (max-width: 1200px) {
            .main-grid { grid-template-columns: 1fr; }
            .current-panel { border-right: 0; border-bottom: 2px solid #1e3a6e; }
            .current-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="hospital-name">{{ $hospitalName ?? config('app.name') }} — OPD Queue</div>
    <div class="header-right">
        <span id="clock">--:--:--</span>
    </div>
</div>

<div class="dept-tabs" id="dept-tabs-wrap">
    <span style="color:#94a3b8; font-size:12px;">Department:</span>
    <button class="dept-tab-btn active">All</button>
</div>

<div class="main-grid">
    <!-- Current Token -->
    <div class="current-panel" id="current-panel">
        <div class="now-label">Now Serving</div>
        <div class="current-list" id="current-token-wrap">
            <div class="no-patient-msg">No Patient In Room</div>
        </div>
    </div>

    <!-- Waiting Queue -->
    <div class="queue-panel">
        <div class="queue-title"><span class="pulse-dot"></span> Waiting Queue</div>
        <div class="queue-list" id="queue-list">
            <div class="empty-queue">Queue is empty</div>
        </div>
    </div>
</div>

<div class="footer-bar">
    <span><span class="pulse-dot"></span> Live — auto refreshes every 5 seconds</span>
    <span id="last-updated">Last updated: --</span>
</div>

<script>
const queueApiUrl = "{{ route('hospital.opd-patient.queue-status') }}";
let selectedDepartment = 'all';
let lastQueueData = null;

function normalizeDept(dept) {
    return (dept || 'General').toString().trim() || 'General';
}

function renderDeptTabs(rawCurrent, rawQueue) {
    const wrap = document.getElementById('dept-tabs-wrap');
    const names = rawCurrent.map(x => normalizeDept(x.dept)).concat(rawQueue.map(x => normalizeDept(x.dept)));
    const depts = ['all', ...Array.from(new Set(names)).sort((a, b) => a.localeCompare(b))];
    const deptCountMap = names.reduce((acc, name) => {
        acc[name] = (acc[name] || 0) + 1;
        return acc;
    }, {});
    const totalCount = names.length;

    if (selectedDepartment !== 'all' && !depts.includes(selectedDepartment)) {
        selectedDepartment = 'all';
    }

    wrap.innerHTML = `
        <span style="color:#94a3b8; font-size:12px;">Department:</span>
        ${depts.map(dept => `
            <button class="dept-tab-btn ${selectedDepartment === dept ? 'active' : ''}" onclick="changeDepartment('${dept.replace(/'/g, "\\'")}')">
                ${dept === 'all' ? `All (${totalCount})` : `${dept} (${deptCountMap[dept] || 0})`}
            </button>
        `).join('')}
    `;
}

function changeDepartment(dept) {
    selectedDepartment = dept;
    renderFromData(lastQueueData);
}

function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();

function renderFromData(data) {
    if (!data) {
        return;
    }

    const rawCurrent = data.current_list || (data.current ? [data.current] : []);
    const rawQueue = data.queue || [];
    renderDeptTabs(rawCurrent, rawQueue);

    const currentList = selectedDepartment === 'all'
        ? rawCurrent
        : rawCurrent.filter(c => normalizeDept(c.dept) === selectedDepartment);

    const queueList = selectedDepartment === 'all'
        ? rawQueue
        : rawQueue.filter(q => normalizeDept(q.dept) === selectedDepartment);

    // Update current panel
    const wrap = document.getElementById('current-token-wrap');
    if (currentList.length > 0) {
        wrap.innerHTML = currentList.map(c => `
            <div class="doctor-current-card">
                <div class="doctor-current-name">Dr. ${c.doctor} (${c.dept})</div>
                <div class="doctor-current-token">${c.token}</div>
                <div class="doctor-current-patient">${c.name}</div>
                <div class="doctor-current-meta">Case: ${c.case_no}</div>
            </div>
        `).join('');
    } else {
        wrap.innerHTML = `<div class="no-patient-msg">No Patient In Room</div>`;
    }

    // Update queue list
    const qList = document.getElementById('queue-list');
    if (queueList.length > 0) {
        qList.innerHTML = queueList.map(q => `
            <div class="queue-item ${q.absent ? 'absent' : ''}">
                <div class="q-token">${q.token}</div>
                <div class="q-info">
                    <div class="q-name">${q.name} ${q.absent ? '<span class="absent-badge">Not Present</span>' : ''}</div>
                    <div class="q-meta">Dr. ${q.doctor} &nbsp;|&nbsp; ${q.dept}</div>
                </div>
            </div>
        `).join('');
    } else {
        qList.innerHTML = `<div class="empty-queue">No patients waiting</div>`;
    }

    document.getElementById('last-updated').textContent = 'Last updated: ' + new Date().toLocaleTimeString('en-IN');
}

async function fetchQueue() {
    try {
        const resp = await fetch(queueApiUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!resp.ok) return;
        lastQueueData = await resp.json();
        renderFromData(lastQueueData);
    } catch(e) {
        console.error('Queue fetch failed', e);
    }
}

fetchQueue();
setInterval(fetchQueue, 5000);
</script>
</body>
</html>
