window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.payroll = {
    init: function () {
        const panel = document.getElementById('hrx-panel-payroll');
        if (!panel) return false;

        const config = window.HRDashboardConfig || {};
        const listEndpoint = config.payrollListDataUrl || '';
        const payslipEndpoint = config.payrollPayslipCardUrl || '';
        const payslipPdfEndpoint = config.payrollPayslipPdfUrl || '';
        const markPaidEndpoint = config.payrollMarkPaidUrl || '';
        const markPaidBulkEndpoint = config.payrollMarkPaidBulkUrl || '';
        const exportCsvEndpoint = config.payrollExportCsvUrl || '';
        const sendSlipEndpoint = config.payrollSendSlipUrl || '';
        const sendSelectedEndpoint = config.payrollSendSelectedUrl || '';
        const sendBulkEndpoint = config.payrollSendBulkUrl || '';
        const processEndpoint = config.payrollProcessUrl || '';
        const defaultMonth = config.payrollDefaultMonth || '';
        const tableEl = document.getElementById('hrxPayrollTable');
        const monthInput = document.getElementById('hrxPayrollMonth');
        const searchInput = document.getElementById('hrxPayrollSearch');
        const statusSelect = document.getElementById('hrxPayrollStatus');
        const processBtn = document.getElementById('hrxPayrollProcess');
        const exportBtn = document.getElementById('hrxPayrollExport');
        const bulkEmailBtn = document.getElementById('hrxPayrollBulkEmail');
        const bulkPaidBtn = document.getElementById('hrxPayrollBulkPaid');
        const selectAllCheckbox = document.getElementById('hrxPayrollSelectAll');
        const selectedCountChip = document.getElementById('hrxPayrollSelectedCount');
        const payslipContainer = document.getElementById('hrxPayslipContainer');
        const payslipEmpty = document.getElementById('hrxPayslipEmpty');

        let selectedRecordId = null;
        const selectedBulkIds = new Set();

        if (monthInput && defaultMonth && !monthInput.value) {
            monthInput.value = defaultMonth;
        }

        function markSelectedButton() {
            $('.hrx-payroll-slip').removeClass('is-selected');
            $('#hrxPayrollTable tbody tr').removeClass('hrx-payroll-row-selected');
            if (!selectedRecordId) return;
            const selector = '.hrx-payroll-slip[data-record-id="' + selectedRecordId + '"]';
            const btn = document.querySelector(selector);
            if (btn) {
                btn.classList.add('is-selected');
                const selectedRow = btn.closest('tr');
                if (selectedRow) {
                    selectedRow.classList.add('hrx-payroll-row-selected');
                }
            }
        }

        function syncSelectAllState() {
            if (selectedCountChip) {
                selectedCountChip.textContent = 'Selected: ' + selectedBulkIds.size;
                if (selectedBulkIds.size > 0) {
                    selectedCountChip.classList.add('is-visible');
                } else {
                    selectedCountChip.classList.remove('is-visible');
                }
            }

            if (!selectAllCheckbox) return;

            const rowChecks = Array.from(document.querySelectorAll('#hrxPayrollTable .hrx-payroll-select:not(:disabled)'));
            if (rowChecks.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
                return;
            }

            const selectedCount = rowChecks.filter(function (el) {
                return selectedBulkIds.has(String(el.getAttribute('data-record-id') || ''));
            }).length;

            if (selectedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (selectedCount === rowChecks.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        function applySelectionToCurrentPage() {
            document.querySelectorAll('#hrxPayrollTable .hrx-payroll-select').forEach(function (checkbox) {
                const recordId = String(checkbox.getAttribute('data-record-id') || '');
                checkbox.checked = selectedBulkIds.has(recordId);
            });
            syncSelectAllState();
        }

        function getSelectedIds() {
            return Array.from(selectedBulkIds.values());
        }

        function getPayslipSkeletonMarkup() {
            return ''
                + '<div class="hrx-payslip-skeleton">'
                + '  <div class="hrx-sk-head"></div>'
                + '  <div class="hrx-sk-lines">'
                + '    <div class="hrx-sk-line w70"></div>'
                + '    <div class="hrx-sk-line w50"></div>'
                + '  </div>'
                + '  <div class="hrx-sk-block"></div>'
                + '  <div class="hrx-sk-block"></div>'
                + '  <div class="hrx-sk-block short"></div>'
                + '</div>';
        }

        function openPayslipByRecord(recordId) {
            if (!recordId || !payslipEndpoint || !payslipContainer) {
                return;
            }

            selectedRecordId = String(recordId);
            markSelectedButton();

            payslipContainer.style.display = '';
            payslipContainer.innerHTML = getPayslipSkeletonMarkup();
            if (payslipEmpty) payslipEmpty.style.display = 'none';

            if (typeof loader === 'function') loader();

            $.ajax({
                url: payslipEndpoint,
                type: 'GET',
                data: {
                    payroll_record_id: recordId
                },
                success: function (response) {
                    if (!response || !response.status) {
                        if (typeof sendmsg === 'function') sendmsg('error', (response && response.message) || 'Payslip load failed.');
                        return;
                    }

                    payslipContainer.innerHTML = response.html || '';
                    payslipContainer.style.display = '';
                    if (payslipEmpty) payslipEmpty.style.display = 'none';

                    const payslipPanel = document.getElementById('hrxPayslipPanel');
                    if (payslipPanel && typeof payslipPanel.scrollIntoView === 'function') {
                        payslipPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                },
                error: function () {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Payslip load failed.');
                },
                complete: function () {
                    if (typeof loader === 'function') loader('hide');
                }
            });
        }

        let table = null;
        if (tableEl && $.fn.DataTable && listEndpoint) {
            if ($.fn.DataTable.isDataTable(tableEl)) {
                $(tableEl).DataTable().destroy();
            }

            table = $(tableEl).DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: false,
                pageLength: 10,
                autoWidth: false,
                ordering: false,
                dom: 't<"hrx-dt-footer"ip>',
                ajax: {
                    url: listEndpoint,
                    type: 'GET',
                    data: function (d) {
                        d.status = statusSelect ? statusSelect.value : '';
                        d.month = monthInput && monthInput.value ? monthInput.value : defaultMonth;
                    }
                },
                columns: [
                    { data: 'select', name: 'select', orderable: false, searchable: false },
                    { data: 'emp_id', name: 'emp_id', orderable: false, searchable: true },
                    { data: 'slip_no', name: 'slip_no', orderable: false, searchable: true },
                    { data: 'name', name: 'name', orderable: false, searchable: true },
                    { data: 'designation', name: 'designation', orderable: false, searchable: true },
                    { data: 'basic', name: 'basic', orderable: false, searchable: false },
                    { data: 'allowances', name: 'allowances', orderable: false, searchable: false },
                    { data: 'deductions', name: 'deductions', orderable: false, searchable: false },
                    { data: 'net_pay', name: 'net_pay', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    emptyTable: 'No payroll rows found for current month.'
                },
                drawCallback: function () {
                    const pager = $(tableEl).closest('.dataTables_wrapper').find('.dataTables_paginate');
                    pager.addClass('hrx-att-dt-pager');
                    markSelectedButton();
                    applySelectionToCurrentPage();
                }
            });
        }

        if (searchInput && table) {
            searchInput.addEventListener('input', function () {
                table.search(searchInput.value || '').draw();
            });
        }

        if (statusSelect && table) {
            statusSelect.addEventListener('change', function () {
                table.ajax.reload(null, true);
            });
        }

        if (monthInput && table) {
            monthInput.addEventListener('change', function () {
                selectedRecordId = null;
                selectedBulkIds.clear();
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
                if (payslipContainer) {
                    payslipContainer.style.display = 'none';
                    payslipContainer.innerHTML = '';
                }
                if (payslipEmpty) payslipEmpty.style.display = '';
                table.ajax.reload(null, true);
            });
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                const shouldCheck = !!this.checked;
                document.querySelectorAll('#hrxPayrollTable .hrx-payroll-select:not(:disabled)').forEach(function (checkbox) {
                    const recordId = String(checkbox.getAttribute('data-record-id') || '');
                    checkbox.checked = shouldCheck;
                    if (shouldCheck) {
                        selectedBulkIds.add(recordId);
                    } else {
                        selectedBulkIds.delete(recordId);
                    }
                });
                syncSelectAllState();
            });
        }

        $(document).off('change.hrxPayrollSelect').on('change.hrxPayrollSelect', '.hrx-payroll-select', function () {
            const recordId = String(this.getAttribute('data-record-id') || '');
            if (!recordId) return;

            if (this.checked) {
                selectedBulkIds.add(recordId);
            } else {
                selectedBulkIds.delete(recordId);
            }

            syncSelectAllState();
        });

        $(document).off('click.hrxPayrollSlip').on('click.hrxPayrollSlip', '.hrx-payroll-slip', function () {
            const recordId = this.getAttribute('data-record-id') || '';
            openPayslipByRecord(recordId);
            if (typeof sendmsg === 'function') {
                sendmsg('success', 'Payslip loaded for ' + (this.dataset.name || 'staff') + '.');
            }
        });

        $(document).off('click.hrxPayrollSend').on('click.hrxPayrollSend', '.hrx-payroll-send', function () {
            const recordId = this.getAttribute('data-record-id') || '';
            if (!recordId || !sendSlipEndpoint) {
                if (typeof sendmsg === 'function') sendmsg('error', 'Payslip record not available.');
                return;
            }

            if (typeof loader === 'function') loader();
            csrftoken().then(function (token) {
                $.ajax({
                    url: sendSlipEndpoint,
                    type: 'POST',
                    data: {
                        payroll_record_id: recordId,
                        _token: token
                    },
                    success: function (response) {
                        if (response && response.status) {
                            if (typeof sendmsg === 'function') sendmsg('success', response.message || 'Payslip email sent successfully.');
                        } else if (typeof sendmsg === 'function') {
                            sendmsg('error', (response && response.message) || 'Unable to send payslip email.');
                        }
                    },
                    error: function (xhr) {
                        const message = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to send payslip email.';
                        if (typeof sendmsg === 'function') sendmsg('error', message);
                    },
                    complete: function () {
                        if (typeof loader === 'function') loader('hide');
                    }
                });
            }).catch(function () {
                if (typeof loader === 'function') loader('hide');
                if (typeof sendmsg === 'function') sendmsg('error', 'Unable to fetch CSRF token.');
            });
        });

        $(document).off('click.hrxPayrollPaid').on('click.hrxPayrollPaid', '.hrx-payroll-mark-paid', function () {
            const recordId = this.getAttribute('data-record-id') || '';
            if (!recordId || !markPaidEndpoint || markPaidEndpoint.indexOf('__RECORD__') === -1) {
                if (typeof sendmsg === 'function') sendmsg('error', 'Mark paid route not configured.');
                return;
            }

            const endpoint = markPaidEndpoint.replace('__RECORD__', encodeURIComponent(String(recordId)));
            if (typeof loader === 'function') loader();
            csrftoken().then(function (token) {
                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    data: {
                        _token: token
                    },
                    success: function (response) {
                        if (response && response.status) {
                            if (typeof sendmsg === 'function') sendmsg('success', response.message || 'Marked as paid.');
                            if (table) table.ajax.reload(null, false);
                        } else if (typeof sendmsg === 'function') {
                            sendmsg('error', (response && response.message) || 'Unable to update payroll status.');
                        }
                    },
                    error: function (xhr) {
                        const message = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to update payroll status.';
                        if (typeof sendmsg === 'function') sendmsg('error', message);
                    },
                    complete: function () {
                        if (typeof loader === 'function') loader('hide');
                    }
                });
            }).catch(function () {
                if (typeof loader === 'function') loader('hide');
                if (typeof sendmsg === 'function') sendmsg('error', 'Unable to fetch CSRF token.');
            });
        });

        if (processBtn) {
            processBtn.addEventListener('click', function () {
                const monthValue = monthInput && monthInput.value ? monthInput.value : defaultMonth;
                if (!monthValue) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Please select payroll month first.');
                    return;
                }

                // Block future month processing
                const selectedMonth = new Date(monthValue + '-01');
                const currentMonth = new Date();
                currentMonth.setDate(1);
                currentMonth.setHours(0, 0, 0, 0);
                if (selectedMonth >= currentMonth) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Payroll cannot be processed for a future month. Please select a previous month.');
                    return;
                }

                if (typeof loader === 'function') loader();
                csrftoken().then(function (token) {
                    $.ajax({
                        url: processEndpoint,
                        type: 'POST',
                        data: {
                            month: monthValue,
                            _token: token
                        },
                        success: function (response) {
                            if (response && response.status) {
                                if (typeof sendmsg === 'function') sendmsg('success', response.message || 'Payroll processed successfully.');
                                selectedBulkIds.clear();
                                if (selectAllCheckbox) {
                                    selectAllCheckbox.checked = false;
                                    selectAllCheckbox.indeterminate = false;
                                }
                                if (table) table.ajax.reload(null, true);
                            } else if (typeof sendmsg === 'function') {
                                sendmsg('error', (response && response.message) || 'Payroll processing failed.');
                            }
                        },
                        error: function (xhr) {
                            const message = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Payroll processing failed.';
                            if (typeof sendmsg === 'function') sendmsg('error', message);
                        },
                        complete: function () {
                            if (typeof loader === 'function') loader('hide');
                        }
                    });
                }).catch(function () {
                    if (typeof loader === 'function') loader('hide');
                    if (typeof sendmsg === 'function') sendmsg('error', 'Unable to fetch CSRF token.');
                });
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                const monthValue = monthInput && monthInput.value ? monthInput.value : defaultMonth;
                if (!monthValue) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Please select payroll month first.');
                    return;
                }

                if (!exportCsvEndpoint) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Export route not configured.');
                    return;
                }

                const exportUrl = exportCsvEndpoint + '?month=' + encodeURIComponent(monthValue);
                window.open(exportUrl, '_blank');
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Payroll CSV export started.');
                }
            });
        }

        if (bulkEmailBtn) {
            bulkEmailBtn.addEventListener('click', function () {
                const selectedIds = getSelectedIds();

                if (selectedIds.length > 0) {
                    if (!sendSelectedEndpoint) {
                        if (typeof sendmsg === 'function') sendmsg('error', 'Selected bulk email route not configured.');
                        return;
                    }

                    if (typeof loader === 'function') loader();
                    csrftoken().then(function (token) {
                        $.ajax({
                            url: sendSelectedEndpoint,
                            type: 'POST',
                            data: {
                                record_ids: selectedIds,
                                _token: token
                            },
                            success: function (response) {
                                if (response && response.status) {
                                    if (typeof sendmsg === 'function') sendmsg('success', response.message || 'Selected payslip email completed.');
                                    selectedBulkIds.clear();
                                    if (selectAllCheckbox) {
                                        selectAllCheckbox.checked = false;
                                        selectAllCheckbox.indeterminate = false;
                                    }
                                    if (table) table.ajax.reload(null, false);
                                } else if (typeof sendmsg === 'function') {
                                    sendmsg('error', (response && response.message) || 'Selected bulk email failed.');
                                }
                            },
                            error: function (xhr) {
                                const message = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Selected bulk email failed.';
                                if (typeof sendmsg === 'function') sendmsg('error', message);
                            },
                            complete: function () {
                                if (typeof loader === 'function') loader('hide');
                            }
                        });
                    }).catch(function () {
                        if (typeof loader === 'function') loader('hide');
                        if (typeof sendmsg === 'function') sendmsg('error', 'Unable to fetch CSRF token.');
                    });
                    return;
                }

                const monthValue = monthInput && monthInput.value ? monthInput.value : defaultMonth;
                if (!monthValue) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Please select payroll month first.');
                    return;
                }

                if (!sendBulkEndpoint) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Bulk email route not configured.');
                    return;
                }

                if (typeof loader === 'function') loader();
                csrftoken().then(function (token) {
                    $.ajax({
                        url: sendBulkEndpoint,
                        type: 'POST',
                        data: {
                            month: monthValue,
                            _token: token
                        },
                        success: function (response) {
                            if (response && response.status) {
                                if (typeof sendmsg === 'function') sendmsg('success', response.message || 'Bulk payslip email completed.');
                            } else if (typeof sendmsg === 'function') {
                                sendmsg('error', (response && response.message) || 'Bulk email failed.');
                            }
                        },
                        error: function (xhr) {
                            const message = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Bulk email failed.';
                            if (typeof sendmsg === 'function') sendmsg('error', message);
                        },
                        complete: function () {
                            if (typeof loader === 'function') loader('hide');
                        }
                    });
                }).catch(function () {
                    if (typeof loader === 'function') loader('hide');
                    if (typeof sendmsg === 'function') sendmsg('error', 'Unable to fetch CSRF token.');
                });
            });
        }

        if (bulkPaidBtn) {
            bulkPaidBtn.addEventListener('click', function () {
                const selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Please select rows first to mark paid.');
                    return;
                }

                if (!markPaidBulkEndpoint) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Bulk paid route not configured.');
                    return;
                }

                if (typeof loader === 'function') loader();
                csrftoken().then(function (token) {
                    $.ajax({
                        url: markPaidBulkEndpoint,
                        type: 'POST',
                        data: {
                            record_ids: selectedIds,
                            _token: token
                        },
                        success: function (response) {
                            if (response && response.status) {
                                if (typeof sendmsg === 'function') sendmsg('success', response.message || 'Selected rows marked as paid.');
                                selectedBulkIds.clear();
                                if (selectAllCheckbox) {
                                    selectAllCheckbox.checked = false;
                                    selectAllCheckbox.indeterminate = false;
                                }
                                if (table) table.ajax.reload(null, false);
                            } else if (typeof sendmsg === 'function') {
                                sendmsg('error', (response && response.message) || 'Unable to update selected rows.');
                            }
                        },
                        error: function (xhr) {
                            const message = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to update selected rows.';
                            if (typeof sendmsg === 'function') sendmsg('error', message);
                        },
                        complete: function () {
                            if (typeof loader === 'function') loader('hide');
                        }
                    });
                }).catch(function () {
                    if (typeof loader === 'function') loader('hide');
                    if (typeof sendmsg === 'function') sendmsg('error', 'Unable to fetch CSRF token.');
                });
            });
        }

        $(document).off('click.hrxSlipPrint').on('click.hrxSlipPrint', '.hrx-slip-print', function () {
            if (!selectedRecordId) {
                if (typeof sendmsg === 'function') sendmsg('error', 'No payslip loaded to print.');
                return;
            }

            if (!payslipPdfEndpoint || payslipPdfEndpoint.indexOf('__RECORD__') === -1) {
                if (typeof sendmsg === 'function') sendmsg('error', 'Payslip PDF route not configured.');
                return;
            }

            const pdfUrl = payslipPdfEndpoint.replace('__RECORD__', encodeURIComponent(String(selectedRecordId)));
            const printWin = window.open(pdfUrl, '_blank');
            if (!printWin) {
                if (typeof sendmsg === 'function') sendmsg('error', 'Popup blocked. Please allow popups and try again.');
                return;
            }

            if (typeof sendmsg === 'function') {
                sendmsg('info', 'Payslip PDF opened in new tab.');
            }
        });

        $(document).off('click.hrxSlipEmail').on('click.hrxSlipEmail', '.hrx-slip-email', function () {
            if (!selectedRecordId || !sendSlipEndpoint) {
                if (typeof sendmsg === 'function') sendmsg('error', 'No payslip selected for email.');
                return;
            }

            if (typeof loader === 'function') loader();
            csrftoken().then(function (token) {
                $.ajax({
                    url: sendSlipEndpoint,
                    type: 'POST',
                    data: {
                        payroll_record_id: selectedRecordId,
                        _token: token
                    },
                    success: function (response) {
                        if (response && response.status) {
                            if (typeof sendmsg === 'function') sendmsg('success', response.message || 'Payslip email sent successfully.');
                        } else if (typeof sendmsg === 'function') {
                            sendmsg('error', (response && response.message) || 'Unable to send payslip email.');
                        }
                    },
                    error: function (xhr) {
                        const message = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to send payslip email.';
                        if (typeof sendmsg === 'function') sendmsg('error', message);
                    },
                    complete: function () {
                        if (typeof loader === 'function') loader('hide');
                    }
                });
            }).catch(function () {
                if (typeof loader === 'function') loader('hide');
                if (typeof sendmsg === 'function') sendmsg('error', 'Unable to fetch CSRF token.');
            });
        });

        return true;
    }
};
