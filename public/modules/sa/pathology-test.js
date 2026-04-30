$(document).ready(function() {
    const loadroute = route('loadtable');

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
            { data: 'test_name', name: 'test_name' },
            { data: 'test_code', name: 'test_code', defaultContent: '' },
            { data: 'category.name', name: 'category.name', defaultContent: '' },
            { data: 'parameters_list', name: 'parameters_list', orderable: false },
            {
                data: 'standard_charge',
                name: 'standard_charge',
                render: function (data) {
                    const value = parseFloat(data || 0);
                    return value.toFixed(2);
                }
            },
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

    flatpickr('input[type="date"]', { dateFormat: 'd-m-Y' });
    flatpickr('input[type="datetime-local"]', { enableTime: true, dateFormat: 'd-m-Y H:i' });

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function refreshParameterPreview() {
        const $select = $('#pathology_parameter_ids');
        const $tbody = $('#parameter-preview-body');

        if (!$select.length || !$tbody.length) {
            return;
        }

        const selectedOptions = $select.find('option:selected');

        if (!selectedOptions.length) {
            $tbody.html('<tr class="empty-parameter-row"><td colspan="3" class="text-muted text-center">No parameter selected.</td></tr>');
            return;
        }

        let rows = '';
        selectedOptions.each(function () {
            const $opt = $(this);
            const name = $opt.text().trim();
            const unit = ($opt.data('unit') || 'N/A').toString();
            const range = ($opt.data('range') || 'N/A').toString();

            rows += `<tr>
                <td>${escapeHtml(name)}</td>
                <td>${escapeHtml(unit)}</td>
                <td>${escapeHtml(range)}</td>
            </tr>`;
        });

        $tbody.html(rows);
    }

    function syncChargeMasterRate() {
        const $selected = $('#charge_master_id option:selected');
        if (!$selected.length) {
            $('#standard_charge').val('0.00');
            return;
        }

        const standardRate = parseFloat($selected.data('standard-rate') || 0);
        $('#standard_charge').val(standardRate.toFixed(2));
    }

    $(document).on('click', '.adddata, .editdata', async function() {
        loader();
        const id = $(this).data('id');
        const url = route('showform');
        const token = await csrftoken();

        $.ajax({
            url: url,
            type: "POST",
            data: { id: id, _token: token },
            success: function (response) {
                loader('hide');
                if (response) {
                    $("#ajaxdata").html(response);
                    $(".add-datamodal").modal('show');
                    $(".add-datamodal .modal-dialog").addClass('modal-xl');

                    $('.add-datamodal .select2-modal').select2({
                        dropdownParent: $('.add-datamodal')
                    });

                    refreshParameterPreview();
                    syncChargeMasterRate();
                }
            },
            error: function () {
                loader('hide');
                $('.err').remove();
            }
        });
    });

    $(document).on('change', '#pathology_parameter_ids', function () {
        refreshParameterPreview();
    });

    $(document).on('change', '#charge_master_id', function () {
        syncChargeMasterRate();
    });

    $(document).on('click', '.deletebtn', async function() {
        const id = $(this).data('id');
        const url = route('destroy', { test: id });
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
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { id: id, _token: token, _method: 'DELETE' },
                    success: function (response) {
                        loader('hide');
                        if (response.status) {
                            sendmsg('success', response.message);
                            $('#xin-table').DataTable().ajax.reload(null, false);
                        } else {
                            sendmsg('error', response.message);
                        }
                    }
                });
            }
        });
    });

    $(document).on("submit", "#savedata", async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();

        const fd = new FormData(this);
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
                } else {
                    sendmsg('error', response.message);
                }
            },
            error: function (xhr) {
                loader('hide');
                $('.err').remove();

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];

                    for (const field in errors) {
                        const fieldCode = errors[field]['code'];
                        let $field = $(`[name="${fieldCode}"]`);
                        if (!$field.length) {
                            $field = $(`[name="${fieldCode}[]"]`);
                        }

                        if ($field.hasClass('select2') || $field.hasClass('select2-modal') || $field.hasClass('select2-hidden-accessible')) {
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
});
