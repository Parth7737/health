var marTimePickers = [];

function to12Hour(time24) {
    if (!time24) {
        return '';
    }

    var value = String(time24).trim();
    if (/^\d{1,2}:\d{2}\s*(AM|PM)$/i.test(value)) {
        return value.replace(/\s*(am|pm)$/i, function (match) {
            return ' ' + match.trim().toUpperCase();
        });
    }

    var match = value.match(/^(\d{1,2}):(\d{2})$/);
    if (!match) {
        return value;
    }

    var hour = parseInt(match[1], 10);
    var minute = match[2];
    var period = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12;
    if (hour === 0) {
        hour = 12;
    }

    return hour + ':' + minute + ' ' + period;
}

function to24Hour(time12) {
    if (!time12) {
        return '';
    }

    var value = String(time12).trim();
    var match = value.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
    if (match) {
        var hour = parseInt(match[1], 10);
        var minute = match[2];
        var period = match[3].toUpperCase();

        if (period === 'PM' && hour !== 12) {
            hour += 12;
        }
        if (period === 'AM' && hour === 12) {
            hour = 0;
        }

        return String(hour).padStart(2, '0') + ':' + minute;
    }

    if (/^\d{1,2}:\d{2}$/.test(value)) {
        var parts = value.split(':');
        return String(parts[0]).padStart(2, '0') + ':' + parts[1];
    }

    return '';
}

function defaultMarTimesForCount(count) {
    var presets = {
        1: ['08:00'],
        2: ['08:00', '20:00'],
        3: ['08:00', '14:00', '22:00'],
        4: ['08:00', '12:00', '18:00', '22:00']
    };

    if (presets[count]) {
        return presets[count].slice();
    }

    var start = 8 * 60;
    var end = 22 * 60;
    var step = Math.floor((end - start) / Math.max(1, count - 1));
    var times = [];

    for (var i = 0; i < count; i++) {
        var minutes = start + (step * i);
        var hour = Math.floor(minutes / 60);
        var minute = minutes % 60;
        times.push(String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0'));
    }

    return times;
}

function destroyMarTimePickers() {
    marTimePickers.forEach(function (fp) {
        if (fp && typeof fp.destroy === 'function') {
            fp.destroy();
        }
    });
    marTimePickers = [];
}

function readExistingMarTimesFromForm() {
    var times = [];
    $('#marScheduleTimesList .mar-schedule-time-input').each(function () {
        times.push($(this).val() || '');
    });
    return times;
}

function readMarTimesSeed() {
    var node = document.getElementById('marScheduleTimesData');
    if (!node) {
        return [];
    }

    try {
        var parsed = JSON.parse(node.textContent || '[]');
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        return [];
    }
}

function renderMarScheduleTimes(existingTimes) {
    destroyMarTimePickers();

    var count = parseInt($('#no_of_medicine').val(), 10) || 1;
    count = Math.max(1, Math.min(12, count));

    var defaults = defaultMarTimesForCount(count);
    var $list = $('#marScheduleTimesList');
    $list.empty();

    for (var i = 0; i < count; i++) {
        var value = '';
        if (existingTimes && existingTimes[i]) {
            value = to12Hour(existingTimes[i]);
        } else if (defaults[i]) {
            value = to12Hour(defaults[i]);
        }

        var rowHtml = '' +
            '<div class="mar-time-row">' +
            '  <span class="mar-dose-label">Dose ' + (i + 1) + '</span>' +
            '  <input type="text" class="form-control mar-schedule-time-input" name="schedule_times[]" value="' + value + '" placeholder="hh:mm AM" readonly autocomplete="off">' +
            '</div>';

        $list.append(rowHtml);
    }

    $list.find('.mar-schedule-time-input').each(function () {
        if (typeof flatpickr === 'undefined') {
            return;
        }

        marTimePickers.push(flatpickr(this, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'h:i K',
            time_24hr: false,
            minuteIncrement: 5,
            allowInput: false
        }));
    });
}

