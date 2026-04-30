$(document).ready(function() {
    var table = $('#visits-table').DataTable({
        dom: "fBrtip",
        buttons: [{
                extend: 'copy',
                className: 'buttons-copy btn btn-light',
                text: '<i class=\"fa fa-copy\"></i>',
                titleAttr: 'Copy'
            },
            {
                extend: 'csv',
                className: 'buttons-csv btn btn-info',
                text: '<i class=\"fa fa-file-csv\"></i>',
                titleAttr: 'Export as CSV'
            },
            {
                extend: 'excel',
                className: 'buttons-excel btn btn-success',
                text: '<i class=\"fa fa-file-excel\"></i>',
                titleAttr: 'Export as Excel'
            },
            {
                extend: 'pdf',
                className: 'buttons-pdf btn btn-danger',
                text: '<i class=\"fa fa-file-pdf\"></i>',
                titleAttr: 'Export as PDF'
            },
            {
                extend: 'print',
                className: 'buttons-print btn btn-primary',
                text: '<i class=\"fa fa-print\"></i>',
                titleAttr: 'Print Table'
            },
            {
                extend: 'colvis',
                className: 'buttons-colvis btn btn-dark',
                text: '<i class=\"fa fa-columns\"></i>',
                titleAttr: 'Column Visibility'
            }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search Leaves...'
        },
        lengthChange: true,
        paging: true,
        info: true,
        ordering: false,
        scrollX: true,
        autoWidth: true,
        responsive: true
    });
    var liveconsultationTable = $('#live-consultation-table').DataTable({
        dom: "fBrtip",
        buttons: [{
                extend: 'copy',
                className: 'buttons-copy btn btn-light',
                text: '<i class=\"fa fa-copy\"></i>',
                titleAttr: 'Copy'
            },
            {
                extend: 'csv',
                className: 'buttons-csv btn btn-info',
                text: '<i class=\"fa fa-file-csv\"></i>',
                titleAttr: 'Export as CSV'
            },
            {
                extend: 'excel',
                className: 'buttons-excel btn btn-success',
                text: '<i class=\"fa fa-file-excel\"></i>',
                titleAttr: 'Export as Excel'
            },
            {
                extend: 'pdf',
                className: 'buttons-pdf btn btn-danger',
                text: '<i class=\"fa fa-file-pdf\"></i>',
                titleAttr: 'Export as PDF'
            },
            {
                extend: 'print',
                className: 'buttons-print btn btn-primary',
                text: '<i class=\"fa fa-print\"></i>',
                titleAttr: 'Print Table'
            },
            {
                extend: 'colvis',
                className: 'buttons-colvis btn btn-dark',
                text: '<i class=\"fa fa-columns\"></i>',
                titleAttr: 'Column Visibility'
            }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search Leaves...'
        },
        lengthChange: true,
        paging: true,
        info: true,
        ordering: true,
        scrollX: true,
        autoWidth: true,
        responsive: true
    });

    $(table.table().container()).find('.dataTables_filter input').addClass('form-control').css({
        'width': '300px',
        'display': 'inline-block'
    });
    initTooltips();
    table.on('draw.dt responsive-display.dt column-visibility.dt', function () {
        initTooltips();
    });
    liveconsultationTable.on('draw.dt responsive-display.dt column-visibility.dt', function () {
        initTooltips();
    });

    function initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }
    // Flatpickr init for date fields
    flatpickr('input[type="date"]', {
        dateFormat: 'd-m-Y'
    });
    flatpickr('input[type="datetime-local"]', {
        enableTime: true,
        dateFormat: 'd-m-Y H:i'
    });

    function initChargesLedgerTables() {
        if ($.fn.dataTable.isDataTable('#charges-ledger-table') === false && $('#charges-ledger-table').length) {
            $('#charges-ledger-table').DataTable({
                dom: 'frtip',
                paging: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50],
                ordering: true,
                info: true,
                scrollX: true,
                autoWidth: false,
                responsive: true,
                language: {
                    search: '',
                    searchPlaceholder: 'Search charges...'
                }
            });
        }

        if ($.fn.dataTable.isDataTable('#payments-ledger-table') === false && $('#payments-ledger-table').length) {
            $('#payments-ledger-table').DataTable({
                dom: 'frtip',
                paging: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50],
                ordering: true,
                info: true,
                scrollX: true,
                autoWidth: false,
                responsive: true,
                language: {
                    search: '',
                    searchPlaceholder: 'Search payments...'
                }
            });
        }
    }

    initChargesLedgerTables();

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
        var tabTarget = $(event.target).data('bs-target');

        if (tabTarget === '#charges-ledger-panel' && $.fn.dataTable.isDataTable('#charges-ledger-table')) {
            setTimeout(function () {
                $('#charges-ledger-table').DataTable().columns.adjust().responsive.recalc();
            }, 120);
        }

        if (tabTarget === '#payments-ledger-panel' && $.fn.dataTable.isDataTable('#payments-ledger-table')) {
            setTimeout(function () {
                $('#payments-ledger-table').DataTable().columns.adjust().responsive.recalc();
            }, 120);
        }
    });
});

