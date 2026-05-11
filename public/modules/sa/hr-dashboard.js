(function ($) {
    'use strict';

    const config = window.HRDashboardConfig || {};
    const tabContainerSelector = '#hrxTabContainer';

    window.HRDashboardTabs = window.HRDashboardTabs || {};

    function getCsrfToken() {
        if (window.Laravel && window.Laravel.csrfToken) {
            return window.Laravel.csrfToken;
        }
        return $('meta[name="csrf-token"]').attr('content');
    }

    function loadTab(tab) {
        const urlTemplate = config.tabUrl || '';
        const url = urlTemplate.replace('__TAB__', encodeURIComponent(tab));

        if (!url) {
            return;
        }

        $(tabContainerSelector).html('<div class="hrx-loading">Loading ' + tab + '...</div>');
        loader();

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $(tabContainerSelector).html(response.html || '<div class="hrx-loading">No content returned.</div>');
                if (window.HRDashboardTabs[tab] && typeof window.HRDashboardTabs[tab].init === 'function') {
                    window.HRDashboardTabs[tab].init();
                }
            },
            error: function () {
                $(tabContainerSelector).html('<div class="hrx-loading">Unable to load this tab right now.</div>');
            },
            complete: function () {
                loader('hide');
            }
        });
    }

    window.HRDashboard = window.HRDashboard || {};
    window.HRDashboard.state = window.HRDashboard.state || { tabFilters: {} };
    window.HRDashboard.loadTab = loadTab;
    window.HRDashboard.openTab = function (tab) {
        $('.hrx-tab-btn').removeClass('active');
        $('.hrx-tab-btn[data-tab="' + tab + '"]').addClass('active');
        loadTab(tab);
    };
    window.HRDashboard.setTabFilter = function (tab, key, value) {
        if (!window.HRDashboard.state.tabFilters[tab]) {
            window.HRDashboard.state.tabFilters[tab] = {};
        }
        window.HRDashboard.state.tabFilters[tab][key] = value;
    };
    window.HRDashboard.consumeTabFilters = function (tab) {
        const filters = window.HRDashboard.state.tabFilters[tab] || {};
        delete window.HRDashboard.state.tabFilters[tab];
        return filters;
    };

    function openModal(type) {
        if (type === 'add-staff') {
            openStaffForm('');
            return;
        }

        var dialog = $('.add-datamodal .modal-dialog');
        dialog.removeClass('modal-xl modal-lg modal-sm hrx-leave-modal-dialog').addClass('modal-xl');

        loader();
        $.ajax({
            url: config.showModalUrl,
            type: 'POST',
            data: {
                _token: getCsrfToken(),
                type: type
            },
            success: function (response) {
                if (!response.status) {
                    sendmsg('error', response.message || 'Modal could not be loaded.');
                    return;
                }
                $('#ajaxdata').html(response.html);

                if (type === 'leave-request-ajax') {
                    dialog.removeClass('modal-xl').addClass('hrx-leave-modal-dialog');
                    $('.add-datamodal .modal-content').addClass('hrx-leave-modal-content');

                    if ($.fn.select2) {
                        $('.add-datamodal .select2-modal').each(function () {
                            var $field = $(this);
                            if ($field.hasClass('select2-hidden-accessible')) {
                                $field.select2('destroy');
                            }
                            $field.select2({
                                width: '100%',
                                dropdownParent: $('.add-datamodal'),
                                dropdownCssClass: 'hrx-leave-select-dropdown'
                            });
                        });
                    }
                } else {
                    dialog.removeClass('hrx-leave-modal-dialog').addClass('modal-xl');
                    $('.add-datamodal .modal-content').removeClass('hrx-leave-modal-content');
                }

                $('.add-datamodal').modal('show');
            },
            error: function () {
                sendmsg('error', 'Modal could not be loaded.');
            },
            complete: function () {
                loader('hide');
            }
        });
    }

    function openStaffForm(id) {
        loader();
        $.ajax({
            url: config.staffShowFormUrl,
            type: 'POST',
            data: {
                id: id || '',
                _token: getCsrfToken()
            },
            success: function (response) {
                $('#ajaxdata').html(response);
                $('.add-datamodal').modal('show');

                if (typeof flatpickr === 'function') {
                    flatpickr('input[name="date_of_joining"]', { dateFormat: 'd-m-Y', maxDate: 'today' });
                    flatpickr('input[name="date_of_birth"]', { dateFormat: 'd-m-Y', maxDate: 'today' });
                }

                if ($.fn.select2) {
                    $('.select2-modal').select2({
                        dropdownParent: $('.add-datamodal')
                    });
                }
            },
            error: function () {
                sendmsg('error', 'Error loading staff form.');
            },
            complete: function () {
                loader('hide');
            }
        });
    }

    function openStaffView(id) {
        const viewUrlTemplate = config.staffViewUrl || '';
        if (!viewUrlTemplate) {
            sendmsg('error', 'Staff view route is not configured.');
            return;
        }

        const url = viewUrlTemplate.replace('__STAFF__', encodeURIComponent(id || ''));
        loader();

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (!response || !response.status) {
                    sendmsg('error', (response && response.message) || 'Unable to load staff profile.');
                    return;
                }

                $('#ajaxdata').html(response.html || '');
                $('.add-datamodal').modal('show');
            },
            error: function () {
                sendmsg('error', 'Unable to load staff profile.');
            },
            complete: function () {
                loader('hide');
            }
        });
    }

    function deleteStaff(id) {
        const destroyUrlTemplate = config.staffDestroyUrl || '';
        if (!destroyUrlTemplate) {
            sendmsg('error', 'Staff delete route is not configured.');
            return;
        }

        const url = destroyUrlTemplate.replace('__STAFF__', encodeURIComponent(id || ''));

        // SweetAlert2 (Swal.fire) takes priority, then SweetAlert1, then native confirm
        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    proceedDelete();
                }
            });
            return;
        }

        if (!confirm('Are you sure you want to delete this staff member?')) {
            return;
        }
        proceedDelete();

        function proceedDelete() {
            $('.add-datamodal').modal('hide');
            loader();

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: getCsrfToken(),
                    _method: 'DELETE'
                },
                success: function (response) {
                    if (!response || !response.status) {
                        sendmsg('error', (response && response.message) || 'Unable to delete staff.');
                        return;
                    }

                    sendmsg('success', response.message || 'Staff deleted successfully.');
                    const activeTab = $('.hrx-tab-btn.active').data('tab') || 'dashboard';
                    loadTab(activeTab);
                },
                error: function () {
                    sendmsg('error', 'Unable to delete staff.');
                },
                complete: function () {
                    loader('hide');
                }
            });
        }
    }

    function submitAjaxForm($form, mode) {
        const endpointMap = {
            'staff'      : config.storeStaffUrl,
            'attendance' : config.storeAttendanceUrl,
            'leave'      : config.storeLeaveUrl,
        };
        const endpoint = endpointMap[mode] || config.storeLeaveUrl;
        const payload = $form.serializeArray();
        payload.push({ name: '_token', value: getCsrfToken() });

        $.ajax({
            url: endpoint,
            type: 'POST',
            data: payload,
            success: function (response) {
                if (!response.status) {
                    sendmsg('error', response.message || 'Action failed.');
                    return;
                }
                sendmsg('success', response.message || 'Saved successfully.');
                $('.add-datamodal').modal('hide');
                const activeTab = $('.hrx-tab-btn.active').data('tab') || 'dashboard';
                loadTab(activeTab);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstError = Object.values(xhr.responseJSON.errors)[0];
                    const text = Array.isArray(firstError) ? firstError[0] : firstError;
                    sendmsg('error', text || 'Validation failed.');
                    return;
                }
                sendmsg('error', 'Unable to complete request.');
            }
        });
    }

    $(document).on('click', '.hrx-tab-btn', function () {
        const tab = $(this).data('tab');
        window.HRDashboard.openTab(tab);
    });

    $(document).on('click', '.hr-open-modal', function () {
        openModal($(this).data('modal-type'));
    });

    $(document).on('click', '.hrx-staff-view', function () {
        const id = $(this).data('id');
        if (!id) {
            sendmsg('error', 'Invalid staff record.');
            return;
        }
        openStaffView(id);
    });

    $(document).on('click', '.hrx-staff-edit', function () {
        const id = $(this).data('id');
        if (!id) {
            sendmsg('error', 'Invalid staff record.');
            return;
        }
        openStaffForm(id);
    });

    $(document).on('click', '.hrx-staff-delete', function () {
        const id = $(this).data('id');
        if (!id) {
            sendmsg('error', 'Invalid staff record.');
            return;
        }
        deleteStaff(id);
    });

    $(document).on('submit', 'form[data-hr-ajax-form]', function (e) {
        e.preventDefault();
        submitAjaxForm($(this), $(this).data('hr-ajax-form'));
    });

    // Use existing staff module save flow for the AJAX-loaded staff form.
    $(document).on('submit', '#savedata', function (e) {
        e.preventDefault();
        $('.err').remove();
        loader();

        const formData = new FormData(this);
        formData.append('_token', getCsrfToken());

        $.ajax({
            url: config.staffStoreUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if (!response.status) {
                    sendmsg('error', response.message || 'Action failed.');
                    return;
                }

                $('.add-datamodal').modal('hide');
                sendmsg('success', response.message || 'Saved successfully.');
                const activeTab = $('.hrx-tab-btn.active').data('tab') || 'dashboard';
                loadTab(activeTab);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const messages = [];

                    Object.keys(errors).forEach(function (key) {
                        const item = errors[key];
                        const fieldName = item.code || key;
                        const message = item.message || (Array.isArray(item) ? item[0] : 'Validation failed.');
                        const $field = $('[name="' + fieldName + '"]');

                        if ($field.length) {
                            $field.after('<div class="err text-danger">' + message + '</div>');
                        }

                        messages.push(message);
                    });

                    if (messages.length) {
                        sendmsg('error', messages.join('<br>'));
                    }
                    return;
                }

                sendmsg('error', 'Unable to complete request.');
            },
            complete: function () {
                loader('hide');
            }
        });
    });

    $(document).on('change', 'select[name="hr_department_id"]', function () {
        const deptId = $(this).val();
        if (!config.loadUnitsUrl) {
            return;
        }

        $.get(config.loadUnitsUrl, { hr_department_id: deptId }, function (data) {
            let html = '<option value="">Select</option>';
            $.each(data || [], function (i, unit) {
                html += '<option value="' + unit.id + '">' + unit.name + '</option>';
            });

            $('select[name="hr_department_unit_id"]').html(html);

            if ($.fn.select2) {
                $('.select2-modal').select2({
                    dropdownParent: $('.add-datamodal')
                });
            }
        });
    });

    $(document).ready(function () {
        loadTab('dashboard');
    });
})(jQuery);
