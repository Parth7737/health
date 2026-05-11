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
            registerPerPage: 10
        };

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

        function renderRegisterTable(days, rows) {
            if (!registerTableEl || !$.fn.DataTable) return;

            var theadHtml = '<tr><th>Name</th>';
            (days || []).forEach(function (day) {
                theadHtml += '<th>' + escapeHtml(day.label) + '</th>';
            });
            theadHtml += '</tr>';

            var tbodyHtml = '';
            if (!rows || rows.length === 0) {
                tbodyHtml = '<tr><td colspan="' + ((days || []).length + 1) + '" style="text-align:center;padding:14px">No staff found for selected filters/week</td></tr>';
            } else {
                rows.forEach(function (row) {
                    tbodyHtml += '<tr>';
                    tbodyHtml += '<td>' + escapeHtml(row.name) + '</td>';
                    (row.cells || []).forEach(function (cell) {
                        tbodyHtml += '<td><span class="att-' + cell['class'] + '">' + escapeHtml(cell.code) + '</span></td>';
                    });
                    tbodyHtml += '</tr>';
                });
            }

            $(registerTableEl).find('thead').html(theadHtml);
            $(registerTableEl).find('tbody').html(tbodyHtml);

            if ($.fn.DataTable.isDataTable(registerTableEl)) {
                $(registerTableEl).DataTable().destroy();
            }

            $(registerTableEl).DataTable({
                searching: false,
                lengthChange: false,
                pageLength: state.registerPerPage,
                ordering: false,
                autoWidth: false,
                dom: 't<"hrx-dt-footer"ip>'
            });
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
