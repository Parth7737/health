window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.reports = {
    init: function () {
        const panel = document.getElementById('hrx-panel-reports');
        if (!panel || typeof Chart === 'undefined') {
            return;
        }

        const config = window.HRDashboardConfig || {};
        const reportsUrl = config.reportsDataUrl || '';

        const periodSelect = document.getElementById('hrxReportsPeriod');
        const refreshBtn = document.getElementById('hrxReportsRefresh');
        const exportBtn = document.getElementById('hrxReportsExport');
        const loaderEl = document.getElementById('hrxReportsLoader');

        function setReportsLoading(on) {
            if (loaderEl) {
                loaderEl.classList.toggle('hrx-reports-loader--show', !!on);
                loaderEl.setAttribute('aria-busy', on ? 'true' : 'false');
            }
            if (periodSelect) {
                periodSelect.disabled = !!on;
            }
            if (refreshBtn) {
                refreshBtn.disabled = !!on;
            }
        }

        let payload = parsePayload(panel.getAttribute('data-reports'));
        let attChart = null;
        let leaveChart = null;
        let payrollChart = null;

        const leavePalette = ['#1565c0', '#2e7d32', '#c62828', '#880e4f', '#e65100', '#00695c', '#4a148c', '#ef6c00'];

        function parsePayload(raw) {
            try {
                const o = JSON.parse(raw || '{}');
                return o && typeof o === 'object' ? o : {};
            } catch (e) {
                return {};
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text == null ? '' : String(text);
            return div.innerHTML;
        }

        function chartBaseOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f3f8' }
                    }
                }
            };
        }

        function renderDeptTable(rows) {
            const tbody = document.getElementById('hrxDeptStaffBody');
            if (!tbody) {
                return;
            }
            if (!rows || !rows.length) {
                tbody.innerHTML = '<tr><td colspan="5">No department data found.</td></tr>';
                return;
            }
            tbody.innerHTML = rows.map(function (d) {
                const doc = d.doctors ? d.doctors : '—';
                const nur = d.nurses ? d.nurses : '—';
                return (
                    '<tr><td class="fw-700">' + escapeHtml(d.dept) + '</td><td>' + Number(d.total || 0) + '</td>' +
                    '<td style="color:#4a148c;font-weight:700">' + doc + '</td>' +
                    '<td style="color:#2e7d32;font-weight:700">' + nur + '</td>' +
                    '<td>' + Number(d.support || 0) + '</td></tr>'
                );
            }).join('');
        }

        function renderCharts() {
            const att = payload.attendance_trend || [];
            const leave = payload.leave_by_type || [];
            const pay = payload.payroll_trend || [];

            const attEl = document.getElementById('hrxAttTrendChart');
            if (attEl) {
                if (attChart) {
                    attChart.destroy();
                }
                attChart = new Chart(attEl, {
                    type: 'line',
                    data: {
                        labels: att.map(function (r) { return r.label; }),
                        datasets: [
                            {
                                label: 'Present %',
                                data: att.map(function (r) { return Number(r.present_pct || 0); }),
                                borderColor: '#2e7d32',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Absent %',
                                data: att.map(function (r) { return Number(r.absent_pct || 0); }),
                                borderColor: '#c62828',
                                fill: false,
                                tension: 0.4
                            }
                        ]
                    },
                    options: chartBaseOptions()
                });
            }

            const leaveEl = document.getElementById('hrxLeaveTypeChart');
            if (leaveEl) {
                if (leaveChart) {
                    leaveChart.destroy();
                }
                const labels = leave.length ? leave.map(function (r) { return r.name; }) : ['No approved leave in range'];
                const data = leave.length ? leave.map(function (r) { return Number(r.days || 0); }) : [0];
                const bg = labels.map(function (_, i) {
                    return leavePalette[i % leavePalette.length];
                });
                leaveChart = new Chart(leaveEl, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Days used',
                            data: data,
                            backgroundColor: bg
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f0f3f8' }
                            }
                        }
                    }
                });
            }

            const payEl = document.getElementById('hrxPayrollTrendChart');
            if (payEl) {
                if (payrollChart) {
                    payrollChart.destroy();
                }
                payrollChart = new Chart(payEl, {
                    type: 'line',
                    data: {
                        labels: pay.map(function (r) { return r.month_label || r.month_key; }),
                        datasets: [{
                            label: 'Payroll (₹L)',
                            data: pay.map(function (r) { return Number(r.total_lakhs != null ? r.total_lakhs : (Number(r.total_net || 0) / 100000)); }),
                            borderColor: '#4a148c',
                            backgroundColor: 'rgba(74,20,140,0.08)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f0f3f8' },
                                ticks: {
                                    callback: function (v) {
                                        return '₹' + v + 'L';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            renderDeptTable(payload.department_staff || []);
        }

        function exportCsv() {
            const lines = [];
            const p = payload || {};
            lines.push('HR Reports export');
            lines.push('Period months,' + (p.months || ''));
            lines.push('');
            lines.push('Attendance trend (last 7 days)');
            lines.push('Day,Present %,Absent %');
            (p.attendance_trend || []).forEach(function (r) {
                lines.push([r.label, r.present_pct, r.absent_pct].join(','));
            });
            lines.push('');
            lines.push('Leave by type (days in selected period)');
            lines.push('Leave type,Days');
            (p.leave_by_type || []).forEach(function (r) {
                lines.push([String(r.name).replace(/,/g, ' '), r.days].join(','));
            });
            lines.push('');
            lines.push('Payroll trend');
            lines.push('Month,Net INR,Lakhs');
            (p.payroll_trend || []).forEach(function (r) {
                lines.push([r.month_label || r.month_key, r.total_net, r.total_lakhs].join(','));
            });
            lines.push('');
            lines.push('Department-wise staff');
            lines.push('Department,Total,Doctors,Nurses,Support');
            (p.department_staff || []).forEach(function (d) {
                lines.push([d.dept, d.total, d.doctors, d.nurses, d.support].join(','));
            });

            const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'hr-reports-' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            if (typeof sendmsg === 'function') {
                sendmsg('success', 'Report exported as CSV.');
            }
        }

        function syncPeriodFromPayload() {
            if (periodSelect && payload.months) {
                const m = String(payload.months);
                if (['3', '6', '12'].indexOf(m) >= 0) {
                    periodSelect.value = m;
                }
            }
        }

        function loadMonths(months, silent) {
            if (!reportsUrl) {
                renderCharts();
                return;
            }
            setReportsLoading(true);
            const url = reportsUrl + (reportsUrl.indexOf('?') >= 0 ? '&' : '?') + 'months=' + encodeURIComponent(months);
            fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(function (r) { return r.json(); })
                .then(function (j) {
                    if (j && j.status && j.data) {
                        payload = j.data;
                        panel.setAttribute('data-reports', JSON.stringify(payload));
                        renderCharts();
                        if (!silent && typeof sendmsg === 'function') {
                            sendmsg('success', 'Reports updated.');
                        }
                    } else if (!silent && typeof sendmsg === 'function') {
                        sendmsg('error', 'Could not load reports.');
                    }
                })
                .catch(function () {
                    if (!silent && typeof sendmsg === 'function') {
                        sendmsg('error', 'Could not load reports.');
                    }
                })
                .finally(function () {
                    setReportsLoading(false);
                });
        }

        syncPeriodFromPayload();
        renderCharts();

        if (periodSelect) {
            periodSelect.addEventListener('change', function () {
                loadMonths(this.value, false);
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                const m = periodSelect ? periodSelect.value : '6';
                loadMonths(m, false);
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                exportCsv();
            });
        }
    }
};
