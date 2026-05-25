@extends('layouts.state-admin.app')
@section('title', 'Facility Onboarding Dashboard')
@section('content')
    <!-- ════════════════ TRACKER VIEW ════════════════ -->
    <div id="view-tracker">
        <div class="section-hdr"
            style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div style="font-size:18px;font-weight:800;color:#fff;display:flex;align-items:center;gap:10px">
                <i class="fas fa-tasks" style="color:#60a5fa"></i>Onboarding Applications Tracker</div>
            <button class="tb-btn primary" onclick="switchView('wizard')"><i class="fas fa-plus"></i> New
                Application</button>
        </div>
        <div class="tracker-grid" id="trackerGrid"></div>
        <div class="card">
            <div class="card-hdr">
                <div class="card-title"><i class="fas fa-table"></i>All Applications</div>
            </div>
            <div class="card-body">
                <div style="overflow-x:auto">
                    <table>
                        <thead>
                            <tr>
                                <th>App ID</th>
                                <th>Facility</th>
                                <th>Type</th>
                                <th>District</th>
                                <th>Submitted</th>
                                <th>DHO Status</th>
                                <th>Inspection</th>
                                <th>AB Status</th>
                                <th>HMIS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="trackerTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    
    <script>

    const TRACKER_DATA = [{
            id: 'ONB-UTT-2025-00121',
            name: 'CHC Munsiyari',
            type: 'CHC',
            dist: 'Pithoragarh',
            date: '2025-03-28',
            dho: 'Approved',
            insp: 'Scheduled',
            ab: 'Pending',
            hmis: 'Pending'
        },
        {
            id: 'ONB-UTT-2025-00122',
            name: 'PHC Joshimath',
            type: 'PHC',
            dist: 'Chamoli',
            date: '2025-04-01',
            dho: 'Under Review',
            insp: 'Not Started',
            ab: 'NA',
            hmis: 'Pending'
        },
        {
            id: 'ONB-UTT-2025-00123',
            name: 'Disha Private Hospital',
            type: 'Private Hospital',
            dist: 'Dehradun',
            date: '2025-04-05',
            dho: 'Approved',
            insp: 'Done',
            ab: 'Approved',
            hmis: 'Active'
        },
        {
            id: 'ONB-UTT-2025-00120',
            name: 'Sub-Centre Kanalichhina',
            type: 'Sub-Centre',
            dist: 'Almora',
            date: '2025-03-20',
            dho: 'Approved',
            insp: 'Done',
            ab: 'NA',
            hmis: 'Active'
        },
    ];

    function renderTracker() {
        const summary = [{
            label: 'Submitted',
            count: 4,
            color: '#60a5fa'
        }, {
            label: 'Under Review',
            count: 2,
            color: '#ffb74d'
        }, {
            label: 'Approved',
            count: 1,
            color: '#81c784'
        }, {
            label: 'HMIS Active',
            count: 2,
            color: '#4db6ac'
        }];
        document.getElementById('trackerGrid').innerHTML = summary.map(s => `
    <div class="tracker-card">
      <div class="tc-status"><div class="ts-dot" style="background:${s.color}"></div><div class="ts-label" style="color:${s.color}">${s.label}</div></div>
      <div class="tc-name" style="font-size:28px;font-weight:900;color:${s.color}">${s.count}</div>
      <div class="tc-meta">Applications this month</div>
    </div>`).join('');

        const tbody = document.getElementById('trackerTableBody');
        tbody.innerHTML = TRACKER_DATA.map(r => {
            const statusMap = {
                'Approved': 'badge-green',
                'Under Review': 'badge-orange',
                'Not Started': 'badge-gray',
                'NA': 'badge-gray',
                'Scheduled': 'badge-blue',
                'Done': 'badge-teal',
                'Pending': 'badge-orange',
                'Active': 'badge-teal'
            };
            return `<tr>
      <td><span class="badge badge-blue">${r.id}</span></td>
      <td><span class="strong">${r.name}</span></td>
      <td>${r.type}</td>
      <td>${r.dist}</td>
      <td>${r.date}</td>
      <td><span class="badge ${statusMap[r.dho]||'badge-gray'}">${r.dho}</span></td>
      <td><span class="badge ${statusMap[r.insp]||'badge-gray'}">${r.insp}</span></td>
      <td><span class="badge ${statusMap[r.ab]||'badge-gray'}">${r.ab}</span></td>
      <td><span class="badge ${statusMap[r.hmis]||'badge-gray'}">${r.hmis}</span></td>
      <td><button class="tb-btn" style="font-size:11px;padding:4px 10px">View</button></td>
    </tr>`;
        }).join('');
    }
    </script>
@endpush
