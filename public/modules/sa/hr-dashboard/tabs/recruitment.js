window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.recruitment = {
    init: function () {
        const searchInput = document.getElementById('hrxRecruitmentSearch');
        const statusSelect = document.getElementById('hrxRecruitmentStatus');
        const postBtn = document.getElementById('hrxRecruitmentPost');
        const exportBtn = document.getElementById('hrxRecruitmentExport');
        const rows = Array.from(document.querySelectorAll('.hrx-recruitment-row'));

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

        document.querySelectorAll('.hrx-recruitment-view').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', this.dataset.title + ' details opened.');
                }
            });
        });

        if (postBtn) {
            postBtn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'New vacancy workflow opened.');
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (typeof sendmsg === 'function') {
                    sendmsg('success', 'Recruitment data exported.');
                }
            });
        }

        applyFilter();
        return true;
    }
};
