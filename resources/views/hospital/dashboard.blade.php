@extends('layouts.hospital.app', ['is_dashbaord' => true, 'is_header_hiden' => true])

@section('title', 'Executive Dashboard')

@section('content')

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <div class="page-title">&#x1F4CA; Executive Dashboard</div>
          <div class="page-subtitle">District Hospital Dehradun &mdash; Real-Time Operations Overview | <span id="liveClock" style="font-weight:700;color:var(--primary)"></span></div>
        </div>
        <div class="page-actions">
          <button class="btn btn-secondary btn-sm" onclick="refreshDashboard()">&#x1F504; Refresh</button>
          <button class="btn btn-outline-primary btn-sm" onclick="printSection('dashPrintArea','Executive Dashboard')">&#x1F5A8; Print Report</button>
          <a href="{{ route('hospital.front-office.index') }}" class="btn btn-primary btn-sm">&#x2795; New Patient</a>
        </div>
      </div>

      <!-- Critical Alerts Bar -->
      <div id="alertsBar" style="margin-bottom:16px"></div>

      <!-- KPI Stats -->
      <div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr))">
        <div class="stat-card stat-blue"><div class="stat-icon">&#x1F9D1;&#x200D;&#x2695;&#xFE0F;</div><div class="stat-info"><div class="stat-value" id="s-opd">&mdash;</div><div class="stat-label">Today OPD</div><div class="stat-change up">&#x2191; 12% vs yesterday</div></div></div>
        <div class="stat-card stat-teal"><div class="stat-icon">&#x1F6CF;&#xFE0F;</div><div class="stat-info"><div class="stat-value" id="s-ipd">&mdash;</div><div class="stat-label">IPD Admitted</div><div class="stat-change neutral">86% Bed Occupancy</div></div></div>
        <div class="stat-card stat-orange"><div class="stat-icon">&#x1F6A8;</div><div class="stat-info"><div class="stat-value" id="s-emergency">&mdash;</div><div class="stat-label">Emergency Cases</div><div class="stat-change up">&#x2191; 3 in last hour</div></div></div>
        <div class="stat-card stat-green"><div class="stat-icon">&#x2705;</div><div class="stat-info"><div class="stat-value" id="s-discharged">&mdash;</div><div class="stat-label">Discharged Today</div><div class="stat-change up">&#x2191; Good throughput</div></div></div>
        <div class="stat-card stat-purple"><div class="stat-icon">&#x1F9EA;</div><div class="stat-info"><div class="stat-value" id="s-lab">&mdash;</div><div class="stat-label">Lab Tests Today</div><div class="stat-change neutral">4.2h avg TAT</div></div></div>
        <div class="stat-card stat-pink"><div class="stat-icon">&#x1F4B3;</div><div class="stat-info"><div class="stat-value" id="s-revenue">&mdash;</div><div class="stat-label">Today Revenue</div><div class="stat-change up">&#x2191; 8% vs last week</div></div></div>
        <div class="stat-card stat-red"><div class="stat-icon">&#x1F691;</div><div class="stat-info"><div class="stat-value" id="s-ambulance">&mdash;</div><div class="stat-label">Active Ambulances</div><div class="stat-change neutral">14 total fleet</div></div></div>
        <div class="stat-card stat-indigo"><div class="stat-icon">&#x1F48A;</div><div class="stat-info"><div class="stat-value" id="s-pharma">&mdash;</div><div class="stat-label">Rx Dispensed</div><div class="stat-change up">&#x2191; Smooth queue</div></div></div>
      </div>

      <!-- Quick Access Tiles -->
      <div class="card mb-20">
        <div class="card-header"><div class="card-title"><span class="ct-icon">&#x26A1;</span> Quick Actions</div></div>
        <div class="card-body p-12">
          <div class="quick-tiles" style="grid-template-columns:repeat(auto-fill,minmax(110px,1fr))">
            <a href="{{ route('hospital.front-office.index') }}" class="quick-tile"><span class="qt-icon">&#x1F464;</span><span class="qt-label">New Patient</span></a>
            <a href="{{ route('hospital.front-office.index') }}" class="quick-tile"><span class="qt-icon">&#x1FA7A;</span><span class="qt-label">OPD Token</span></a>
            <a href="{{ route('hospital.ipd-patient.index') }}" class="quick-tile"><span class="qt-icon">&#x1F6CF;&#xFE0F;</span><span class="qt-label">IPD Admit</span></a>
            <a href="{{ route('hospital.pharmacy.sale.index') }}" class="quick-tile"><span class="qt-icon">&#x1F48A;</span><span class="qt-label">Pharmacy</span></a>
            <a href="{{ route('hospital.pathology.worklist.index') }}" class="quick-tile"><span class="qt-icon">&#x1F9EA;</span><span class="qt-label">Lab / LIS</span></a>
            <a href="{{ route('hospital.radiology.worklist.index') }}" class="quick-tile"><span class="qt-icon">&#x1FA7B;</span><span class="qt-label">Radiology</span></a>
            <a href="{{ route('hospital.dashboard') }}" class="quick-tile"><span class="qt-icon">&#x1F4B3;</span><span class="qt-label">Billing</span></a>
            <a href="{{ route('hospital.dashboard') }}" class="quick-tile"><span class="qt-icon">&#x1F691;</span><span class="qt-label">Ambulance</span></a>
            <a href="{{ route('hospital.dashboard') }}" class="quick-tile"><span class="qt-icon">&#x1FA78;</span><span class="qt-label">Blood Bank</span></a>
            <a href="{{ route('hospital.dashboard') }}" class="quick-tile"><span class="qt-icon">&#x1F4E6;</span><span class="qt-label">Inventory</span></a>
            <a href="{{ route('hospital.hr.staff.index') }}" class="quick-tile"><span class="qt-icon">&#x1F465;</span><span class="qt-label">HR &amp; Payroll</span></a>
            <a href="{{ route('hospital.dashboard') }}" class="quick-tile"><span class="qt-icon">&#x1F4DC;</span><span class="qt-label">Certificates</span></a>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="dash-grid cols-3" style="margin-bottom:16px">
        <div class="card col-span-2">
          <div class="card-header">
            <div><div class="card-title"><span class="ct-icon">&#x1F4C8;</span> OPD / IPD Trend (Last 7 Days)</div><div class="card-subtitle">Daily patient registration and admission trend</div></div>
            <div class="card-actions"><select class="form-control" style="font-size:11px;padding:4px 8px;width:auto" onchange="updateChart(this.value)"><option>7 Days</option><option>30 Days</option><option>3 Months</option></select></div>
          </div>
          <div class="card-body" style="height:240px"><canvas id="opdIpdChart"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title"><span class="ct-icon">&#x1F3E5;</span> Dept. Load</div></div>
          <div class="card-body" style="height:240px"><canvas id="deptChart"></canvas></div>
        </div>
      </div>

      <!-- Revenue + Bed Occupancy + Activity Feed -->
      <div class="dash-grid cols-3" style="margin-bottom:16px">
        <div class="card">
          <div class="card-header"><div class="card-title"><span class="ct-icon">&#x1F4B0;</span> Revenue (&#x20B9;) &mdash; This Month</div></div>
          <div class="card-body" style="height:200px"><canvas id="revenueChart"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title"><span class="ct-icon">&#x1F6CF;&#xFE0F;</span> Bed Occupancy</div></div>
          <div class="card-body"><div id="bedOccupancyList"></div></div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title"><span class="ct-icon">&#x1F514;</span> Live Activity Feed</div><span class="badge badge-green" style="font-size:10px">&#x25CF; LIVE</span></div>
          <div id="activityFeed" style="max-height:240px;overflow-y:auto"></div>
        </div>
      </div>

      <!-- Critical Patients + Pending Tasks -->
      <div class="dash-grid cols-2" style="margin-bottom:16px">
        <div class="card">
          <div class="card-header">
            <div><div class="card-title"><span class="ct-icon">&#x1F6A8;</span> Critical Patients</div><div class="card-subtitle">Requires immediate attention</div></div>
            <a href="{{ route('hospital.ipd-patient.index') }}" class="btn btn-secondary btn-xs">View All</a>
          </div>
          <div class="table-wrap">
            <table class="hims-table">
              <thead><tr><th>Patient</th><th>Ward</th><th>Condition</th><th>Assigned To</th><th>Action</th></tr></thead>
              <tbody id="criticalTable"><tr><td colspan="5" class="text-center text-muted" style="padding:20px">Loading&hellip;</td></tr></tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <div><div class="card-title"><span class="ct-icon">&#x1F4CB;</span> Pending Approvals &amp; Tasks</div><div class="card-subtitle">System-wide pending items requiring action</div></div>
            <span class="badge badge-red" id="pendingCount">&hellip;</span>
          </div>
          <div id="pendingTasksList" style="max-height:320px;overflow-y:auto"></div>
        </div>
      </div>

      <!-- Stock Alerts + Staff On Duty -->
      <div class="dash-grid cols-2" style="margin-bottom:16px">
        <div class="card">
          <div class="card-header"><div class="card-title"><span class="ct-icon">&#x26A0;&#xFE0F;</span> Critical Stock Alerts</div><a href="{{ route('hospital.dashboard') }}" class="btn btn-warning btn-xs">Manage</a></div>
          <div id="stockAlertsList"></div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title"><span class="ct-icon">&#x1F468;&#x200D;&#x2695;&#xFE0F;</span> Staff On Duty &mdash; Current Shift</div><a href="{{ route('hospital.hr.staff.index') }}" class="btn btn-secondary btn-xs">Full Roster</a></div>
          <div id="staffOnDutyList"></div>
        </div>
      </div>

      <!-- Module Status Grid -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header"><div class="card-title"><span class="ct-icon">&#x1F5A5;&#xFE0F;</span> Module Status Overview</div><div class="card-subtitle">Real-time status of all HIMS modules</div></div>
        <div class="card-body"><div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px" id="moduleStatusGrid"></div></div>
      </div>

