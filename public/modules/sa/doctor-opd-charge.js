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
            { data: 'doctor_name', name: 'doctor_name', orderable: false },
            { data: 'new_case_charge', name: 'charge' },
            { data: 'follow_up_charge_display', name: 'follow_up_charge', orderable: false },
            { data: 'emergency_charge_display', name: 'emergency_charge', orderable: false },
            { data: 'follow_up_window_display', name: 'follow_up_validity_months', orderable: false },
            { data: 'tpa_opd_charges_count', name: 'tpa_opd_charges_count' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: 'fBrtip',
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

    $(document).on('click', '.adddata, .editdata', async function() {
        loader();
        var id = $(this).data('id');
        var url = route('showform');
        const token = await csrftoken();

        $.ajax({
            url: url,
            type: 'POST',
            data: {id: id, _token: token},
            success: function (response) {
                loader('hide');
                if (response) {
                    $('#ajaxdata').html(response);
                    $('.add-datamodal').modal('show');
                    $('.add-datamodal .modal-dialog').addClass('modal-xl');
                    $('.add-datamodal .select2-modal').select2({
                        dropdownParent: $('.add-datamodal')
                    });
                }
            },
            error: function () {
                loader('hide');
                $('.err').remove();
            }
        });
    });

    $(document).on('click', '.deletebtn', async function() {
        var id = $(this).data('id');
        var url = route('destroy', {doctor_opd_charge: id});
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
                    type: 'POST',
                    data: {id: id, _token: token, _method: 'DELETE'},
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

    $(document).on('click', '#apply-all-tpa-charges', function() {
        const baseCharge = $('#charge').val();

        if (baseCharge === '') {
            sendmsg('error', 'Please enter default OPD charge first.');
            return;
        }

        $('.tpa-charge-input').val(baseCharge);
    });

    $(document).on('submit', '#savedata', async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();

        var fd = new FormData(this);
        fd.append('_token', token);

        $.ajax({
            url: route('store'),
            type: 'POST',
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) {
                    $('.add-datamodal').modal('hide');
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
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        const $field = $(`[name="${errors[field]['code']}"]`);
                        if ($field.hasClass('select2')) { $field.closest('.form-group').append(`<span class="text-danger err">${errors[field]['message']}</span>`); } else { $field.parent().append(`<span class="text-danger err">${errors[field]['message']}</span>`); }
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
