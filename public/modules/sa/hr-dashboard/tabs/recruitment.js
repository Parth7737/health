window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.recruitment = {
    init: function () {
        var panel = document.getElementById('hrx-panel-recruitment');
        if (!panel || typeof $ === 'undefined') {
            return;
        }

        var config = window.HRDashboardConfig || {};
        var tableEl = document.getElementById('hrxRecruitmentTable');
        var searchInput = document.getElementById('hrxRecruitmentSearch');
        var statusSelect = document.getElementById('hrxRecruitmentStatus');
        var postBtn = document.getElementById('hrxRecruitmentPost');
        var exportBtn = document.getElementById('hrxRecruitmentExport');
        var table = null;

        function getCsrfToken() {
            if (window.Laravel && window.Laravel.csrfToken) {
                return window.Laravel.csrfToken;
            }
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function openRecruitmentModal(type, vacancyId) {
            if (!config.showModalUrl) {
                if (typeof sendmsg === 'function') sendmsg('error', 'Modal URL not configured.');
                return;
            }

            if (typeof loader === 'function') loader();
            $.ajax({
                url: config.showModalUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    type: type,
                    vacancy_id: vacancyId || ''
                },
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') sendmsg('error', (res && res.message) || 'Unable to load modal.');
                        return;
                    }
                    var dialog = $('.add-datamodal .modal-dialog');
                    var $content = $('.add-datamodal .modal-content');
                    dialog.removeClass('modal-xl modal-lg modal-sm hrx-leave-modal-dialog').addClass('hrx-recruitment-modal-dialog');
                    $content.removeClass('hrx-leave-modal-content').addClass('hrx-recruitment-modal-content');
                    $('#ajaxdata').html(res.html || '');
                    $('.add-datamodal').modal('show');
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to load modal.';
                    if (typeof sendmsg === 'function') sendmsg('error', msg);
                },
                complete: function () {
                    if (typeof loader === 'function') loader('hide');
                }
            });
        }

        function initTable() {
            if (!tableEl || !config.recruitmentVacanciesDataUrl || !$.fn.DataTable) {
                return;
            }
            if ($.fn.DataTable.isDataTable(tableEl)) {
                $(tableEl).DataTable().clear().destroy(true);
            }

            table = $(tableEl).DataTable({
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
                        action: function (e, dt) { dt.ajax.reload(); }
                    },
                    { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
                    { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'Export as CSV' },
                    { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Export as Excel' },
                    { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'Export as PDF' },
                    { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print' },
                    { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Column Visibility' }
                ],
                ajax: {
                    url: config.recruitmentVacanciesDataUrl,
                    type: 'GET',
                    data: function (d) {
                        d.search_custom = searchInput ? searchInput.value.trim() : '';
                        d.status_filter = statusSelect ? statusSelect.value.trim() : '';
                    }
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'title', name: 'title', orderable: false, searchable: false },
                    { data: 'department', name: 'department', orderable: false, searchable: false },
                    { data: 'required', name: 'required', orderable: false, searchable: false },
                    { data: 'applicants', name: 'applicants', orderable: false, searchable: false },
                    { data: 'shortlisted', name: 'shortlisted', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'open_range', name: 'open_range', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        }

        function bindFilters() {
            if (searchInput) {
                var timer = null;
                searchInput.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        if (table) table.ajax.reload();
                    }, 350);
                });
            }
            if (statusSelect) {
                statusSelect.addEventListener('change', function () {
                    if (table) table.ajax.reload();
                });
            }
        }

        if (postBtn) {
            postBtn.addEventListener('click', function () {
                openRecruitmentModal('recruitment-vacancy-form');
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (table && typeof table.button === 'function') {
                    table.button('.buttons-excel').trigger();
                }
            });
        }

        $(document).off('click.hrxRecruitmentView').on('click.hrxRecruitmentView', '.hrx-recruitment-view', function () {
            openRecruitmentModal('recruitment-vacancy-view', $(this).data('vacancy-id'));
        });

        $(document).off('click.hrxRecruitmentEdit').on('click.hrxRecruitmentEdit', '.hrx-recruitment-edit', function () {
            openRecruitmentModal('recruitment-vacancy-form', $(this).data('vacancy-id'));
        });

        $(document).off('submit.hrxRecruitmentVacancy').on('submit.hrxRecruitmentVacancy', '#hrxRecruitmentVacancyForm', function (e) {
            e.preventDefault();
            if (!config.storeRecruitmentVacancyUrl) {
                if (typeof sendmsg === 'function') sendmsg('error', 'Vacancy save URL missing.');
                return;
            }
            var formData = $(this).serialize();
            if (typeof loader === 'function') loader();
            $.ajax({
                url: config.storeRecruitmentVacancyUrl,
                type: 'POST',
                data: formData,
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') sendmsg('error', (res && res.message) || 'Unable to save vacancy.');
                        return;
                    }
                    if (typeof sendmsg === 'function') sendmsg('success', res.message || 'Vacancy saved.');
                    $('.add-datamodal').modal('hide');
                    if (table) table.ajax.reload();
                },
                error: function (xhr) {
                    var msg = 'Unable to save vacancy.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var firstErr = Object.values(xhr.responseJSON.errors)[0];
                        msg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof sendmsg === 'function') sendmsg('error', msg);
                },
                complete: function () {
                    if (typeof loader === 'function') loader('hide');
                }
            });
        });

        $(document).off('submit.hrxRecruitmentAppStatus').on('submit.hrxRecruitmentAppStatus', '.hrxRecruitmentAppStatusForm', function (e) {
            e.preventDefault();
            if (!config.updateRecruitmentApplicationStatusUrl) {
                return;
            }
            var payload = $(this).serialize();
            $.ajax({
                url: config.updateRecruitmentApplicationStatusUrl,
                type: 'POST',
                data: payload,
                success: function (res) {
                    if (typeof sendmsg === 'function') {
                        sendmsg((res && res.status) ? 'success' : 'error', (res && res.message) || 'Status update response received.');
                    }
                    if (table) table.ajax.reload();
                },
                error: function () {
                    if (typeof sendmsg === 'function') sendmsg('error', 'Failed to update applicant status.');
                }
            });
        });

        bindFilters();
        initTable();
        return true;
    }
};