@endsection

@push('scripts')
<script>
(function(){function tick(){var el=document.getElementById('liveClock');if(el)el.textContent=new Date().toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit'});}tick();setInterval(tick,1000);})();

function loadDashboardData(){var d={opd:48+Math.floor(Math.random()*8),ipd:127+Math.floor(Math.random()*5),emergency:12+Math.floor(Math.random()*3),discharged:18+Math.floor(Math.random()*4),lab:156+Math.floor(Math.random()*12),revenue:Math.floor(Math.random()*20000+180000),ambulance:3+Math.floor(Math.random()*3),pharma:89+Math.floor(Math.random()*15)};document.getElementById('s-opd').textContent=d.opd;document.getElementById('s-ipd').textContent=d.ipd;document.getElementById('s-emergency').textContent=d.emergency;document.getElementById('s-discharged').textContent=d.discharged;document.getElementById('s-lab').textContent=d.lab;document.getElementById('s-revenue').textContent='\u20b9'+d.revenue.toLocaleString('en-IN');document.getElementById('s-ambulance').textContent=d.ambulance;document.getElementById('s-pharma').textContent=d.pharma;}

var chartOpd;
function initCharts(){var days=['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];chartOpd=HIMS_CHARTS.line('opdIpdChart',days,[{label:'OPD Patients',data:[42,55,38,61,48,52,48],borderColor:'#1565c0',backgroundColor:'rgba(21,101,192,.08)',fill:true,tension:.4,pointRadius:4},{label:'IPD Admissions',data:[18,22,15,25,20,19,22],borderColor:'#00897b',backgroundColor:'rgba(0,137,123,.08)',fill:true,tension:.4,pointRadius:4},{label:'Emergency',data:[8,12,9,14,10,11,12],borderColor:'#e53935',backgroundColor:'rgba(229,57,53,.08)',fill:true,tension:.4,pointRadius:4}]);HIMS_CHARTS.doughnut('deptChart',['Gen. Medicine','Surgery','Ortho','Gynae','Paediatrics','Others'],[28,18,14,12,10,18],['#1565c0','#00897b','#f57c00','#e91e63','#7b1fa2','#607d8b']);HIMS_CHARTS.bar('revenueChart',['Wk1','Wk2','Wk3','Wk4'],[{label:'OPD Revenue',data:[42000,56000,38000,61000],backgroundColor:'rgba(21,101,192,.7)'},{label:'IPD Revenue',data:[180000,220000,195000,240000],backgroundColor:'rgba(0,137,123,.7)'},{label:'Lab Revenue',data:[28000,32000,25000,35000],backgroundColor:'rgba(245,124,0,.7)'}]);var wards=[{name:'Medical Ward',total:40,occupied:36},{name:'Surgical Ward',total:30,occupied:24},{name:'Maternity Ward',total:20,occupied:17},{name:'Paediatric Ward',total:15,occupied:12},{name:'ICU',total:10,occupied:9},{name:'Ortho Ward',total:20,occupied:15}];var el=document.getElementById('bedOccupancyList');if(el)el.innerHTML=wards.map(function(w){var pct=Math.round(w.occupied/w.total*100);var clr=pct>=90?'#d32f2f':pct>=75?'#f57c00':'#2e7d32';return'<div style="margin-bottom:10px"><div style="display:flex;justify-content:space-between;margin-bottom:3px"><span style="font-size:12px;font-weight:600">'+w.name+'</span><span style="font-size:11px;font-weight:700;color:'+clr+'">'+w.occupied+'/'+w.total+' ('+pct+'%)</span></div><div style="height:6px;background:#eee;border-radius:99px;overflow:hidden"><div style="height:100%;width:'+pct+'%;background:'+clr+';border-radius:99px;transition:width .5s"></div></div></div>';}).join('');}

function updateChart(period){if(!chartOpd)return;var maps={'7 Days':{labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],opd:[42,55,38,61,48,52,48]},'30 Days':{labels:['W1','W2','W3','W4'],opd:[280,320,295,340]},'3 Months':{labels:['Jan','Feb','Mar'],opd:[1240,1180,1360]}};var d=maps[period];if(d){chartOpd.data.labels=d.labels;chartOpd.data.datasets[0].data=d.opd;chartOpd.update();}}

function loadCriticalAlerts(){var alerts=[{type:'error',icon:'\u{1F6A8}',msg:'<b>Critical Blood Stock:</b> O\u2212 group only 2 units remaining. Immediate action needed.'},{type:'warning',icon:'\u26a0\ufe0f',msg:'<b>Low Drug Stock:</b> Metformin 500mg stock below reorder level (12 strips).'},{type:'info',icon:'\u2139\ufe0f',msg:'<b>Pending Approvals:</b> 5 leave requests and 3 purchase orders awaiting admin approval.'}];var bar=document.getElementById('alertsBar');if(!bar)return;bar.innerHTML=alerts.map(function(a){var cls=a.type==='error'?'red':a.type==='warning'?'orange':'blue';return'<div class="alert alert-'+cls+'" style="margin-bottom:6px"><span class="alert-icon">'+a.icon+'</span><div class="alert-content">'+a.msg+'</div><button class="alert-close" onclick="this.closest(\'.alert\').remove()">\u2715</button></div>';}).join('');}

var feedItems=[{icon:'\u{1F464}',color:'#1565c0',msg:'New patient registered: Ramesh Kumar (MRN-10029)',time:'Just now'},{icon:'\u{1FA7A}',color:'#00897b',msg:'Dr. Negi completed consultation: OPD-2024-1847',time:'1m ago'},{icon:'\u{1F48A}',color:'#7b1fa2',msg:'STAT order dispensed: Ceftriaxone 1g IV \u2014 Ward 3',time:'3m ago'},{icon:'\u{1F9EA}',color:'#f57c00',msg:'Lab result critical: Potassium 6.8 mEq/L',time:'5m ago'},{icon:'\u{1F4B3}',color:'#0288d1',msg:'Bill settled: \u20b924,500 \u2014 Sunita Devi',time:'8m ago'},{icon:'\u{1F691}',color:'#c62828',msg:'Ambulance UK-108-03 dispatched to Patel Nagar',time:'10m ago'},{icon:'\u{1FA78}',color:'#880e4f',msg:'Blood issued: B+ (2 units) \u2014 IPD-2024-0901',time:'12m ago'},{icon:'\u2705',color:'#2e7d32',msg:'Patient discharged: Ajay Bisht \u2014 Ward A, Bed 14',time:'15m ago'}];
var feedIdx=0;
function addActivityItem(){var feed=document.getElementById('activityFeed');if(!feed)return;var item=feedItems[feedIdx%feedItems.length];feedIdx++;var div=document.createElement('div');div.className='list-item';div.innerHTML='<div class="li-icon" style="background:'+item.color+'22;color:'+item.color+'">'+item.icon+'</div><div class="li-content"><div class="li-title" style="font-size:11.5px">'+item.msg+'</div></div><div class="li-right"><div class="li-time">'+item.time+'</div></div>';feed.insertBefore(div,feed.firstChild);if(feed.children.length>15)feed.lastChild.remove();}
function startLiveFeed(){feedItems.forEach(function(_,i){setTimeout(addActivityItem,i*400);});}

function loadPendingTasks(){var tasks=[{icon:'\u{1F3D6}\ufe0f',color:'#f57c00',title:'5 Leave Requests pending',sub:'HR Module \u2014 Needs admin approval',badge:'warn'},{icon:'\u{1F4E6}',color:'#1565c0',title:'3 Purchase Orders for approval',sub:'Inventory \u2014 Medical supplies and drugs',badge:'count'},{icon:'\u{1FA78}',color:'#880e4f',title:'2 Blood Cross-match pending',sub:'Blood Bank \u2014 Urgent requisitions',badge:'warn'},{icon:'\u{1F48A}',color:'#7b1fa2',title:'7 Drug Expiry items action needed',sub:'Pharmacy \u2014 Items expiring within 30 days',badge:'warn'},{icon:'\u{1F4CB}',color:'#00897b',title:'4 Discharge Summaries pending',sub:'IPD \u2014 Doctors need to complete summaries',badge:'count'},{icon:'\u{1F52C}',color:'#c62828',title:'2 Critical Lab Values unacknowledged',sub:'LIS \u2014 Requires clinician acknowledgement',badge:'warn'}];var pc=document.getElementById('pendingCount');if(pc)pc.textContent=tasks.length+' Items';var el=document.getElementById('pendingTasksList');if(!el)return;el.innerHTML=tasks.map(function(t){return'<div class="list-item"><div class="li-icon" style="background:'+t.color+'18;color:'+t.color+'">'+t.icon+'</div><div class="li-content"><div class="li-title">'+t.title+'</div><div class="li-sub">'+t.sub+'</div></div><div class="li-right"><span class="badge badge-'+(t.badge==='warn'?'red':'blue')+'" style="font-size:9.5px">Action</span></div></div>';}).join('');}

function loadStockAlerts(){var alerts=[{icon:'\u{1FA78}',color:'#c62828',item:'O\u2212 Blood Group',current:'2 units',min:'10 units',level:'critical'},{icon:'\u{1F48A}',color:'#f57c00',item:'Metformin 500mg',current:'12 strips',min:'50 strips',level:'low'},{icon:'\u{1F48A}',color:'#f57c00',item:'Amlodipine 5mg',current:'18 strips',min:'50 strips',level:'low'},{icon:'\u{1F9EA}',color:'#1565c0',item:'Blood Culture Bottles',current:'8 units',min:'30 units',level:'low'},{icon:'\u{1FA79}',color:'#880e4f',item:'Surgical Gloves L',current:'2 boxes',min:'10 boxes',level:'critical'},{icon:'\u{1F489}',color:'#7b1fa2',item:'IV Cannula 18G',current:'15 pcs',min:'100 pcs',level:'low'}];var el=document.getElementById('stockAlertsList');if(!el)return;el.innerHTML=alerts.map(function(a){return'<div class="list-item"><div class="li-icon" style="background:'+a.color+'15;color:'+a.color+'">'+a.icon+'</div><div class="li-content"><div class="li-title">'+a.item+'</div><div class="li-sub">Current: <b>'+a.current+'</b> | Min: '+a.min+'</div></div><div class="li-right"><span class="badge badge-'+(a.level==='critical'?'red':'orange')+'">'+(a.level==='critical'?'\u{1F6A8} Critical':'\u26a0 Low')+'</span></div></div>';}).join('');}

function loadStaffOnDuty(){var staff=[{name:'Dr. Rajesh Negi',role:'Senior Physician',dept:'General Medicine',shift:'Morning',status:'on'},{name:'Dr. Sunita Verma',role:'Gynaecologist',dept:'Obs & Gynae',shift:'Morning',status:'on'},{name:'Nurse Priya Rawat',role:'Head Nurse',dept:'Ward 3',shift:'Morning',status:'on'},{name:'Dr. Amit Kumar',role:'Radiologist',dept:'Radiology',shift:'Morning',status:'on'},{name:'Pharm. Suresh Lal',role:'Pharmacist',dept:'Pharmacy',shift:'Morning',status:'on'},{name:'Dr. Vinay Bisht',role:'Surgeon',dept:'Surgery',shift:'Evening',status:'off'}];var el=document.getElementById('staffOnDutyList');if(!el)return;el.innerHTML=staff.map(function(s){return'<div class="list-item"><div class="li-icon" style="background:'+(s.status==='on'?'#e8f5e9':'#f5f5f5')+';color:'+(s.status==='on'?'#2e7d32':'#999')+'">'+s.name.charAt(0)+'</div><div class="li-content"><div class="li-title">'+s.name+'</div><div class="li-sub">'+s.role+' \u2014 '+s.dept+'</div></div><div class="li-right"><span class="status-dot '+(s.status==='on'?'active':'closed')+'" style="font-size:11px">'+(s.status==='on'?'On Duty':'Off Duty')+'</span><div class="li-time">'+s.shift+' Shift</div></div></div>';}).join('');}

function loadCriticalPatients(){var patients=[{name:'Mohan Lal Gupta',mrn:'MRN-10025',ward:'ICU Bed 3',condition:'\u{1F534} Hemorrhagic Stroke',doctor:'Dr. Negi'},{name:'Rekha Singh',mrn:'MRN-10034',ward:'ICU Bed 7',condition:'\u{1F534} MI \u2014 Post PTCA',doctor:'Dr. Sharma'},{name:'Baby Suresh',mrn:'MRN-10041',ward:'NICU Bed 2',condition:'\u{1F7E0} Premature 32 wks',doctor:'Dr. Verma'},{name:'Ajit Rawat',mrn:'MRN-10028',ward:'HDU Bed 1',condition:'\u{1F7E0} Respiratory Failure',doctor:'Dr. Bisht'}];var el=document.getElementById('criticalTable');if(!el)return;el.innerHTML=patients.map(function(p){return'<tr><td><div style="font-weight:700;font-size:12.5px">'+p.name+'</div><div style="font-size:10.5px;color:#6b7280">'+p.mrn+'</div></td><td>'+p.ward+'</td><td>'+p.condition+'</td><td>'+p.doctor+'</td><td><button class="btn btn-danger btn-xs" onclick="showToast(\'Patient Alert\',\'Opening record\',\'info\')">View</button></td></tr>';}).join('');}

function loadModuleStatus(){var modules=[{name:'Patient / ADT',icon:'\u{1F464}',status:'online',count:'247 active'},{name:'OPD',icon:'\u{1FA7A}',status:'online',count:'48 today'},{name:'IPD / Wards',icon:'\u{1F6CF}\ufe0f',status:'online',count:'127 admitted'},{name:'Pharmacy',icon:'\u{1F48A}',status:'online',count:'12 in queue'},{name:'Laboratory LIS',icon:'\u{1F9EA}',status:'online',count:'18 pending'},{name:'Radiology RIS',icon:'\u{1FA7B}',status:'partial',count:'11 worklist'},{name:'Billing',icon:'\u{1F4B3}',status:'online',count:'\u20b91.84L today'},{name:'Ambulance',icon:'\u{1F691}',status:'online',count:'3 active'},{name:'Blood Bank',icon:'\u{1FA78}',status:'critical',count:'\u26a0 O\u2212 low'},{name:'Inventory',icon:'\u{1F4E6}',status:'online',count:'12 low-stock'},{name:'HR & Payroll',icon:'\u{1F465}',status:'online',count:'5 pending'},{name:'Certificates',icon:'\u{1F4DC}',status:'online',count:'Ready'}];var colors={online:'#2e7d32',partial:'#f57c00',critical:'#c62828',offline:'#999'};var bgs={online:'#e8f5e9',partial:'#fff3e0',critical:'#ffebee',offline:'#f5f5f5'};var el=document.getElementById('moduleStatusGrid');if(!el)return;el.innerHTML=modules.map(function(m){return'<div style="display:flex;flex-direction:column;align-items:center;padding:14px 10px;background:'+bgs[m.status]+';border:1.5px solid '+colors[m.status]+'40;border-radius:12px;cursor:pointer;transition:all .2s;gap:5px" onmouseover="this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.transform=\'\'"><span style="font-size:24px">'+m.icon+'</span><span style="font-size:11.5px;font-weight:700;text-align:center">'+m.name+'</span><span style="font-size:10px;font-weight:700;color:'+colors[m.status]+'">'+m.status.toUpperCase()+'</span><span style="font-size:10px;color:#6b7280">'+m.count+'</span></div>';}).join('');}

function refreshDashboard(){loadDashboardData();if(typeof showToast==='function')showToast('Dashboard Refreshed','All data updated','success',2000);}

document.addEventListener('DOMContentLoaded',function(){
  loadDashboardData();
  initCharts();
  startLiveFeed();
  loadPendingTasks();
  loadStockAlerts();
  loadStaffOnDuty();
  loadCriticalPatients();
  loadModuleStatus();
  loadCriticalAlerts();
  setInterval(loadDashboardData,15000);
  setInterval(addActivityItem,6000);
});
</script>
@endpush
