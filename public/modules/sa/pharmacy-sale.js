$(document).ready(function () {
    const currency = function (value) {
        return 'Rs ' + (parseFloat(value || 0).toFixed(2));
    };

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
            { data: null, orderable: false, searchable: false, render: function (d, t, r, m) { return m.row + m.settings._iDisplayStart + 1; } },
            { data: 'bill_no', name: 'bill_no' },
            { data: 'bill_date', name: 'bill_date' },
            { data: 'patient_name', name: 'patient.name' },
            { data: 'subtotal', name: 'subtotal', render: currency },
            { data: 'discount_amount', name: 'discount_amount', render: currency },
            { data: 'tax_amount', name: 'tax_amount', render: currency },
            { data: 'net_total', name: 'net_total', render: function (value) { return '<strong>' + currency(value) + '</strong>'; } },
            { data: 'paid_amount', name: 'paid_amount', render: currency },
            { data: 'due_amount', name: 'due_amount', render: currency },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function (e, dt) { dt.ajax.reload(); } },
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

    function syncQuickPrescriptionOptions() {
        const type = $('#quick_prescription_type').val();
        const target = $('#quick_prescription_id');
        if (!target.length) {
            return;
        }

        const options = type === 'opd' ? (window.quickOpdPrescriptionOptions || []) : (window.quickIpdPrescriptionOptions || []);
        target.empty().append('<option value="">Select prescription</option>');
        options.forEach(function (option) {
            target.append('<option value="' + option.id + '">' + option.label + '</option>');
        });
        target.val('').trigger('change');
    }

    $('#quick_prescription_type').on('change', syncQuickPrescriptionOptions);

    if ($('#quick_prescription_id').length && $.fn.select2) {
        $('#quick_prescription_id').select2({
            placeholder: 'Search prescription',
            allowClear: true,
            width: '100%'
        });
    }

    let saleRowIndex = 0;

    const medOptions = function () {
        return (window.saleMedicines || []).map(function (medicine) {
            const label = medicine.name + (medicine.unit ? ' (' + medicine.unit + ')' : '');
            return '<option value="' + medicine.id + '" title="' + label.replace(/"/g, '&quot;') + '">' + label + '</option>';
        }).join('');
    };

    const batchOptionLabel = function (batch) {
        const expiry = batch.expiry_date ? ' EXP ' + batch.expiry_date : ' No-EXP';
        return batch.batch_no + ' | Avl ' + batch.available_qty + expiry;
    };

    const saleRowHtml = function (index, item) {
        const row = item || {};
        return '<tr>' +
            '<td><select class="form-control item-medicine" name="items[' + index + '][medicine_id]" required><option value="">Select</option>' + medOptions() + '</select></td>' +
            '<td><select class="form-control item-batch" name="items[' + index + '][stock_batch_id]"><option value="">Auto batch</option></select></td>' +
            '<td><input type="number" step="0.01" min="0.01" class="form-control item-qty" name="items[' + index + '][quantity]" value="' + (row.quantity || 1) + '" required></td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control item-price bg-light" name="items[' + index + '][unit_price]" value="' + (row.unit_price || '') + '" readonly></td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control item-mrp bg-light" name="items[' + index + '][unit_mrp]" value="' + (row.unit_mrp || '') + '" readonly></td>' +
            '<td><input type="number" step="0.01" min="0" max="100" class="form-control item-tax bg-light" name="items[' + index + '][tax_percent]" value="' + (row.tax_percent || 0) + '" readonly></td>' +
            '<td class="text-end"><span class="line-total fw-semibold">Rs 0.00</span></td>' +
            '<td><select class="form-control item-subst" name="items[' + index + '][is_substituted]"><option value="0">No</option><option value="1">Yes</option></select></td>' +
            '<td><input type="text" class="form-control item-note" name="items[' + index + '][substitution_note]" value="' + (row.substitution_note || '') + '"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm remove-item" title="Remove row">x</button></td>' +
            '</tr>';
    };

    function initMedicineSelect2($scope) {
        if (!$.fn.select2) {
            return;
        }

        const parent = $('.add-datamodal');
        $scope.find('.item-medicine').each(function () {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            $el.select2({
                dropdownParent: parent,
                width: '100%',
                placeholder: 'Medicine',
                allowClear: false
            });
        });
    }

    function syncPrescriptionOptions(selectedPrescriptionId) {
        const type = $('#prescription_type').val();
        const patientId = $('#sale_patient_id').val();
        const target = $('#prescription_id');
        const preferredId = selectedPrescriptionId ? String(selectedPrescriptionId) : '';
        target.empty().append('<option value="">Select</option>');

        if (!type) {
            target.val('').trigger('change.select2');
            return;
        }

        // Walk-in customer should never show patient-linked prescriptions.
        if (!patientId) {
            target.val('').trigger('change.select2');
            return;
        }

        const source = type === 'opd' ? (window.opdPrescriptionOptions || []) : (window.ipdPrescriptionOptions || []);
        source.forEach(function (option) {
            if (patientId && String(option.patient_id || '') !== String(patientId)) {
                return;
            }
            target.append('<option value="' + option.id + '">' + option.label + '</option>');
        });

        if (preferredId && target.find('option[value="' + preferredId + '"]').length) {
            target.val(preferredId);
        } else {
            target.val('');
        }
        target.trigger('change.select2');
    }

    function recalcRow($row) {
        const qty = parseFloat($row.find('.item-qty').val()) || 0;
        const unitPrice = parseFloat($row.find('.item-price').val()) || 0;
        const discPercent = parseFloat($row.find('.item-disc').val()) || 0;
        const taxPercent = parseFloat($row.find('.item-tax').val()) || 0;

        const lineSubtotal = qty * unitPrice;
        const lineDiscount = lineSubtotal * discPercent / 100;
        const taxable = Math.max(0, lineSubtotal - lineDiscount);
        const lineTax = taxable * taxPercent / 100;
        const lineTotal = taxable + lineTax;

        $row.attr('data-line-subtotal', lineSubtotal.toFixed(2));
        $row.attr('data-line-discount', lineDiscount.toFixed(2));
        $row.attr('data-line-tax', lineTax.toFixed(2));
        $row.find('.line-total').text(currency(lineTotal));
    }

    function recalcSummary() {
        let subtotal = 0;
        let itemDiscount = 0;
        let taxTotal = 0;

        $('#sale-items-table tbody tr').each(function () {
            const $row = $(this);
            recalcRow($row);
            subtotal += parseFloat($row.attr('data-line-subtotal')) || 0;
            itemDiscount += parseFloat($row.attr('data-line-discount')) || 0;
            taxTotal += parseFloat($row.attr('data-line-tax')) || 0;
        });

        const headerDiscount = parseFloat($('#header_discount_amount').val()) || 0;
        let paid = parseFloat($('#paid_amount').val()) || 0;
        const beforeDiscount = Math.max(0, subtotal - itemDiscount + taxTotal);
        const netTotal = Math.max(0, beforeDiscount - headerDiscount);
        if (paid > netTotal) {
            paid = netTotal;
            $('#paid_amount').val(netTotal.toFixed(2));
        }
        $('#paid_amount').attr('max', netTotal.toFixed(2));
        const due = Math.max(0, netTotal - paid);

        $('#sum-subtotal').text(currency(subtotal));
        $('#sum-item-discount').text(currency(itemDiscount));
        $('#sum-tax').text(currency(taxTotal));
        if ($('#sum-before-discount').length) {
            $('#sum-before-discount').text(currency(beforeDiscount));
        }
        $('#sum-net-total').text(currency(netTotal));
        $('#sum-due').text(currency(due));
    }

    function appendSaleRow(item) {
        $('#sale-items-table tbody').append(saleRowHtml(saleRowIndex++, item));
        initMedicineSelect2($('#sale-items-table tbody tr:last'));
    }

    async function fetchAndBindBatches($row, preferredBatchId) {
        const medicineId = $row.find('.item-medicine').val();
        const batchSelect = $row.find('.item-batch');

        batchSelect.html('<option value="">Loading...</option>');
        if (!medicineId) {
            batchSelect.html('<option value="">Auto batch</option>');
            return;
        }

        const token = await csrftoken();
        $.post(route('medicineBatches'), { _token: token, medicine_id: medicineId }, function (response) {
            batchSelect.empty().append('<option value="">Auto batch</option>');

            (response.batches || []).forEach(function (batch) {
                batchSelect.append(
                    '<option value="' + batch.id + '" data-price="' + batch.unit_sale_price + '" data-mrp="' + batch.unit_mrp + '" data-tax="' + (batch.tax_percent || 0) + '">' +
                    batchOptionLabel(batch) +
                    '</option>'
                );
            });

            if (response.batches && response.batches.length) {
                const selectedBatch = preferredBatchId || response.batches[0].id;
                batchSelect.val(String(selectedBatch));
                batchSelect.trigger('change');
            } else {
                batchSelect.val('');
                sendmsg('warning', 'Selected medicine has no active stock batch.');
            }
        }).fail(function () {
            batchSelect.html('<option value="">Auto batch</option>');
            sendmsg('error', 'Unable to load medicine batches.');
        });
    }

    function loadSelectedPrescriptionItems() {
        const type = $('#prescription_type').val();
        const prescriptionId = $('#prescription_id').val();
        const selectedPrescriptionId = String(prescriptionId);
        if (!type || !prescriptionId) {
            return;
        }

        loader();
        csrftoken().then(function (token) {
            $.post(route('loadPrescriptionItems'), {
                _token: token,
                prescription_type: type,
                prescription_id: prescriptionId,
                patient_id: $('#sale_patient_id').val() || ''
            }, async function (response) {
                loader('hide');
                $('#sale-items-table tbody').empty();
                saleRowIndex = 0;

                if (response.patient_id) {
                    const currentPatientId = String($('#sale_patient_id').val() || '');
                    const responsePatientId = String(response.patient_id);
                    if (responsePatientId !== currentPatientId) {
                        $('#sale_patient_id').val(responsePatientId);
                    }
                    syncPrescriptionOptions(selectedPrescriptionId);
                }

                if (!response.items || !response.items.length) {
                    appendSaleRow();
                    recalcSummary();
                    return;
                }

                for (let i = 0; i < response.items.length; i++) {
                    const item = response.items[i];
                    appendSaleRow({ quantity: item.quantity || 1 });
                    const $lastRow = $('#sale-items-table tbody tr:last');
                    const medValue = String(item.medicine_id);
                    $lastRow.find('.item-medicine').val(medValue).trigger('change.select2');
                    await fetchAndBindBatches($lastRow, item.stock_batch_id || null);
                }

                recalcSummary();
            }).fail(function () {
                loader('hide');
                sendmsg('error', 'Unable to load prescription items.');
            });
        });
    }

    function openForm(prefill) {
        loader();
        csrftoken().then(function (token) {
            const payload = {
                _token: token,
                prescription_type: (prefill && prefill.prescription_type) ? prefill.prescription_type : '',
                prescription_id: (prefill && prefill.prescription_id) ? prefill.prescription_id : ''
            };

            $.post(route('showform'), payload, function (response) {
                loader('hide');
                $('#ajaxdata').html(response);
                $('.add-datamodal').modal('show');
                $('.add-datamodal .modal-dialog')
                    .removeClass('modal-sm modal-lg modal-xl')
                    .addClass('modal-fullscreen');

                if ($.fn.select2) {
                    $('.select2-modal').select2({ dropdownParent: $('.add-datamodal') });
                }

                saleRowIndex = 0;
                $('#sale-items-table tbody').empty();
                appendSaleRow();
                syncPrescriptionOptions();

                const initial = window.initialSalePrescription || {};
                if (initial.patientId) {
                    $('#sale_patient_id').val(String(initial.patientId)).trigger('change');
                }
                if (initial.type) {
                    $('#prescription_type').val(initial.type).trigger('change');
                }
                if (initial.id) {
                    $('#prescription_id').val(String(initial.id)).trigger('change');
                }

                recalcSummary();
                const firstMed = $('#sale-items-table tbody tr:first .item-medicine');
                if (firstMed.hasClass('select2-hidden-accessible')) {
                    firstMed.select2('open');
                } else {
                    firstMed.focus();
                }
            }).fail(function () {
                loader('hide');
                sendmsg('error', 'Unable to open sale bill form.');
            });
        });
    }

    $(document).on('click', '.adddata', function () {
        openForm(null);
    });

    $(document).on('click', '.quick-sale-open', function () {
        const type = $('#quick_prescription_type').val();
        const prescriptionId = $('#quick_prescription_id').val();
        if (!type || !prescriptionId) {
            sendmsg('error', 'Quick bill ke liye type aur prescription select karein.');
            return;
        }
        openForm({ prescription_type: type, prescription_id: prescriptionId });
    });

    $(document).on('change', '#prescription_type', syncPrescriptionOptions);
    $(document).on('change', '#sale_patient_id', function () {
        syncPrescriptionOptions();
    });
    $(document).on('change', '#prescription_id', loadSelectedPrescriptionItems);

    $(document).on('click', '#add-sale-item', function () {
        appendSaleRow();
        recalcSummary();
        $('#sale-items-table tbody tr:last .item-medicine').focus();
    });

    $(document).on('click', '.remove-item', function () {
        $(this).closest('tr').remove();
        if (!$('#sale-items-table tbody tr').length) {
            appendSaleRow();
        }
        recalcSummary();
    });

    $(document).on('change', '.item-medicine', function () {
        const $row = $(this).closest('tr');
        fetchAndBindBatches($row);
    });

    $(document).on('change', '.item-batch', function () {
        const selected = $(this).find('option:selected');
        const $row = $(this).closest('tr');
        if (selected.val()) {
            $row.find('.item-price').val(selected.data('price') || 0);
            $row.find('.item-mrp').val(selected.data('mrp') || 0);
            $row.find('.item-tax').val(selected.data('tax') || 0);
            recalcSummary();
        }
    });

    $(document).on('input', '.item-qty, .item-price, .item-mrp, .item-tax, #header_discount_amount, #paid_amount', recalcSummary);

    $(document).on('keydown', '#savedata input, #savedata select, #savedata textarea', function (event) {
        if (event.key !== 'Enter') {
            return;
        }

        if ($(this).is('textarea')) {
            return;
        }

        event.preventDefault();
        const $current = $(this);
        const $row = $current.closest('#sale-items-table tbody tr');

        if ($row.length) {
            const sequence = ['.item-medicine', '.item-batch', '.item-qty', '.item-price', '.item-mrp', '.item-tax', '.item-subst', '.item-note'];
            let currentIndex = -1;
            for (let i = 0; i < sequence.length; i++) {
                if ($current.hasClass(sequence[i].replace('.', ''))) {
                    currentIndex = i;
                    break;
                }
            }

            if (currentIndex !== -1) {
                if (currentIndex === sequence.length - 1) {
                    if ($row.is('#sale-items-table tbody tr:last')) {
                        appendSaleRow();
                        recalcSummary();
                    }
                    $row.next().find('.item-medicine').focus();
                    if ($row.is('#sale-items-table tbody tr:last')) {
                        $('#sale-items-table tbody tr:last .item-medicine').focus();
                    }
                } else {
                    $row.find(sequence[currentIndex + 1]).focus();
                }
                return;
            }
        }

        const $focusables = $('#savedata').find('input, select, textarea, button').filter(':visible:not([disabled])');
        const nextIndex = $focusables.index(this) + 1;
        if (nextIndex >= 0 && nextIndex < $focusables.length) {
            $focusables.eq(nextIndex).focus();
        }
    });

    $(document).on('submit', '#savedata', function (event) {
        event.preventDefault();
        loader();

        csrftoken().then(function (token) {
            const fd = new FormData(document.getElementById('savedata'));
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
                    sendmsg('success', response.message + ' (' + response.bill_no + ')');
                },
                error: function (xhr) {
                    loader('hide');
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        sendmsg('error', xhr.responseJSON.errors.map(function (error) { return error.message; }).join('<br>'));
                    } else {
                        sendmsg('error', 'Unable to save sale bill.');
                    }
                }
            });
        });
    });

    $(document).on('click', '.print-bill-btn', function () {
        const url = route('print').replace('__ID__', $(this).data('id'));
        window.open(url, '_blank');
    });
});