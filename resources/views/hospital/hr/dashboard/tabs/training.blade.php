<div id="hrx-panel-training">
    <div class="hrx-card">
        <div class="hrx-card-header">
            <span class="hrx-card-title"><i class="fa fa-graduation-cap" style="color:#4a148c"></i> Training &amp; CPD Programmes</span>
            <div class="hrx-actions">
                @can('edit-staff')
                    <button type="button" class="hrx-btn-lite text-white" id="hrxTrainingSchedule" style="background-color:#4a148c">
                        <i class="fa fa-calendar-plus-o"></i> Schedule training
                    </button>
                @endcan
                <button type="button" class="hrx-btn-lite btn-info text-white" id="hrxTrainingExport"><i class="fa fa-download"></i> Export</button>
            </div>
        </div>
        <div class="hrx-toolbar" style="padding:10px 12px 0">
            <div class="hrx-filters">
                <input type="text" id="hrxTrainingSearch" class="hrx-input" placeholder="Search programme / category / trainer">
                <select id="hrxTrainingStatus" class="hrx-select">
                    <option value="">All status</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
        <div class="hrx-table-wrap" style="padding:10px 12px 12px">
            <table id="hrxTrainingTable" class="hrx-table display table-striped" style="width:100%">
                <thead>
                <tr>
                    <th style="display:none">ID</th>
                    <th>Programme</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Trainer</th>
                    <th>Participants</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
