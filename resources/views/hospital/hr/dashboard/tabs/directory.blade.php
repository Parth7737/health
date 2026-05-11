<div id="hrx-panel-directory">
    <div class="hrx-card">
        <div class="hrx-card-header">
            <div class="hrx-card-title"><i class="fa fa-id-card" style="color:#4a148c"></i>Staff Directory</div>
            <div class="hrx-actions">
                
                <div class="hrx-filters">
                    <input type="text" id="hrxDirectorySearch" class="hrx-input" placeholder="Search name/email/ID">
                    <select id="hrxDirectoryDepartment" class="hrx-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <select id="hrxDirectoryStatus" class="hrx-select">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                @can('create-staff')
                    <button type="button" class="btn btn-primary btn-sm hr-open-modal" data-modal-type="add-staff"><i class="fa fa-plus"></i></button>
                @endcan
                <div class="hrx-filters" style="margin-right:4px;">
                    <button type="button" class="hrx-btn-lite hrx-directory-view-toggle active" data-view="grid" id="hrxDirectoryGridToggle"><i class="fa fa-th-large"></i></button>
                    <button type="button" class="hrx-btn-lite hrx-directory-view-toggle" data-view="list" id="hrxDirectoryListToggle"><i class="fa fa-list"></i></button>
                </div>
            </div>
        </div>
        <div class="hrx-card-body">
            <div id="hrxDirectoryGridSection">
                <div class="hrx-staff-grid" id="hrxDirectoryCards">
                    @include('hospital.hr.dashboard.tabs.partials.directory-cards', ['staff' => $staff, 'showEmpty' => true])
                </div>
                <div style="padding-top:14px;">
                    <div class="d-flex flex-column align-items-center" style="gap:8px;">
                        <div id="hrxDirectoryCounter" style="font-size:12px;color:#5a7894;">
                            Showing {{ (int) ($shownCount ?? count($staff)) }} of {{ (int) ($totalCount ?? count($staff)) }} staff
                        </div>
                        <button type="button" class="hrx-btn-lite" id="hrxDirectoryLoadMore" data-next-page="{{ $nextPage ?? 2 }}" style="{{ !empty($hasMore) ? '' : 'display:none;' }}">
                            <i class="fa fa-chevron-down"></i>Load More
                        </button>
                    </div>
                </div>
            </div>
            <div id="hrxDirectoryListSection" style="display:none;">
                <div class="hrx-table-wrap">
                    <table class="hrx-table" id="hrxDirectoryListTable" style="width:100%;">
                        <thead>
                        <tr>
                            <th>Emp ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
