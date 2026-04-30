$(document).ready(function() {
    const $diagnosisTable = $('#diagnosis-table');
    if (!$diagnosisTable.length) {
        return;
    }

    const patientId = $diagnosisTable.data('patient-id');
    if (!patientId) {
        return;
    }

    const loadroute = route('loadtable', { patient: patientId });

    const diagnosistable = $diagnosisTable.DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: true,
        lengthChange: true,
        scrollX: true,
        ajax: {
            url: loadroute,
            type: 'POST',
            data: function(d) {
                d._token = window.Laravel.csrfToken;
            }
        },
        columns: [
            { data: 'report_type', name: 'report_type' },
            { data: 'report_date', name: 'report_date' },
            { data: 'description', name: 'description' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: 'fBrtip',
        autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function(e, dt) { dt.ajax.reload(null, false); } },
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

    initTooltips();
    diagnosistable.on('draw.dt responsive-display.dt column-visibility.dt', function () {
        initTooltips();
    });
    diagnosistable.on('draw.dt', function() {
        initTooltips();
    });

    function initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }

    function initDiagnosisDatePicker() {
        flatpickr('.diagnosis-date', {
            dateFormat: 'd-m-Y',
            maxDate: 'today'
        });
    }

    $(document).on('click', '.add-diagnosis-btn, .edit-diagnosis-btn', async function() {
        loader();
        const id = $(this).data('id') || '';
        const url = route('showform', { patient: patientId });
        const token = await csrftoken();

        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id, _token: token },
            success: function(response) {
                loader('hide');
                $('#ajaxdata').html(response);
                $('.add-datamodal .modal-dialog').removeClass('modal-fullscreen').addClass('modal-xl');
                $('.add-datamodal').modal('show');
                initDiagnosisDatePicker();
            },
            error: function() {
                loader('hide');
                $('.err').remove();
                sendmsg('error', 'Failed to load diagnosis form.');
            }
        });
    });

    $(document).on('submit', '#diagnosis-form', async function(e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();

        const fd = new FormData(this);
        fd.append('_token', token);

        $.ajax({
            url: route('store', { patient: patientId }),
            type: 'POST',
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                loader('hide');
                if (response.status) {
                    $('.add-datamodal').modal('hide');
                    diagnosistable.ajax.reload(null, false);
                    sendmsg('success', response.message);
                } else {
                    sendmsg('error', response.message || 'Unable to save diagnosis.');
                }
            },
            error: function(xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];

                    for (const field in errors) {
                        const $field = $(`[name="${errors[field].code}"]`);
                        $field.after(`<div class="err text-danger">${errors[field].message}</div>`);
                        errorMessages.push(errors[field].message);
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

    $(document).on('click', '.delete-diagnosis-btn', async function() {
        const id = $(this).data('id');
        const url = route('destroy', { patient: patientId, diagnosis: id });
        const token = await csrftoken();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            loader();
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: token, _method: 'DELETE' },
                success: function(response) {
                    loader('hide');
                    if (response.status) {
                        sendmsg('success', response.message);
                        diagnosistable.ajax.reload(null, false);
                    } else {
                        sendmsg('error', response.message || 'Unable to delete diagnosis.');
                    }
                },
                error: function() {
                    loader('hide');
                    sendmsg('error', 'An error occurred while deleting diagnosis.');
                }
            });
        });
    });

    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(event) {
        const tabId = $(event.target).attr('href');
        if (tabId === '#diagnosis') {
            setTimeout(function() {
                diagnosistable.columns.adjust().responsive.recalc();
            }, 150);
        }
    });
});
