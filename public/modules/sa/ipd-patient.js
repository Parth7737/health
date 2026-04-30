$(document).ready(function () {
    let patientLookupByPhone = {};
    let patientLookupByHealthId = {};
    let selectedExistingPhone = '';

    initTable();
    bindAdmissionFlow();
    bindTransferAndDischarge();
    bindPayments();
    bindProgressNotes();
    bindPrescriptions();
    bindDiagnosticOrders();
    bindClinicalSnapshot();

    function initTable() {
        if (!$('#xin-table').length) {
            return;
        }

        const xintable = $('#xin-table').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            lengthChange: true,
            scrollX: true,
            ajax: {
                url: route('loadtable'),
                type: 'POST',
                data: function (d) {
                    d._token = window.Laravel.csrfToken;
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'admission_no', name: 'bed_allocations.admission_no' },
                { data: 'patient_name', name: 'patient_name' },
                { data: 'patient_id', name: 'patient_code' },
                { data: 'age_gender', name: 'patients.age_years' },
                { data: 'consultant', name: 'consultant_name' },
                { data: 'bed_identifier', name: 'bed_identifier', orderable: false },
                { data: 'los', name: 'bed_allocations.admission_date' },
                { data: 'payer', name: 'tpa_name' },
                { data: 'outstanding', name: 'outstanding_amount' },
                { data: 'status', name: 'bed_allocations.discharge_date' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            dom: 'fBrtip',
            autoWidth: true,
            buttons: [
                { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function (e, dt) { dt.ajax.reload(); }},
                { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>' },
                { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>' },
                { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>' },
                { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>' },
                { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>' },
                { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>' }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search...'
            },
            responsive: true
        });

        $(document).find('.dataTables_filter input').addClass('form-control').css({ width: '300px', display: 'inline-block' });

        xintable.on('draw.dt responsive-display.dt column-visibility.dt', function () {
            $('[data-bs-toggle="tooltip"]').tooltip('dispose');
            $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
        });
    }

    function bindAdmissionFlow() {
        $(document).on('click', '.adddata', async function () {
            const token = await csrftoken();
            loader('show');
            $.ajax({
                url: route('showform'),
                type: 'POST',
                data: { _token: token },
                success: function (response) {
                    loader('hide');
                    showModal(response, 'modal-xl');
                    initAdmissionModal($('.add-datamodal'));
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to open admission form.');
                }
            });
        });

        $(document).on('submit', '#save-ipd-admission', function (event) {
            event.preventDefault();
            const form = this;
            const submitUrl = $(form).data('submit-url');
            const formData = new FormData(form);

            loader('show');
            $('.err').remove();

            csrftoken().then(function (token) {
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

                        if ($('#xin-table').length) {
                            $('#xin-table').DataTable().ajax.reload(null, false);
                        }

                        if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                        }
                    },
                    error: function (xhr) {
                        loader('hide');
                        handleValidationErrors(xhr, $(form));
                    }
                });
            });
        });

        $(document).on('input', '#ipd-patient-search', function () {
            const searchText = ($(this).val() || '').trim();
            const searchBy = $('input[name="searchBy"]:checked').val() || 'phone';

            if (selectedExistingPhone && searchText !== selectedExistingPhone) {
                clearExistingPatientSelection();
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
                $('#ipd-patient-suggestions').html('');
            }
        });

        $(document).on('change', '#ipd-patient-search', function () {
            const searchText = ($(this).val() || '').trim();
            const searchBy = $('input[name="searchBy"]:checked').val() || 'phone';
            const matched = getMatchedPatient(searchText, searchBy);
            if (matched) {
                applyExistingPatient(matched);
            }
        });

        $(document).on('change', '.add-datamodal select[name="hr_department_id"]', function () {
            loadDoctors($(this).closest('.add-datamodal'));
        });

        $(document).on('change', '.add-datamodal select[name="bed_id"]', function () {
            updateBedSummary($(this).closest('.add-datamodal'));
        });
    }

    function bindTransferAndDischarge() {
        $(document).on('click', '.transfer-ipd-btn, .discharge-ipd-btn', async function () {
            const url = $(this).data('url');
            const token = await csrftoken();

            loader('show');
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: token },
                success: function (response) {
                    loader('hide');
                    showModal(response, 'modal-lg');
                    initAdmissionModal($('.add-datamodal'));
                    if ($('#ipd-discharge-date').length && window.flatpickr) {
                        flatpickr('#ipd-discharge-date', { enableTime: true, dateFormat: 'd-m-Y H:i' });
                    }
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to open action form.');
                }
            });
        });

        $(document).on('submit', '#transfer-ipd-form, #discharge-ipd-form', function (event) {
            event.preventDefault();
            const form = $(this);
            loader('show');
            $('.err').remove();

            csrftoken().then(function (token) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize() + '&_token=' + encodeURIComponent(token),
                    success: function (response) {
                        loader('hide');
                        $('.add-datamodal').modal('hide');
                        sendmsg('success', response.message || 'Action completed successfully.');

                        if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                            return;
                        }

                        if ($('#xin-table').length) {
                            $('#xin-table').DataTable().ajax.reload(null, false);
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function (xhr) {
                        loader('hide');
                        handleValidationErrors(xhr, form);
                    }
                });
            });
        });
    }

    function bindPayments() {
        $(document).on('click', '.open-charge-payment-form', async function (event) {
            event.preventDefault();
            const url = $(this).data('url');
            const chargeIdRaw = parseInt($(this).data('charge-id'), 10);
            const chargeIdsRaw = ($(this).data('charge-ids') || '').toString().trim();
            let chargeIds = chargeIdsRaw
                ? chargeIdsRaw.split(',').map(function (id) { return parseInt(id, 10); }).filter(function (id) { return !isNaN(id); })
                : [];
            if (!isNaN(chargeIdRaw)) {
                chargeIds.push(chargeIdRaw);
            }
            chargeIds = Array.from(new Set(chargeIds));

            const title = ($(this).data('title') || '').toString();
            const contextNote = ($(this).data('context-note') || '').toString();
            const token = await csrftoken();

            loader('show');
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: token, charge_ids: chargeIds, title: title, context_note: contextNote },
                success: function (response) {
                    loader('hide');
                    $('#chargePaymentContent').html(response);
                    $('#chargePaymentModal').modal('show');
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to open payment form.');
                }
            });
        });

        $(document).on('click', '.open-charge-discount-form', async function (event) {
            event.preventDefault();
            const url = $(this).data('url');
            const chargeIdRaw = parseInt($(this).data('charge-id'), 10);
            const chargeIdsRaw = ($(this).data('charge-ids') || '').toString().trim();
            let chargeIds = chargeIdsRaw
                ? chargeIdsRaw.split(',').map(function (id) { return parseInt(id, 10); }).filter(function (id) { return !isNaN(id); })
                : [];
            if (!isNaN(chargeIdRaw)) {
                chargeIds.push(chargeIdRaw);
            }
            chargeIds = Array.from(new Set(chargeIds));
            const token = await csrftoken();

            loader('show');
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: token, charge_ids: chargeIds },
                success: function (response) {
                    loader('hide');
                    $('#chargePaymentContent').html(response);
                    $('#chargePaymentModal').modal('show');
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to open discount form.');
                }
            });
        });

        $(document).on('submit', '#collectPaymentForm', async function (event) {
            event.preventDefault();
            const submitUrl = $(this).data('submit-url');
            const token = await csrftoken();
            loader('show');
            $('.err').remove();

            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: $(this).serialize() + '&_token=' + encodeURIComponent(token),
                success: function (response) {
                    loader('hide');
                    $('#chargePaymentModal').modal('hide');
                    sendmsg('success', response.message || 'Payment collected successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    handleValidationErrors(xhr, $('#collectPaymentForm'));
                }
            });
        });

        $(document).on('submit', '#applyDiscountForm', async function (event) {
            event.preventDefault();
            const submitUrl = $(this).data('submit-url');
            const token = await csrftoken();
            loader('show');
            $('.err').remove();

            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: $(this).serialize() + '&_token=' + encodeURIComponent(token),
                success: function (response) {
                    loader('hide');
                    $('#chargePaymentModal').modal('hide');
                    sendmsg('success', response.message || 'Discount applied successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    handleValidationErrors(xhr, $('#applyDiscountForm'));
                }
            });
        });

        $(document).on('click', '.open-ipd-add-charge-form', async function (event) {
            event.preventDefault();
            const url = $(this).data('url');
            const token = await csrftoken();

            loader('show');
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: token },
                success: function (response) {
                    loader('hide');
                    $('#chargePaymentContent').html(response);
                    $('#chargePaymentModal').modal('show');

                    if ($.fn.select2) {
                        $('#chargePaymentModal .select2-modal').select2({
                            dropdownParent: $('#chargePaymentModal'),
                            width: '100%'
                        });
                    }
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to open add charge form.');
                }
            });
        });

        $(document).on('submit', '#addIpdChargeForm', async function (event) {
            event.preventDefault();
            const submitUrl = $(this).data('submit-url');
            const token = await csrftoken();

            loader('show');
            $('.err').remove();

            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: $(this).serialize() + '&_token=' + encodeURIComponent(token),
                success: function (response) {
                    loader('hide');
                    $('#chargePaymentModal').modal('hide');
                    sendmsg('success', response.message || 'Charge added successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    handleValidationErrors(xhr, $('#addIpdChargeForm'));
                }
            });
        });

        $(document).on('hidden.bs.modal', '#chargePaymentModal', function () {
            $('#chargePaymentContent').empty();
        });
    }

    function bindDiagnosticOrders() {
        function refreshDiagnosticPreview() {
            if (!window.OPDCareShared || typeof window.OPDCareShared.refreshDiagnosticPreview !== 'function') {
                return;
            }

            window.OPDCareShared.refreshDiagnosticPreview('#ipd_diagnostic_test_ids', '#ipd-diagnostic-test-preview-body');
        }

        $(document).on('click', '.open-ipd-diagnostic-order', async function (event) {
            event.preventDefault();

            const showUrl = $(this).data('show-url');
            const orderType = $(this).data('order-type');
            const storeUrl = $(this).data('store-url');
            const token = await csrftoken();

            loader('show');
            $.ajax({
                url: showUrl,
                type: 'POST',
                data: { _token: token, order_type: orderType },
                success: function (response) {
                    loader('hide');
                    $('#ipdDiagnosticOrderContent').html(response);
                    $('#ipdDiagnosticOrderModal').modal('show');
                    $('#saveIpdDiagnosticOrderForm').data('store-url', storeUrl);

                    if ($.fn.select2) {
                        $('#ipdDiagnosticOrderModal .select2-modal').select2({
                            dropdownParent: $('#ipdDiagnosticOrderModal'),
                            width: '100%'
                        });
                    }

                    refreshDiagnosticPreview();
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to load diagnostic order form.');
                }
            });
        });

        $(document).on('change', '#ipd_diagnostic_test_ids', function () {
            refreshDiagnosticPreview();
        });

        $(document).on('submit', '#saveIpdDiagnosticOrderForm', async function (event) {
            event.preventDefault();

            const storeUrl = $(this).data('store-url');
            if (!storeUrl) {
                sendmsg('error', 'Save URL is missing.');
                return;
            }

            loader('show');
            $('.err').remove();
            const token = await csrftoken();

            $.ajax({
                url: storeUrl,
                type: 'POST',
                data: $(this).serialize() + '&_token=' + encodeURIComponent(token),
                success: function (response) {
                    loader('hide');
                    $('#ipdDiagnosticOrderModal').modal('hide');
                    sendmsg('success', response.message || 'Diagnostic order created successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    handleValidationErrors(xhr, $('#saveIpdDiagnosticOrderForm'));
                }
            });
        });

        $(document).on('click', '.delete-ipd-diagnostic-item', async function (event) {
            event.preventDefault();
            const deleteUrl = $(this).data('delete-url');
            const testName = $(this).data('test-name') || 'this test';

            const confirm = await Swal.fire({
                title: 'Are you sure?',
                text: 'You want to remove ' + testName + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel'
            });

            if (!confirm.isConfirmed) {
                return;
            }

            const token = await csrftoken();
            loader('show');

            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: { _token: token, _method: 'DELETE' },
                success: function (response) {
                    loader('hide');
                    sendmsg('success', response.message || 'Diagnostic test deleted successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to delete diagnostic test.');
                }
            });
        });
    }

    function bindProgressNotes() {
        $(document).on('submit', '#ipd-note-form', async function (event) {
            event.preventDefault();
            const submitUrl = $(this).data('submit-url');
            const token = await csrftoken();

            loader('show');
            $('.err').remove();

            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: $(this).serialize() + '&_token=' + encodeURIComponent(token),
                success: function (response) {
                    loader('hide');
                    sendmsg('success', response.message || 'Note saved successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    handleValidationErrors(xhr, $('#ipd-note-form'));
                }
            });
        });
    }

    function bindClinicalSnapshot() {
        $(document).on('click', '.ipd-clinical-edit-toggle', function () {
            $('#ipd-clinical-form').removeClass('d-none');
            $(this).addClass('d-none');
        });

        $(document).on('click', '.ipd-clinical-cancel', function () {
            $('#ipd-clinical-form').addClass('d-none');
            $('.ipd-clinical-edit-toggle').removeClass('d-none');
        });

        $(document).on('submit', '#ipd-clinical-form', function (event) {
            event.preventDefault();
            const form = this;
            const submitUrl = $(form).data('submit-url');
            loader('show');
            $('.err').remove();

            csrftoken().then(function (token) {
                $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    data: $(form).serialize() + '&_token=' + encodeURIComponent(token),
                    success: function (response) {
                        loader('hide');
                        sendmsg('success', response.message || 'Clinical snapshot updated.');
                        window.location.reload();
                    },
                    error: function (xhr) {
                        loader('hide');
                        handleValidationErrors(xhr, $(form));
                    }
                });
            });
        });
    }

    function bindPrescriptions() {
        let prescriptionComposer = null;

        function openPrescriptionModal(url) {
            if (!url) {
                return;
            }

            loader('show');
            $.get(url, function (response) {
                loader('hide');
                $('#ipdPrescriptionContent').html(response);
                $('#ipdPrescriptionModal').modal('show');

                if (window.flatpickr) {
                    flatpickr('.prescription-valid-till', { dateFormat: 'd-m-Y', minDate: 'today' });
                }

                if ($.fn.select2) {
                    $('#ipdPrescriptionModal .select2-modal').each(function () {
                        const $select = $(this);
                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        $select.select2({
                            dropdownParent: $('#ipdPrescriptionModal'),
                            width: '100%'
                        });
                    });
                }
            }).fail(function (xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to load prescription.');
            });
        }

        function getPrescriptionComposer() {
            if (!window.OPDCareShared || typeof window.OPDCareShared.createPrescriptionComposer !== 'function') {
                return null;
            }

            if (!prescriptionComposer) {
                prescriptionComposer = window.OPDCareShared.createPrescriptionComposer({
                    select2Parent: '#ipdPrescriptionModal',
                    getCsrfToken: function () {
                        return $('meta[name="csrf-token"]').attr('content') || '';
                    },
                    selectors: {
                        tbody: '#prescriptionItemsTbody',
                        medicine: '#prescription_entry_medicine',
                        dosage: '#prescription_entry_dosage',
                        instruction: '#prescription_entry_instruction',
                        frequency: '#prescription_entry_frequency',
                        days: '#prescription_entry_days',
                        addButton: '#addPrescriptionItemRow',
                        addIcon: '#prescriptionAddIcon',
                        cancelButton: '#cancelPrescriptionItemEdit',
                        form: '#ipdPrescriptionForm'
                    }
                });
            }

            return prescriptionComposer;
        }

        $(document).on('click', '.open-ipd-prescription-form', function (event) {
            event.preventDefault();
            openPrescriptionModal($(this).data('form-url'));
        });

        $(document).on('click', '.open-ipd-prescription-view', function (event) {
            event.preventDefault();
            openPrescriptionModal($(this).data('view-url'));
        });

        $(document).on('click', '#addPrescriptionItemRow', function () {
            const composer = getPrescriptionComposer();
            if (!composer) {
                return;
            }

            composer.addOrUpdateFromComposer(function (message, focusSelector) {
                sendmsg('error', message);
                $(focusSelector).trigger('focus');
            });
        });

        $(document).on('click', '#cancelPrescriptionItemEdit', function () {
            const composer = getPrescriptionComposer();
            if (composer) {
                composer.clearComposer();
            }
        });

        $(document).on('click', '.edit-prescription-item-row', function () {
            const composer = getPrescriptionComposer();
            if (composer) {
                composer.loadFromRow($(this).closest('tr'));
            }
        });

        $(document).on('click', '.remove-prescription-item-row', function () {
            const composer = getPrescriptionComposer();
            if (composer) {
                composer.removeRow($(this).closest('tr'));
            }
        });

        $(document).on('change', '#prescription_entry_medicine', function () {
            const composer = getPrescriptionComposer();
            if (composer) {
                composer.onMedicineChanged(true);
            }
        });

        $(document).on('select2:select', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_frequency', function () {
            const composer = getPrescriptionComposer();
            if (composer) {
                composer.focusNextField(this.id);
            }
        });

        $(document).on('keydown', '#ipdPrescriptionForm', function (event) {
            const targetId = event.target && event.target.id ? event.target.id : '';
            if (event.key === 'Enter' && /^prescription_entry_/.test(targetId)) {
                event.preventDefault();
                $('#addPrescriptionItemRow').trigger('click');
                return;
            }

            if (event.altKey && (event.key === 'n' || event.key === 'N')) {
                event.preventDefault();
                $('#addPrescriptionItemRow').trigger('click');
            }
        });

        $(document).on('submit', '#ipdPrescriptionForm', async function (event) {
            event.preventDefault();

            const storeUrl = $(this).data('store-url');
            if (!storeUrl) {
                sendmsg('error', 'Save URL is missing.');
                return;
            }

            const token = await csrftoken();
            const payload = $(this).serialize() + '&_token=' + encodeURIComponent(token);
            const $saveButton = $('.save-ipd-prescription-btn');

            $saveButton.prop('disabled', true).text('Saving...');
            loader('show');

            $.ajax({
                url: storeUrl,
                type: 'POST',
                data: payload,
                success: function (response) {
                    loader('hide');
                    $('#ipdPrescriptionModal').modal('hide');
                    sendmsg('success', response.message || 'Prescription saved successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    let message = xhr?.responseJSON?.message || 'Unable to save prescription.';
                    if (xhr?.responseJSON?.errors && xhr.responseJSON.errors.length) {
                        message = xhr.responseJSON.errors[0].message || message;
                    }
                    sendmsg('error', message);
                },
                complete: function () {
                    $saveButton.prop('disabled', false).text('Save Prescription');
                }
            });
        });

        $(document).on('click', '.edit-ipd-prescription-btn', function () {
            openPrescriptionModal($(this).data('form-url'));
        });

        $(document).on('click', '.print-ipd-prescription-btn', function () {
            const printUrl = $(this).data('print-url');
            if (printUrl) {
                window.open(printUrl, '_blank');
            }
        });

        $(document).on('click', '.delete-ipd-prescription-btn', async function () {
            const deleteUrl = $(this).data('delete-url');
            if (!deleteUrl) {
                return;
            }

            const result = await Swal.fire({
                title: 'Delete prescription?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) {
                return;
            }

            const token = await csrftoken();
            loader('show');

            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: token,
                    _method: 'DELETE'
                },
                success: function (response) {
                    loader('hide');
                    $('#ipdPrescriptionModal').modal('hide');
                    sendmsg('success', response.message || 'Prescription deleted successfully.');
                    window.location.reload();
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to delete prescription.');
                }
            });
        });

        $(document).on('hidden.bs.modal', '#ipdPrescriptionModal', function () {
            $('#ipdPrescriptionModal .select2-modal').each(function () {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });

            $('#ipdPrescriptionContent').empty();
        });

        $(document).on('shown.bs.modal', '#ipdPrescriptionModal', function () {
            const composer = getPrescriptionComposer();
            if (composer) {
                composer.initialize();
                composer.focusStart();
            }
        });
    }

    function fetchPatientSuggestions(searchText, searchBy) {
        $.get(route('search-patients'), { query: searchText, search_by: searchBy }, function (data) {
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

            $('#ipd-patient-suggestions').html(html);
        });
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
        selectedExistingPhone = patient.phone || '';
        $('input[name="selected_patient_id"]').val(patient.id || '');
        $('input[name="phone"]').val(patient.phone || '');
        $('input[name="name"]').val(patient.name || '');
        $('input[name="guardian_name"]').val(patient.guardian_name || '');
        $('input[name="age_years"]').val(patient.age_years || '');
        $('input[name="age_months"]').val(patient.age_months || '');
        $('input[name="email"]').val(patient.email || '');
        $('textarea[name="address"]').val(patient.address || '');
        $('select[name="country_code"]').val(patient.country_code || '+91').trigger('change');
        $('select[name="gender"]').val(patient.gender || '').trigger('change');
        $('select[name="blood_group"]').val(patient.blood_group || '').trigger('change');
        $('select[name="marital_status"]').val(patient.marital_status || '').trigger('change');
        $('select[name="patient_category_id"]').val(patient.patient_category_id || '').trigger('change');
        $('select[name="nationality_id"]').val(patient.nationality_id || '').trigger('change');
        $('select[name="religion_id"]').val(patient.religion_id || '').trigger('change');
        if (patient.date_of_birth) {
            $('#ipd-dob').val(patient.date_of_birth);
        }
    }

    function clearExistingPatientSelection() {
        selectedExistingPhone = '';
        $('input[name="selected_patient_id"]').val('');
    }

    function loadDoctors($scope, selectedId) {
        const departmentId = $scope.find('select[name="hr_department_id"]').val();
        const doctorId = selectedId || $scope.find('#prefill_doctor_id').val() || '';
        const $doctorSelect = $scope.find('select[name="doctor_id"]');
        
        if (!departmentId) {
            $doctorSelect.html('<option value="">Select</option>');
            if ($doctorSelect.hasClass('select2-hidden-accessible')) {
                const $dropdownParent = $scope.closest('.add-datamodal');
                $doctorSelect.select2('destroy').select2({ 
                    dropdownParent: $dropdownParent.length ? $dropdownParent : $scope,
                    width: '100%'
                });
            }
            $doctorSelect.trigger('change');
            return;
        }

        $.get(route('load-doctors'), { hr_department_id: departmentId }, function (data) {
            let html = '<option value="">Select</option>';
            $.each(data, function (_, doctor) {
                const selected = String(doctor.id) === String(doctorId) ? 'selected' : '';
                html += `<option value="${doctor.id}" ${selected}>${doctor.full_name}</option>`;
            });
            
            $doctorSelect.html(html);
            
            // Reinitialize Select2 after updating options
            if ($doctorSelect.hasClass('select2-hidden-accessible')) {
                const $dropdownParent = $scope.closest('.add-datamodal');
                $doctorSelect.select2('destroy').select2({ 
                    dropdownParent: $dropdownParent.length ? $dropdownParent : $scope,
                    width: '100%'
                });
            }
            
            $doctorSelect.trigger('change');
            $scope.find('#prefill_doctor_id').val('');
        });
    }

    function updateBedSummary($scope) {
        const option = $scope.find('select[name="bed_id"] option:selected');
        if (!option.val()) {
            $scope.find('#ipd-bed-summary').text('Select a bed to see its location, type and standard base charge.');
            return;
        }

        const summary = [
            'Ward: ' + (option.data('ward') || '-'),
            'Room: ' + (option.data('room') || '-'),
            'Type: ' + (option.data('type') || '-'),
            'Base Charge: ' + (option.data('charge') || '0.00')
        ].join(' | ');

        $scope.find('#ipd-bed-summary').text(summary);
    }

    function initAdmissionModal($modal) {
        const $dialog = $modal.find('.modal-dialog');
        $dialog.addClass('modal-dialog-scrollable');
        if (!$dialog.hasClass('modal-xl') && !$dialog.hasClass('modal-lg')) {
            $dialog.addClass('modal-xl');
        }

        if ($.fn.select2) {
            const $dropdownParent = $modal.closest('.add-datamodal');
            $modal.find('.select2-modal').each(function () {
                const $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({ 
                    dropdownParent: $dropdownParent.length ? $dropdownParent : $modal,
                    width: '100%'
                });
            });

            // Keep Select2 popups stable when the modal body scrolls.
            $modal.find('.modal-body').off('scroll.ipdSelect2Fix').on('scroll.ipdSelect2Fix', function () {
                $modal.find('.select2-hidden-accessible').each(function () {
                    $(this).select2('close');
                });
            });
        }

        if (window.flatpickr) {
            if ($('#ipd-admission-date').length) {
                flatpickr('#ipd-admission-date', { enableTime: true, dateFormat: 'd-m-Y H:i' });
            }
            if ($('#ipd-expected-discharge-date').length) {
                flatpickr('#ipd-expected-discharge-date', { dateFormat: 'd-m-Y', minDate: 'today' });
            }
            if ($('#ipd-dob').length) {
                flatpickr('#ipd-dob', { dateFormat: 'd-m-Y', maxDate: 'today' });
            }
        }

        if ($modal.find('#save-ipd-admission').length) {
            loadDoctors($modal);
            updateBedSummary($modal);
        }
    }

    function handleValidationErrors(xhr, $scope) {
        if (xhr.status === 422 && xhr.responseJSON?.errors) {
            const errors = xhr.responseJSON.errors;
            const errorMessages = [];

            for (const field in errors) {
                const fieldCode = errors[field].code;
                let $field = $scope.find('[name="' + fieldCode + '"]');
                if (!$field.length) {
                    $field = $scope.find('[name="' + fieldCode + '[]"]');
                }
                if ($field.length) {
                    if ($field.hasClass('select2-hidden-accessible') || $field.hasClass('select2-modal')) {
                        $field.next('.select2-container').after('<div class="err text-danger">' + errors[field].message + '</div>');
                    } else {
                        $field.last().after('<div class="err text-danger">' + errors[field].message + '</div>');
                    }
                }
                errorMessages.push(errors[field].message);
            }

            if (errorMessages.length) {
                sendmsg('error', errorMessages.join('<br>'));
            }
            return;
        }

        sendmsg('error', xhr?.responseJSON?.message || 'Something went wrong.');
    }

    function showModal(response, sizeClass) {
        $('#ajaxdata').html(response);
        const $modal = $('.add-datamodal');
        const $dialog = $modal.find('.modal-dialog');
        $dialog
            .removeClass('modal-sm modal-lg modal-xl modal-fullscreen modal-dialog-centered')
            .addClass((sizeClass || 'modal-xl') + ' modal-dialog-scrollable');
        $modal.modal('show');
    }
});