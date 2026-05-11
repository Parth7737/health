window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.training = {
    init: function () {
        const searchInput = document.getElementById('hrxTrainingSearch');
        const statusSelect = document.getElementById('hrxTrainingStatus');
        const scheduleBtn = document.getElementById('hrxTrainingSchedule');
        const exportBtn = document.getElementById('hrxTrainingExport');
        const rows = Array.from(document.querySelectorAll('.hrx-training-row'));

        function applyFilter() {
            const keyword = (searchInput ? searchInput.value : '').trim().toLowerCase();
            const status = (statusSelect ? statusSelect.value : '').trim().toLowerCase();

            rows.forEach(function (row) {
                const title = row.dataset.title || '';
                const rowStatus = row.dataset.status || '';
                const searchHit = !keyword || title.includes(keyword);
                const statusHit = !status || rowStatus === status;
                row.style.display = searchHit && statusHit ? '' : 'none';
            });
        }

        [searchInput, statusSelect].forEach(function (el) {
            if (!el) {
                return;
            }
            el.addEventListener('input', applyFilter);
            el.addEventListener('change', applyFilter);
        });

        document.querySelectorAll('.hrx-training-view').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', this.dataset.title + ' details opened.');
                }
            });
        });

        if (scheduleBtn) {
            scheduleBtn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Training schedule workflow opened.');
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Training register exported.');
                }
            });
        }

        applyFilter();
        return true;
    }
};
