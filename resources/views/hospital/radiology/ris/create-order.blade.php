<div class="modal-header">
    <div class="modal-title">🩻 New Radiology Order</div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="form-row cols-2">
        <div>
            <div class="form-group">
                <label class="form-label">Patient Search <span class="req">*</span></label>
                <div class="input-group">
                    <span class="input-addon">🔍</span>
                    <input class="form-control" id="rad_order_patient_search" placeholder="MRN / Name..." autocomplete="off" />
                    <div style="position:relative;width:100%">
                        <div id="radOrderPatientSearchResults" class="dropdown-menu patient-search-dropdown"></div>
                    </div>
                </div>
            </div>
            <div id="radOrderPatientChipContainer"></div>

            <div class="form-group mt-12">
                <label class="form-label">Ordered By</label>
                <select class="form-control" id="rad_order_doctor_staff_id" name="doctor_staff_id">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc['id'] }}">{{ $doc['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- <div class="form-group">
                <label class="form-label">Ward / OPD</label>
                <select class="form-control" id="rad_order_ward_or_opd" name="ward_or_opd">
                    <option value="OPD">OPD</option>
                    <option value="General Ward">General Ward</option>
                    <option value="ICU">ICU</option>
                    <option value="Emergency">Emergency</option>
                    <option value="Casualty">Casualty</option>
                </select>
            </div> -->

            <div class="form-group">
                <label class="form-label">Clinical Indication / History</label>
                <textarea class="form-control" id="rad_order_clinical_indication" rows="3" placeholder="Clinical history, indication for study..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Previous Relevant Imaging</label>
                <select class="form-control" id="rad_order_previous_relevant_imaging">
                    <option value="">Select Patient First</option>
                </select>
            </div>
        </div>

        <div>
            <div class="form-group">
                <label class="form-label">Modality</label>
                <select class="form-control" id="rad_order_modality">
                    <option value="">Select Modality</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Examination <span class="req">*</span></label>
                <select class="form-control" id="rad_order_test_id">
                    <option value="">Select Modality First</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Priority</label>
                <select class="form-control" id="rad_order_priority">
                    <option value="Routine">Routine</option>
                    <option value="Urgent">Urgent</option>
                    <option value="STAT">STAT</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Contrast Required</label>
                <select class="form-control" id="rad_order_contrast_required">
                    <option value="No">No</option>
                    <option value="IV Contrast">IV Contrast</option>
                    <option value="Oral Contrast">Oral Contrast</option>
                    <option value="Both">Both</option>
                </select>
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label">Scheduled Date</label>
                    <input type="date" class="form-control" id="rad_order_scheduled_date" value="{{ now()->toDateString() }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Scheduled Time</label>
                    <input type="time" class="form-control" id="rad_order_scheduled_time" value="10:00">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Radiation Consent</label>
                <select class="form-control" id="rad_order_radiation_consent">
                    <option value="Obtained">Obtained</option>
                    <option value="Not Required">Not Required</option>
                    <option value="Refused">Refused</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Pregnancy Status (F)</label>
                <select class="form-control" id="rad_order_pregnancy_status">
                    <option value="N/A">N/A</option>
                    <option value="Not Pregnant">Not Pregnant</option>
                    <option value="Unknown">Unknown</option>
                </select>
            </div>

            <div style="background:var(--success-light);border:1px solid rgba(46,125,50,.2);border-radius:8px;padding:10px;margin-top:8px">
                <div class="fw-700 fs-12 mb-4">Selected Examination</div>
                <div id="radOrderSelectedSummary" class="text-muted fs-12">No exam selected</div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-success" id="btnSaveRadiologyManualOrder">🩻 Place Order</button>
</div>

