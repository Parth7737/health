$(document).ready(function () {
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
            { data: null, orderable: false, searchable: false, render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'bed_number', name: 'bed_number' },
            { data: 'bed_code', name: 'bed_code' },
            { data: 'barcode', name: 'barcode', orderable: false, searchable: false },
            { data: 'ward_name', name: 'ward_name', orderable: false },
            { data: 'room_number', name: 'room_number', orderable: false },
            { data: 'bed_type', name: 'bed_type', orderable: false },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: 'fBrtip',
        responsive: true
    });

    function refreshRoomCapacityHint() {
        const roomOption = $('select[name="room_id"] option:selected');
        const capacity = Number(roomOption.data('capacity') || 0);
        const used = Number(roomOption.data('used') || 0);
        if (capacity > 0) {
            $('#room_capacity_hint').text(`Current beds: ${used}/${capacity}`);
        } else {
            $('#room_capacity_hint').text('');
        }
    }

    function toggleBedCreationMode() {
        const mode = $('#creation_mode').val() || 'single';
        const isBulk = mode === 'bulk';
        $('#is_bulk').val(isBulk ? '1' : '0');
        $('.single-bed-fields').toggle(!isBulk);
        $('.bulk-bed-fields').toggle(isBulk);
    }

    $(document).on('click', '.adddata, .editdata', async function () {
        loader();
        const id = $(this).data('id');
        const bulk = $(this).data('bulk') ? 1 : 0;
        const token = await csrftoken();

        $.post(route('showform'), { id: id, bulk: bulk, _token: token }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal').modal('show');
            $('.select2-modal').select2({ dropdownParent: $('.add-datamodal') });
            toggleBedCreationMode();
            refreshRoomCapacityHint();
        }).fail(function () {
            loader('hide');
        });
    });

    $(document).on('change', '#creation_mode', toggleBedCreationMode);
    $(document).on('change', 'select[name="room_id"]', refreshRoomCapacityHint);

    $(document).on('click', '.deletebtn', async function () {
        const id = $(this).data('id');
        const token = await csrftoken();

        Swal.fire({ title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true }).then((result) => {
            if (!result.isConfirmed) return;
            loader();
            $.post(route('destroy', { bed: id }), { _token: token, _method: 'DELETE' }, function (response) {
                loader('hide');
                if (response.status) {
                    sendmsg('success', response.message);
                    xintable.ajax.reload(null, false);
                } else {
                    sendmsg('error', response.message);
                }
            }).fail(function () {
                loader('hide');
                sendmsg('error', 'Something went wrong.');
            });
        });
    });

    $(document).on('submit', '#savedata', async function (e) {
        e.preventDefault();
        loader();

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
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    sendmsg('error', xhr.responseJSON.errors.map(function (e) { return e.message; }).join('<br>'));
                } else {
                    sendmsg('error', 'Something went wrong. Please try again later.');
                }
            }
        });
    });
});
