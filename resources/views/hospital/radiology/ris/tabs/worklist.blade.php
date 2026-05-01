<div id="rad-ris-panel-worklist" class="rad-ris-panel active">
    <div class="rad-ris-card">
        <div class="rad-ris-card-header">
            <h2 class="rad-ris-card-title"><i class="fa-solid fa-list-ul" style="color:#1565c0"></i> Radiology Worklist</h2>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="rad-ris-btn rad-ris-btn-success rad-ris-btn-sm" id="rad-ris-btn-new-order"><i class="fa-solid fa-plus"></i> New Order</button>
                <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="rad-ris-btn-export"><i class="fa-solid fa-download"></i> Export</button>
            </div>
        </div>
        <div class="rad-ris-card-body">
            <div class="rad-ris-worklist-filters">
                <div class="rad-ris-worklist-filters-row rad-ris-worklist-filters-row--controls">
                    <div class="rad-ris-filter-group rad-ris-filter-group--modality">
                        <label for="rad-ris-filter-modality">Modality</label>
                        <select id="rad-ris-filter-modality" class="form-control rad-ris-select2" data-placeholder="All modalities">
                            <option value="">All modalities</option>
                            @foreach ($modalities as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--status">
                        <label for="rad-ris-filter-status">Status</label>
                        <select id="rad-ris-filter-status" class="form-control rad-ris-select2">
                            <option value="">All status</option>
                            <option value="ordered">Ordered</option>
                            <option value="examination">Examination</option>
                            <option value="in_progress">In progress (legacy)</option>
                        </select>
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--priority">
                        <label for="rad-ris-filter-priority">Priority</label>
                        <select id="rad-ris-filter-priority" class="form-control rad-ris-select2">
                            <option value="">All priority</option>
                            <option value="STAT">STAT</option>
                            <option value="URGENT">Urgent</option>
                            <option value="ROUTINE">Routine</option>
                        </select>
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--date">
                        <label for="rad-ris-filter-date-from">From</label>
                        <input type="text" id="rad-ris-filter-date-from" class="form-control rad-ris-flatpickr diagnosis-date" placeholder="dd-mm-yyyy" autocomplete="off">
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--date">
                        <label for="rad-ris-filter-date-to">To</label>
                        <input type="text" id="rad-ris-filter-date-to" class="form-control rad-ris-flatpickr diagnosis-date" placeholder="dd-mm-yyyy" autocomplete="off">
                    </div>
                    <div class="rad-ris-filter-group rad-ris-filter-group--actions">
                        <label class="rad-ris-filter-label-spacer" aria-hidden="true">Reset</label>
                        <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="rad-ris-clear-filters" title="Clear modality, status, priority & dates">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Reset filters
                        </button>
                    </div>
                </div>
                <div class="rad-ris-worklist-filters-row rad-ris-worklist-filters-row--search">
                    <label for="rad-ris-worklist-search">Search</label>
                    <div class="rad-ris-search-input-row">
                        <div class="rad-ris-search-wrap">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <input type="search" id="rad-ris-worklist-search" name="rad_ris_worklist_search" placeholder="Patient name, MRN, order no, accession, test…" autocomplete="off" aria-label="Search worklist">
                        </div>
                        <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm" id="rad-ris-clear-search" title="Clear search">
                            <i class="fa-solid fa-xmark"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive rad-ris-worklist-table-wrap mt-3">
                <table id="rad-ris-worklist-table" class="display table-striped w-100" style="width:100%">
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
                            <th>Time</th>
                            <th>Workflow</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
