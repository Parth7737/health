window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.attendance = {
    init: function () {
        var panel = document.getElementById('hrx-panel-attendance');
        if (!panel) return;

        var config = window.HRDashboardConfig || {};
        var endpoint = config.attendanceRegisterDataUrl || '';
        var dailyEndpoint = config.attendanceDailyDataUrl || '';
        var exportUrl = config.attendanceExportUrl || '';
        var showModalUrl = config.showModalUrl || '';

        var weekInput = document.getElementById('hrxWeekPicker');
        var deptSelect = document.getElementById('hrxDeptFilter');
        var refreshBtn = document.getElementById('hrxAttendanceRefresh');
        var exportBtn = document.getElementById('hrxAttendanceExport');
        var prevWeekBtn = document.getElementById('hrxWeekPrev');
        var nextWeekBtn = document.getElementById('hrxWeekNext');
        var weekLabel = document.getElementById('hrxWeekLabel');
        var registerTableEl = document.getElementById('hrxRegisterTable');
        var loading = document.getElementById('hrxAttGridLoading');
        var dailyTableEl = document.getElementById('hrxDailyAttendanceTable');
        var dailySearchInput = document.getElementById('hrxDailySearch');
        var dailyDeptSelect = document.getElementById('hrxDailyDeptFilter');
        var dailyDateInput = document.getElementById('hrxDailyDate');

        var todayStr = toDateInput(new Date());

        if (dailyDateInput) {
            dailyDateInput.max = todayStr;
            if (!dailyDateInput.value || dailyDateInput.value > todayStr) {
                dailyDateInput.value = todayStr;
            }
        }

        var state = {
            weekStart: panel.getAttribute('data-week-start') || toDateInput(new Date()),
            registerPerPage: 25,
            registerDays: [],
            registerAllRows: [],
            registerPage: 1
        };
        var registerWrap = document.getElementById('hrxRegisterWrap');

        function toDateInput(date) {
            var d = new Date(date);
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            return d.getFullYear() + '-' + m + '-' + day;
        }

        function getCsrfToken() {
            var tokenMeta = document.querySelector('meta[name="csrf-token"]');
            return tokenMeta ? tokenMeta.getAttribute('content') : '';
        }

        function getMonday(date) {
            var d = new Date(date);
            var day = d.getDay() || 7;
            d.setDate(d.getDate() - day + 1);
            return d;
        }

        function addDays(dateStr, days) {
            var d = new Date(dateStr);
            d.setDate(d.getDate() + days);
            return toDateInput(d);
        }

        function dateToWeekValue(date) {
            var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            var dayNum = d.getUTCDay() || 7;
            d.setUTCDate(d.getUTCDate() + 4 - dayNum);
            var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            var weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            return d.getUTCFullYear() + '-W' + String(weekNo).padStart(2, '0');
        }

        function weekValueToDate(weekValue) {
            if (!weekValue || weekValue.indexOf('-W') < 0) return null;
            var parts = weekValue.split('-W');
            var year = parseInt(parts[0], 10);
            var week = parseInt(parts[1], 10);
            if (!year || !week) return null;

            var firstJan = new Date(year, 0, 1);
            var dayOfWeek = firstJan.getDay() || 7;
            var mondayOffset = (dayOfWeek <= 4 ? 1 : 8) - dayOfWeek;
            var firstMonday = new Date(year, 0, 1 + mondayOffset);
            firstMonday.setDate(firstMonday.getDate() + ((week - 1) * 7));
            return firstMonday;
        }

        function syncInputsFromWeek() {
            var monday = new Date(state.weekStart);
            if (weekInput) weekInput.value = dateToWeekValue(monday);
        }

        function registerBadgeClass(cellClass) {
            var k = String(cellClass || 'a').toLowerCase();
            if (k === 'p') {
                return 'hrx-att-badge hrx-att-badge--present';
            }
            if (k === 'h') {
                return 'hrx-att-badge hrx-att-badge--holiday';
            }
            if (k === 'l') {
                return 'hrx-att-badge hrx-att-badge--leave';
            }
            return 'hrx-att-badge hrx-att-badge--absent';
        }

        function destroyRegisterDataTableIfAny() {
            var t = document.getElementById('hrxRegisterTable');
            if (!t || typeof $ === 'undefined' || !$.fn.DataTable || !$.fn.DataTable.isDataTable(t)) {
                return;
            }
            try {
                $(t).DataTable().destroy();
            } catch (e) { /* ignore */ }
            $(t).removeClass('dataTable no-footer nowrap compact stripe hover row-border order-column');
            var $w = $(t).closest('.dataTables_wrapper');
            if ($w.length && registerWrap && t.parentNode !== registerWrap) {
                registerWrap.insertBefore(t, registerWrap.firstChild);
                $w.remove();
            }
        }

        function buildRegisterRowHtml(row, days) {
            var n = (days && days.length) ? days.length : 0;
            var cells = row.cells || [];
            var html = '<tr><td>' + escapeHtml(row.name != null ? String(row.name) : '') + '</td>';
            var i;
            for (i = 0; i < n; i++) {
                var cell = cells[i] || { code: '—', class: 'a', title: '' };
                var titleAttr = cell.title ? (' title="' + escapeHtml(cell.title) + '"') : '';
                var rawCode = cell.code != null ? String(cell.code).trim() : '';
                var displayCode = rawCode ? rawCode.toUpperCase() : '—';
                var badgeClass = registerBadgeClass(cell['class']);
                html += '<td><span class="' + badgeClass + '"' + titleAttr + '>' + escapeHtml(displayCode) + '</span></td>';
            }
            html += '</tr>';
            return html;
        }

        function paintRegisterTable() {
            destroyRegisterDataTableIfAny();
            var tbl = document.getElementById('hrxRegisterTable');
            if (!tbl) {
                return;
            }
            var days = state.registerDays || [];
            var all = state.registerAllRows || [];
            var per = Math.max(1, parseInt(state.registerPerPage, 10) || 25);
            var maxPage = all.length ? Math.ceil(all.length / per) : 1;
            if (state.registerPage < 1) {
                state.registerPage = 1;
            }
            if (state.registerPage > maxPage) {
                state.registerPage = maxPage;
            }
            var page = state.registerPage;
            var startIdx = (page - 1) * per;
            var slice = all.slice(startIdx, startIdx + per);

            var theadHtml = '<tr><th>Name</th>';
            days.forEach(function (day) {
                theadHtml += '<th>' + escapeHtml(day.label) + '</th>';
            });
            theadHtml += '</tr>';

            var tbodyHtml = '';
            if (!all.length) {
                var colspan = Math.max(1, days.length + 1);
                tbodyHtml = '<tr><td colspan="' + colspan + '" style="text-align:center;padding:14px">No staff found for selected filters/week</td></tr>';
            } else {
                slice.forEach(function (row) {
                    tbodyHtml += buildRegisterRowHtml(row, days);
                });
            }

            $(tbl).find('thead').html(theadHtml);
            $(tbl).find('tbody').html(tbodyHtml);

            var pager = document.getElementById('hrxRegisterPager');
            var info = document.getElementById('hrxRegisterPageInfo');
            var prevBtn = document.getElementById('hrxRegisterPrev');
            var nextBtn = document.getElementById('hrxRegisterNext');
            if (pager && info && prevBtn && nextBtn) {
                if (!all.length) {
                    pager.style.display = 'none';
                } else {
                    pager.style.display = 'flex';
                    var from = all.length ? startIdx + 1 : 0;
                    var to = Math.min(startIdx + slice.length, all.length);
                    info.textContent = 'Showing ' + from + ' to ' + to + ' of ' + all.length + ' staff';
                    prevBtn.disabled = page <= 1;
                    nextBtn.disabled = page >= maxPage;
                }
            }
        }

        function renderRegisterTable(days, rows) {
            state.registerDays = days || [];
            state.registerAllRows = rows || [];
            state.registerPage = 1;
            paintRegisterTable();
        }

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function loadRegister(resetPage) {
            if (!endpoint) return;
            if (loading) loading.style.display = 'block';

            $.ajax({
                url: endpoint,
                type: 'GET',
                data: {
                    week_start: state.weekStart,
                    department: deptSelect ? deptSelect.value : '',
                    page: 1,
                    per_page: 5000
                },
                success: function (res) {
                    renderRegisterTable(res.days || [], res.rows || []);
                    state.weekStart = res.week_start || state.weekStart;
                    if (weekLabel) weekLabel.textContent = res.week_label ? ' - ' + res.week_label : '';
                    syncInputsFromWeek();
                },
                error: function () {
                    renderRegisterTable([], []);
                    if (typeof sendmsg === 'function') sendmsg('error', 'Attendance register load failed.');
                },
                complete: function () {
                    if (loading) loading.style.display = 'none';
                }
            });
        }

        function initDailyAttendanceTable() {
            if (!dailyTableEl || !dailyEndpoint || !$.fn.DataTable) return;

            if ($.fn.DataTable.isDataTable(dailyTableEl)) {
                $(dailyTableEl).DataTable().destroy();
            }

            $(dailyTableEl).DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: false,
                dom: 't<"hrx-dt-footer"ip>',
                pageLength: 10,
                autoWidth: false,
                order: [[0, 'asc']],
                ajax: {
                    url: dailyEndpoint,
                    type: 'GET',
                    data: function (d) {
                        d.department = dailyDeptSelect ? dailyDeptSelect.value : '';
                        d.attendance_date = dailyDateInput ? dailyDateInput.value : '';
                    }
                },
                columns: [
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'designation', name: 'designation', orderable: false, searchable: true },
                    { data: 'shift', name: 'shift', orderable: false, searchable: false },
                    { data: 'in_time', name: 'in_time', orderable: false, searchable: false },
                    { data: 'out_time', name: 'out_time', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'ot_hrs', name: 'ot_hrs', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    emptyTable: 'No staff found for today.'
                },
                drawCallback: function () {
                    var pager = $(dailyTableEl).closest('.dataTables_wrapper').find('.dataTables_paginate');
                    pager.addClass('hrx-att-dt-pager');
                }
            });
        }

        if (weekInput) {
            weekInput.addEventListener('change', function () {
                var monday = weekValueToDate(weekInput.value);
                if (!monday) return;
                state.weekStart = toDateInput(getMonday(monday));
                loadRegister(true);
            });
        }

        if (deptSelect) {
            deptSelect.addEventListener('change', function () {
                loadRegister(true);
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                state.weekStart = toDateInput(getMonday(new Date()));
                if (deptSelect) deptSelect.value = '';
                loadRegister(true);
                if ($.fn.DataTable && $.fn.DataTable.isDataTable(dailyTableEl)) {
                    if (dailyDeptSelect) dailyDeptSelect.value = '';
                    if (dailyDateInput) dailyDateInput.value = todayStr;
                    if (dailySearchInput) dailySearchInput.value = '';
                    $(dailyTableEl).DataTable().search('');
                    $(dailyTableEl).DataTable().ajax.reload(null, true);
                }
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (!exportUrl) {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Export URL not configured.');
                    return;
                }

                var params = new URLSearchParams();
                params.append('week_start', state.weekStart);
                if (deptSelect && deptSelect.value !== '') {
                    params.append('department', deptSelect.value);
                }

                var downloadUrl = exportUrl + '?' + params.toString();
                
                var link = document.createElement('a');
                link.href = downloadUrl;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                if (typeof sendmsg === 'function') sendmsg('success', 'Attendance report exported for the week.');
            });
        }

        if (prevWeekBtn) {
            prevWeekBtn.addEventListener('click', function () {
                state.weekStart = addDays(state.weekStart, -7);
                loadRegister(true);
            });
        }

        if (nextWeekBtn) {
            nextWeekBtn.addEventListener('click', function () {
                state.weekStart = addDays(state.weekStart, 7);
                loadRegister(true);
            });
        }

        destroyRegisterDataTableIfAny();

        if (typeof $ !== 'undefined' && panel) {
            $(panel).off('click.hrxRegPager').on('click.hrxRegPager', '#hrxRegisterPrev', function (e) {
                e.preventDefault();
                if (state.registerPage > 1) {
                    state.registerPage -= 1;
                    paintRegisterTable();
                }
            });
            $(panel).on('click.hrxRegPager', '#hrxRegisterNext', function (e) {
                e.preventDefault();
                var all = state.registerAllRows || [];
                var per = Math.max(1, parseInt(state.registerPerPage, 10) || 25);
                var maxPage = all.length ? Math.ceil(all.length / per) : 1;
                if (state.registerPage < maxPage) {
                    state.registerPage += 1;
                    paintRegisterTable();
                }
            });
        }

        syncInputsFromWeek();
        loadRegister(true);
        initDailyAttendanceTable();

        if (dailySearchInput) {
            dailySearchInput.addEventListener('input', function () {
                if (!$.fn.DataTable || !$.fn.DataTable.isDataTable(dailyTableEl)) return;
                $(dailyTableEl).DataTable().search(dailySearchInput.value || '').draw();
            });
        }

        if (dailyDeptSelect) {
            dailyDeptSelect.addEventListener('change', function () {
                if (!$.fn.DataTable || !$.fn.DataTable.isDataTable(dailyTableEl)) return;
                $(dailyTableEl).DataTable().ajax.reload(null, true);
            });
        }

        if (dailyDateInput) {
            dailyDateInput.addEventListener('change', function () {
                if (dailyDateInput.value > todayStr) {
                    dailyDateInput.value = todayStr;
                }
                if (!$.fn.DataTable || !$.fn.DataTable.isDataTable(dailyTableEl)) return;
                $(dailyTableEl).DataTable().ajax.reload(null, true);
            });
        }

        $(document).off('click.hrxDailyAttEdit').on('click.hrxDailyAttEdit', '.hrx-daily-att-edit', function () {
            var staffId = this.getAttribute('data-staff-id') || '';
            var attendanceDate = this.getAttribute('data-attendance-date') || (dailyDateInput ? dailyDateInput.value : todayStr);

            if (!showModalUrl) {
                if (typeof sendmsg === 'function') sendmsg('error', 'Modal URL not configured.');
                return;
            }

            if (typeof loader === 'function') loader();

            $.ajax({
                url: showModalUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    type: 'mark-attendance',
                    staff_id: staffId,
                    attendance_date: attendanceDate
                },
                success: function (response) {
                    if (!response || !response.status) {
                        if (typeof sendmsg === 'function') sendmsg('error', (response && response.message) || 'Modal could not be loaded.');
                        return;
                    }
                    $('#ajaxdata').html(response.html || '');
                    $('.add-datamodal').modal('show');
                },
                error: function () {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Modal could not be loaded.');
                },
                complete: function () {
                    if (typeof loader === 'function') loader('hide');
                }
            });
        });
    }
};
