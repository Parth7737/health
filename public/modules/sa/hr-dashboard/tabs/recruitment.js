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

        function getVacancyIdFromViewModal() {
            var el = document.querySelector('.hrx-recruitment-vacancy-view-root');
            if (!el) {
                return 0;
            }
            var v = parseInt(el.getAttribute('data-vacancy-id'), 10);
            return isNaN(v) ? 0 : v;
        }

        function reloadRecruitmentVacancyViewModal() {
            var vid = getVacancyIdFromViewModal();
            if (!vid || !config.showModalUrl) {
                return;
            }
            $.ajax({
                url: config.showModalUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    type: 'recruitment-vacancy-view',
                    vacancy_id: vid
                },
                success: function (res) {
                    if (res && res.status && res.html) {
                        $('#ajaxdata').html(res.html);
                    }
                }
            });
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function formatRecruitmentDetailDateTime(iso) {
            if (!iso) {
                return '—';
            }
            var d = new Date(iso);
            if (isNaN(d.getTime())) {
                return '—';
            }
            return d.toLocaleString(undefined, {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        function buildRecruitmentApplicantDetailHtml(payload) {
            var a = payload.applicant || {};
            var logs = payload.logs || [];
            var resume = a.resume_url
                ? '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Resume</span><a href="' + escapeHtml(a.resume_url) + '" target="_blank" rel="noopener">Open resume</a></div>'
                : '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Resume</span>—</div>';
            var note = a.status_note
                ? '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Latest HR note</span>' + escapeHtml(a.status_note) + '</div>'
                : '';
            var vacancy = a.vacancy_title
                ? '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Position</span>' + escapeHtml(a.vacancy_title) + '</div>'
                : '';
            var summary = ''
                + '<div class="hrx-app-detail-summary">'
                + '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Name</span><strong>' + escapeHtml(a.full_name) + '</strong></div>'
                + vacancy
                + '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Email</span>' + escapeHtml(a.email) + '</div>'
                + '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Phone</span>' + escapeHtml(a.phone || '—') + '</div>'
                + '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Applied</span>' + escapeHtml(formatRecruitmentDetailDateTime(a.applied_at)) + '</div>'
                + '<div class="hrx-app-detail-row"><span class="hrx-app-detail-label">Current status</span>' + escapeHtml(a.current_status || '—') + '</div>'
                + note
                + resume
                + '</div>';

            var timelineBody;
            if (!logs.length) {
                timelineBody = '<p class="text-muted mb-0" style="font-size:13px">No status history recorded yet.</p>';
            } else {
                timelineBody = '<ul class="hrx-app-timeline">' + logs.map(function (log) {
                    var when = formatRecruitmentDetailDateTime(log.created_at);
                    var change;
                    if (log.from_status && log.from_status !== log.to_status) {
                        change = escapeHtml(log.from_status) + ' → <strong>' + escapeHtml(log.to_status) + '</strong>';
                    } else if (log.from_status) {
                        change = '<strong>' + escapeHtml(log.to_status) + '</strong> <span style="color:#64748b;font-weight:600">(note added)</span>';
                    } else {
                        change = '<strong>' + escapeHtml(log.to_status) + '</strong> <span style="color:#64748b;font-weight:600">(submitted)</span>';
                    }
                    var noteBlock = log.note
                        ? '<div class="hrx-app-timeline-note">' + escapeHtml(log.note) + '</div>'
                        : '';
                    var byLine = log.changed_by
                        ? 'By ' + escapeHtml(log.changed_by)
                        : 'By careers / system';
                    return ''
                        + '<li class="hrx-app-timeline-item">'
                        + '<div class="hrx-app-timeline-when">' + escapeHtml(when) + '</div>'
                        + '<div class="hrx-app-timeline-change">' + change + '</div>'
                        + noteBlock
                        + '<div class="hrx-app-timeline-by">' + byLine + '</div>'
                        + '</li>';
                }).join('') + '</ul>';
            }

            return summary + '<div class="hrx-app-timeline-heading">Status history</div>' + timelineBody;
        }

        function openApplicantDetailView(trigger) {
            var $btn = $(trigger);
            var applicationId = parseInt($btn.attr('data-application-id'), 10);
            if (!applicationId || !config.recruitmentApplicationDetailUrl) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'Applicant detail URL not configured.');
                }
                return;
            }
            var url = config.recruitmentApplicationDetailUrl.replace('__ID__', encodeURIComponent(String(applicationId)));

            if (typeof loader === 'function') {
                loader();
            }
            $.ajax({
                url: url,
                type: 'GET',
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (res && res.message) || 'Unable to load applicant.');
                        }
                        return;
                    }
                    var html = buildRecruitmentApplicantDetailHtml(res);
                    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                        Swal.fire({
                            title: 'Applicant',
                            html: html,
                            icon: 'info',
                            width: 540,
                            customClass: { popup: 'hrx-recruitment-applicant-view-popup' },
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#64748b'
                        });
                    } else {
                        window.alert((res.applicant && res.applicant.full_name) ? String(res.applicant.full_name) : 'Applicant');
                    }
                },
                error: function (xhr) {
                    var msg = 'Unable to load applicant details.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('error', msg);
                    }
                },
                complete: function () {
                    if (typeof loader === 'function') {
                        loader('hide');
                    }
                }
            });
        }

        function openApplicantStatusPopup(trigger) {
            var $btn = $(trigger);
            var applicationId = parseInt($btn.attr('data-application-id'), 10);
            var applicantName = $btn.attr('data-applicant-name') || 'Applicant';
            var currentStatus = $btn.attr('data-current-status') || 'Applied';
            var existingNote = $btn.attr('data-status-note') || '';

            if (!applicationId || !config.updateRecruitmentApplicationStatusUrl) {
                return;
            }

            var statuses = ['Applied', 'Screening', 'Shortlisted', 'Interview', 'Selected', 'Rejected', 'Hired'];
            var optionsHtml = statuses.map(function (s) {
                return '<option value="' + escapeHtml(s) + '"' + (s === currentStatus ? ' selected' : '') + '>' + escapeHtml(s) + '</option>';
            }).join('');
            var html = ''
                + '<div class="hrx-swal-field"><label for="hrxSwalApplicantStatus">New status</label>'
                + '<select id="hrxSwalApplicantStatus">' + optionsHtml + '</select></div>'
                + '<div class="hrx-swal-field"><label for="hrxSwalApplicantNote">Note for this status change</label>'
                + '<textarea id="hrxSwalApplicantNote" rows="3" maxlength="1000" placeholder="What changed and why (saved in applicant history with date &amp; time)...">' + escapeHtml(existingNote) + '</textarea></div>';

            function submitStatus(payload) {
                if (typeof loader === 'function') {
                    loader();
                }
                $.ajax({
                    url: config.updateRecruitmentApplicationStatusUrl,
                    type: 'POST',
                    data: $.extend({ _token: getCsrfToken() }, payload),
                    success: function (res) {
                        if (typeof sendmsg === 'function') {
                            sendmsg((res && res.status) ? 'success' : 'error', (res && res.message) || 'Status updated.');
                        }
                        reloadRecruitmentVacancyViewModal();
                        if (table) {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Failed to update applicant status.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var first = Object.values(xhr.responseJSON.errors)[0];
                            msg = Array.isArray(first) ? first[0] : first;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', msg);
                        }
                    },
                    complete: function () {
                        if (typeof loader === 'function') {
                            loader('hide');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({
                    title: 'Update applicant status',
                    html: '<p style="margin:0 0 12px;font-size:13px;color:#5a7894">' + escapeHtml(applicantName) + '</p>' + html,
                    icon: 'question',
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save status',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#4a148c',
                    cancelButtonColor: '#64748b',
                    reverseButtons: true,
                    width: 480,
                    customClass: { popup: 'hrx-recruitment-status-popup' },
                    preConfirm: function () {
                        var st = document.getElementById('hrxSwalApplicantStatus');
                        var nt = document.getElementById('hrxSwalApplicantNote');
                        if (!st) {
                            return false;
                        }
                        return {
                            application_id: applicationId,
                            status: st.value,
                            status_note: nt ? String(nt.value || '').trim() : ''
                        };
                    }
                }).then(function (result) {
                    if (!result.isConfirmed || !result.value) {
                        return;
                    }
                    submitStatus(result.value);
                });
                return;
            }

            var nextStatus = window.prompt('New status (Applied / Screening / Shortlisted / Interview / Selected / Rejected / Hired):', currentStatus);
            if (!nextStatus) {
                return;
            }
            var note = window.prompt('Note (optional):', existingNote);
            submitStatus({
                application_id: applicationId,
                status: nextStatus.trim(),
                status_note: note ? String(note).trim() : ''
            });
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
                    dialog.removeClass('modal-xl modal-lg modal-sm hrx-leave-modal-dialog hrx-recruitment-modal-dialog-lg').addClass('hrx-recruitment-modal-dialog');
                    if (type === 'recruitment-vacancy-view') {
                        dialog.addClass('hrx-recruitment-modal-dialog-lg');
                    }
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

        $(document).off('click.hrxRecruitmentApplicantStatus').on('click.hrxRecruitmentApplicantStatus', '.hrx-recruitment-applicant-status-btn', function () {
            openApplicantStatusPopup(this);
        });

        $(document).off('click.hrxRecruitmentApplicantDetail').on('click.hrxRecruitmentApplicantDetail', '.hrx-recruitment-applicant-detail-btn', function () {
            openApplicantDetailView(this);
        });

        bindFilters();
        initTable();
        return true;
    }
};