$(document).ready(function() {
    function activateTabFromHash() {
        var hash = window.location.hash;
        if (!hash || hash.length < 2) {
            return;
        }

        var $tabTrigger = $('a[data-bs-toggle="pill"][href="' + hash + '"]');
        if (!$tabTrigger.length) {
            return;
        }

        var tabEl = $tabTrigger.get(0);
        if (window.bootstrap && window.bootstrap.Tab) {
            window.bootstrap.Tab.getOrCreateInstance(tabEl).show();
            return;
        }

        $tabTrigger.tab('show');
    }

    activateTabFromHash();

    // Trigger adjustment after tab is shown
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(event) {
        let tabId = $(event.target).attr('href');

        if (tabId && tabId.startsWith('#')) {
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', tabId);
            } else {
                window.location.hash = tabId;
            }
        }

        if (tabId === '#visits') {
            setTimeout(() => {
                if ($.fn.dataTable.isDataTable('#visits-table')) {
                    $('#visits-table').DataTable().columns.adjust().responsive.recalc();
                }
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#diagnosis') {
            setTimeout(() => {
                if ($.fn.dataTable.isDataTable('#diagnosis-table')) {
                    $('#diagnosis-table').DataTable().columns.adjust().responsive.recalc();
                }
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#live-consultation-project') {
            setTimeout(() => {
                if ($.fn.dataTable.isDataTable('#live-consultation-table')) {
                    $('#live-consultation-table').DataTable().columns.adjust().responsive.recalc();
                }
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#charges') {
            setTimeout(() => {
                if ($.fn.dataTable.isDataTable('#charges-ledger-table')) {
                    $('#charges-ledger-table').DataTable().columns.adjust().responsive.recalc();
                }
                if ($.fn.dataTable.isDataTable('#payments-ledger-table')) {
                    $('#payments-ledger-table').DataTable().columns.adjust().responsive.recalc();
                }
            }, 200);
        }
    });

    $(window).on('hashchange', function() {
        activateTabFromHash();
    });
});

// Habits clone/remove
$(document).on('click', '.add-habit', function() {
    var row = $(this).closest('.habit-row').clone();
    row.find('input,select').val('');
    row.find('.remove-habit').removeClass('d-none');
    $('#habitsRows').append(row);
});

$(document).on('click', '.remove-habit', function() {
    $(this).closest('.habit-row').remove();
});

// Family clone/remove
$(document).on('click', '.add-family', function() {
    var row = $(this).closest('.family-row').clone();
    row.find('input,select').val('');
    row.find('.remove-family').removeClass('d-none');
    $('#familyRows').append(row);
});

$(document).on('click', '.remove-family', function() {
    $(this).closest('.family-row').remove();
});

(function() {
    var selectedVisit = null;
    var allAllergicReactionOptions = [];

    function initVitalsSocialSelect2() {
        if (!$.fn.select2) {
            return;
        }

        $('.select2-vitals-social').each(function() {
            var $field = $(this);
            if ($field.hasClass('select2-hidden-accessible')) {
                $field.select2('destroy');
            }

            $field.select2({
                dropdownParent: $('#vitalsSocialModal'),
                width: '100%',
                placeholder: 'Select options'
            });
        });
    }

    function normalizeMultiValues(values) {
        if (!Array.isArray(values)) {
            return [];
        }

        return values
            .filter(function(value) {
                return value !== null && value !== '';
            })
            .map(function(value) {
                return String(value);
            });
    }

    function cacheAllergicReactionOptions() {
        allAllergicReactionOptions = [];

        $('#allergic-reactions-input option').each(function() {
            var $option = $(this);
            allAllergicReactionOptions.push({
                value: String($option.val() || ''),
                text: $option.text(),
                allergyId: String($option.data('allergy-id') || ''),
            });
        });
    }

    function renderAllergicReactionOptions(selectedAllergies, selectedReactions) {
        var selectedAllergyIds = normalizeMultiValues(selectedAllergies);
        var selectedReactionIds = normalizeMultiValues(selectedReactions);
        var $reactionSelect = $('#allergic-reactions-input');

        if (allAllergicReactionOptions.length === 0) {
            cacheAllergicReactionOptions();
        }

        var filteredOptions = allAllergicReactionOptions.filter(function(option) {
            if (selectedAllergyIds.length === 0) {
                return true;
            }

            return selectedAllergyIds.indexOf(option.allergyId) !== -1;
        });

        $reactionSelect.empty();
        filteredOptions.forEach(function(option) {
            var isSelected = selectedReactionIds.indexOf(option.value) !== -1;
            var $option = $('<option></option>')
                .val(option.value)
                .text(option.text)
                .attr('data-allergy-id', option.allergyId);

            if (isSelected) {
                $option.prop('selected', true);
            }

            $reactionSelect.append($option);
        });

        $reactionSelect.trigger('change');
    }

    function setMultiSelectValues($select, rawValues) {
        var values = normalizeMultiValues(rawValues);
        var selected = [];

        values.forEach(function(value) {
            if ($select.find('option[value="' + value + '"]').length > 0) {
                selected.push(value);
                return;
            }

            var legacyValue = value.toLowerCase();
            $select.find('option').each(function() {
                var $option = $(this);
                var optionText = ($option.text() || '').trim().toLowerCase();
                if (optionText === legacyValue || optionText.indexOf(legacyValue + ' (') === 0) {
                    selected.push(String($option.val()));
                    return false;
                }
            });
        });

        $select.val(selected).trigger('change');
    }

    function resetAlert() {
        var $alert = $('#vitals-social-alert');
        $alert.addClass('d-none').removeClass('alert-success alert-danger').text('');
    }

    function resetRepeaters() {
        $('#habitsRows .habit-row:not(:first)').remove();
        $('#familyRows .family-row:not(:first)').remove();

        $('#habitsRows .habit-row:first').find('select,input').val('');
        $('#habitsRows .habit-row:first .remove-habit').addClass('d-none');

        $('#familyRows .family-row:first').find('select,input').val('');
        $('#familyRows .family-row:first .remove-family').addClass('d-none');
    }

    function appendHabitRow(item) {
        var $row = $('#habitsRows .habit-row:first').clone();
        $row.find('select,input').val('');
        $row.find('.remove-habit').removeClass('d-none');

        $row.find('select[name="habit_name[]"]').val(item.name || '');
        $row.find('select[name="habit_status[]"]').val(item.status || '');
        $('#habitsRows').append($row);
    }

    function appendFamilyRow(item) {
        var $row = $('#familyRows .family-row:first').clone();
        $row.find('select,input').val('');
        $row.find('.remove-family').removeClass('d-none');

        $row.find('select[name="family_disease[]"]').val(item.disease || '');
        $row.find('select[name="family_relation[]"]').val(item.relation || '');
        $row.find('input[name="family_age[]"]').val(item.age || '');
        $row.find('input[name="family_comments[]"]').val(item.comments || '');
        $('#familyRows').append($row);
    }

    function fillVisitData(visit) {
        resetAlert();
        resetRepeaters();

        $('#selected-opd-case-no').text(visit.case_no || '-');
        $('#selected-opd-date').text(visit.appointment_date || '-');

        $('#respiration-input').val(visit.respiration || '');
        $('#diabetes-input').val(visit.diabetes || '');
        $('#pluse-input').val(visit.pluse || '');
        $('#systolic-bp-input').val(visit.systolic_bp || '');
        $('#diastolic-bp-input').val(visit.diastolic_bp || '');
        $('#temperature-input').val(visit.temperature || '');
        $('#height-input').val(visit.height || '');
        $('#weight-input').val(visit.weight || '');
        $('#bmi-input').val(visit.bmi || '');

        $('#occupation-input').val(visit.occupation || '');
        $('#social-marital-status-input').val(visit.social_marital_status || '');
        $('#place-of-birth-input').val(visit.place_of_birth || '');
        $('#current-location-input').val(visit.current_location || '');
        $('#years-location-input').val(visit.years_in_current_location || '');

        setMultiSelectValues($('#known-allergies-input'), visit.social_known_allergies);
        renderAllergicReactionOptions($('#known-allergies-input').val(), visit.social_allergic_reactions);

        var habits = Array.isArray(visit.social_habits) ? visit.social_habits : [];
        if (habits.length > 0) {
            $('#habitsRows .habit-row:first select[name="habit_name[]"]').val(habits[0].name || '');
            $('#habitsRows .habit-row:first select[name="habit_status[]"]').val(habits[0].status || '');
            for (var i = 1; i < habits.length; i++) {
                appendHabitRow(habits[i]);
            }
        }

        var familyHistory = Array.isArray(visit.family_history) ? visit.family_history : [];
        if (familyHistory.length > 0) {
            $('#familyRows .family-row:first select[name="family_disease[]"]').val(familyHistory[0].disease || '');
            $('#familyRows .family-row:first select[name="family_relation[]"]').val(familyHistory[0].relation || '');
            $('#familyRows .family-row:first input[name="family_age[]"]').val(familyHistory[0].age || '');
            $('#familyRows .family-row:first input[name="family_comments[]"]').val(familyHistory[0].comments || '');
            for (var j = 1; j < familyHistory.length; j++) {
                appendFamilyRow(familyHistory[j]);
            }
        }
    }

    function updateBmiIfPossible() {
        var heightFeet = parseFloat($('#height-input').val());
        var weightKg = parseFloat($('#weight-input').val());
        if (!isNaN(heightFeet) && heightFeet > 0 && !isNaN(weightKg) && weightKg > 0) {
            var heightM = heightFeet * 0.3048;
            var bmi = weightKg / (heightM * heightM);
            if (isFinite(bmi)) {
                $('#bmi-input').val(bmi.toFixed(2));
            }
        }
    }

    $(document).on('click', '.human-body svg', function() {
        var area = $(this).data('position') || '';
        $('#data').text(area);
        $('#body-area-input').val(area);
    });

    $(document).on('input', '#height-input, #weight-input', function() {
        updateBmiIfPossible();
    });

    $(document).on('click', '.open-vitals-social', function() {
        var visitRaw = $(this).attr('data-visit');
        if (!visitRaw) {
            return;
        }

        try {
            selectedVisit = JSON.parse(visitRaw);
        } catch (error) {
            selectedVisit = null;
            sendmsg('error', 'Unable to load visit data. Please try again.');
            return;
        }

        fillVisitData(selectedVisit);
        $('#vitalsSocialForm').data('save-url-template', $(this).data('save-url-template'));
    });

    $('#vitalsSocialModal').on('shown.bs.modal', function() {
        initVitalsSocialSelect2();
    });

    $(document).on('change', '#known-allergies-input', function() {
        renderAllergicReactionOptions($(this).val(), $('#allergic-reactions-input').val() || []);
    });

    cacheAllergicReactionOptions();

    $(document).on('submit', '#vitalsSocialForm', function(event) {
        event.preventDefault();

        if (!selectedVisit || !selectedVisit.id) {
            sendmsg('error', 'Please select an OPD visit first.');
            return;
        }

        var saveUrlTemplate = $(this).data('save-url-template');
        if (!saveUrlTemplate) {
            sendmsg('error', 'Save URL is missing.');
            return;
        }

        var saveUrl = saveUrlTemplate.replace('__OPD_PATIENT__', selectedVisit.id);
        var $buttons = $('.save-vitals-social-btn');

        $buttons.each(function() {
            var $button = $(this);
            $button.data('original-label', $button.text());
        });

        $buttons.prop('disabled', true).text('Saving...');
        resetAlert();
        loader('show');
        $.ajax({
            url: saveUrl,
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                loader('hide');
                sendmsg('success', response.message || 'Vitals and social history saved successfully.');
                $("#vitalsSocialModal").modal('hide');
            },
            error: function(xhr) {
                loader('hide');
                var message = 'Failed to save vitals and social history.';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.length > 0) {
                    message = xhr.responseJSON.errors[0].message || message;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                sendmsg('error', message);
            },
            complete: function() {
                loader('hide');
                $buttons.each(function() {
                    var $button = $(this);
                    $button.prop('disabled', false).text($button.data('original-label') || $button.data('label') || 'Save');
                });
            }
        });
    });
})();

(function () {
    function openVisitSummaryModal(url) {
        if (!url) {
            return;
        }

        loader('show');
        $.get(url, function (response) {
            loader('hide');
            $('#opdVisitSummaryContent').html(response);
            $('#opdVisitSummaryModal').modal('show');
        }).fail(function (xhr) {
            loader('hide');
            var message = xhr?.responseJSON?.message || 'Unable to load visit summary.';
            sendmsg('error', message);
        });
    }

    $(document).on('click', '.open-visit-summary', function (event) {
        event.preventDefault();
        openVisitSummaryModal($(this).data('view-url'));
    });

    $(document).on('hidden.bs.modal', '#opdVisitSummaryModal', function () {
        $('#opdVisitSummaryContent').empty();
    });
})();

(function () {
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    $(document).on('click', '.open-charge-payment-form', function (event) {
        event.preventDefault();
        var url = $(this).data('url');
        var chargeIdRaw = parseInt($(this).data('charge-id'), 10);
        var chargeIdsRaw = ($(this).data('charge-ids') || '').toString().trim();
        var chargeIds = chargeIdsRaw
            ? chargeIdsRaw.split(',').map(function (id) { return parseInt(id, 10); }).filter(function (id) { return !isNaN(id); })
            : [];
        if (!isNaN(chargeIdRaw)) {
            chargeIds.push(chargeIdRaw);
        }
        chargeIds = Array.from(new Set(chargeIds));

        var title = ($(this).data('title') || '').toString();
        var contextNote = ($(this).data('context-note') || '').toString();
        if (!url) {
            sendmsg('error', 'Payment form URL missing.');
            return;
        }

        loader('show');
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: getCsrfToken(),
                charge_ids: chargeIds,
                title: title,
                context_note: contextNote
            },
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

    $(document).on('click', '.open-charge-discount-form', function (event) {
        event.preventDefault();
        var url = $(this).data('url');
        var chargeIdRaw = parseInt($(this).data('charge-id'), 10);
        var chargeIdsRaw = ($(this).data('charge-ids') || '').toString().trim();
        var chargeIds = chargeIdsRaw
            ? chargeIdsRaw.split(',').map(function (id) { return parseInt(id, 10); }).filter(function (id) { return !isNaN(id); })
            : [];
        if (!isNaN(chargeIdRaw)) {
            chargeIds.push(chargeIdRaw);
        }
        chargeIds = Array.from(new Set(chargeIds));

        if (!url) {
            sendmsg('error', 'Discount form URL missing.');
            return;
        }

        loader('show');
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: getCsrfToken(),
                charge_ids: chargeIds
            },
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

    $(document).on('submit', '#collectPaymentForm', function (event) {
        event.preventDefault();
        var submitUrl = $(this).data('submit-url');
        if (!submitUrl) {
            sendmsg('error', 'Payment submit URL missing.');
            return;
        }

        loader('show');
        $('.err').remove();

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: $(this).serialize() + '&_token=' + encodeURIComponent(getCsrfToken()),
            success: function (response) {
                loader('hide');
                $('#chargePaymentModal').modal('hide');
                sendmsg('success', response.message || 'Payment collected successfully.');
                window.location.reload();
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];

                    for (const field in errors) {
                        const fieldCode = errors[field].code;
                        const $field = $('[name="' + fieldCode + '"]');
                        $field.after('<div class="err text-danger">' + errors[field].message + '</div>');
                        errorMessages.push(errors[field].message);
                    }

                    if (errorMessages.length) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                    return;
                }

                sendmsg('error', xhr?.responseJSON?.message || 'Unable to collect payment.');
            }
        });
    });

    $(document).on('submit', '#applyDiscountForm', function (event) {
        event.preventDefault();
        var submitUrl = $(this).data('submit-url');
        if (!submitUrl) {
            sendmsg('error', 'Discount submit URL missing.');
            return;
        }

        loader('show');
        $('.err').remove();

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: $(this).serialize() + '&_token=' + encodeURIComponent(getCsrfToken()),
            success: function (response) {
                loader('hide');
                $('#chargePaymentModal').modal('hide');
                sendmsg('success', response.message || 'Discount applied successfully.');
                window.location.reload();
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];

                    for (const field in errors) {
                        const fieldCode = errors[field].code;
                        const $field = $('[name="' + fieldCode + '"]');
                        $field.after('<div class="err text-danger">' + errors[field].message + '</div>');
                        errorMessages.push(errors[field].message);
                    }

                    if (errorMessages.length) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                    return;
                }

                sendmsg('error', xhr?.responseJSON?.message || 'Unable to apply discount.');
            }
        });
    });

    $(document).on('change', '#is_advance', function () {
        var $amount = $('#collectPaymentForm').find('input[name="amount"]');
        if (!$amount.length) {
            return;
        }

        if ($(this).is(':checked')) {
            if (typeof $amount.attr('max') !== 'undefined') {
                $amount.data('pending-max', $amount.attr('max'));
            }
            $amount.removeAttr('max');
        } else {
            var pendingMax = $amount.data('pending-max');
            if (pendingMax) {
                $amount.attr('max', pendingMax);
            }
        }
    });

    $(document).on('hidden.bs.modal', '#chargePaymentModal', function () {
        $('#chargePaymentContent').empty();
    });

    $(document).on('click', '.delete-patient-payment', function (event) {
        event.preventDefault();

        var deleteUrl = $(this).data('delete-url');
        var amount = $(this).data('amount') || '0.00';
        if (!deleteUrl) {
            sendmsg('error', 'Delete URL is missing.');
            return;
        }

        Swal.fire({
            title: 'Delete payment?',
            text: 'Payment amount ' + amount + ' will be removed and charge balances will be recalculated.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            loader('show');
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    _method: 'DELETE'
                },
                success: function (response) {
                    loader('hide');
                    if (response.status) {
                        sendmsg('success', response.message || 'Payment deleted successfully.');
                        window.location.reload();
                    } else {
                        sendmsg('error', response.message || 'Unable to delete payment.');
                    }
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to delete payment.');
                }
            });
        });
    });
})();

