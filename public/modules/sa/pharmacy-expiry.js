$(document).ready(function () {
    const xintable = $('#xin-table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: true,
        scrollX: true,
        ajax: {
            url: route('loadtable'),
            type: 'POST',
            data: function (d) { d._token = window.Laravel.csrfToken; }
        },
        columns: [
            { data: null, orderable: false, searchable: false, render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1 },
            { data: 'medicine_name', name: 'medicine.name' },
            { data: 'batch_no', name: 'batch_no' },
            { data: 'expiry_date', name: 'expiry_date' },
            { data: 'available_qty', name: 'available_qty' },
            { data: 'expired_qty', name: 'expired_qty' },
            { data: 'status', name: 'status' },
            { data: 'expiry_status', name: 'expiry_status', orderable: false, searchable: false }
        ],
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

    $(document).on('click', '.process-expiry', async function () {
        const token = await csrftoken();
        loader();
        $.post(route('process'), { _token: token }, function (response) {
            loader('hide');
            xintable.ajax.reload(null, false);
            sendmsg('success', response.message);
        }).fail(function () {
            loader('hide');
            sendmsg('error', 'Unable to process expiry stock.');
        });
    });
});