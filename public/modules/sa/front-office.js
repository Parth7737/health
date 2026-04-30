$(document).ready(function() {
    const loadroute = route('loadtable');

    const xintable = $('#xin-table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: true,
        lengthChange: true,
        scrollX: true,
        ajax: {
            url: loadroute,
            type: 'POST',
            data: function (d) {
                d._token = window.Laravel.csrfToken;
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            }
        },
        columns: [
            {
                data: null,
                name: 'serial_no',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'name', name: 'appointments.name' },
            { data: 'appointment_id', name: 'appointments.appointment_id' },
            { data: 'appointment_date', name: 'appointments.appointment_date' },
            { data: 'patient_phone', name: 'appointments.patient_phone' },
            { data: 'gender', name: 'appointments.gender' },
            { data: 'doctor', name: 'doctor_name', orderable: false },
            { data: 'source', name: 'appointments.source' },
            { data: 'priority', name: 'appointments.priority' },
            { data: 'live_consultation', name: 'appointments.live_consultation' },
            { data: 'status_badge', name: 'appointments.status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: 'fBrtip',
        autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function (e, dt) { dt.ajax.reload(); }},
            { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
            { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'Export as CSV' },
            { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Export as Excel' },
            { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'Export as PDF' },
            { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print Table' },
            { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Column Visibility' }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search...'
        },
        responsive: true
    });

    $(document).find('.dataTables_filter input').addClass('form-control').css({'width':'300px','display':'inline-block'});

    initTooltips();
    xintable.on('draw.dt responsive-display.dt column-visibility.dt', function () {
        initTooltips();
    });

    function initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }

    flatpickr('#from_date', { dateFormat: 'Y-m-d' });
    flatpickr('#to_date', { dateFormat: 'Y-m-d' });

    $(document).on('click', '#filterBtn', function() {
        xintable.ajax.reload();
    });

    $(document).on('click', '#resetBtn', function() {
        $('#from_date').val('');
        $('#to_date').val('');
        xintable.ajax.reload();
    });

    $(document).on('click', '.adddata, .editdata', async function() {
        loader();
        var id = $(this).data('id');
        var url = route('showform');
        const token = await csrftoken();

        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id, _token: token },
            success: function(response) {
                loader('hide');
                if (response) {
                    $('#ajaxdata').html(response);
                    $('.add-datamodal').modal('show');
                    flatpickr('input[name="appointment_date"]', { dateFormat: 'd-m-Y',minDate: "today" });
                    initSelect2InModal();
                    loadDoctorSlots();
                }
            },
            error: function() {
                loader('hide');
                sendmsg('error', 'Failed to load form.');
            }
        });
    });

    $(document).on('change', '#appointment-doctor-id', function() {
        loadDoctorSlots();
    });

    function initSelect2InModal() {
        if (!$.fn.select2) {
            return;
        }

        $('.add-datamodal .select2-modal').each(function() {
            const $field = $(this);

            if ($field.hasClass('select2-hidden-accessible')) {
                $field.select2('destroy');
            }

            $field.select2({
                dropdownParent: $('.add-datamodal'),
                width: '100%'
            });
        });
    }

    function loadDoctorSlots() {
        var doctor_id = $('#appointment-doctor-id').val();
        const loaddoctorslots = route('load-doctor-slots');
        const selectedSlot = $('#appointment-slot').val();

        if (!doctor_id) {
            $('#appointment-slot').html('<option value="">Select Slot</option>').trigger('change');
            return;
        }

        $.get(loaddoctorslots, { doctor_id: doctor_id }, function(data) {
            var html = '<option value="">Select Slot</option>';
            $.each(data, function(i, s) {
                const slotValue = s.start + ' - ' + s.end;
                const selected = selectedSlot === slotValue ? ' selected' : '';
                html += '<option value="' + slotValue + '"' + selected + '>' + slotValue + '</option>';
            });

            if (selectedSlot && html.indexOf('value="' + selectedSlot + '"') === -1) {
                html += '<option value="' + selectedSlot + '" selected>' + selectedSlot + '</option>';
            }

            $('#appointment-slot').html(html).trigger('change');
        });
    }

    $(document).on('submit', '#savedata', async function(e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();

        var fd = new FormData(this);
        fd.append('_token', token);

        $.ajax({
            url: route('store'),
            type: 'POST',
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                loader('hide');
                if (response.status) {
                    $('.add-datamodal').modal('hide');
                    xintable.ajax.reload(null, false);
                    sendmsg('success', response.message);
                } else {
                    sendmsg('error', response.message || 'Unable to save appointment.');
                }
            },
            error: function(xhr) {
                loader('hide');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || {};
                    for (let field in errors) {
                        const fieldName = errors[field]['code'];
                        const message = errors[field]['message'];
                        const $field = $('[name="' + fieldName + '"]');
                        $field.after('<div class="err text-danger">' + message + '</div>');
                    }
                    sendmsg('error', 'Please fix validation errors.');
                    return;
                }
                sendmsg('error', 'Something went wrong.');
            }
        });
    });

    $(document).on('click', '.deletebtn', async function() {
        var id = $(this).data('id');
        var url = route('destroy', { appointment: id });
        const token = await csrftoken();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: token, _method: 'DELETE' },
                    success: function(response) {
                        loader('hide');
                        if (response.status) {
                            xintable.ajax.reload(null, false);
                            sendmsg('success', response.message);
                        } else {
                            sendmsg('error', response.message || 'Unable to delete appointment.');
                        }
                    },
                    error: function() {
                        loader('hide');
                        sendmsg('error', 'Unable to delete appointment.');
                    }
                });
            }
        });
    });

    $(document).on('click', '.change-status-btn', async function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const token = await csrftoken();
        const url = route('update-status', { appointment: id });

        loader();
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: token, status: status },
            success: function(response) {
                loader('hide');
                if (response.status) {
                    xintable.ajax.reload(null, false);
                    sendmsg('success', response.message);
                } else {
                    sendmsg('error', response.message || 'Unable to update status.');
                }
            },
            error: function(xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to update status.');
            }
        });
    });

    $(document).on('click', '.move-to-opd-btn', async function() {
        const id = $(this).data('id');
        const token = await csrftoken();
        const url = route('move-to-opd', { appointment: id });

        loader();
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: token },
            success: function(response) {
                loader('hide');
                if (response.status) {
                    xintable.ajax.reload(null, false);
                    sendmsg('success', response.message);
                } else {
                    sendmsg('error', response.message || 'Unable to move appointment to OPD.');
                }
            },
            error: function(xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to move appointment to OPD.');
            }
        });
    });
});
