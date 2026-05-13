$(document).ready(function() {
    const xintable = $('#xin-table').DataTable({
        processing: true, serverSide: true, paging: true, info: true, lengthChange: true, scrollX: true,
        ajax: { url: route('loadtable'), type: 'POST', data: function (d) { d._token = window.Laravel.csrfToken; } },
        columns: [
            { data: null, name: 'serial_no', orderable: false, searchable: false, render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'name', name: 'name' }, { data: 'code', name: 'code' },
            { data: 'speciality_name', name: 'speciality_name', orderable: false, searchable: false },
            { data: 'no_of_multiplier', name: 'no_of_multiplier' },
            { data: 'price', name: 'price' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: "fBrtip", autoWidth: true,
        buttons: [
            { text: '<i class="fa fa-sync"></i>', className: 'btn btn-secondary', action: function (e, dt) { dt.ajax.reload(); }},
            { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>' },
            { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>' },
            { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>' },
            { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>' },
            { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>' },
            { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>' }
        ],
        language: { search: '', searchPlaceholder: 'Search...' }, responsive: true
    });
    $(document).find('.dataTables_filter input').addClass('form-control').css({'width':'300px','display':'inline-block'});
    function initTooltips() { $('[data-bs-toggle="tooltip"]').tooltip('dispose'); $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' }); }
    xintable.on('draw.dt', initTooltips); initTooltips();

    $(document).on('click', '.adddata, .editdata', async function() {
        loader();
        const token = await csrftoken();
        $.ajax({
            url: route('showform'), type: "POST", data: {id: $(this).data('id'), _token: token},
            success: function (response) {
                loader('hide');
                if (response) { $("#ajaxdata").html(response); $(".add-datamodal").modal('show'); $(".add-datamodal .modal-dialog").removeClass('modal-xl'); $(".select2").select2(); }
            },
            error: function () { loader('hide'); }
        });
    });
    $(document).on('click', '.deletebtn', async function() {
        var id = $(this).data('id');
        const token = await csrftoken();
        Swal.fire({ title: 'Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it!' }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: route('destroy', {implant: id}), type: "POST", data: {_token: token, _method: 'DELETE'},
                    success: function (response) {
                        loader('hide');
                        if (response.status) { sendmsg('success', response.message); xintable.ajax.reload(null, false); }
                    }
                });
            }
        });
    });
    $(document).on("submit", "#savedata", async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();
        var fd = new FormData(this);
        fd.append("_token", token);
        $.ajax({
            url: route('store'), type: "POST", data: fd, contentType: false, processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) { $(".add-datamodal").modal('hide'); xintable.ajax.reload(null, false); sendmsg('success', response.message); }
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`[name="${errors[field]['code']}"]`).after(`<div class="err text-danger">${errors[field]['message']}</div>`);
                    }
                } else { sendmsg('error', 'Something went wrong.'); }
            }
        });
    });
});
