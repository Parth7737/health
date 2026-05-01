<div id="rad-ris-panel-completed" class="rad-ris-panel">
    <div class="rad-ris-card">
        <div class="rad-ris-card-header">
            <h2 class="rad-ris-card-title"><i class="fa-solid fa-circle-check" style="color:#2e7d32"></i> Completed reports</h2>
        </div>
        <div class="rad-ris-card-body">
            <div class="rad-ris-worklist-filters">
                <div class="rad-ris-worklist-filters-row rad-ris-worklist-filters-row--controls">
                    <div class="rad-ris-filter-group rad-ris-filter-group--modality">
                        <label for="rad-ris-c-filter-modality">Modality</label>
                        <select id="rad-ris-c-filter-modality" class="form-control rad-ris-select2" data-placeholder="All modalities">
                            <option value="">All modalities</option>
                            @foreach ($modalities as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--status">
                        <label for="rad-ris-c-filter-status">Status</label>
                        <select id="rad-ris-c-filter-status" class="form-control rad-ris-select2">
                            <option value="">Completed (all)</option>
                            <option value="completed" selected>Completed</option>
                        </select>
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--priority">
                        <label for="rad-ris-c-filter-priority">Priority</label>
                        <select id="rad-ris-c-filter-priority" class="form-control rad-ris-select2">
                            <option value="">All priority</option>
                            <option value="STAT">STAT</option>
                            <option value="URGENT">Urgent</option>
                            <option value="ROUTINE">Routine</option>
                        </select>
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--date">
                        <label for="rad-ris-c-filter-date-from">From</label>
                        <input type="text" id="rad-ris-c-filter-date-from" class="form-control rad-ris-flatpickr diagnosis-date" placeholder="dd-mm-yyyy" autocomplete="off">
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--date">
                        <label for="rad-ris-c-filter-date-to">To</label>
                        <input type="text" id="rad-ris-c-filter-date-to" class="form-control rad-ris-flatpickr diagnosis-date" placeholder="dd-mm-yyyy" autocomplete="off">
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--actions">
                        <label class="rad-ris-filter-label-spacer" aria-hidden="true">Reset</label>
                        <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="rad-ris-c-clear-filters">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Reset filters
                        </button>
                    </div>
                </div>
                <div class="rad-ris-worklist-filters-row rad-ris-worklist-filters-row--search">
                    <label for="rad-ris-completed-search">Search</label>
                    <div class="rad-ris-search-input-row">
                        <div class="rad-ris-search-wrap">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <input type="search" id="rad-ris-completed-search" placeholder="Patient, MRN, order no…" autocomplete="off">
                        </div>
                        <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="rad-ris-c-clear-search"><i class="fa-solid fa-xmark"></i> Clear</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive mt-3">
                <table id="rad-ris-completed-table" class="display table-striped w-100" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Accession</th>
                            <th>Patient</th>
                            <th>Age / sex</th>
                            <th>Modality</th>
                            <th>Examination</th>
                            <th>Ordered by</th>
                            <th>Visit</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Reported</th>
                            <th>Print</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