(function () {
    var prescriptionComposer = null;

    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function openPrescriptionModal(url) {
        if (!url) {
            return;
        }

        loader('show');
        $.get(url, function (response) {
            loader('hide');
            $('#opdPrescriptionContent').html(response);
            $('#opdPrescriptionModal').modal('show');
            flatpickr('.prescription-valid-till', { dateFormat: 'd-m-Y', minDate: 'today' });
            initPrescriptionSelect2($('#opdPrescriptionModal'));
        }).fail(function (xhr) {
            loader('hide');
            var message = xhr?.responseJSON?.message || 'Unable to load prescription.';
            sendmsg('error', message);
        });
    }

    function initPrescriptionSelect2($scope) {
        if (!$.fn.select2) {
            return;
        }

        ($scope || $('#opdPrescriptionModal')).find('.select2-modal').each(function () {
            var $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                return;
            }

            $select.select2({
                dropdownParent: $('#opdPrescriptionModal'),
                width: '100%'
            });
        });
    }

    function reInitTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }

    function refreshPrescriptionSections() {
        return $.get(window.location.href, function (response) {
            var $html = $('<div>').append($.parseHTML(response));
            var visitsHtml = $html.find('#opd-visits-table-body').html();
            var prescriptionListHtml = $html.find('#patient-prescription-list-body').html();

            if (typeof visitsHtml !== 'undefined') {
                $('#opd-visits-table-body').html(visitsHtml);
            }

            if (typeof prescriptionListHtml !== 'undefined') {
                $('#patient-prescription-list-body').html(prescriptionListHtml);
            }

            reInitTooltips();
        });
    }

    function getPrescriptionComposer() {
        if (!window.OPDCareShared || typeof window.OPDCareShared.createPrescriptionComposer !== 'function') {
            return null;
        }

        if (!prescriptionComposer) {
            prescriptionComposer = window.OPDCareShared.createPrescriptionComposer({
                select2Parent: '#opdPrescriptionModal',
                getCsrfToken: getCsrfToken
            });
        }

        return prescriptionComposer;
    }

    $(document).on('click', '.open-prescription-form', function (event) {
        event.preventDefault();
        openPrescriptionModal($(this).data('form-url'));
    });

    $(document).on('click', '.open-prescription-view', function (event) {
        event.preventDefault();
        openPrescriptionModal($(this).data('view-url'));
    });

    $(document).on('click', '#addPrescriptionItemRow', function () {
        var composer = getPrescriptionComposer();
        if (!composer) {
            return;
        }

        composer.addOrUpdateFromComposer(function (message, focusSelector) {
            sendmsg('error', message);
            $(focusSelector).trigger('focus');
        });
    });

    $(document).on('click', '#cancelPrescriptionItemEdit', function () {
        var composer = getPrescriptionComposer();
        if (composer) {
            composer.clearComposer();
        }
    });

    $(document).on('click', '.edit-prescription-item-row', function () {
        var composer = getPrescriptionComposer();
        if (composer) {
            composer.loadFromRow($(this).closest('tr'));
        }
    });

    $(document).on('click', '.remove-prescription-item-row', function () {
        var composer = getPrescriptionComposer();
        if (composer) {
            composer.removeRow($(this).closest('tr'));
        }
    });

    $(document).on('change', '#prescription_entry_medicine', function () {
        var composer = getPrescriptionComposer();
        if (composer) {
            composer.onMedicineChanged(true);
        }
    });

    $(document).on('select2:select', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_frequency', function () {
        var composer = getPrescriptionComposer();
        if (composer) {
            composer.focusNextField(this.id);
        }
    });

    $(document).on('keydown', '#prescription_entry_days', function (event) {
        if (event.key !== 'Tab' || event.shiftKey) {
            return;
        }

        event.preventDefault();
        $('#addPrescriptionItemRow').trigger('focus');
    });

    $(document).on('keydown', '#opdPrescriptionForm', function (event) {
        var targetId = event.target && event.target.id ? event.target.id : '';
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

    $(document).on('submit', '#opdPrescriptionForm', function (event) {
        event.preventDefault();

        var storeUrl = $(this).data('store-url');
        if (!storeUrl) {
            sendmsg('error', 'Save URL is missing.');
            return;
        }

        var payload = $(this).serialize() + '&_token=' + encodeURIComponent(getCsrfToken());
        var $saveButton = $('.save-prescription-btn');
        $saveButton.prop('disabled', true).text('Saving...');
        loader('show');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            data: payload,
            success: function (response) {
                refreshPrescriptionSections().always(function () {
                    loader('hide');
                    $('#opdPrescriptionModal').modal('hide');
                    sendmsg('success', response.message || 'Prescription saved successfully.');
                });
            },
            error: function (xhr) {
                loader('hide');
                var message = xhr?.responseJSON?.message || 'Unable to save prescription.';
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

    $(document).on('click', '.edit-prescription-btn', function () {
        openPrescriptionModal($(this).data('form-url'));
    });

    $(document).on('click', '.print-prescription-btn', function () {
        var printUrl = $(this).data('print-url');
        if (printUrl) {
            window.open(printUrl, '_blank');
        }
    });

    $(document).on('click', '.delete-prescription-btn', function () {
        var deleteUrl = $(this).data('delete-url');
        if (!deleteUrl) {
            return;
        }

        Swal.fire({
            title: 'Delete prescription?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            loader('show');
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    _method: 'DELETE'
                },
                success: function (response) {
                    refreshPrescriptionSections().always(function () {
                        loader('hide');
                        $('#opdPrescriptionModal').modal('hide');
                        sendmsg('success', response.message || 'Prescription deleted successfully.');
                    });
                },
                error: function (xhr) {
                    loader('hide');
                    var message = xhr?.responseJSON?.message || 'Unable to delete prescription.';
                    sendmsg('error', message);
                }
            });
        });
    });

    $(document).on('hidden.bs.modal', '#opdPrescriptionModal', function () {
        $('#opdPrescriptionModal .select2-modal').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        $('#opdPrescriptionContent').empty();
    });

    $(document).on('shown.bs.modal', '#opdPrescriptionModal', function () {
        var composer = getPrescriptionComposer();
        if (composer) {
            composer.initialize();
            composer.focusStart();
        }
    });
})();

