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
            {
                data: null,
                name: 'serial_no',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'token', name: 'opd_patients.token_no' },
            { data: 'patient', name: 'patients.name' },
            { data: 'age_gender', name: 'patients.age_years' },
            { data: 'complaint', name: 'opd_patients.symptoms' },
            { data: 'wait_time', name: 'opd_patients.appointment_date' },
            { data: 'priority', name: 'opd_patients.absent_flag' },
            { data: 'doctor', name: 'consultant_name' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        dom: 'fBrtip',
        autoWidth: true,
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
            { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print Table' }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search queue...'
        },
        responsive: true
    });

    $(document).find('.dataTables_filter input').addClass('form-control').css({
        width: '300px',
        display: 'inline-block'
    });

    xintable.on('draw.dt', function () {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    });
});