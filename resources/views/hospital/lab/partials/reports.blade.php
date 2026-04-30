<div id="reportsPane" class="lab-pane" hidden>
    <div class="lab-report-pane-head">
        <div class="lab-report-pane-title">📄 Lab Reports — Completed</div>
        <div class="lab-report-filter-bar">
            <input type="date" class="form-control lab-report-date-input" id="labReportDate" autocomplete="off">
            <button class="btn btn-secondary btn-sm lab-report-filter-btn" type="button" id="labReportFilterBtn">🔍 Filter</button>
        </div>
    </div>
    <div class="dt-ext table-responsive custom-scrollbar html-expert-table lab-report-table-shell">
        <table id="lab-reports-table" class="display table table-striped table-hover w-100 lab-reports-dt">
            <thead class="table-light">
                <tr>
                    <th>SAMPLE ID</th>
                    <th>PATIENT</th>
                    <th>TESTS</th>
                    <th>KEY RESULT</th>
                    <th>TIME</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
