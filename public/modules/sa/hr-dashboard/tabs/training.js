window.HRDashboardTabs = window.HRDashboardTabs || {};
window.HRDashboardTabs.training = {
    init: function () {
        var panel = document.getElementById('hrx-panel-training');
        if (!panel || typeof $ === 'undefined') {
            return;
        }

        var config = window.HRDashboardConfig || {};
        var tableEl = document.getElementById('hrxTrainingTable');
        var searchInput = document.getElementById('hrxTrainingSearch');
        var statusSelect = document.getElementById('hrxTrainingStatus');
        var scheduleBtn = document.getElementById('hrxTrainingSchedule');
        var exportBtn = document.getElementById('hrxTrainingExport');
        var table = null;

        function getCsrfToken() {
            if (window.Laravel && window.Laravel.csrfToken) {
                return window.Laravel.csrfToken;
            }
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function getTrainingProgramIdFromViewModal() {
            var el = document.querySelector('.hrx-training-program-view-root');
            if (!el) {
                return 0;
            }
            var v = parseInt(el.getAttribute('data-training-id'), 10);
            return isNaN(v) ? 0 : v;
        }

        function reloadTrainingProgramViewModal() {
            var tid = getTrainingProgramIdFromViewModal();
            if (!tid || !config.showModalUrl) {
                return;
            }
            $.ajax({
                url: config.showModalUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    type: 'training-program-view',
                    training_program_id: tid
                },
                success: function (res) {
                    if (res && res.status && res.html) {
                        $('#ajaxdata').html(res.html);
                    }
                }
            });
        }

        function openTrainingModal(type, trainingProgramId) {
            if (!config.showModalUrl) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'Modal URL not configured.');
                }
                return;
            }

            if (typeof loader === 'function') {
                loader();
            }
            $.ajax({
                url: config.showModalUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    type: type,
                    training_program_id: trainingProgramId || ''
                },
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (res && res.message) || 'Unable to load modal.');
                        }
                        return;
                    }
                    var dialog = $('.add-datamodal .modal-dialog');
                    var $content = $('.add-datamodal .modal-content');
                    dialog.removeClass('modal-xl modal-lg modal-sm hrx-leave-modal-dialog hrx-recruitment-modal-dialog-lg hrx-training-modal-dialog hrx-training-modal-xl').addClass('hrx-recruitment-modal-dialog');
                    if (type === 'training-program-form' || type === 'training-program-view') {
                        dialog.addClass('hrx-training-modal-xl');
                    } else if (type === 'recruitment-vacancy-view') {
                        dialog.addClass('hrx-recruitment-modal-dialog-lg');
                    }
                    $content.removeClass('hrx-leave-modal-content').addClass('hrx-recruitment-modal-content hrx-training-modal-content');
                    $('#ajaxdata').html(res.html || '');
                    $('.add-datamodal').modal('show');
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to load modal.';
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

        function initTable() {
            if (!tableEl || !config.trainingProgramsDataUrl || !$.fn.DataTable) {
                return;
            }
            if ($.fn.DataTable.isDataTable(tableEl)) {
                $(tableEl).DataTable().clear().destroy(true);
            }

            table = $(tableEl).DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
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
                        titleAttr: 'Reload',
                        action: function (e, dt) { dt.ajax.reload(); }
                    },
                    { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
                    { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'CSV' },
                    { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Excel' },
                    { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'PDF' },
                    { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print' },
                    { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Columns' }
                ],
                ajax: {
                    url: config.trainingProgramsDataUrl,
                    type: 'GET',
                    data: function (d) {
                        d.search_custom = searchInput ? searchInput.value.trim() : '';
                        d.status_filter = statusSelect ? statusSelect.value.trim() : '';
                    }
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'title', name: 'title', orderable: false, searchable: false },
                    { data: 'category', name: 'category', orderable: false, searchable: false },
                    { data: 'date', name: 'date', orderable: false, searchable: false },
                    { data: 'trainer', name: 'trainer', orderable: false, searchable: false },
                    { data: 'participants', name: 'participants', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
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
                        if (table) {
                            table.ajax.reload();
                        }
                    }, 350);
                });
            }
            if (statusSelect) {
                statusSelect.addEventListener('change', function () {
                    if (table) {
                        table.ajax.reload();
                    }
                });
            }
        }

        if (scheduleBtn) {
            scheduleBtn.addEventListener('click', function () {
                openTrainingModal('training-program-form', 0);
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function () {
                if (table && typeof table.button === 'function') {
                    table.button('.buttons-excel').trigger();
                }
            });
        }

        $(document).off('click.hrxTrainingView').on('click.hrxTrainingView', '.hrx-training-view', function () {
            var tid = parseInt($(this).attr('data-training-id'), 10);
            if (tid) {
                openTrainingModal('training-program-view', tid);
            }
        });

        $(document).off('click.hrxTrainingEdit').on('click.hrxTrainingEdit', '.hrx-training-edit-btn', function () {
            var tid = parseInt($(this).attr('data-training-id'), 10);
            if (tid) {
                openTrainingModal('training-program-form', tid);
            }
        });

        $(document).off('submit.hrxTrainingProgram').on('submit.hrxTrainingProgram', '#hrxTrainingProgramForm', function (e) {
            e.preventDefault();
            if (!config.storeTrainingProgramUrl) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'Save URL missing.');
                }
                return;
            }
            var formData = $(this).serialize();
            if (typeof loader === 'function') {
                loader();
            }
            $.ajax({
                url: config.storeTrainingProgramUrl,
                type: 'POST',
                data: formData,
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (res && res.message) || 'Unable to save.');
                        }
                        return;
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('success', res.message || 'Saved.');
                    }
                    $('.add-datamodal').modal('hide');
                    if (table) {
                        table.ajax.reload();
                    }
                },
                error: function (xhr) {
                    var msg = 'Unable to save programme.';
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
        });

        $(document).off('click.hrxTrainingAddParticipant').on('click.hrxTrainingAddParticipant', '.hrx-training-add-participant-btn', function () {
            if (!config.addTrainingParticipantUrl) {
                return;
            }
            var tid = getTrainingProgramIdFromViewModal();
            var sid = parseInt($('#hrxTrainingAddStaff').val(), 10);
            if (!tid || !sid) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'Select a staff member to add.');
                }
                return;
            }
            if (typeof loader === 'function') {
                loader();
            }
            $.ajax({
                url: config.addTrainingParticipantUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    hr_training_program_id: tid,
                    staff_id: sid
                },
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (res && res.message) || 'Unable to add participant.');
                        }
                        return;
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('success', res.message || 'Participant added.');
                    }
                    reloadTrainingProgramViewModal();
                    if (table) {
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    var msg = 'Unable to add participant.';
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
        });

        $(document).off('click.hrxTrainingRemoveParticipant').on('click.hrxTrainingRemoveParticipant', '.hrx-training-remove-participant-btn', function () {
            if (!config.removeTrainingParticipantUrl) {
                return;
            }
            var pid = parseInt($(this).attr('data-participant-id'), 10);
            if (!pid) {
                return;
            }
            if (!window.confirm('Remove this participant from the programme?')) {
                return;
            }
            if (typeof loader === 'function') {
                loader();
            }
            $.ajax({
                url: config.removeTrainingParticipantUrl,
                type: 'POST',
                data: { _token: getCsrfToken(), participant_id: pid },
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (res && res.message) || 'Unable to remove.');
                        }
                        return;
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('success', res.message || 'Removed.');
                    }
                    reloadTrainingProgramViewModal();
                    if (table) {
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to remove participant.';
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
        });

        $(document).off('click.hrxTrainingGenerateCert').on('click.hrxTrainingGenerateCert', '.hrx-training-generate-cert-btn', function () {
            if (!config.generateTrainingParticipantCertificateUrl) {
                return;
            }
            var pid = parseInt($(this).attr('data-participant-id'), 10);
            var regen = String($(this).attr('data-regenerate') || '0') === '1';
            if (!pid) {
                return;
            }
            if (regen && !window.confirm('Replace the existing certificate PDF for this participant?')) {
                return;
            }
            if (typeof loader === 'function') {
                loader();
            }
            $.ajax({
                url: config.generateTrainingParticipantCertificateUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    participant_id: pid,
                    regenerate: regen ? 1 : 0
                },
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (res && res.message) || 'Unable to generate certificate.');
                        }
                        return;
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('success', (res && res.message) || 'Done.');
                    }
                    reloadTrainingProgramViewModal();
                    if (table) {
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to generate certificate.';
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
        });

        $(document).off('click.hrxTrainingUpdateStatus').on('click.hrxTrainingUpdateStatus', '.hrx-training-update-status-btn', function () {
            if (!config.updateTrainingProgramStatusUrl) {
                return;
            }
            var tid = getTrainingProgramIdFromViewModal();
            var st = $('#hrxTrainingStatusNext').val();
            var note = $('#hrxTrainingStatusNote').val();
            if (!tid || !st) {
                if (typeof sendmsg === 'function') {
                    sendmsg('error', 'Select a new status.');
                }
                return;
            }
            if (typeof loader === 'function') {
                loader();
            }
            $.ajax({
                url: config.updateTrainingProgramStatusUrl,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    hr_training_program_id: tid,
                    status: st,
                    note: note ? String(note).trim() : ''
                },
                success: function (res) {
                    if (!res || !res.status) {
                        if (typeof sendmsg === 'function') {
                            sendmsg('error', (res && res.message) || 'Unable to update status.');
                        }
                        return;
                    }
                    var extra = '';
                    if (res.certificates_issued > 0) {
                        extra = ' ' + res.certificates_issued + ' certificate(s) generated.';
                    }
                    if (typeof sendmsg === 'function') {
                        sendmsg('success', (res.message || 'Updated.') + extra);
                    }
                    reloadTrainingProgramViewModal();
                    if (table) {
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to update status.';
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
        });

        bindFilters();
        initTable();
        return true;
    }
};
