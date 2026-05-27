$(document).ready(function () {

    /* ─── DataTable ─── */
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
            { data: 'bill_no', name: 'bill_no' },
            { data: 'bill_date', name: 'bill_date' },
            { data: 'supplier_name', name: 'supplier_name', defaultContent: '—' },
            { data: 'items_count', name: 'items_count', defaultContent: '0' },
            { data: 'net_total', name: 'net_total', render: v => '<strong>₹' + parseFloat(v || 0).toFixed(2) + '</strong>' },
            { data: 'status', name: 'status', render: function (v) {
                var st = String(v || 'pending').toLowerCase();
                var colors = { approved: 'success', rejected: 'danger', partially_received: 'info', received: 'primary' };
                var cls = colors[st] || 'warning';
                var label = st.replace('_', ' ');
                return '<span class="badge bg-' + cls + '">' + label.charAt(0).toUpperCase() + label.slice(1) + '</span>';
            }},
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
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

    /* ─── Medicine options HTML ─── */
    const medicineOptions = () =>
        (window.purchaseMedicines || [])
            .map(m => `<option value="${m.id}" data-unit="${m.unit || ''}">${m.name}${m.unit ? ' [' + m.unit + ']' : ''}</option>`)
            .join('');

    /* ─── Build a new item row ─── */
    let rowIdx = 0;
    function buildRow() {
        const i = rowIdx++;
        return `
        <tr>
          <td>
            <select class="form-control form-control-sm item-medicine" name="items[${i}][medicine_id]" >
              <option value="">Select</option>${medicineOptions()}
            </select>
          </td>
          <td><input type="number" step="1" min="1" class="form-control form-control-sm item-qty" name="items[${i}][quantity_purchased]" value="1" ></td>
          <td><input type="number" step="0.01" min="0" class="form-control form-control-sm item-price" name="items[${i}][unit_purchase_price]" value="0" ></td>
          <td class="text-end"><span class="item-amount fw-semibold">₹0.00</span></td>
          <td><button type="button" class="btn btn-danger btn-sm remove-item">×</button></td>
        </tr>`;
    }

    /* ─── Live summary calc ─── */
    function recalcSummary() {
        let total = 0;
        $('#purchase-items-body tr').each(function () {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const lineAmt = qty * price;
            $(this).find('.item-amount').text('₹' + lineAmt.toFixed(2));
            total += lineAmt;
        });
        $('#summary-net-total').text('₹' + total.toFixed(2));
    }

    /* ─── Edit-mode summary init ─── */
    function initEditSummary() {
        if (window.isEditMode && window.editBillData) {
            $('#summary-net-total').text('₹' + parseFloat(window.editBillData.net_total).toFixed(2));
        }
    }

    /* ─── Open Add / Edit form ─── */
    async function openForm(id) {
        loader();
        const token = await csrftoken();
        $.post(route('showform'), { _token: token, id: id || '' }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal').modal('show');
            $('.add-datamodal .modal-dialog')
                .removeClass('modal-sm modal-lg modal-xl')
                .addClass('modal-fullscreen');
            if (!window.isEditMode) {
                rowIdx = 0;
                $('#purchase-items-body').html(buildRow());
                recalcSummary();
            } else {
                initEditSummary();
            }
            flatpickr('#bill_date', { dateFormat: 'd-m-Y' });
            // init select2 on supplier if available
            if ($.fn.select2) {
                $('#supplier_id').select2({ dropdownParent: $('.add-datamodal'), placeholder: '— Select Supplier —', allowClear: true });
            }
        });
    }

    $(document).on('click', '.adddata', () => openForm(null));
    $(document).on('click', '.editdata', function () { openForm($(this).data('id')); });

    /* ─── View PO details ─── */
    async function openView(id) {
        loader();
        const token = await csrftoken();
        $.post(route('showform'), { _token: token, id: id, view: 1 }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal').modal('show');
            $('.add-datamodal .modal-dialog')
                .removeClass('modal-sm modal-lg modal-xl')
                .addClass('modal-fullscreen');
        }).fail(function () {
            loader('hide');
            sendmsg('error', 'Unable to load purchase order details.');
        });
    }

    $(document).on('click', '.view-purchase-btn', function () {
        openView($(this).data('id'));
    });

    /* ─── Add / Remove item rows ─── */
    $(document).on('click', '#add-purchase-item', function () {
        $('#purchase-items-body').append(buildRow());
        recalcSummary();
    });
    $(document).on('click', '.remove-item', function () {
        $(this).closest('tr').remove();
        recalcSummary();
    });

    /* ─── Live recalc triggers ─── */
    $(document).on('input', '.item-qty,.item-price', function () {
        recalcSummary();
    });

    /* ─── Form submit (Create or Update) ─── */
    $(document).on('submit', '#savedata', async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();
        const fd = new FormData(this);
        fd.append('_token', token);

        const billId = $('#bill_id').val();
        const url = billId
            ? route('update').replace('__ID__', billId)
            : route('store');

        $.ajax({
            url: url,
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                loader('hide');
                $('.add-datamodal').modal('hide');
                xintable.ajax.reload(null, false);
                sendmsg('success', response.message + (response.bill_no ? ' (' + response.bill_no + ')' : ''));
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    sendmsg('error', xhr.responseJSON.errors.map(e => e.message).join('<br>'));
                } else {
                    sendmsg('error', 'Unable to save purchase bill.');
                }
            }
        });
    });

    /* ─── Approve button ─── */
    $(document).on('click', '.approve-btn', async function () {
        const id = $(this).data('id');
        const url = route('approve').replace('__ID__', id);
        const confirmed = typeof Swal !== 'undefined'
            ? (await Swal.fire({ title: 'Approve PO?', text: 'PO will be available for GRN.', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, approve' })).isConfirmed
            : confirm('Approve this PO?');
        if (!confirmed) return;
        loader();
        const token = await csrftoken();
        $.post(url, { _token: token }, function (res) {
            loader('hide');
            xintable.ajax.reload(null, false);
            sendmsg('success', res.message || 'PO approved.');
        }).fail(function (xhr) {
            loader('hide');
            sendmsg('error', (xhr.responseJSON && xhr.responseJSON.message) || 'Approval failed.');
        });
    });

    /* ─── Reject button ─── */
    $(document).on('click', '.reject-btn', async function () {
        const id = $(this).data('id');
        const url = route('reject').replace('__ID__', id);
        let reason = '';
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({ title: 'Reject PO?', input: 'text', inputLabel: 'Reason (optional)', showCancelButton: true, confirmButtonText: 'Reject', confirmButtonColor: '#d33' });
            if (!result.isConfirmed) return;
            reason = result.value || '';
        } else {
            reason = prompt('Rejection reason (optional):') || '';
        }
        loader();
        const token = await csrftoken();
        $.post(url, { _token: token, reject_reason: reason }, function (res) {
            loader('hide');
            xintable.ajax.reload(null, false);
            sendmsg('success', res.message || 'PO rejected.');
        }).fail(function (xhr) {
            loader('hide');
            sendmsg('error', (xhr.responseJSON && xhr.responseJSON.message) || 'Rejection failed.');
        });
    });

    /* ─── Print button ─── */
    $(document).on('click', '.print-bill-btn', function () {
        const url = route('print').replace('__ID__', $(this).data('id'));
        window.open(url, '_blank');
    });

});