function initFrequencyMarForm() {
    var existing = readMarTimesSeed();
    if (!existing.length) {
        existing = readExistingMarTimesFromForm();
    }

    renderMarScheduleTimes(existing);

    $('#no_of_medicine')
        .off('change.marTimes input.marTimes')
        .on('change.marTimes input.marTimes', function () {
            var current = readExistingMarTimesFromForm();
            renderMarScheduleTimes(current);
        });
}

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
            { data: 'frequency', name: 'frequency' },
            { data: 'no_of_medicine', name: 'no_of_medicine' },
            { data: 'schedule_times', name: 'schedule_times', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: "fBrtip",
        autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function (e, dt, node, config) { dt.ajax.reload(); }},
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
    xintable.on('draw.dt', function () {
        initTooltips();
    });
    function initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }
    // Flatpickr init for date fields
    flatpickr('input[type="date"]', { dateFormat: 'd-m-Y' });
    flatpickr('input[type="datetime-local"]', { enableTime: true, dateFormat: 'd-m-Y H:i' });

    $(document).on('click', '.adddata, .editdata', async function() {
        loader();
        var id = $(this).data('id');       
        var url = route('showform');
        const token = await csrftoken(); // wait for the new token

        $.ajax({
            url: url,
            type: "POST",
            data: {id: id, _token: token},
            success: function (response) {
                loader('hide');
                if (response) {
                    $("#ajaxdata").html(response);
                    $(".add-datamodal").modal('show');
                    $(".add-datamodal .modal-dialog").removeClass('modal-xl');
                    initFrequencyMarForm();
                }
            },
            error: function (xhr) {
                loader('hide');
                $('.err').remove();
            }
        });
    });


    $(document).on('click', '.deletebtn', async function() {
        var id = $(this).data('id');    
        var url = route('destroy', {frequency : id});
        const token = await csrftoken(); // wait for the new token
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
                    type: "POST",
                    data: {id: id, _token: token, _method: 'DELETE'},
                    success: function (response) {
                        loader('hide');
                        if (response.status) {
                            sendmsg('success', response.message);
                            $('#xin-table').DataTable().ajax.reload(null, false);
                        } else {
                            sendmsg('error', response.message);
                        }
                    }
                });
            }
        });
    });
    
    $(document).on("submit", "#savedata", async function (e) {

        e.preventDefault();
        loader();
        $('.err').remove();

        var expectedCount = parseInt($('#no_of_medicine').val(), 10) || 0;
        var filledTimes = readExistingMarTimesFromForm().filter(function (time) {
            return String(time || '').trim() !== '';
        });

        if (filledTimes.length !== expectedCount) {
            loader('hide');
            sendmsg('error', 'Please set time for all ' + expectedCount + ' dose(s).');
            return;
        }

        var filledTimes24 = filledTimes.map(to24Hour).filter(function (time) {
            return String(time || '').trim() !== '';
        });

        if (filledTimes24.length !== expectedCount) {
            loader('hide');
            sendmsg('error', 'Please enter valid time for all dose(s) in 12-hour format.');
            return;
        }

        var uniqueTimes = filledTimes24.filter(function (time, index, arr) {
            return arr.indexOf(time) === index;
        });
        if (uniqueTimes.length !== filledTimes24.length) {
            loader('hide');
            sendmsg('error', 'Duplicate MAR dose times are not allowed.');
            return;
        }

        const token = await csrftoken();

        var fd = new FormData();
        var form = this;
        Array.prototype.forEach.call(form.elements, function (el) {
            if (!el.name || el.disabled) {
                return;
            }
            if (el.name === 'schedule_times[]') {
                return;
            }
            if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) {
                return;
            }
            if (el.type === 'file') {
                if (el.files && el.files.length) {
                    fd.append(el.name, el.files[0]);
                }
                return;
            }
            fd.append(el.name, el.value);
        });
        filledTimes24.forEach(function (time) {
            fd.append('schedule_times[]', time);
        });
        fd.append('_token', token);
        $.ajax({
            url: route('store'),
            type: "POST",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) {
                    $(".add-datamodal").modal('hide');
                    $('#xin-table').DataTable().ajax.reload(null, false);
                    sendmsg('success', response.message);
                } else {
                    sendmsg('error', response.message);
                }
            },
            error: function (xhr) {
                loader('hide');
                $('.err').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        const $field = $(`[name="${errors[field]['code']}"]`);

                        if ($field.hasClass('select2')) {
                            $field.next('.select2-container').after(`<div class="err text-danger">${errors[field]['message']}</div>`);
                        } else {
                            $field.after(`<div class="err text-danger">${errors[field]['message']}</div>`);
                        }

                        errorMessages.push(errors[field]['message']);
                    }
                    if (errorMessages.length > 0) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    sendmsg('error', xhr.responseJSON.message);
                } else {
                    sendmsg('error', 'Something went wrong. Please try again later.');
                }
            }
        });
    });

    $('.add-datamodal').on('hidden.bs.modal', function () {
        destroyMarTimePickers();
    });
});