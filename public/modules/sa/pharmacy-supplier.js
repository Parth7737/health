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
            { data: null, orderable: false, searchable: false, render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1 },
            { data: 'name', name: 'name' },
            { data: 'contact_person', name: 'contact_person' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'gstin', name: 'gstin' },
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

    // Open add form
    $(document).on('click', '.adddata', async function () {
        loader();
        const token = await csrftoken();
        $.post(route('showform'), { _token: token }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal').modal('show');
        });
    });

    // Open edit form
    $(document).on('click', '.editdata', async function () {
        loader();
        const id = $(this).data('id');
        const token = await csrftoken();
        $.post(route('showform'), { _token: token, id: id }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deletebtn', async function () {
        const id = $(this).data('id');
        const token = await csrftoken();
        const confirm = await swal({ title: 'Delete Supplier?', icon: 'warning', buttons: true, dangerMode: true });
        if (!confirm) return;
        loader();
        $.ajax({
            url: route('destroy').replace('__ID__', id),
            type: 'DELETE',
            data: { _token: token },
            success: function (res) {
                loader('hide');
                xintable.ajax.reload(null, false);
                sendmsg('success', res.message);
            },
            error: function () {
                loader('hide');
                sendmsg('error', 'Unable to delete supplier.');
            }
        });
    });

    // Save form
    $(document).on('submit', '#savedata', async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
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
                $('.add-datamodal').modal('hide');
                xintable.ajax.reload(null, false);
                sendmsg('success', response.message);
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    sendmsg('error', xhr.responseJSON.errors.map(e => e.message).join('<br>'));
                } else {
                    sendmsg('error', 'Unable to save supplier.');
                }
            }
        });
    });
});
