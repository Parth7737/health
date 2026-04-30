$(document).ready(function () {
    const currentType = $('#header-footer-page').data('selected-type');
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
                d.type = currentType;
            }
        },
        columns: [
            { data: 'header_preview', name: 'header_image', orderable: false, searchable: false },
            { data: 'footer_preview', name: 'footer_text' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: 'fBrtip',
        autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', titleAttr: 'Reload Table', action: function (e, dt) { dt.ajax.reload(); } },
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

    $(document).find('.dataTables_filter input').addClass('form-control').css({ 'width': '300px', 'display': 'inline-block' });

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

    $(document).on('click', '.adddata, .editdata', async function () {
        loader();
        const id = $(this).data('id') || '';
        const type = $(this).data('type') || currentType;
        const token = await csrftoken();

        $.ajax({
            url: route('showform'),
            type: 'POST',
            data: { id: id, type: type, _token: token },
            success: function (response) {
                loader('hide');
                $('#ajaxdata').html(response);
                $('.add-datamodal').modal('show');
                $('.add-datamodal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
            },
            error: function () {
                loader('hide');
                sendmsg('error', 'Failed to load form');
            }
        });
    });

    $(document).on('click', '.deletebtn', async function () {
        const id = $(this).data('id');
        const url = route('destroy', { header_footer: id });
        const token = await csrftoken();

        Swal.fire({
            title: 'Are you sure?',
            text: 'You will need to configure this print type again after delete.',
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
                    success: function (response) {
                        loader('hide');
                        if (response.status) {
                            sendmsg('success', response.message);
                            xintable.ajax.reload(null, false);
                        } else {
                            sendmsg('error', response.message);
                        }
                    },
                    error: function () {
                        loader('hide');
                        sendmsg('error', 'An error occurred');
                    }
                });
            }
        });
    });

    $(document).on('change', '#header_image', function () {
        const [file] = this.files;
        const previewWrapper = $('.header-image-preview-wrapper');
        const preview = $('.header-image-preview');

        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            preview.attr('src', event.target.result);
            previewWrapper.removeClass('d-none');
        };
        reader.readAsDataURL(file);
    });

    $(document).on('submit', '#savedata', async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        $('.text-danger[class*="err_"]').html('');
        const token = await csrftoken();

        const fd = new FormData(this);
        fd.append('_token', token);

        $.ajax({
            url: route('store'),
            type: 'POST',
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) {
                    $('.add-datamodal').modal('hide');
                    xintable.ajax.reload(null, false);
                    sendmsg('success', response.message);
                } else {
                    sendmsg('error', response.message);
                }
            },
            error: function (xhr) {
                loader('hide');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];

                    for (let field in errors) {
                        const message = errors[field]['message'];
                        const code = errors[field]['code'];
                        const $field = $(`[name="${code}"]`);

                        if ($field.length) {
                            $field.after(`<div class="err text-danger">${message}</div>`);
                        }

                        errorMessages.push(message);
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
});