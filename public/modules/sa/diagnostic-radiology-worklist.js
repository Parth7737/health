$(document).ready(function () {
    const table = $('#xin-table').DataTable({
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
                d.status = $('#filter-status').val();
                d.date_from = $('#filter-date-from').val();
                d.date_to = $('#filter-date-to').val();
            }
        },
        columns: [
            { data: null, name: 'serial_no', orderable: false, searchable: false, render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1 },
            { data: 'order_no', name: 'order.order_no' },
            { data: 'ordered_at', name: 'created_at' },
            { data: 'patient_name', name: 'order.patient.name' },
            { data: 'visit_no', name: 'order.visitable.case_no' },
            { data: 'test_name', name: 'test_name' },
            { data: 'status', name: 'status' },
            { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: 'fBrtip',
        responsive: true
    });

    function initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }

    initTooltips();
    table.on('draw.dt responsive-display.dt column-visibility.dt', function () {
        initTooltips();
    });
    table.on('draw.dt responsive-display.dt', initTooltips);

    function initDiagnosisDatePicker() {
        flatpickr('.diagnosis-date', {
            dateFormat: 'd-m-Y',
        });
    }
    initDiagnosisDatePicker();
    $('#filter-status, #filter-date-from, #filter-date-to').on('change', function () {
        table.ajax.reload();
    });

    $('#clear-filters').on('click', function () {
        $('#filter-status').val('');
        $('#filter-date-from').val('');
        $('#filter-date-to').val('');
        table.ajax.reload();
    });

    $(document).on('click', '.update-item-status', async function () {
        const url = $(this).data('url');
        const confirm = await Swal.fire({
            title: 'Move to next status?',
            text: 'Are you sure you want to move this item to the next status.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, move',
            cancelButtonText: 'Cancel'
        });

        if (!confirm.isConfirmed) {
            return;
        }

        const token = await csrftoken();

        loader('show');
        $.post(url, { _token: token }, function (response) {
            loader('hide');
            table.ajax.reload(null, false);
            sendmsg('success', response.message || 'Status updated successfully.');
        }).fail(function (xhr) {
            loader('hide');
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to update status.');
        });
    });

    $(document).on('click', '.open-report-form', async function () {
        const id = $(this).data('id');
        loader('show');
        const token = await csrftoken();

        $.post(route('showform', { item: id }), { _token: token }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal .modal-dialog').addClass('modal-xl');
            $('.add-datamodal').modal('show');
        }).fail(function (xhr) {
            loader('hide');
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to load report form.');
        });
    });

    $(document).on('click', '.open-charge-payment-form', async function () {
        const url = $(this).data('url');
        const chargeId = $(this).data('charge-id');
        const title = $(this).data('title');
        const contextNote = $(this).data('context-note');
        const token = await csrftoken();

        loader('show');
        $.post(url, {
            _token: token,
            charge_ids: [chargeId],
            title: title,
            context_note: contextNote
        }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal .modal-dialog').addClass('modal-xl');
            $('.add-datamodal').modal('show');
        }).fail(function (xhr) {
            loader('hide');
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to load payment form.');
        });
    });

    $(document).on('submit', '#saveReportForm', async function (e) {
        e.preventDefault();
        loader('show');
        $('.err').remove();
        const token = await csrftoken();
        const $form = $(this);
        const id = $form.find('input[name="item_id"]').val();

        $.ajax({
            url: route('save', { item: id }),
            type: 'POST',
            data: $form.serialize() + '&_token=' + encodeURIComponent(token),
            success: function (response) {
                loader('hide');
                $('.add-datamodal').modal('hide');
                table.ajax.reload(null, false);
                sendmsg('success', response.message || 'Saved successfully.');
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];

                    for (const field in errors) {
                        errorMessages.push(errors[field].message);
                    }

                    if (errorMessages.length) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                } else {
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to save report.');
                }
            }
        });
    });

    $(document).on('submit', '#collectPaymentForm', async function (e) {
        e.preventDefault();
        loader('show');
        $('.err').remove();
        const token = await csrftoken();
        const $form = $(this);
        const submitUrl = $form.data('submit-url');

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: $form.serialize() + '&_token=' + encodeURIComponent(token),
            success: function (response) {
                loader('hide');
                $('.add-datamodal').modal('hide');
                table.ajax.reload(null, false);
                sendmsg('success', response.message || 'Payment collected successfully.');
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];

                    for (const field in errors) {
                        errorMessages.push(errors[field].message);
                    }

                    if (errorMessages.length) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                } else {
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to collect payment.');
                }
            }
        });
    });

    $(document).on('change', '#is_advance', function () {
        const $amount = $('#collectPaymentForm').find('input[name="amount"]');
        if (!$amount.length) {
            return;
        }

        if ($(this).is(':checked')) {
            if (typeof $amount.attr('max') !== 'undefined') {
                $amount.data('pending-max', $amount.attr('max'));
            }
            $amount.removeAttr('max');
        } else {
            const pendingMax = $amount.data('pending-max');
            if (pendingMax) {
                $amount.attr('max', pendingMax);
            }
        }
    });
});
