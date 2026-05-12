<div id="hrx-panel-recruitment">
    <div class="hrx-card">
        <div class="hrx-card-header">
            <span class="hrx-card-title"><i class="fas fa-user-plus" style="color:#2e7d32"></i> Open Positions & Recruitment</span>
            <div class="hrx-actions">
                @can('create-hr-recruitment')
                <button type="button" class="hrx-btn-lite text-white" id="hrxRecruitmentPost" style="background-color:#2e7d32"><i class="fa fa-plus"></i> Post Vacancy</button>
                @endcan
                <button type="button" class="hrx-btn-lite btn-info  text-white" id="hrxRecruitmentExport"><i class="fa fa-download"></i> Export</button>
            </div>
        </div>
        <div class="hrx-toolbar" style="padding:10px 12px 0">
            <div class="hrx-filters">
                <input type="text" id="hrxRecruitmentSearch" class="hrx-input" placeholder="Search position/designation">
                <select id="hrxRecruitmentStatus" class="hrx-select">
                    <option value="">All Status</option>
                    <option value="Open">Open</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Closed">Closed</option>
                </select>
            </div>
        </div>
        <div class="hrx-table-wrap" style="padding:10px 12px 12px">
            <table id="hrxRecruitmentTable" class="hrx-table display table-striped" style="width:100%">
                <thead>
                <tr>
                    <th style="display:none">ID</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Required</th>
                    <th>Applicants</th>
                    <th>Shortlisted</th>
                    <th>Status</th>
                    <th>Open Period</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
