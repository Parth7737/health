
<div class="modal-header">
    <div class="modal-title">🧫 Sample Registration</div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="form-row cols-2">
    <div>
        <div class="form-group"><label class="form-label">Patient Search <span class="req">*</span></label>
            <div class="input-group">
                <span class="input-addon">🔍</span>
                <input class="form-control patient_search" id="patient_search" placeholder="MRN / Name…" autocomplete="off" />
                <div style="position:relative;width:100%">
                    <div id="patientSearchResults" class="dropdown-menu patient-search-dropdown"></div>
                </div>
            </div>
        </div>
        <div id="patientChipContainer"></div>
        <div class="form-group mt-12"><label class="form-label">Ordered By</label>
            <select class="form-control doctor_id" id="doctor_id" name="doctor_id">
                <option value="">Select</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc['id'] }}">{{ $doc['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group"><label class="form-label">Clinical Details / Diagnosis</label>
        <textarea class="form-control" rows="2" placeholder="Hypertension, DM follow-up…"></textarea>
        </div>
        <div class="form-group"><label class="form-label">Priority</label>
        <select class="form-control" id="samplePriority"><option>Routine</option><option>Urgent</option><option>STAT</option></select>
        </div>
    </div>
    <div>
        <div class="form-group"><label class="form-label">Select Tests <span class="req">*</span></label>
        <div style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:8px">
            <div style="margin-bottom:8px"><input type="text" class="form-control" style="font-size:11px" placeholder="Search tests…"/></div>
            <div id="testCheckboxList"></div>
        </div>
        </div>
        <div class="form-group mt-8"><label class="form-label">Sample Type</label>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
            <label class="form-check"><input type="checkbox" checked/><span class="form-check-label">🩸 Blood (EDTA)</span></label>
            <label class="form-check"><input type="checkbox"/><span class="form-check-label">🩸 Blood (Plain)</span></label>
            <label class="form-check"><input type="checkbox"/><span class="form-check-label">💛 Urine</span></label>
            <label class="form-check"><input type="checkbox"/><span class="form-check-label">💩 Stool</span></label>
            <label class="form-check"><input type="checkbox"/><span class="form-check-label">🫁 Sputum</span></label>
            <label class="form-check"><input type="checkbox"/><span class="form-check-label">🧪 CSF</span></label>
        </div>
        </div>
        <!-- Selected Tests Cost -->
        <div style="background:var(--success-light);border:1px solid rgba(46,125,50,.2);border-radius:8px;padding:10px;margin-top:8px">
        <div class="fw-700 fs-12 mb-4">Selected Tests Summary</div>
        <div id="selectedTestsSummary" class="text-muted fs-12">No tests selected</div>
        </div>
    </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-success" onclick="registerSample()">🧫 Register & Print Barcode</button>
</div>
<script>
$(function() {
    let searchTimeout;
    const $search = $('#patient_search');
    const $dropdown = $('#patientSearchResults');
  
    $search.on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        if (query.length < 2) {
            $dropdown.hide();
            return;
        }
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("hospital.patient-management.search-patients") }}',
                method: 'GET',
                data: { q: query },
                success: function(res) {
                    let html = '';
                    if (res && res.length) {
                        res.forEach(function(p) {
                            html += `<a href="#" class="dropdown-item patient-result-item" data-patient='${JSON.stringify(p)}'>
                                <div><b>${p.name}</b> <span class="text-muted">(${p.mrn})</span></div>
                                <div class="fs-12 text-muted">${p.age_sex} | ${p.blood_group} | ${p.phone}</div>
                            </a>`;
                        });
                    } else {
                        html = '<div class="dropdown-item text-muted">No patient found</div>';
                    }
                    $dropdown.html(html).show();
                    // Position dropdown below input
                    const inputRect = $search[0].getBoundingClientRect();
                    $dropdown.css({
                        display: 'block',
                        left: 0,
                        top: $search.outerHeight() + 2,
                        width: $search.outerWidth()
                    });
                },
                error: function() {
                    $dropdown.html('<div class="dropdown-item text-danger">Error searching</div>').show();
                }
            });
        }, 300);
    });

    $(document).on('click', '.patient-result-item', function(e) {
        e.preventDefault();
        const p = $(this).data('patient');
        $dropdown.hide();
        // Render patient chip
        let chip = `<div class="patient-chip">
            <input type="hidden" value="${p.id}" name="patient_id"/>
            <div class="patient-chip-avatar">${p.name.charAt(0)}</div>
            <div class="patient-chip-info">
                <div class="patient-chip-name">${p.name}</div>
                <div class="patient-chip-meta">${p.mrn} | ${p.age_sex} | ${p.blood_group} | ${p.phone}</div>
            </div>
        </div>`;
        $('#patientChipContainer').html(chip);
        $search.val(p.mrn);
    });

    // Hide dropdown on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.input-group').length) {
            $dropdown.hide();
        }
    });
});
</script>
<style>
.patient-search-dropdown {
    position: absolute !important;
    left: 0;
    top: 100%;
    width: 100%;
    min-width: 180px;
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid #e0e0e0;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    background: #fff;
    z-index: 1050;
    padding: 0;
}
.patient-search-dropdown .dropdown-item {
    padding: 8px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    transition: background 0.15s;
}
.patient-search-dropdown .dropdown-item:last-child {
    border-bottom: none;
}
.patient-search-dropdown .dropdown-item:hover, .patient-search-dropdown .dropdown-item.active {
    background: #f5f5f5;
}
</style>
</script>