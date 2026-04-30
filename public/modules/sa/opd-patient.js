$(document).ready(function() {
    const loadroute = route('loadtable');
    let patientLookupByPhone = {};
    let patientLookupByHealthId = {};
    let selectedExistingPhone = '';

    const xintable = $('#xin-table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: true,
        lengthChange: true,
        scrollX: true,
        ajax: {
            url: loadroute, 
            type: 'POST',
            data: function (d) {
                d._token = window.Laravel.csrfToken;
            }
        },
        columns: [
            {
                data: null,
                name: 'serial_no',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'token_no', name: 'opd_patients.token_no' },
            { data: 'name', name: 'patients.name' },
            { data: 'patient_id', name: 'patients.patient_id' },
            { data: 'phone', name: 'patients.phone' },
            { data: 'consultant', name: 'consultant' },
            { data: 'last_visit', name: 'last_visit' },
            { data: 'status', name: 'opd_patients.status' },
            { data: 'number_of_visits', name: 'number_of_visits' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: "fBrtip",
        autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function (e, dt, node, config) { dt.ajax.reload(); }},
            { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
            { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'Export as CSV' },
            { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Export as Excel' },
            { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'Export as PDF' },
            { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print Table' },
            { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Column Visibility' }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search...'
        },
        responsive: true
    });


    $(document).find('.dataTables_filter input').addClass('form-control').css({'width':'300px','display':'inline-block'});

    initTooltips();
    xintable.on('draw.dt responsive-display.dt column-visibility.dt', function () {
        initTooltips();
    });
    xintable.on('draw.dt', function () {
        initTooltips();
    });
    function initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }
    // Flatpickr init for date fields
    // flatpickr('input[type="date"]', { dateFormat: 'd-m-Y' });
    // flatpickr('input[type="datetime-local"]', { enableTime: true, dateFormat: 'd-m-Y H:i' });

    $(document).on('click', '.adddata, .editdata, .revisit-btn', async function() {
        loader();
        var id = $(this).data('id');
        var patientId = $(this).data('patient-id') || '';
        var url = route('showform');
        const token = await csrftoken(); // wait for the new token

        $.ajax({
            url: url,
            type: "POST",
            data: {id: id, patient_id: patientId, _token: token},
            success: function (response) {
                loader('hide');
                if (response) {
                    $("#ajaxdata").html(response);
                    const $modal = $('.add-datamodal');
                    const $modalDialog = $modal.find('.modal-dialog');

                    $modalDialog.removeClass('modal-xl modal-dialog-centered').addClass('modal-fullscreen');
                    $modal.modal('show');

                    $modal.find('.modal-body').css({
                        maxHeight: 'calc(100vh - 130px)',
                        overflowY: 'auto',
                        overscrollBehavior: 'contain',
                        WebkitOverflowScrolling: 'touch'
                    });

                    flatpickr('input[id="opd-appointment-date"]', { enableTime: true, dateFormat: 'd-m-Y H:i',minDate: "today" });
                    flatpickr('input[id="opd-dob"]', { dateFormat: 'd-m-Y',maxDate: "today" });
                    initSelect2InModal();
                    unlockPatientFields();
                    loadTpas(function () {
                        applyRevisitPrefill();
                    });
                }
            },
            error: function (xhr) {
                loader('hide');
                $('.err').remove();
            }
        });
    });


    $(document).on('click', '.deletebtn', async function() {
        var id = $(this).data('id');    
        const fallbackUrl = route('destroy-post', { opd_patient: id });
        const token = await csrftoken(); // wait for the new token
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: fallbackUrl,
                    type: 'POST',
                    data: {id: id, _token: token},
                    success: function (response) {
                        loader('hide');
                        if (response.status) {
                            sendmsg('success', response.message);
                            $('#xin-table').DataTable().ajax.reload(null, false);
                        } else {
                            sendmsg('error', response.message || 'Unable to delete record.');
                        }
                    },
                    error: function () {
                        loader('hide');
                        sendmsg('error', 'Unable to delete record right now.');
                    }
                });
            }
        });
    });

    $(document).on('click', '.change-status-btn', async function() {
        if ($(this).hasClass('disabled')) {
            return;
        }

        const id = $(this).data('id');
        const token = await csrftoken();
        const url = route('update-status', { opd_patient: id });

        loader();
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: token },
            success: function (response) {
                loader('hide');
                if (response.status) {
                    sendmsg('success', response.message);
                    $('#xin-table').DataTable().ajax.reload(null, false);
                } else {
                    sendmsg('error', response.message || 'Unable to update status.');
                }
            },
            error: function (xhr) {
                loader('hide');
                const message = xhr?.responseJSON?.message || 'Unable to update status right now.';
                sendmsg('error', message);
            }
        });
    });

    $(document).on('click', '.move-to-ipd-btn', async function () {
        const url = $(this).data('url');
        const opdPatientId = $(this).data('opd-patient-id');
        const token = await csrftoken();

        loader();
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: token,
                opd_patient_id: opdPatientId
            },
            success: function (response) {
                loader('hide');
                $('#ajaxdata').html(response);
                const $modal = $('.add-datamodal');
                const $modalDialog = $modal.find('.modal-dialog');

                $modalDialog.removeClass('modal-sm modal-lg').addClass('modal-xl');
                $modal.modal('show');

                if (window.flatpickr) {
                    flatpickr('input[id="ipd-admission-date"]', { enableTime: true, dateFormat: 'd-m-Y H:i' });
                    flatpickr('input[id="ipd-expected-discharge-date"]', { dateFormat: 'd-m-Y', minDate: 'today' });
                    flatpickr('input[id="ipd-dob"]', { dateFormat: 'd-m-Y', maxDate: 'today' });
                }

                if ($.fn.select2) {
                    $('.add-datamodal .select2-modal').select2({
                        dropdownParent: $('.add-datamodal'),
                        width: '100%'
                    });
                }

                initIpdAdmissionModalForOpd();
            },
            error: function (xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to open IPD admission form.');
            }
        });
    });

    $(document).on('submit', '#save-ipd-admission', async function (event) {
        event.preventDefault();

        const form = this;
        const submitUrl = $(form).data('submit-url');
        const formData = new FormData(form);
        const token = await csrftoken();

        loader('show');
        $('.err').remove();
        formData.append('_token', token);

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                loader('hide');
                $('.add-datamodal').modal('hide');
                sendmsg('success', response.message || 'IPD admission completed successfully.');
                $('#xin-table').DataTable().ajax.reload(null, false);

                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                }
            },
            error: function (xhr) {
                loader('hide');
                handleIpdModalErrors(xhr, $(form));
            }
        });
    });

    $(document).on('change', '.add-datamodal select[name="hr_department_id"]', function () {
        if (!$('#save-ipd-admission').length) {
            return;
        }

        loadIpdDoctorsInOpdModal();
    });

    $(document).on('change', '.add-datamodal select[name="bed_id"]', function () {
        if (!$('#save-ipd-admission').length) {
            return;
        }

        updateIpdBedSummaryInOpdModal();
    });

    function initIpdAdmissionModalForOpd() {
        loadIpdDoctorsInOpdModal();
        updateIpdBedSummaryInOpdModal();
    }

    function loadIpdDoctorsInOpdModal() {
        const departmentId = $('.add-datamodal select[name="hr_department_id"]').val();
        const prefillDoctorId = $('#prefill_doctor_id').val() || '';

        if (!departmentId) {
            $('.add-datamodal select[name="doctor_id"]').html('<option value="">Select</option>').trigger('change');
            return;
        }

        $.get(route('load-doctors'), { hr_department_id: departmentId }, function (data) {
            let html = '<option value="">Select</option>';

            $.each(data, function (_, doctor) {
                const selected = String(doctor.id) === String(prefillDoctorId) ? 'selected' : '';
                html += `<option value="${doctor.id}" ${selected}>${doctor.full_name}</option>`;
            });

            $('.add-datamodal select[name="doctor_id"]').html(html).trigger('change');
            $('#prefill_doctor_id').val('');
        });
    }

    function updateIpdBedSummaryInOpdModal() {
        const $option = $('.add-datamodal select[name="bed_id"] option:selected');
        if (!$option.val()) {
            $('#ipd-bed-summary').text('Select a bed to see its location, type and standard base charge.');
            return;
        }

        $('#ipd-bed-summary').text([
            'Ward: ' + ($option.data('ward') || '-'),
            'Room: ' + ($option.data('room') || '-'),
            'Type: ' + ($option.data('type') || '-'),
            'Base Charge: ' + ($option.data('charge') || '0.00')
        ].join(' | '));
    }

    function handleIpdModalErrors(xhr, $scope) {
        if (xhr.status === 422 && xhr.responseJSON?.errors) {
            const errors = xhr.responseJSON.errors;
            const errorMessages = [];

            for (const field in errors) {
                const fieldCode = errors[field].code;
                const $field = $scope.find('[name="' + fieldCode + '"]');
                if ($field.length) {
                    $field.last().after('<div class="err text-danger">' + errors[field].message + '</div>');
                }
                errorMessages.push(errors[field].message);
            }

            if (errorMessages.length) {
                sendmsg('error', errorMessages.join('<br>'));
            }
            return;
        }

        sendmsg('error', xhr?.responseJSON?.message || 'Unable to complete IPD admission.');
    }
    
    $(document).on('input', 'input[name="phone"]', function () {
        const searchText = ($(this).val() || '').trim();
        const searchBy = getSearchBy();

        if (selectedExistingPhone && searchText !== selectedExistingPhone) {
            clearExistingPatientSelection(true);
        }

        if (searchText.length >= 2) {
            fetchPatientSuggestions(searchText, searchBy);
            const matched = getMatchedPatient(searchText, searchBy);
            if (matched) {
                applyExistingPatient(matched);
            }
        } else {
            patientLookupByPhone = {};
            patientLookupByHealthId = {};
            $('#patient-phone-suggestions').html('');
        }
    });

    $(document).on('change', 'input[name="phone"]', function () {
        const searchText = ($(this).val() || '').trim();
        const searchBy = getSearchBy();
        const matched = getMatchedPatient(searchText, searchBy);
        if (matched) {
            applyExistingPatient(matched);
        }
    });

    $(document).on('change', 'input[name="searchBy"]', function () {
        const searchBy = getSearchBy();
        const isHealthId = searchBy === 'health_id';
        const placeholder = isHealthId ? 'Search by Health ID' : 'Search by Phone';
        const label = isHealthId ? 'Health ID *' : 'Phone *';

        $('#patient-search-input-label').html(label);
        $('input[name="phone"]').attr('placeholder', placeholder).val('');
        patientLookupByPhone = {};
        patientLookupByHealthId = {};
        $('#patient-phone-suggestions').html('');
        clearExistingPatientSelection(true);
    });

    $(document).on('click', '.btn-submit-with-print', function () {
        const mode = ($(this).data('print-mode') || 'none').toString();
        $('#print_mode').val(mode);
        $('#savedata').trigger('submit');
    });

    $(document).on('change', 'input[name="date_of_birth"]', function () {
        autoFillAgeFromDob($(this).val());
    });

    $(document).on("submit", "#savedata", async function (e) {

        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();

        var fd = new FormData(this);
        fd.append("_token", token);
        $.ajax({
            url: route('store'),
            type: "POST",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) {
                    $(".add-datamodal").modal('hide');
                    $('#xin-table').DataTable().ajax.reload(null, false);
                    sendmsg('success', response.message);

                    const printMode = ($('#print_mode').val() || 'none').toString();
                    if (printMode === 'health_card' && response.patient_id) {
                        const healthCardUrl = route('health-card', { patient: response.patient_id });
                        window.open(healthCardUrl, '_blank');
                    } else if (printMode === 'sticker' && response.id) {
                        const stickerUrl = route('sticker', { opd_patient: response.id }) + '?autoprint=1';
                        window.open(stickerUrl, '_blank');
                    } else if (printMode === 'opd_print' && response.id) {
                        const visitSummaryUrl = route('visit-summary', { opd_patient: response.id });
                        window.open(visitSummaryUrl, '_blank');
                    }

                    $('#print_mode').val('none');
                } else {
                    sendmsg('error', response.message);
                }
            },
            error: function (xhr) {
                loader('hide');
                $('.err').remove();
                $('#print_mode').val('none');
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        const fieldCode = errors[field]['code'];
                        let $field = $(`[name="${fieldCode}"], [name="${fieldCode}[]"]`);

                        if (!$field.length && fieldCode.indexOf('.') !== -1) {
                            const parentField = fieldCode.split('.')[0];
                            $field = $(`[name="${parentField}"], [name="${parentField}[]"]`);
                        }

                        if ($field.hasClass('select2-modal')) {
                            $field.next('.select2-container').after(`<div class="err text-danger">${errors[field]['message']}</div>`);
                        } else {
                            $field.after(`<div class="err text-danger">${errors[field]['message']}</div>`);
                        }

                        errorMessages.push(errors[field]['message']);
                    }
                    if (errorMessages.length > 0) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                } else {
                    sendmsg('error', 'Something went wrong. Please try again later.');
                }
            }
        });
    });
    
    function initSelect2InModal() {
        $('.add-datamodal .select2-modal').each(function () {
            const $el = $(this);
            if (!$el.hasClass('select2-hidden-accessible')) {
                $el.select2({
                    dropdownParent: $('.add-datamodal'),
                    width: '100%'
                });
            }
        });
    }


    function fetchPatientSuggestions(searchText, searchBy) {
        const searchPatientsUrl = route('search-patients');
        $.get(searchPatientsUrl, { query: searchText, search_by: searchBy }, function (data) {
            patientLookupByPhone = {};
            patientLookupByHealthId = {};
            let html = '';

            $.each(data, function (_, patient) {
                patientLookupByPhone[patient.phone] = patient;
                if (patient.patient_code) {
                    patientLookupByHealthId[patient.patient_code] = patient;
                    patientLookupByHealthId[patient.patient_code.toUpperCase()] = patient;
                }
                const label = `${patient.name || ''} (${patient.patient_code || ''})`;
                const optionValue = searchBy === 'health_id' ? (patient.patient_code || '') : (patient.phone || '');
                html += `<option value="${optionValue}">${label}</option>`;
            });

            $('#patient-phone-suggestions').html(html);
        });
    }

    function getSearchBy() {
        return $('input[name="searchBy"]:checked').val() || 'phone';
    }

    function getMatchedPatient(searchText, searchBy) {
        if (!searchText) {
            return null;
        }

        if (searchBy === 'health_id') {
            return patientLookupByHealthId[searchText] || patientLookupByHealthId[searchText.toUpperCase()] || null;
        }

        return patientLookupByPhone[searchText] || null;
    }

    function applyExistingPatient(patient) {
        loader('show');
        selectedExistingPhone = patient.phone || '';
        $('input[name="selected_patient_id"]').val(patient.id || '');

        const dietaryIds = normalizeToStringArray(patient.dietary_id);
        const allergyIds = normalizeToStringArray(patient.allergy_id);
        const habitIds = normalizeToStringArray(patient.habit_id);
        const diseaseTypeIds = normalizeToStringArray(patient.disease_type_id);
        const diseaseIds = normalizeToStringArray(patient.disease_id);

        $('select[name="country_code"]').val(patient.country_code || '+91').trigger('change');
        $('input[name="name"]').val(patient.name || '');
        $('input[name="guardian_name"]').val(patient.guardian_name || '');
        $('input[name="date_of_birth"]').val(patient.date_of_birth || '').trigger('change');
        $('input[name="age_years"]').val(patient.age_years || '');
        $('input[name="age_months"]').val(patient.age_months || '');
        $('select[name="gender"]').val(patient.gender || '').trigger('change');
        $('select[name="blood_group"]').val(patient.blood_group || '').trigger('change');
        $('select[name="marital_status"]').val(patient.marital_status || '').trigger('change');
        $('input[name="aadhar_card_no"]').val(patient.aadhar_no || '');
        $('input[name="email"]').val(patient.email || '');
        $('select[name="nationality_id"]').val(patient.nationality_id || '').trigger('change');
        $('select[name="patient_category_id"]').val(patient.patient_category_id || '').trigger('change');
        $('select[name="religion_id"]').val(patient.religion_id || '').trigger('change');
        $('select[name="dietary_id[]"]').val(dietaryIds).trigger('change');
        $('select[name="allergy_id[]"]').val(allergyIds).trigger('change');
        $('select[name="habit_id[]"]').val(habitIds).trigger('change');
        $('select[name="disease_type_id[]"]').val(diseaseTypeIds).trigger('change');
        $('textarea[name="address"]').val(patient.address || '');
        $('textarea[name="known_allergies"]').val(patient.known_allergies || '');

        if (diseaseTypeIds.length) {
            loadDiseasesByTypes(diseaseTypeIds, function () {
                $('select[name="disease_id[]"]').val(diseaseIds).trigger('change');
            });
        } else {
            $('select[name="disease_id[]"]').val(diseaseIds).trigger('change');
        }

        const staffValue = patient.is_staff || 'No';
        $('input[name="is_staff"]').prop('checked', false);
        $(`input[name="is_staff"][value="${staffValue}"]`).prop('checked', true);

        unlockPatientFields();
        getOPDCharge();
        loader('hide');
    }

    function autoFillAgeFromDob(dobString) {
        const value = (dobString || '').trim();
        if (!value) {
            return;
        }

        const parts = value.split('-');
        if (parts.length !== 3) {
            return;
        }

        const day = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        const year = parseInt(parts[2], 10);

        if (!day || !month || !year) {
            return;
        }

        const dob = new Date(year, month - 1, day);
        if (
            Number.isNaN(dob.getTime()) ||
            dob.getFullYear() !== year ||
            dob.getMonth() !== month - 1 ||
            dob.getDate() !== day
        ) {
            return;
        }

        const today = new Date();
        if (dob > today) {
            return;
        }

        let totalMonths = (today.getFullYear() - dob.getFullYear()) * 12 + (today.getMonth() - dob.getMonth());
        if (today.getDate() < dob.getDate()) {
            totalMonths -= 1;
        }

        if (totalMonths < 0) {
            totalMonths = 0;
        }

        const years = Math.floor(totalMonths / 12);
        const months = totalMonths % 12;

        $('input[name="age_years"]').val(years);
        $('input[name="age_months"]').val(months);
    }

    function clearExistingPatientSelection(resetValues) {
        selectedExistingPhone = '';
        $('input[name="selected_patient_id"]').val('');
        unlockPatientFields();

        if (resetValues) {
            $('input[name="name"], input[name="guardian_name"], input[name="date_of_birth"], input[name="age_years"], input[name="age_months"], input[name="aadhar_card_no"], input[name="email"], input[name="image"]').val('');
            $('textarea[name="address"], textarea[name="known_allergies"]').val('');
            $('select[name="gender"], select[name="blood_group"], select[name="marital_status"], select[name="nationality_id"], select[name="patient_category_id"], select[name="religion_id"]').val('').trigger('change');
            $('select[name="dietary_id[]"], select[name="allergy_id[]"], select[name="habit_id[]"], select[name="disease_type_id[]"], select[name="disease_id[]"]').val([]).trigger('change');
            $('input[name="is_staff"]').prop('checked', false);
            $('input[name="is_staff"][value="No"]').prop('checked', true);
        }

        getOPDCharge();
    }

    function lockPatientFields() {
        $('input[name="name"], input[name="guardian_name"], input[name="date_of_birth"], input[name="age_years"], input[name="age_months"], input[name="aadhar_card_no"], input[name="email"], textarea[name="address"], textarea[name="known_allergies"]').prop('readonly', true);
        $('select[name="country_code"], select[name="gender"], select[name="blood_group"], select[name="marital_status"], select[name="nationality_id"], select[name="patient_category_id"], select[name="religion_id"], select[name="dietary_id[]"], select[name="allergy_id[]"], select[name="habit_id[]"], select[name="disease_type_id[]"], select[name="disease_id[]"]').prop('disabled', true).trigger('change');
        $('input[name="image"], input[name="is_staff"]').prop('disabled', true);
    }

    function unlockPatientFields() {
        $('input[name="name"], input[name="guardian_name"], input[name="date_of_birth"], input[name="age_years"], input[name="age_months"], input[name="aadhar_card_no"], input[name="email"], textarea[name="address"], textarea[name="known_allergies"]').prop('readonly', false);
        $('select[name="country_code"], select[name="gender"], select[name="blood_group"], select[name="marital_status"], select[name="nationality_id"], select[name="patient_category_id"], select[name="religion_id"], select[name="dietary_id[]"], select[name="allergy_id[]"], select[name="habit_id[]"], select[name="disease_type_id[]"], select[name="disease_id[]"]').prop('disabled', false).trigger('change');
        $('input[name="image"], input[name="is_staff"]').prop('disabled', false);
    }

    function normalizeToStringArray(values) {
        if (values === null || values === undefined || values === '') {
            return [];
        }

        if (Array.isArray(values)) {
            return values.map(String);
        }

        return [String(values)];
    }

    function loadTpas(callback) {
        const loadtpas = route('load-tpas');
        $.get(loadtpas, function(data) {
            var html = '<option value="">Select</option>';
            $.each(data, function(i, t) {
                html += '<option value="'+t.id+'">'+t.name+'</option>';
            });
            $('select[name=tpa_id]').html(html);
            initSelect2InModal();

            if (typeof callback === 'function') {
                callback();
            }
        });
    }
    $(document).on('change', 'select[name=tpa_id]', function() {
        getOPDCharge();
    });

    $(document).on('change', 'select[name=hr_department_id]', function() {
        var dept = $(this).val();
        const loadunit = route('load-units');
        $.get(loadunit, { hr_department_id: dept }, function(data) {
            var html = '<option value="">Select</option>';
            $.each(data, function(i, u) {
                html += '<option value="'+u.id+'">'+u.name+'</option>';
            });
            $('select[name=hr_department_unit_id]').html(html);
            initSelect2InModal();
        });
        
        loadDoctors();
        getOPDCharge();
    });
    function loadDoctors(callback){
        var hr_department_id = $("select[name=hr_department_id]").val();
        const loaddoctor = route('load-doctors');
        $.get(loaddoctor, { hr_department_id: hr_department_id }, function(data) {
            var html = '<option value="">Select</option>';
            $.each(data, function(i, d) {
                html += '<option value="'+d.id+'">'+d.full_name+'</option>';
            });
            $('select[name=doctor_id]').html(html);
            initSelect2InModal();

            if (typeof callback === 'function') {
                callback();
            }
        });
    }
    async function getOPDCharge(){
        var hr_department_id = $("select[name=hr_department_id]").val();
        var doctor_id = $("select[name=doctor_id]").val();
        var tpa_id = $("select[name=tpa_id]").val();
        var selected_patient_id = $("input[name=selected_patient_id]").val();
        var phone = $("input[name=phone]").val();
        var appointment_date = $("input[name=appointment_date]").val();
        const opdcharge = route('get-opd-charge');
        
        const token = await csrftoken();
        $.post(opdcharge, {
            hr_department_id: hr_department_id,
            doctor_id: doctor_id,
            tpa_id: tpa_id,
            selected_patient_id: selected_patient_id,
            phone: phone,
            appointment_date: appointment_date,
            _token: token
        }, function(data) {
            $("input[name=standard_charge]").val(data.standard_charge);
            $("input[name=applied_charge]").val(data.charge);
            $("input[name=apply_charge_type]").val(data.apply_charge_type);
            $("input[name=consultation_case_label_display]").val(data.consultation_case_label || '');
            $("input[name=consultation_charge_source_display]").val(data.consultation_charge_source || '');
            $("input[name=consultation_valid_until_display]").val(data.consultation_valid_until || '');
        });
    }
    
    $(document).on('change', 'select[name=doctor_id]', function() {
        loadDoctorSlots();
        getOPDCharge();
    });
    function loadDoctorSlots(callback){
        var doctor_id = $("select[name=doctor_id]").val();
        const loaddoctorslots = route('load-doctor-slots');
        $.get(loaddoctorslots, { doctor_id: doctor_id }, function(data) {
            var html = '<option value="">Select</option>';
            // data now contains objects with start and end times
            $.each(data, function(i, s) {
                // display as "HH:mm - HH:mm" and keep start time as value
                html += '<option value="'+s.start+' - '+s.end+'">'+s.start+' - '+s.end+'</option>';
            });
            $('select[name=slot]').html(html);
            initSelect2InModal();

            if (typeof callback === 'function') {
                callback();
            }
        });
    }
    // symptoms type -> symptoms
    $(document).on('change', 'select[name="symptoms_type[]"]', function() {
        var vals = $(this).val() || [];
        loadSymptomsByTypes(vals);
    });

    $(document).on('change', 'select[name="disease_type_id[]"]', function() {
        var vals = $(this).val() || [];
        loadDiseasesByTypes(vals);
    });

    async function loadDiseasesByTypes(vals, callback) {
        const loadDiseasesByTypesRoute = route('load-diseases-by-types');
        const token = await csrftoken();

        $.post(loadDiseasesByTypesRoute, { types: vals, _token: token }, function(data) {
            var html = '<option value="" disabled>Select</option>';
            $.each(data, function(i, d) {
                html += '<option value="'+d.id+'">'+d.name+'</option>';
            });
            $('select[name="disease_id[]"]').html(html);
            initSelect2InModal();

            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    async function loadSymptomsByTypes(vals, callback) {
        const loadsymptoms = route('load-symptoms');
        
        const token = await csrftoken();
        $.post(loadsymptoms, { types: vals, _token: token }, function(data) {
            var html = '<option value="" disabled>Select</option>';
            $.each(data, function(i, s) {
                html += '<option value="'+s.id+'">'+s.name+'</option>';
            });
            $('select[name="symptoms[]"]').html(html);
            initSelect2InModal();

            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function applyRevisitPrefill() {
        const isRevisit = $('#is_revisit_prefilled').length > 0;
        if (!isRevisit) {
            return;
        }

        unlockPatientFields();

        const revisitTpaId = $('#revisit_tpa_id').val() || '';
        const revisitDoctorId = $('#revisit_doctor_id').val() || '';
        const revisitSlot = $('#revisit_slot').val() || '';
        const revisitSymptomsRaw = $('#revisit_symptoms').val() || '[]';
        const revisitSymptomsTypeRaw = $('#revisit_symptoms_type').val() || '[]';
        const revisitDiseaseTypeRaw = $('#revisit_disease_type').val() || '[]';
        const revisitDiseaseRaw = $('#revisit_disease').val() || '[]';

        let revisitSymptomsType = [];
        try {
            revisitSymptomsType = JSON.parse(revisitSymptomsTypeRaw);
        } catch (e) {
            revisitSymptomsType = [];
        }

        if (!Array.isArray(revisitSymptomsType)) {
            revisitSymptomsType = revisitSymptomsType ? [revisitSymptomsType] : [];
        }
        revisitSymptomsType = revisitSymptomsType.map(String);

        if (!revisitSymptomsType.length) {
            revisitSymptomsType = $('select[name="symptoms_type[]"] option:selected').map(function() {
                return $(this).val();
            }).get();
        }

        if (revisitTpaId) {
            $('select[name="tpa_id"]').val(revisitTpaId).trigger('change');
        }

        if (revisitSymptomsType.length) {
            $('select[name="symptoms_type[]"]').val(revisitSymptomsType).trigger('change');

            loadSymptomsByTypes(revisitSymptomsType, function () {
                let revisitSymptoms = [];
                try {
                    revisitSymptoms = JSON.parse(revisitSymptomsRaw);
                } catch (e) {
                    revisitSymptoms = [];
                }

                if (!Array.isArray(revisitSymptoms)) {
                    revisitSymptoms = revisitSymptoms ? [revisitSymptoms] : [];
                }

                if (revisitSymptoms.length) {
                    $('select[name="symptoms[]"]').val(revisitSymptoms.map(String)).trigger('change');
                }
            });
        }

        let revisitDiseaseType = [];
        try {
            revisitDiseaseType = JSON.parse(revisitDiseaseTypeRaw);
        } catch (e) {
            revisitDiseaseType = [];
        }

        if (!Array.isArray(revisitDiseaseType)) {
            revisitDiseaseType = revisitDiseaseType ? [revisitDiseaseType] : [];
        }
        revisitDiseaseType = revisitDiseaseType.map(String);

        if (!revisitDiseaseType.length) {
            revisitDiseaseType = $('select[name="disease_type_id[]"] option:selected').map(function() {
                return $(this).val();
            }).get();
        }

        if (revisitDiseaseType.length) {
            $('select[name="disease_type_id[]"]').val(revisitDiseaseType).trigger('change');

            loadDiseasesByTypes(revisitDiseaseType, function () {
                let revisitDisease = [];
                try {
                    revisitDisease = JSON.parse(revisitDiseaseRaw);
                } catch (e) {
                    revisitDisease = [];
                }

                if (!Array.isArray(revisitDisease)) {
                    revisitDisease = revisitDisease ? [revisitDisease] : [];
                }

                if (revisitDisease.length) {
                    $('select[name="disease_id[]"]').val(revisitDisease.map(String)).trigger('change');
                }
            });
        }
    }

});
