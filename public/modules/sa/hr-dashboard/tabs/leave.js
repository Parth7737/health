window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.leave = {
    init: function () {
        var config = window.HRDashboardConfig || {};
        const panel = document.getElementById('hrx-panel-leave');
        if (!panel || typeof Chart === 'undefined') {
            return;
        }

        /* ── data from blade ───────────────────────────────────────── */
        const statusRows    = JSON.parse(panel.getAttribute('data-status-chart') || '[]');
        const balanceRows   = JSON.parse(panel.getAttribute('data-balance-chart') || '[]');

        const statusCanvas  = document.getElementById('hrxLeaveStatusChart');
        const balanceCanvas = document.getElementById('hrxLeaveBalanceChart');
        const searchInput   = document.getElementById('hrxLeaveSearch');
        const statusSelect  = document.getElementById('hrxLeaveStatus');
        const refreshBtn    = document.getElementById('hrxLeaveRefresh');
        const exportBtn     = document.getElementById('hrxLeaveExport');
        const tableRows     = Array.from(document.querySelectorAll('.hrx-leave-row'));

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
            if (!config.updateLeaveStatusUrl || typeof $ === 'undefined') {
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

        function openStatusPopup(requestNo, status) {
            var title = status === 'Approved' ? 'Approve Leave Request' : 'Reject Leave Request';
            var confirmText = status === 'Approved' ? 'Approve Request' : 'Reject Request';
            var confirmColor = status === 'Approved' ? '#15803d' : '#dc2626';
            var icon = status === 'Approved' ? 'success' : 'warning';

            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({
                    title: title,
                    icon: icon,
                    input: 'textarea',
                    inputLabel: 'Add note',
                    inputPlaceholder: status === 'Approved' ? 'Approval note...' : 'Rejection reason...',
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

            var note = window.prompt((status === 'Approved' ? 'Approval' : 'Rejection') + ' note:');
            if (!note || !String(note).trim()) {
                return;
            }
            submitStatusChange(requestNo, status, String(note).trim());
        }

        /* ── Leave Balance Summary – stacked bar (hr.html leaveChart) ── */
        if (balanceCanvas && balanceRows.length) {
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
        } else if (balanceCanvas) {
            /* fallback: no data */
            var ctx = balanceCanvas.getContext('2d');
            ctx.font = '13px Inter, sans-serif';
            ctx.fillStyle = '#5a7894';
            ctx.textAlign = 'center';
            ctx.fillText('No leave type data yet.', balanceCanvas.width / 2, balanceCanvas.height / 2);
        }

        /* ── Leave Status Mix – doughnut ─────────────────────────── */
        if (statusCanvas) {
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

        /* ── Filter ──────────────────────────────────────────────── */
        function applyFilter() {
            var keyword = (searchInput ? searchInput.value : '').trim().toLowerCase();
            var status  = (statusSelect ? statusSelect.value : '').trim().toLowerCase();

            tableRows.forEach(function (row) {
                var req      = row.dataset.request || '';
                var name     = row.dataset.name    || '';
                var rowStat  = row.dataset.status  || '';
                var searchHit = !keyword || req.includes(keyword) || name.includes(keyword);
                var statusHit = !status  || rowStat === status;
                row.style.display = searchHit && statusHit ? '' : 'none';
            });
        }

        [searchInput, statusSelect].forEach(function (el) {
            if (!el) { return; }
            el.addEventListener('input',  applyFilter);
            el.addEventListener('change', applyFilter);
        });

        /* ── Action buttons ──────────────────────────────────────── */
        document.querySelectorAll('.hrx-leave-approve').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openStatusPopup(this.dataset.request, 'Approved');
            });
        });

        document.querySelectorAll('.hrx-leave-reject').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openStatusPopup(this.dataset.request, 'Rejected');
            });
        });

        document.querySelectorAll('.hrx-leave-view').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var row = this.closest('.hrx-leave-row');
                if (!row || typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
                    if (typeof sendmsg === 'function') {
                        sendmsg('info', 'Opened leave request ' + this.dataset.request + '.');
                    }
                    return;
                }

                var statusBadgeClass = row.dataset.status === 'approved'
                    ? 'background:#e8f5e9;color:#2e7d32;'
                    : (row.dataset.status === 'rejected'
                        ? 'background:#ffebee;color:#c62828;'
                        : 'background:#fff3e0;color:#e65100;');

                Swal.fire({
                    title: 'Leave Request ' + this.dataset.request,
                    html: '' +
                        '<div style="text-align:left;font-size:13px;line-height:1.7">' +
                        '<div><strong>Staff:</strong> ' + (row.dataset.displayName || 'N/A') + '</div>' +
                        '<div><strong>Type:</strong> ' + (row.dataset.type || 'N/A') + '</div>' +
                        '<div><strong>Reason:</strong> ' + (row.dataset.reason || 'N/A') + '</div>' +
                        '<div><strong>Status:</strong> <span style="display:inline-flex;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700;' + statusBadgeClass + '">' + (row.dataset.status || '').toUpperCase() + '</span></div>' +
                        '<div style="margin-top:8px"><strong>Note:</strong><br>' + (row.dataset.note || 'No note added.') + '</div>' +
                        '</div>',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#1565c0'
                });
            });
        });

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                applyFilter();
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Leave section refreshed.');
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Leave report exported.');
                }
            });
        }

        applyFilter();
    }
};
