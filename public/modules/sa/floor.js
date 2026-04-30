$(document).ready(function () {
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
                data: null, orderable: false, searchable: false,
                render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }
            },
            { data: 'building_name', name: 'building_name', orderable: false },
            { data: 'name', name: 'name' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: "fBrtip",
        autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function (e, dt) { dt.ajax.reload(); }},
            { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
            { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'Export as CSV' },
            { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Export as Excel' },
            { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'Export as PDF' },
            { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print Table' },
            { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Column Visibility' }
        ],
        responsive: true
    });

    $(document).on('click', '.adddata, .editdata', async function () {
        loader();
        const id = $(this).data('id');
        const token = await csrftoken();

        $.post(route('showform'), { id: id, _token: token }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal').modal('show');
            $('.select2-modal').select2({ dropdownParent: $('.add-datamodal') });
        }).fail(function () {
            loader('hide');
        });
    });

    $(document).on('click', '.deletebtn', async function () {
        const id = $(this).data('id');
        const token = await csrftoken();

        Swal.fire({ title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true }).then((result) => {
            if (!result.isConfirmed) return;
            loader();
            $.post(route('destroy', { floor: id }), { _token: token, _method: 'DELETE' }, function (response) {
                loader('hide');
                if (response.status) {
                    sendmsg('success', response.message);
                    xintable.ajax.reload(null, false);
                } else {
                    sendmsg('error', response.message);
                }
            }).fail(function () {
                loader('hide');
                sendmsg('error', 'Something went wrong.');
            });
        });
    });

    $(document).on('submit', '#savedata', async function (e) {
        e.preventDefault();
        loader();

        const token = await csrftoken();
        const fd = new FormData(this);
        fd.append('_token', token);

        $.ajax({
            url: route('store'),
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) {
                    sendmsg('success', response.message);
                    $('.add-datamodal').modal('hide');
                    xintable.ajax.reload(null, false);
                } else {
                    sendmsg('error', response.message || 'Something went wrong.');
                }
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errs = '';
                    $.each(xhr.responseJSON.errors, function (k, v) { errs += v[0] + '<br>'; });
                    sendmsg('error', errs, true);
                } else {
                    sendmsg('error', 'Something went wrong.');
                }
            }
        });
    });
});