<script>
(function() {
    const RAD_MODAL = {
        testsUrl: @json($routes['tests'] ?? ''),
        saveUrl: @json($routes['save'] ?? ''),
        patientsUrl: @json($routes['patients'] ?? ''),
        previousUrl: @json($routes['previous'] ?? ''),
        csrf: @json(csrf_token()),
    };

    let tests = [];
    let patientSearchTimer;

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function loadTests() {
        if (!RAD_MODAL.testsUrl) return;
        $.get(RAD_MODAL.testsUrl, function(res) {
            tests = (res && res.data) ? res.data : [];
            renderModalities();
            renderExams();
        }).fail(function() {
            sendmsg('error', 'Could not load radiology tests.');
        });
    }

    function renderModalities() {
        const seen = {};
        const list = [];
        tests.forEach(function(t) {
            const mod = (t.category_name || 'Uncategorized').trim() || 'Uncategorized';
            const key = mod.toLowerCase();
            if (!seen[key]) {
                seen[key] = true;
                list.push(mod);
            }
        });
        list.sort();
        let html = '<option value="">Select Modality</option>';
        list.forEach(function(m) {
            html += '<option value="' + escapeHtml(m) + '">' + escapeHtml(m) + '</option>';
        });
        $('#rad_order_modality').html(html);
    }

    function renderExams() {
        const mod = ($('#rad_order_modality').val() || '').trim();
        if (!mod) {
            $('#rad_order_test_id').html('<option value="">Select Modality First</option>');
            updateSummary();
            return;
        }
        const rows = tests.filter(function(t) {
            return ((t.category_name || 'Uncategorized').trim() || 'Uncategorized') === mod;
        });
        if (!rows.length) {
            $('#rad_order_test_id').html('<option value="">No examinations available</option>');
            updateSummary();
            return;
        }
        let html = '<option value="">Select Examination</option>';
        rows.forEach(function(t) {
            const c = parseFloat(t.standard_charge || 0);
            const code = t.test_code ? ' (' + t.test_code + ')' : '';
            const charge = c > 0 ? ' - INR ' + c.toFixed(2) : '';
            html += '<option value="' + t.id + '">' + escapeHtml((t.test_name || '') + code + charge) + '</option>';
        });
        $('#rad_order_test_id').html(html);
        updateSummary();
    }

    function updateSummary() {
        const id = parseInt($('#rad_order_test_id').val() || '0', 10);
        const t = tests.find(function(x) { return parseInt(x.id, 10) === id; });
        if (!t) {
            $('#radOrderSelectedSummary').text('No exam selected');
            loadPreviousImagingOptions();
            return;
        }
        const charge = parseFloat(t.standard_charge || 0);
        const mod = t.category_name || 'Uncategorized';
        $('#radOrderSelectedSummary').html(
            '<div><b>' + escapeHtml(t.test_name || '') + '</b></div>' +
            '<div class="mt-4">Modality: ' + escapeHtml(mod) + '</div>' +
            '<div class="mt-4">Charge: INR ' + (isNaN(charge) ? '0.00' : charge.toFixed(2)) + '</div>'
        );
        loadPreviousImagingOptions();
    }

    function loadPreviousImagingOptions() {
        const patientId = $('#rad_order_patient_id').val();
        const testId = $('#rad_order_test_id').val();
        const $select = $('#rad_order_previous_relevant_imaging');

        if (!patientId) {
            $select.html('<option value="">Select Patient First</option>');
            return;
        }
        if (!RAD_MODAL.previousUrl) {
            $select.html('<option value="">No Previous Imaging Found</option>');
            return;
        }

        $select.html('<option value="">Loading...</option>');

        $.ajax({
            url: RAD_MODAL.previousUrl,
            method: 'GET',
            data: {
                patient_id: patientId,
                radiology_test_id: testId || ''
            },
            success: function(res) {
                const rows = (res && res.data) ? res.data : [];
                let html = '<option value="">Select Previous Imaging (Optional)</option>';
                if (!rows.length) {
                    html = '<option value="">No Previous Imaging Found</option>';
                } else {
                    rows.forEach(function(row) {
                        html += '<option value="' + escapeHtml(row.value || '') + '">' + escapeHtml(row.label || '') + '</option>';
                    });
                }
                $select.html(html);
            },
            error: function() {
                $select.html('<option value="">No Previous Imaging Found</option>');
            }
        });
    }

    function bindPatientSearch() {
        const $search = $('#rad_order_patient_search');
        const $dropdown = $('#radOrderPatientSearchResults');

        $search.off('input.radOrderPatient').on('input.radOrderPatient', function() {
            clearTimeout(patientSearchTimer);
            const q = $(this).val().trim();
            if (q.length < 2) {
                $dropdown.hide();
                return;
            }
            patientSearchTimer = setTimeout(function() {
                $.ajax({
                    url: RAD_MODAL.patientsUrl,
                    method: 'GET',
                    data: { q: q },
                    success: function(res) {
                        let html = '';
                        if (res && res.length) {
                            res.forEach(function(p) {
                                html += '<a href="#" class="dropdown-item rad-order-patient-item" ' +
                                    'data-id="' + p.id + '" ' +
                                    'data-name="' + escapeHtml(p.name || '') + '" ' +
                                    'data-mrn="' + escapeHtml(p.mrn || p.patient_id || '') + '" ' +
                                    'data-meta="' + escapeHtml((p.age_sex || '') + ' | ' + (p.blood_group || '') + ' | ' + (p.phone || '')) + '">' +
                                    '<div><b>' + escapeHtml(p.name || '') + '</b> <span class="text-muted">(' + escapeHtml(p.mrn || p.patient_id || '') + ')</span></div>' +
                                    '<div class="fs-12 text-muted">' + escapeHtml((p.age_sex || '') + ' | ' + (p.blood_group || '') + ' | ' + (p.phone || '')) + '</div>' +
                                    '</a>';
                            });
                        } else {
                            html = '<div class="dropdown-item text-muted">No patient found</div>';
                        }
                        $dropdown.html(html).show();
                        $dropdown.css({ display: 'block', left: 0, top: $search.outerHeight() + 2, width: $search.outerWidth() });
                    },
                    error: function() {
                        $dropdown.html('<div class="dropdown-item text-danger">Error searching</div>').show();
                    }
                });
            }, 300);
        });

        $(document).off('click.radOrderPatientItem', '.rad-order-patient-item').on('click.radOrderPatientItem', '.rad-order-patient-item', function(e) {
            e.preventDefault();
            const $el = $(this);
            const pid = $el.data('id');
            const pname = $el.data('name');
            const mrn = $el.data('mrn');
            const meta = $el.data('meta');
            $dropdown.hide();
            $('#radOrderPatientChipContainer').html(
                '<div class="patient-chip">' +
                '<input type="hidden" id="rad_order_patient_id" value="' + pid + '" />' +
                '<div class="patient-chip-avatar">' + escapeHtml((pname || '?').charAt(0)) + '</div>' +
                '<div class="patient-chip-info">' +
                '<div class="patient-chip-name">' + escapeHtml(pname) + '</div>' +
                '<div class="patient-chip-meta">' + escapeHtml(mrn) + ' | ' + escapeHtml(meta) + '</div>' +
                '</div></div>'
            );
            $search.val(mrn);
            loadPreviousImagingOptions();
        });

        $(document).off('click.radOrderPatientOutside').on('click.radOrderPatientOutside', function(e) {
            if (!$(e.target).closest('#rad_order_patient_search').length && !$(e.target).closest('#radOrderPatientSearchResults').length) {
                $dropdown.hide();
            }
        });
    }

    function saveManualOrder() {
        const patientId = $('#rad_order_patient_id').val();
        if (!patientId) {
            sendmsg('error', 'Please select a patient.');
            return;
        }
        const testId = $('#rad_order_test_id').val();
        if (!testId) {
            sendmsg('error', 'Please select an examination.');
            return;
        }

        loader('show');
        $.ajax({
            url: RAD_MODAL.saveUrl,
            method: 'POST',
            data: {
                _token: RAD_MODAL.csrf,
                patient_id: patientId,
                doctor_staff_id: $('#rad_order_doctor_staff_id').val() || null,
                ward_or_opd: $('#rad_order_ward_or_opd').val() || '',
                modality: $('#rad_order_modality').val() || '',
                radiology_test_id: testId,
                priority: $('#rad_order_priority').val() || 'Routine',
                contrast_required: $('#rad_order_contrast_required').val() || 'No',
                clinical_indication: $('#rad_order_clinical_indication').val() || '',
                previous_relevant_imaging: $('#rad_order_previous_relevant_imaging').val() || '',
                scheduled_date: $('#rad_order_scheduled_date').val() || '',
                scheduled_time: $('#rad_order_scheduled_time').val() || '',
                radiation_consent: $('#rad_order_radiation_consent').val() || 'Obtained',
                pregnancy_status: $('#rad_order_pregnancy_status').val() || 'N/A'
            },
            success: function(res) {
                loader('hide');
                $('.add-datamodal').modal('hide');
                sendmsg('success', res.message || 'Radiology order created successfully.');
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#rad-ris-worklist-table')) {
                    $('#rad-ris-worklist-table').DataTable().ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.length) {
                    const msg = xhr.responseJSON.errors.map(function(e) { return e.message; }).join('<br>');
                    sendmsg('error', msg);
                    return;
                }
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to create radiology order.');
            }
        });
    }

    $(function() {
        loadTests();
        bindPatientSearch();
        $('#rad_order_modality').off('change.radOrder').on('change.radOrder', renderExams);
        $('#rad_order_test_id').off('change.radOrder').on('change.radOrder', updateSummary);
        $('#btnSaveRadiologyManualOrder').off('click.radOrder').on('click.radOrder', saveManualOrder);
    });
})();
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
.patient-search-dropdown .dropdown-item:hover,
.patient-search-dropdown .dropdown-item.active {
    background: #f5f5f5;
}
</style>
