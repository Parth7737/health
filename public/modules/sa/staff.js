staffTable = null;
const loadroute = route('loadtable');

// grid view paging controls
let gridStart = 0;
const gridLength = 100;
$(document).ready(function() {
    staffTable = $('#xin-table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: true,
        lengthChange: true,
        searching: false,
        scrollX: true,
        ajax: {
            url: loadroute, 
            type: 'POST',
            data: function(d) {
                d._token = window.Laravel.csrfToken;
                d.keyword = $('#keywordSearch').val();
                d.role_filter = $('#roleFilter').val();
            }
        },
        columns: [
            { data: 'photo', name: 'photo', orderable: false, searchable: false },
            { data: 'staff_id', name: 'staff_id' },
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'department_name', name: 'department_name', orderable: false },
            { data: 'designation_name', name: 'designation_name', orderable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
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
        responsive: true,
        pagingType: "full_numbers",
        pageLength: 10,
        lengthMenu: [[ 10, 25, 50, 100 ], [ 10, 25, 50, 100 ]]
    });
    loadGridView();
    initTooltips();
    staffTable.on('draw.dt', function () {
        initTooltips();
    });
    function initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }
});

/**
 * Load grid view data
 *
 * @param {boolean} append  whether to append results to existing cards
 */
function loadGridView(append = false) {
    loader();
    const keyword = $('#keywordSearch').val();
    const roleFilter = $('#roleFilter').val();
    const token = window.Laravel.csrfToken;

    if (!append) {
        gridStart = 0; // reset paging on fresh load
        $('#staffGridContainer').empty();
    }

    $.ajax({
        url: loadroute,
        type: 'POST',
        data: {
            _token: token,
            keyword: keyword,
            role_filter: roleFilter,
            draw: 1,
            start: gridStart,
            length: gridLength
        },
        success: function(response) {
            loader('hide');
            if (append) {
                appendGridRows(response.data);
            } else {
                renderGridView(response.data);
            }

            // show/hide load more button
            if (response.data && response.data.length === gridLength) {
                $('#loadMoreGrid').removeClass('d-none');
            } else {
                $('#loadMoreGrid').addClass('d-none');
            }
        },
        error: function() {
            loader('hide');
            $('#staffGridContainer').html('<div class="col-12 alert alert-danger">Error loading staff data</div>');
        }
    });
}

/**
 * Append rows when loading more
 */
function appendGridRows(data) {
    // reuse renderGridView's card creation but without clearing container
    const container = $('#staffGridContainer');
    data.forEach(staff => {
        const imagePath = staff.image ? staff.image : 'front/assets/images/dashboard/profile.png';
        const fullName = staff.full_name || (staff.first_name + ' ' + staff.last_name);
        const card = `
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card shadow-sm h-100 staff-card">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <img src="${imagePath}" class="rounded-circle mb-3 staff-avatar" alt="${fullName}" width="80" height="80">
                        <h6 class="fw-bold mb-1">${fullName}</h6>
                        <small class="text-muted mb-2">${staff.staff_id || 'N/A'}</small>
                        <small class="text-muted mb-2">${staff.role.name || 'N/A'}</small>
                        <small class="text-muted mb-2">${staff.department_name || 'N/A'}</small>
                        <div class="mb-2">
                            <span class="badge bg-info">${staff.designation_name || 'N/A'}</span>
                            ${staff.status_badge}
                        </div>
                        <div class="mb-3 small">
                            <div class="d-block mb-1"><i class="fa fa-envelope me-1"></i>${staff.email}</div>
                            <div class="d-block"><i class="fa fa-phone me-1"></i>${staff.phone}</div>
                        </div>
                        <div class="w-100">
                            ${staff.actions}
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(card);
    });
    $('[data-bs-toggle="tooltip"]').tooltip();
}

/**
 * Render grid view cards
 */
function renderGridView(data) {
    const container = $('#staffGridContainer');
    container.empty();

    if (data.length === 0) {
        container.html('<div class="col-12 alert alert-info text-center">No staff members found</div>');
        return;
    }

    data.forEach(staff => {
        const imagePath = staff.image ? staff.image : 'front/assets/images/dashboard/profile.png';
        const fullName = staff.full_name || (staff.first_name + ' ' + staff.last_name);
        
        const card = `
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card shadow-sm h-100 staff-card">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                        <img src="${imagePath}" class="rounded-circle mb-3 staff-avatar" alt="${fullName}" width="80" height="80">
                        <h6 class="fw-bold mb-1">${fullName}</h6>
                        <small class="text-muted mb-2">${staff.staff_id || 'N/A'}</small>
                        <small class="text-muted mb-2">${staff.role.name || 'N/A'}</small>
                        <small class="text-muted mb-2">${staff.department_name || 'N/A'}</small>
                        <div class="mb-2">
                            <span class="badge bg-info">${staff.designation_name || 'N/A'}</span>
                            ${staff.status_badge}
                        </div>
                        <div class="mb-3 small">
                            <div class="d-block mb-1"><i class="fa fa-envelope me-1"></i>${staff.email}</div>
                            <div class="d-block"><i class="fa fa-phone me-1"></i>${staff.phone}</div>
                        </div>
                        <div class="w-100">
                            ${staff.actions}
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(card);
    });

    // Reinitialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
}
// Add/Edit staff button click
$(document).on('click', '.adddata, .editdata', async function() {
    loader();
    const id = $(this).data('id');    
    var url = route('showform');
    const token = await csrftoken();

    $.ajax({
        url: url,
        type: "POST",
        data: { id: id, _token: token },
        success: function(response) {
            loader('hide');
            $("#ajaxdata").html(response);
            $(".add-datamodal").modal('show');
            flatpickr('input[name="date_of_joining"]', { dateFormat: 'd-m-Y',maxDate: "today" });
            flatpickr('input[name="date_of_birth"]', { dateFormat: 'd-m-Y',maxDate: "today"  });
            $(".select2-modal").select2({
                dropdownParent: $('.add-datamodal')
            });
        },
        error: function(xhr) {
            loader('hide');
            sendmsg('error', 'Error loading form');
        }
    });
});

