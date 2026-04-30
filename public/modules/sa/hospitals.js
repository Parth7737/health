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
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'hospital_type', name: 'hospital_type' },
            { data: 'parent_id', name: 'parent_id' },
            { data: 'status', name: 'status' },
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

    $(document).on('click', '.loadmodal', async function() {
        loader();
        var id = $(this).data('targetid');
        var status = $(this).data('status');        
        var url = route('viewstatusmodal');
        const token = await csrftoken(); // wait for the new token

        $.ajax({
            url: url,
            type: "POST",
            data: {id: id, status: status, _token: token},
            success: function (response) {
                loader('hide');
                if (response) {
                    $("#ajax_view_modal").html(response);
                    $(".view_modal_data").modal('show');
                }
            }
        });
    });


    $(document).on('click', '.deletebtn', async function() {
        var id = $(this).data('id');    
        var url = route('destroy', {hospital : id});
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
    

     $(document).on('click', '.autoin', async function() {
        var id = $(this).data('id');    
        var url = route('autoin');
        // wait for the new token
        Swal.fire({
            title: 'Are you sure?',
            text: "To Login as admin & Leave Master Panel!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Please!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                loader();
                const token = await csrftoken();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {id: id, _token: token},
                    success: function (response) {
                        loader('hide');
                        if (response.status) {
                            sendmsg('success', response.message);
                            window.location = response.url;
                        } else {
                            sendmsg('error', response.message);
                        }
                    }
                });
            }
        });
    });
    
});

    $(document).on("submit", "#change-status", async function (e) {

        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();

        var fd = new FormData(this);
        fd.append("_token", token);
        $.ajax({
            url: route('changestatus'),
            type: "POST",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) {
                    $('.approveRejectModal').modal('hide');
                    sendmsg('success', response.message);
                    setTimeout(() => {
                        location.reload();
                    }, 500);
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

     $(document).on('click', '.autoin', async function() {
        var id = $(this).data('id');    
        var url = route('autoin');
        // wait for the new token
        Swal.fire({
            title: 'Are you sure?',
            text: "To Login as admin & Leave Master Panel!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Please!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                loader();
                const token = await csrftoken();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {id: id, _token: token},
                    success: function (response) {
                        loader('hide');
                        if (response.status) {
                            sendmsg('success', response.message);
                            window.location = response.url;
                        } else {
                            sendmsg('error', response.message);
                        }
                    }
                });
            }
        });
    });
    