(function () {
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function refreshDiagnosticPreview() {
        if (!window.OPDCareShared || typeof window.OPDCareShared.refreshDiagnosticPreview !== 'function') {
            return;
        }

        window.OPDCareShared.refreshDiagnosticPreview('#diagnostic_test_ids', '#diagnostic-test-preview-body');
    }

    function openDiagnosticOrderModal(showUrl, orderType, storeUrl) {
        if (!showUrl || !orderType || !storeUrl) {
            sendmsg('error', 'Diagnostic order configuration is missing.');
            return;
        }

        loader('show');
        $.ajax({
            url: showUrl,
            type: 'POST',
            data: {
                order_type: orderType,
                _token: getCsrfToken()
            },
            success: function (response) {
                loader('hide');
                $('#diagnosticOrderContent').html(response);
                $('#diagnosticOrderModal').modal('show');
                $('#saveDiagnosticOrderForm').data('store-url', storeUrl);

                $('#diagnosticOrderModal .select2-modal').select2({
                    dropdownParent: $('#diagnosticOrderModal'),
                    width: '100%'
                });

                refreshDiagnosticPreview();
            },
            error: function (xhr) {
                loader('hide');
                var message = xhr?.responseJSON?.message || 'Unable to load diagnostic order form.';
                sendmsg('error', message);
            }
        });
    }

    $(document).on('click', '.open-diagnostic-order', function (event) {
        event.preventDefault();
        openDiagnosticOrderModal($(this).data('show-url'), $(this).data('order-type'), $(this).data('store-url'));
    });

    $(document).on('change', '#diagnostic_test_ids', function () {
        refreshDiagnosticPreview();
    });

    $(document).on('submit', '#saveDiagnosticOrderForm', function (event) {
        event.preventDefault();

        var storeUrl = $(this).data('store-url');
        if (!storeUrl) {
            sendmsg('error', 'Save URL is missing.');
            return;
        }

        loader('show');
        $('.err').remove();

        $.ajax({
            url: storeUrl,
            type: 'POST',
            data: $(this).serialize() + '&_token=' + encodeURIComponent(getCsrfToken()),
            success: function (response) {
                loader('hide');
                $('#diagnosticOrderModal').modal('hide');
                sendmsg('success', response.message || 'Diagnostic order created successfully.');
                window.location.reload();
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    for (var field in errors) {
                        var fieldCode = errors[field].code;
                        var $field = $('[name="' + fieldCode + '"]');
                        if (!$field.length) {
                            $field = $('[name="' + fieldCode + '[]"]');
                        }

                        if ($field.hasClass('select2-modal') || $field.hasClass('select2-hidden-accessible')) {
                            $field.next('.select2-container').after('<div class="err text-danger">' + errors[field].message + '</div>');
                        } else {
                            $field.after('<div class="err text-danger">' + errors[field].message + '</div>');
                        }
                        errorMessages.push(errors[field].message);
                    }

                    if (errorMessages.length) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                    return;
                }

                var message = xhr?.responseJSON?.message || 'Unable to create diagnostic order.';
                sendmsg('error', message);
            }
        });
    });

    $(document).on('click', '.delete-diagnostic-item', function (event) {
        event.preventDefault();

        var deleteUrl = $(this).data('delete-url');
        var testName = $(this).data('test-name') || 'this test';
        if (!deleteUrl) {
            sendmsg('error', 'Delete URL is missing.');
            return;
        }

        Swal.fire({
            title: 'Delete test?',
            text: testName + ' will be removed and linked charges will be reversed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            loader('show');
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    _method: 'DELETE'
                },
                success: function (response) {
                    loader('hide');
                    if (response.status) {
                        sendmsg('success', response.message || 'Diagnostic test deleted successfully.');
                        window.location.reload();
                    } else {
                        sendmsg('error', response.message || 'Unable to delete diagnostic test.');
                    }
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to delete diagnostic test.');
                }
            });
        });
    });

    $(document).on('hidden.bs.modal', '#diagnosticOrderModal', function () {
        $('#diagnosticOrderModal .select2-modal').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        $('#diagnosticOrderContent').empty();
    });
})();
