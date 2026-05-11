<div id="hrx-panel-recruitment">
    <div class="hrx-toolbar">
        <div class="hrx-filters">
            <input type="text" id="hrxRecruitmentSearch" class="hrx-input" placeholder="Search position">
            <select id="hrxRecruitmentStatus" class="hrx-select">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
                <option value="onhold">On Hold</option>
            </select>
        </div>
        <div class="hrx-actions">
            <button type="button" class="hrx-btn-lite" id="hrxRecruitmentPost"><i class="fa fa-plus"></i>Post Vacancy</button>
            <button type="button" class="hrx-btn-lite" id="hrxRecruitmentExport"><i class="fa fa-download"></i>Export</button>
        </div>
    </div>
    <div class="hrx-card">
        <div class="hrx-card-header">
            <div class="hrx-card-title"><i class="fa fa-user-plus" style="color:#2e7d32"></i>Open Positions</div>
        </div>
        <div class="hrx-table-wrap">
            <table class="hrx-table">
                <thead>
                <tr>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Required</th>
                    <th>Applicants</th>
                    <th>Shortlisted</th>
                    <th>Status</th>
                    <th>Last Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($vacancies as $row)
                    <tr class="hrx-recruitment-row" data-title="{{ strtolower($row->title) }}" data-status="{{ strtolower($row->status) }}">
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->department->name ?? 'General' }}</td>
                        <td>{{ $row->required_positions }}</td>
                        <td>{{ $row->applicants }}</td>
                        <td>{{ $row->shortlisted }}</td>
                        <td><span class="hrx-badge {{ strtolower($row->status) === 'open' ? 'orange' : 'green' }}">{{ $row->status }}</span></td>
                        <td>{{ optional($row->last_date)->format('d M Y') ?: '-' }}</td>
                        <td><button type="button" class="hrx-btn-lite hrx-recruitment-view" data-title="{{ $row->title }}"><i class="fa fa-eye"></i></button></td>
                    </tr>
                @empty
                    <tr><td colspan="8">No recruitment vacancies found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
