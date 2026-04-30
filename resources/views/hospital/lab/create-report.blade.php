
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
            <select class="form-control doctor_id" id="doctor_staff_id" name="doctor_staff_id">
                <option value="">Select</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc['id'] }}">{{ $doc['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group"><label class="form-label">Clinical Details / Diagnosis</label>
        <textarea class="form-control" id="clinical_notes" name="clinical_notes" rows="2" placeholder="Hypertension, DM follow-up…"></textarea>
        </div>
        <div class="form-group"><label class="form-label">Priority</label>
        <select class="form-control" id="samplePriority" name="priority">
            <option value="Routine">Routine</option>
            <option value="Urgent">Urgent</option>
            <option value="STAT">STAT</option>
        </select>
        </div>
    </div>
    <div>
        <div class="form-group"><label class="form-label">Select Tests <span class="req">*</span></label>
        <div style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:8px">
            <div style="margin-bottom:8px"><input type="text" class="form-control" id="pathologyTestSearch" style="font-size:11px" placeholder="Search tests…"/></div>
            <div id="testCheckboxList"></div>
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
    <button type="button" class="btn btn-success" id="btnRegisterWalkInSample">🧫 Register & Print Barcode</button>
</div>
<script>
(function() {
    const LAB_WALKIN = {
        testsUrl: @json($routes['tests'] ?? ''),
        saveUrl: @json($routes['save'] ?? ''),
        csrf: @json(csrf_token()),
    };
    let pathologyTests = [];
    let searchTimer;

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function renderTestList(filter) {
        const q = (filter || '').trim().toLowerCase();
        const $list = $('#testCheckboxList');
        let rows = pathologyTests;
        if (q.length) {
            rows = pathologyTests.filter(function(t) {
                const hay = (t.test_name + ' ' + (t.test_code || '') + ' ' + (t.category_name || '')).toLowerCase();
                return hay.indexOf(q) !== -1;
            });
        }
        if (!rows.length) {
            $list.html('<div class="text-muted fs-12 p-8">No tests match. Adjust search or add tests in Settings → Pathology.</div>');
            updateSummary();
            return;
        }
        let html = '';
        rows.forEach(function(t) {
            const cat = t.category_name ? '<span class="text-muted fs-11"> · ' + escapeHtml(t.category_name) + '</span>' : '';
            const charge = typeof t.standard_charge !== 'undefined' ? parseFloat(t.standard_charge) : 0;
            const chargeLabel = charge > 0 ? ' — ₹' + charge.toFixed(2) : '';
            html += '<label class="form-check d-block mb-4 pathology-test-row" style="cursor:pointer" data-test-name="' + escapeHtml(t.test_name) + '" data-charge="' + charge + '" data-sample-type="' + escapeHtml(t.sample_type || '') + '">';
            html += '<input type="checkbox" class="pathology-test-cb" name="pathology_test_ids[]" value="' + t.id + '"/> ';
            html += '<span class="form-check-label">' + escapeHtml(t.test_name) + cat + chargeLabel + '</span>';
            html += '</label>';
        });
        $list.html(html);
        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        const names = [];
        const uniqueSampleTypes = [];
        const sampleTypeSeen = {};
        $('.pathology-test-cb:checked').each(function() {
            const $row = $(this).closest('.pathology-test-row');
            total += parseFloat($row.data('charge')) || 0;
            names.push($row.data('test-name'));

            const rawSampleType = String($row.data('sample-type') || '').trim();
            if (!rawSampleType) {
                return;
            }
            rawSampleType.split('|').forEach(function(part) {
                const value = String(part || '').trim();
                if (!value) {
                    return;
                }
                const key = value.toLowerCase();
                if (!sampleTypeSeen[key]) {
                    sampleTypeSeen[key] = true;
                    uniqueSampleTypes.push(value);
                }
            });
        });
        const el = $('#selectedTestsSummary');
        if (!names.length) {
            el.text('No tests selected');
            return;
        }
        const sampleTypeBlock = uniqueSampleTypes.length
            ? '<div class="mt-4"><span class="fw-700">Sample Type:</span> ' + escapeHtml(uniqueSampleTypes.join(', ')) + '</div>'
            : '<div class="mt-4 text-muted">Sample Type: N/A</div>';
        el.html('<div>' + escapeHtml(names.join(', ')) + '</div>' + sampleTypeBlock + '<div class="fw-700 mt-4">Total (standard): ₹' + total.toFixed(2) + '</div>');
    }

    function loadPathologyTests() {
        if (!LAB_WALKIN.testsUrl) return;
        $.get(LAB_WALKIN.testsUrl, function(res) {
            pathologyTests = (res && res.data) ? res.data : [];
            const q = $('#pathologyTestSearch').val() || '';
            renderTestList(q);
        }).fail(function() {
            $('#testCheckboxList').html('<div class="text-danger fs-12 p-8">Could not load pathology tests.</div>');
        });
    }

    function registerWalkInSample() {
        const patientId = $('#patientChipContainer input[name="patient_id"]').val();
        if (!patientId) {
            alert('Please select a patient.');
            return;
        }
        const ids = $('.pathology-test-cb:checked').map(function() { return $(this).val(); }).get();
        if (!ids.length) {
            alert('Please select at least one test.');
            return;
        }
        loader('show');
        $.ajax({
            url: LAB_WALKIN.saveUrl,
            method: 'POST',
            data: {
                _token: LAB_WALKIN.csrf,
                patient_id: patientId,
                pathology_test_ids: ids,
                priority: $('#samplePriority').val(),
                clinical_notes: $('#clinical_notes').val(),
                doctor_staff_id: $('#doctor_staff_id').val() || null,
            },
            success: function(res) {
                loader('hide');
                if (res && res.status) {
                    alert(res.message || 'Saved.');
                    $('.add-datamodal').modal('hide');
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#xin-table')) {
                        $('#xin-table').DataTable().ajax.reload(null, false);
                    }
                } else {
                    alert('Save failed.');
                }
            },
            error: function(xhr) {
                loader('hide');
                let msg = 'Could not save registration.';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.length) {
                    msg = xhr.responseJSON.errors.map(function(e) { return e.message; }).join('\n');
                }
                alert(msg);
            }
        });
    }

    $(function() {
        loadPathologyTests();
        $(document).off('input.walkinTest', '#pathologyTestSearch').on('input.walkinTest', '#pathologyTestSearch', function() {
            clearTimeout(searchTimer);
            const v = $(this).val();
            searchTimer = setTimeout(function() { renderTestList(v); }, 200);
        });
        $(document).off('change.walkinTest', '.pathology-test-cb').on('change.walkinTest', '.pathology-test-cb', updateSummary);

        $('#btnRegisterWalkInSample').off('click').on('click', registerWalkInSample);
    });

    let searchTimeout;
    const $search = $('#patient_search');
    const $dropdown = $('#patientSearchResults');

    $search.off('input.walkinPt').on('input.walkinPt', function() {
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
                            html += `<a href="#" class="dropdown-item patient-result-item" data-patient-id="${p.id}" data-patient-name="${escapeHtml(p.name)}" data-patient-mrn="${escapeHtml(p.mrn || '')}" data-patient-meta="${escapeHtml((p.age_sex || '') + ' | ' + (p.blood_group || '') + ' | ' + (p.phone || ''))}">
                                <div><b>${escapeHtml(p.name)}</b> <span class="text-muted">(${escapeHtml(p.mrn)})</span></div>
                                <div class="fs-12 text-muted">${escapeHtml(p.age_sex)} | ${escapeHtml(p.blood_group)} | ${escapeHtml(p.phone)}</div>
                            </a>`;
                        });
                    } else {
                        html = '<div class="dropdown-item text-muted">No patient found</div>';
                    }
                    $dropdown.html(html).show();
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
        const $el = $(this);
        const pid = $el.data('patient-id');
        const pname = $el.data('patient-name');
        const mrn = $el.data('patient-mrn');
        const meta = $el.data('patient-meta');
        $dropdown.hide();
        let chip = `<div class="patient-chip">
            <input type="hidden" value="${pid}" name="patient_id"/>
            <div class="patient-chip-avatar">${escapeHtml((pname || '?').charAt(0))}</div>
            <div class="patient-chip-info">
                <div class="patient-chip-name">${escapeHtml(pname)}</div>
                <div class="patient-chip-meta">${escapeHtml(mrn)} | ${escapeHtml(meta)}</div>
            </div>
        </div>`;
        $('#patientChipContainer').html(chip);
        $search.val(mrn);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.input-group').length) {
            $dropdown.hide();
        }
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
.patient-search-dropdown .dropdown-item:hover, .patient-search-dropdown .dropdown-item.active {
    background: #f5f5f5;
}
</style>
