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
            { data: 'unit_purchase_price', name: 'unit_purchase_price' },
            { data: 'unit_sale_price', name: 'unit_sale_price' },
            { data: 'available_qty', name: 'available_qty' },
            { data: 'expired_qty', name: 'expired_qty' },
            { data: 'damaged_qty', name: 'damaged_qty' },
            { data: 'status', name: 'status' },
            { data: 'actions', orderable: false, searchable: false }
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

    $(document).on('click', '.bad-stock-btn', async function () {
        const token = await csrftoken();
        loader();
        $.post(route('showBadStockForm'), { _token: token, id: $(this).data('id') }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal').modal('show');
            $('.add-datamodal .modal-dialog').removeClass('modal-xl');
        }).fail(function () {
            loader('hide');
            sendmsg('error', 'Unable to load form.');
        });
    });

    $(document).on('submit', '#bad-stock-form', async function (e) {
        e.preventDefault();
        const token = await csrftoken();
        loader();
        const fd = new FormData(this);
        fd.append('_token', token);

        $.ajax({
            url: route('adjustBadStock'),
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
                    sendmsg('error', 'Unable to adjust bad stock.');
                }
            }
        });
    });
});