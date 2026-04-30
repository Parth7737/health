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
            { data: 'subtotal', name: 'subtotal', render: v => '₹' + parseFloat(v || 0).toFixed(2) },
            { data: 'discount_amount', name: 'discount_amount', render: v => '₹' + parseFloat(v || 0).toFixed(2) },
            { data: 'tax_amount', name: 'tax_amount', render: v => '₹' + parseFloat(v || 0).toFixed(2) },
            { data: 'net_total', name: 'net_total', render: v => '<strong>₹' + parseFloat(v || 0).toFixed(2) + '</strong>' },
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
          <td><input type="text" class="form-control form-control-sm" name="items[${i}][pack_size]" placeholder="e.g. 10 Tab Strip"></td>
          <td><input type="text" class="form-control form-control-sm item-batch" name="items[${i}][batch_no]" ></td>
          <td><input type="month" class="form-control form-control-sm item-expiry" name="items[${i}][expiry_date]"></td>
          <td><input type="number" step="0.01" min="0" class="form-control form-control-sm item-price" name="items[${i}][unit_purchase_price]" value="0" ></td>
          <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="items[${i}][unit_mrp]" value="0"></td>
          <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="items[${i}][unit_sale_price]" value="0"></td>
          <td><input type="number" step="1" min="0" class="form-control form-control-sm item-qty" name="items[${i}][quantity_purchased]" value="1" ></td>
          <td><input type="number" step="1" min="0" class="form-control form-control-sm item-free" name="items[${i}][quantity_free]" value="0"></td>
          <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm item-tax" name="items[${i}][tax_percent]" value="0"></td>
          <td class="text-end"><span class="item-amount fw-semibold">₹0.00</span></td>
          <td><button type="button" class="btn btn-danger btn-sm remove-item">×</button></td>
        </tr>`;
    }

    /* ─── Live summary calc ─── */
    function recalcSummary() {
        let subtotal = 0, taxTotal = 0;
        $('#purchase-items-body tr').each(function () {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const taxPct = parseFloat($(this).find('.item-tax').val()) || 0;
            const lineAmt = qty * price;
            const lineTax = lineAmt * taxPct / 100;
            $(this).find('.item-amount').text('₹' + lineAmt.toFixed(2));
            subtotal += lineAmt;
            taxTotal += lineTax;
        });

        const discType = $('#discount_type').val();
        const discVal = parseFloat($('#discount_value').val()) || 0;
        const discAmt = discType === 'percent' ? subtotal * discVal / 100 : discVal;
        const shipping = parseFloat($('#shipping_amount').val()) || 0;
        const roundOff = parseFloat($('#round_off').val()) || 0;
        const netTotal = Math.max(0, subtotal - discAmt + taxTotal + shipping + roundOff);

        $('#summary-subtotal').text('₹' + subtotal.toFixed(2));
        $('#summary-tax').text('₹' + taxTotal.toFixed(2));
        $('#summary-discount').text('−₹' + discAmt.toFixed(2));
        $('#summary-net-total').text('₹' + netTotal.toFixed(2));
    }

    /* ─── Edit-mode summary init ─── */
    function initEditSummary() {
        if (window.isEditMode && window.editBillData) {
            const d = window.editBillData;
            $('#summary-subtotal').text('₹' + parseFloat(d.subtotal).toFixed(2));
            $('#summary-tax').text('₹' + parseFloat(d.tax).toFixed(2));

            const discType = d.discount_type || 'fixed';
            $('#discount_type').val(discType);
            if (discType === 'percent') {
                $('#disc-percent-btn').addClass('active');
                $('#disc-fixed-btn').removeClass('active');
            }

            // Recalc on edit summary changes
            function recalcEditSummary() {
                const subtotal = parseFloat(d.subtotal) || 0;
                const taxAmt = parseFloat(d.tax) || 0;
                const dt = $('#discount_type').val();
                const dv = parseFloat($('#discount_value').val()) || 0;
                const disc = dt === 'percent' ? subtotal * dv / 100 : dv;
                const ship = parseFloat($('#shipping_amount').val()) || 0;
                const ro = parseFloat($('#round_off').val()) || 0;
                const net = Math.max(0, subtotal - disc + taxAmt + ship + ro);
                $('#summary-discount').text('−₹' + disc.toFixed(2));
                $('#summary-net-total').text('₹' + net.toFixed(2));
            }
            $('#discount_value, #shipping_amount, #round_off').on('input', recalcEditSummary);
            recalcEditSummary();
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
    $(document).on('input', '.item-qty,.item-price,.item-tax,#discount_value,#shipping_amount,#round_off', function () {
        recalcSummary();
    });

    /* ─── Discount type toggle ─── */
    $(document).on('click', '.discount-type-btn', function () {
        $('.discount-type-btn').removeClass('active');
        $(this).addClass('active');
        $('#discount_type').val($(this).data('type'));
        recalcSummary();
    });

    /* ─── Convert month input to last-day date for storage ─── */
    function normaliseExpiry(val) {
        if (!val) return '';
        // val is YYYY-MM from month input → convert to YYYY-MM-28 (safe last day)
        return val.length === 7 ? val + '-28' : val;
    }

    /* ─── Form submit (Create or Update) ─── */
    $(document).on('submit', '#savedata', async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();
        const fd = new FormData(this);
        fd.append('_token', token);

        // Normalise expiry month fields
        this.querySelectorAll('.item-expiry').forEach(el => {
            const n = el.name;
            fd.set(n, normaliseExpiry(el.value));
        });

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

    /* ─── Print button ─── */
    $(document).on('click', '.print-bill-btn', function () {
        const url = route('print').replace('__ID__', $(this).data('id'));
        window.open(url, '_blank');
    });

});
