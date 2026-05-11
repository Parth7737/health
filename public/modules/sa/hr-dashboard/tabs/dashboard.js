window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.dashboard = {
    init: function () {
        const panel = document.getElementById('hrx-panel-dashboard');
        if (!panel || typeof Chart === 'undefined') {
            return;
        }

        const headcount = JSON.parse(panel.getAttribute('data-headcount') || '[]');
        const category = JSON.parse(panel.getAttribute('data-category') || '[]');
        const categoryByDepartment = JSON.parse(panel.getAttribute('data-category-by-department') || '{}');
        const departmentFilter = document.getElementById('hrxDashboardDepartmentFilter');
        const refreshBtn = document.getElementById('hrxDashboardRefresh');
        const exportBtn = document.getElementById('hrxDashboardExport');
        const detailsBtn = document.getElementById('hrxDashboardAttendanceDetails');

        let headcountChart = null;
        let categoryChart = null;

        function renderCharts(departmentName) {
            const scopedHeadcount = !departmentName
                ? headcount
                : headcount.filter(item => item.department === departmentName);

            const scopedCategory = !departmentName
                ? category
                : (categoryByDepartment[departmentName] || []);

            const headcountEl = document.getElementById('hrxHeadcountChart');
            if (headcountEl) {
                if (headcountChart) {
                    headcountChart.destroy();
                }

                headcountChart = new Chart(headcountEl, {
                    type: 'bar',
                    data: {
                        labels: scopedHeadcount.map(item => item.department),
                        datasets: [
                            {
                                label: 'Present',
                                data: scopedHeadcount.map(item => Number(item.present || 0)),
                                backgroundColor: '#2e7d32'
                            },
                            {
                                label: 'Absent',
                                data: scopedHeadcount.map(item => Number(item.absent || 0)),
                                backgroundColor: '#c62828'
                            },
                            {
                                label: 'On Leave',
                                data: scopedHeadcount.map(item => Number(item.on_leave || 0)),
                                backgroundColor: '#e65100'
                            }
                        ]
                    },
                    options: {
                        plugins: { legend: { display: true, position: 'top' } },
                        scales: {
                            x: { stacked: true },
                            y: { beginAtZero: true, stacked: true }
                        }
                    }
                });
            }

            const categoryEl = document.getElementById('hrxCategoryChart');
            if (categoryEl) {
                if (categoryChart) {
                    categoryChart.destroy();
                }

                categoryChart = new Chart(categoryEl, {
                    type: 'doughnut',
                    data: {
                        labels: scopedCategory.map(item => item.role_name),
                        datasets: [{
                            data: scopedCategory.map(item => item.total),
                            backgroundColor: ['#4a148c', '#1565c0', '#2e7d32', '#e65100', '#c62828', '#00695c']
                        }]
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }

            const summaryRows = Array.from(document.querySelectorAll('.hrx-attendance-summary-row'));
            summaryRows.forEach(function (row) {
                if (!departmentName) {
                    row.style.display = '';
                    return;
                }

                const rowDepartment = (row.dataset.department || '').trim().toLowerCase();
                row.style.display = rowDepartment === departmentName.trim().toLowerCase() ? '' : 'none';
            });
        }

        renderCharts('');

        if (departmentFilter) {
            departmentFilter.addEventListener('change', function () {
                renderCharts(this.value || '');
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                if (departmentFilter) {
                    departmentFilter.value = '';
                }
                if (window.HRDashboard && typeof window.HRDashboard.loadTab === 'function') {
                    window.HRDashboard.loadTab('dashboard');
                } else {
                    renderCharts('');
                }
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Dashboard refreshed with All Departments.');
                }
            });
        }

        if (detailsBtn) {
            detailsBtn.addEventListener('click', function () {
                const selectedDepartment = departmentFilter ? (departmentFilter.value || '') : '';

                if (window.HRDashboard && typeof window.HRDashboard.setTabFilter === 'function') {
                    window.HRDashboard.setTabFilter('attendance', 'department', selectedDepartment);
                }

                if (window.HRDashboard && typeof window.HRDashboard.openTab === 'function') {
                    window.HRDashboard.openTab('attendance');
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Dashboard snapshot exported.');
                }
            });
        }
    }
};
