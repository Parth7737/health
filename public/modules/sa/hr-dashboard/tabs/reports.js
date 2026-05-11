window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.reports = {
    init: function () {
        const panel = document.getElementById('hrx-panel-reports');
        if (!panel || typeof Chart === 'undefined') {
            return;
        }

        const staffTrend = JSON.parse(panel.getAttribute('data-staff-trend') || '[]');
        const payrollTrend = JSON.parse(panel.getAttribute('data-payroll-trend') || '[]');
        const periodSelect = document.getElementById('hrxReportsPeriod');
        const refreshBtn = document.getElementById('hrxReportsRefresh');
        const exportBtn = document.getElementById('hrxReportsExport');

        let staffChart = null;
        let payrollChart = null;

        function byPeriod(source, months) {
            if (!months || source.length <= months) {
                return source;
            }
            return source.slice(source.length - months);
        }

        function render() {
            const months = parseInt(periodSelect ? periodSelect.value : '6', 10);
            const scopedStaff = byPeriod(staffTrend, months);
            const scopedPayroll = byPeriod(payrollTrend, months);

            const staffCanvas = document.getElementById('hrxStaffTrendChart');
            if (staffCanvas) {
                if (staffChart) {
                    staffChart.destroy();
                }
                staffChart = new Chart(staffCanvas, {
                    type: 'line',
                    data: {
                        labels: scopedStaff.map(row => row.month_key),
                        datasets: [{
                            label: 'Staff Joined',
                            data: scopedStaff.map(row => row.total),
                            borderColor: '#4a148c',
                            backgroundColor: 'rgba(74,20,140,0.08)',
                            fill: true,
                            tension: 0.35
                        }]
                    }
                });
            }

            const payrollCanvas = document.getElementById('hrxPayrollTrendChart');
            if (payrollCanvas) {
                if (payrollChart) {
                    payrollChart.destroy();
                }
                payrollChart = new Chart(payrollCanvas, {
                    type: 'bar',
                    data: {
                        labels: scopedPayroll.map(row => row.month_key),
                        datasets: [{
                            label: 'Net Payroll',
                            data: scopedPayroll.map(row => row.total_net),
                            backgroundColor: '#2e7d32'
                        }]
                    },
                    options: {
                        plugins: { legend: { display: false } }
                    }
                });
            }
        }

        if (periodSelect) {
            periodSelect.addEventListener('change', function () {
                render();
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                render();
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Reports refreshed.');
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Reports exported.');
                }
            });
        }

        render();
    }
};