// Form submission
$(document).on('submit', '#savedata', async function(e) {
    e.preventDefault();
    loader();
    $('.err').remove();
    const token = await csrftoken();

    const fd = new FormData(this);
    fd.append("_token", token);

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
                staffTable.ajax.reload();
                loadGridView();
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
            } else {
                sendmsg('error', 'Something went wrong. Please try again later.');
            }
        }
    });
});
$(document).on('click', '#list-tab', async function() {
    staffTable.ajax.reload();
});
$(document).on('click', '.deletebtn', async function() {
    var id = $(this).data('id');    
    var url = route('destroy', {staff : id});
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

$(document).ready(function() {

    // Search button click
    $('#searchBtn').on('click', function() {
        gridStart = 0; // always reset when performing a new search/filter
        const roleFilter = $('#roleFilter').val();
        const keyword = $('#keywordSearch').val();

        if ($('#listView').hasClass('show')) {
            staffTable.ajax.reload();
        } else {
            loadGridView(false);
        }
    });

    // Keyword search on enter
    $('#keywordSearch').on('keypress', function(e) {
        if (e.which === 13) {
            $('#searchBtn').click();
            return false;
        }
    });

    // Load more button
    $('#loadMoreGrid').on('click', function() {
        gridStart += gridLength;
        loadGridView(true);
    });

    // switching to card view should reset grid start
    $(document).on('click', '#card-tab', function() {
        gridStart = 0;
        loadGridView(false);
    });

});

// Add timing functionality
$(document).on('click', '#add_timing', function() {
    const day = $('#work_day').val();
    const startTime = $('#start_time').val();
    const endTime = $('#end_time').val();

    if (!day) {
        sendmsg('error', 'Please select a day.');
        return;
    }
    if (!startTime || !endTime) {
        sendmsg('error', 'Please enter both start and end times.');
        return;
    }

    // Check if day already exists
    if ($(`.timing-item[data-day="${day}"]`).length > 0) {
        sendmsg('error', `Timing for ${day} already exists.`);
        return;
    }

    // Create timing item HTML
    const timingHtml = `
        <div class="timing-item card p-3 mb-2" data-day="${day}">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <strong class="text-capitalize">${day}</strong>
                </div>
                <div class="col-md-3">
                    <span class="badge bg-primary">${startTime}</span>
                </div>
                <div class="col-md-3">
                    <span class="badge bg-success">${endTime}</span>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-sm btn-danger remove-timing" data-day="${day}">
                        <i class="fa fa-times"></i> Remove
                    </button>
                </div>
            </div>
            <input type="hidden" name="work_timings[${day}][start_time]" value="${startTime}">
            <input type="hidden" name="work_timings[${day}][end_time]" value="${endTime}">
        </div>
    `;

    $('#timings_list').append(timingHtml);

    // Clear inputs
    $('#work_day').val('');
    $('#start_time').val('');
    $('#end_time').val('');

});

// Clear all timings functionality
$(document).on('click', '#clear_timings', function() {
    $('#timings_list').empty();
});

// Remove single timing functionality
$(document).on('click', '.remove-timing', function() {
    const day = $(this).data('day');
    $(`.timing-item[data-day="${day}"]`).remove();
    sendmsg('success', `Timing for ${day} removed.`);
});

    // department->unit
    $(document).on('change', 'select[name=hr_department_id]', function() {
        var dept = $(this).val();
        
        const loadunit = route('load-units');
        $.get(loadunit, { hr_department_id: dept }, function(data) {
            var html = '<option value="">Select</option>';
            $.each(data, function(i, u) {
                html += '<option value="'+u.id+'">'+u.name+'</option>';
            });
            $('select[name=hr_department_unit_id]').html(html);
            $(".select2-modal").select2({
                dropdownParent: $('.add-datamodal')
            });
        });
    });