<div id="sampleQueuePane" class="lab-pane">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:8px">
        <div class="input-group" style="max-width:280px">
            <span class="input-addon">🔍</span>
            <input class="form-control" id="labSearch" placeholder="Search patient, sample ID, MRN..."/>
        </div>
        <div class="d-flex gap-8 flex-wrap justify-content-end" style="margin-left:auto">
            <select id="filter-category" class="form-control" style="width:180px">
                <option value="">All Category</option>
            </select>
            <select id="filter-status" class="form-control" style="width:160px">
                <option value="">All Active Status</option>
                <option value="ordered">Ordered</option>
                <option value="sample_collected">Sample Collected</option>
                <option value="in_progress">In Progress</option>
            </select>
            <input type="text" id="filter-date-from" class="form-control diagnosis-date" style="width:130px" placeholder="From Date">
            <input type="text" id="filter-date-to" class="form-control diagnosis-date" style="width:130px" placeholder="To Date">
            <button type="button" id="clear-filters" class="btn btn-light btn-sm">Clear</button>
        </div>
    </div>

    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
        <table id="xin-table" class="display table-striped w-100">
            <thead class="table-light">
                <tr>
                    <th>Sample ID</th>
                    <th>Patient</th>
                    <th>Tests Ordered</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Ordered By</th>
                    <th>Collected</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
