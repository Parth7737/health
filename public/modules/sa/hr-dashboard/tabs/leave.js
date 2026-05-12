window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.leave = {
    init: function () {
        var config = window.HRDashboardConfig || {};
        var panel = document.getElementById('hrx-panel-leave');
        if (!panel || typeof $ === 'undefined') {
            return;
        }

        var leaveRequestsUrl = config.leaveRequestsDataUrl || '';
        var staffLeaveBalanceUrl = config.staffLeaveBalanceUrl || '';

        var statusRows = JSON.parse(panel.getAttribute('data-status-chart') || '[]');
        var balanceRows = JSON.parse(panel.getAttribute('data-balance-chart') || '[]');
        var statusCanvas = document.getElementById('hrxLeaveStatusChart');
        var balanceCanvas = document.getElementById('hrxLeaveBalanceChart');
        var searchInput = document.getElementById('hrxLeaveSearch');
        var statusSelect = document.getElementById('hrxLeaveStatus');
        var refreshBtn = document.getElementById('hrxLeaveRefresh');
        var exportBtn = document.getElementById('hrxLeaveExport');
        var leaveRequestsTable = null;
        var balanceModalStaffId = null;

        function getCsrfToken() {
            if (window.Laravel && window.Laravel.csrfToken) {
                return window.Laravel.csrfToken;
            }
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function reloadLeaveTab() {
            if (window.HRDashboard && typeof window.HRDashboard.loadTab === 'function') {
                window.HRDashboard.loadTab('leave');
            }
        }

        function submitStatusChange(requestNo, status, note) {
            if (!config.updateLeaveStatusUrl) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'Leave status URL not configured.');
                }
                return;
            }

            if (typeof loader === 'function') {
                loader();
            }

            $.ajax({
                url: config.updateLeaveStatusUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    request_no: requestNo,
                    status: status,
                    note: note
                },
                success: function (response) {
                    if (!response || !response.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (response && response.message) || 'Unable to update leave status.');
                        }
                        return;
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('success', response.message || 'Leave request updated successfully.');
                    }
                    reloadLeaveTab();
                },
                error: function (xhr) {
                    var message = 'Unable to update leave status.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var firstError = Object.values(xhr.responseJSON.errors)[0];
                        message = Array.isArray(firstError) ? firstError[0] : firstError;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('error', message);
                    }
                },
                complete: function () {
                    if (typeof loader === 'function') {
                        loader('hide');
                    }
                }
            });
        }

        function openStatusPopup(requestNo, status, options) {
            options = options || {};
            var withdraw = !!options.withdraw;
            var title = withdraw
                ? 'Withdraw approved leave'
                : (status === 'Approved' ? 'Approve Leave Request' : 'Reject Leave Request');
            var confirmText = withdraw
                ? 'Withdraw'
                : (status === 'Approved' ? 'Approve Request' : 'Reject Request');
            var confirmColor = (status === 'Approved' && !withdraw) ? '#15803d' : (withdraw ? '#b45309' : '#dc2626');
            var icon = (status === 'Approved' && !withdraw) ? 'success' : 'warning';

            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({
                    title: title,
                    icon: icon,
                    input: 'textarea',
                    inputLabel: 'Add note',
                    inputPlaceholder: withdraw
                        ? 'Reason for withdrawal (linked attendance will be removed)...'
                        : (status === 'Approved' ? 'Approval note...' : 'Rejection reason...'),
                    inputAttributes: {
                        'aria-label': 'Status note',
                        maxlength: '1000'
                    },
                    inputValidator: function (value) {
                        if (!value || !String(value).trim()) {
                            return 'Note is required.';
                        }
                        return null;
                    },
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: confirmColor,
                    reverseButtons: true,
                    customClass: {
                        popup: 'hrx-leave-status-popup'
                    }
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }
                    submitStatusChange(requestNo, status, String(result.value || '').trim());
                });
                return;
            }

            var note = window.prompt((withdraw ? 'Withdrawal' : (status === 'Approved' ? 'Approval' : 'Rejection')) + ' note:');
            if (!note || !String(note).trim()) {
                return;
            }
            submitStatusChange(requestNo, status, String(note).trim());
        }

        function destroyDataTableIfExists(tableSelector) {
            if ($.fn.DataTable && $.fn.DataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().clear().destroy(true);
            }
        }

        function initLeaveRequestsTable() {
            if (!leaveRequestsUrl) {
                return;
            }
            destroyDataTableIfExists('#hrxLeaveRequestsTable');

            var hasUpperApi = typeof $.fn.DataTable === 'function';
            var hasLowerApi = typeof $.fn.dataTable === 'function';
            if (!hasUpperApi && !hasLowerApi) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'DataTable library not available.');
                }
                return;
            }

            leaveRequestsTable = $('#hrxLeaveRequestsTable').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                info: true,
                searching: false,
                lengthChange: true,
                responsive: true,
                autoWidth: true,
                dom: 'Blrtip',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'desc']],
                columnDefs: [
                    { targets: 0, visible: false, searchable: false }
                ],
                buttons: [
                    {
                        text: '<i class="fa fa-sync"></i>',
                        className: 'btn btn-secondary',
                        titleAttr: 'Reload Table',
                        action: function (e, dt) {
                            dt.ajax.reload();
                        }
                    },
                    { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
                    { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'Export as CSV' },
                    { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Export as Excel' },
                    { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'Export as PDF' },
                    { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print' },
                    { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Column Visibility' }
                ],
                ajax: {
                    url: leaveRequestsUrl,
                    type: 'GET',
                    data: function (d) {
                        d.search_custom = searchInput ? searchInput.value.trim() : '';
                        d.status_filter = statusSelect ? statusSelect.value.trim() : '';
                    }
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'request_no', name: 'request_no', orderable: true, searchable: false },
                    { data: 'staff_name', name: 'staff_name', orderable: true, searchable: false },
                    { data: 'type_name', name: 'type_name', orderable: true, searchable: false },
                    { data: 'from_date', name: 'from_date', orderable: true, searchable: false },
                    { data: 'to_date', name: 'to_date', orderable: true, searchable: false },
                    { data: 'total_days', name: 'total_days', orderable: true, searchable: false },
                    { data: 'reason', name: 'reason', orderable: true, searchable: false },
                    { data: 'status', name: 'status', orderable: true, searchable: false },
                    { data: 'balance', name: 'balance', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        }

        function populateYearSelect(selectedYear) {
            var sel = document.getElementById('hrxStaffLeaveBalanceYear');
            if (!sel) {
                return;
            }
            var y = new Date().getFullYear();
            var pick = selectedYear && !isNaN(selectedYear) ? parseInt(selectedYear, 10) : y;
            sel.innerHTML = '';
            for (var i = y - 2; i <= y + 1; i++) {
                var opt = document.createElement('option');
                opt.value = String(i);
                opt.textContent = String(i);
                if (i === pick) {
                    opt.selected = true;
                }
                sel.appendChild(opt);
            }
        }

        function loadStaffLeaveBalanceHtml() {
            if (!staffLeaveBalanceUrl || !balanceModalStaffId) {
                return;
            }
            var yearEl = document.getElementById('hrxStaffLeaveBalanceYear');
            var year = yearEl ? parseInt(yearEl.value, 10) : new Date().getFullYear();
            var body = document.getElementById('hrxStaffLeaveBalanceBody');
            if (body) {
                body.innerHTML = '<span class="text-muted" style="font-size:13px;">Loading…</span>';
            }
            $.ajax({
                url: staffLeaveBalanceUrl,
                type: 'GET',
                data: { staff_id: balanceModalStaffId, year: year },
                success: function (res) {
                    if (!res || !res.status) {
                        if (body) {
                            body.innerHTML = '<span class="text-danger">Unable to load balance.</span>';
                        }
                        return;
                    }
                    var title = document.getElementById('hrxStaffLeaveBalanceModalLabel');
                    if (title) {
                        title.textContent = 'Leave balance — ' + (res.staff_name || 'Staff') + ' (' + (res.year || year) + ')';
                    }
                    if (body) {
                        body.innerHTML = res.html || '';
                    }
                },
                error: function () {
                    if (body) {
                        body.innerHTML = '<span class="text-danger">Unable to load balance.</span>';
                    }
                }
            });
        }

        function openBalanceModal(staffId, staffName) {
            balanceModalStaffId = parseInt(staffId, 10);
            populateYearSelect(new Date().getFullYear());
            var title = document.getElementById('hrxStaffLeaveBalanceModalLabel');
            if (title) {
                title.textContent = 'Leave balance summary — ' + (staffName || 'Staff');
            }
            var body = document.getElementById('hrxStaffLeaveBalanceBody');
            if (body) {
                body.innerHTML = '<span class="text-muted" style="font-size:13px;">Loading…</span>';
            }
            $('#hrxStaffLeaveBalanceModal').modal('show');
            loadStaffLeaveBalanceHtml();
        }

        if (balanceCanvas && typeof Chart !== 'undefined' && balanceRows.length) {
            new Chart(balanceCanvas, {
                type: 'bar',
                data: {
                    labels: balanceRows.map(function (r) { return r.name; }),
                    datasets: [
                        {
                            label: 'Available',
                            data: balanceRows.map(function (r) { return r.available; }),
                            backgroundColor: '#e3f0ff',
                            borderRadius: 3
                        },
                        {
                            label: 'Used',
                            data: balanceRows.map(function (r) { return r.used; }),
                            backgroundColor: '#4a148c',
                            borderRadius: 3
                        }
                    ]
                },
                options: {
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, grid: { color: '#f0f3f8' } }
                    },
                    maintainAspectRatio: false
                }
            });
        } else if (balanceCanvas && typeof Chart !== 'undefined') {
            var ctx = balanceCanvas.getContext('2d');
            ctx.font = '13px Inter, sans-serif';
            ctx.fillStyle = '#5a7894';
            ctx.textAlign = 'center';
            ctx.fillText('No leave balance data yet.', balanceCanvas.width / 2, balanceCanvas.height / 2);
        }

        if (statusCanvas && typeof Chart !== 'undefined') {
            new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: statusRows.map(function (r) { return r.status; }),
                    datasets: [{
                        data: statusRows.map(function (r) { return r.total; }),
                        backgroundColor: ['#f57c00', '#2e7d32', '#c62828'],
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: { legend: { position: 'bottom' } },
                    maintainAspectRatio: false
                }
            });
        }

        initLeaveRequestsTable();

        var debounceTimer = null;
        function scheduleReloadRequests() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                if (leaveRequestsTable) {
                    leaveRequestsTable.ajax.reload();
                }
            }, 350);
        }

        if (searchInput) {
            searchInput.addEventListener('input', scheduleReloadRequests);
        }
        if (statusSelect) {
            statusSelect.addEventListener('change', function () {
                if (leaveRequestsTable) {
                    leaveRequestsTable.ajax.reload();
                }
            });
        }

        $(document).off('click.hrxLeaveApprove').on('click.hrxLeaveApprove', '.hrx-leave-approve-btn', function () {
            openStatusPopup($(this).data('request'), 'Approved');
        });
        $(document).off('click.hrxLeaveReject').on('click.hrxLeaveReject', '.hrx-leave-reject-btn', function () {
            openStatusPopup($(this).data('request'), 'Rejected');
        });
        $(document).off('click.hrxLeaveWithdraw').on('click.hrxLeaveWithdraw', '.hrx-leave-withdraw-btn', function () {
            openStatusPopup($(this).data('request'), 'Rejected', { withdraw: true });
        });
        $(document).off('click.hrxLeaveView').on('click.hrxLeaveView', '.hrx-leave-view-btn', function () {
            var btn = $(this);
            var requestNo = btn.data('request');
            var displayName = btn.data('display-name') || 'N/A';
            var typeName = btn.data('type') || 'N/A';
            var reason = btn.data('reason') || 'N/A';
            var note = btn.data('note') || 'No note added.';
            var st = (btn.data('status') || '').toString().toLowerCase();
            if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
                if (typeof sendmsg === 'function') {
                    sendmsg('info', 'Leave request ' + requestNo);
                }
                return;
            }
            var statusBadgeClass = st === 'approved'
                ? 'background:#e8f5e9;color:#2e7d32;'
                : (st === 'rejected' ? 'background:#ffebee;color:#c62828;' : 'background:#fff3e0;color:#e65100;');
            Swal.fire({
                title: 'Leave Request ' + requestNo,
                html: '' +
                    '<div style="text-align:left;font-size:13px;line-height:1.7">' +
                    '<div><strong>Staff:</strong> ' + displayName + '</div>' +
                    '<div><strong>Type:</strong> ' + typeName + '</div>' +
                    '<div><strong>Reason:</strong> ' + reason + '</div>' +
                    '<div><strong>Status:</strong> <span style="display:inline-flex;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700;' + statusBadgeClass + '">' + (st || '').toUpperCase() + '</span></div>' +
                    '<div style="margin-top:8px"><strong>Note:</strong><br>' + note + '</div>' +
                    '</div>',
                confirmButtonText: 'Close',
                confirmButtonColor: '#1565c0'
            });
        });

        $(document).off('click.hrxStaffLeaveBal').on('click.hrxStaffLeaveBal', '.hrx-staff-leave-balance-open', function () {
            var sid = $(this).data('staff-id');
            var sname = $(this).data('staff-name') || '';
            openBalanceModal(sid, sname);
        });

        $('#hrxStaffLeaveBalanceReload').off('click').on('click', function () {
            loadStaffLeaveBalanceHtml();
        });

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                if (leaveRequestsTable) {
                    leaveRequestsTable.ajax.reload();
                }
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Leave requests refreshed.');
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (leaveRequestsTable && typeof leaveRequestsTable.button === 'function') {
                    try {
                        leaveRequestsTable.button('.buttons-excel').trigger();
                        return;
                    } catch (e) { /* ignore */ }
                }
                if (typeof sendmsg === 'function') {
                    sendmsg('info', 'Use the Excel button on the leave requests table to export.');
                }
            });
        }
    }
};
