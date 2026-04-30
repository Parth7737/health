@extends('layouts.front.app')
@section('title','OPD Patient | Paracare+')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-4">
        <h3 class="mb-3 mb-md-0">OPD Patient</h3>
      </div>
      <div class="col-sm-8 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addPatientModal">+ Add Patient</button>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
        <table id="opd-patient-table" class="display table-striped w-100">
          <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Patient Id</th>
              <th>Phone</th>
              <th>Consultant</th>
              <th>Last Visit</th>
              <th>Status</th>
              <th>Total Visit</th>
            </tr>
          </thead>
          <tbody>
            <tr><td><a href="#" class="text-primary">Joshiji</a></td><td>1065</td><td>9960237919</td><td>Dr Ikshit</td><td>19-11-2024 03:21 PM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">Ramesh Joshi</a></td><td>1064</td><td>9955445566</td><td>Dr Ikshit</td><td>19-11-2024 03:14 PM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">Sukesh 2</a></td><td>1062</td><td>7897899877</td><td>Dr Ikshit</td><td>14-11-2024 02:33 PM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">Yagik patel</a></td><td>1061</td><td>8857445456</td><td>Dr Ikshit</td><td>13-11-2024 02:58 PM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">Saurabh sakhuja</a></td><td>1060</td><td>9657478599</td><td>Dr Ikshit</td><td>13-11-2024 02:56 PM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">Alpesh patel</a></td><td>1059</td><td>9885746954</td><td>Dr Ikshit</td><td>13-11-2024 01:43 PM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">Claim Patient</a></td><td>1058</td><td>6565478596</td><td>Dr Ikshit</td><td>12-11-2024 11:44 AM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">Mayur</a></td><td>1055</td><td>7458866874</td><td>Dr Ikshit</td><td>09-11-2024 11:30 AM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">test patient</a></td><td>1053</td><td>7778599856</td><td>Ramesh</td><td>24-09-2024 05:26 PM</td><td>Completed</td><td>1</td></tr>
            <tr><td><a href="#" class="text-primary">hiren</a></td><td>1052</td><td>9998568574</td><td>Ramesh</td><td>24-09-2024 04:20 PM</td><td>Completed</td><td>1</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Add Patient Modal -->
<div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title text-white" id="addPatientModalLabel">OPD Patient</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @include('front.partials.opd-patient-form')
      </div>
    </div>
  </div>
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/jquery.dataTables.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/dataTables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/autoFill.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/keyTable.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/buttons.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/fixedHeader.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/rowReorder.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('public/front/assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatables/dataTables1.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatables/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.autoFill.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/autoFill.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.keyTable.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/keyTable.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.buttons.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.fixedHeader.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/fixedHeader.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/jszip.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/pdfmake.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/vfs_fonts.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.html5.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.print.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.rowReorder.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/rowReorder.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/flat-pickr/flatpickr.js') }}"></script>
<script>
  $(document).ready(function() {
    $('#opd-patient-table').DataTable({
      dom: "fBrtip",
      buttons: [
        { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class=\"fa fa-copy\"></i>', titleAttr: 'Copy' },
        { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class=\"fa fa-file-csv\"></i>', titleAttr: 'Export as CSV' },
        { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class=\"fa fa-file-excel\"></i>', titleAttr: 'Export as Excel' },
        { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class=\"fa fa-file-pdf\"></i>', titleAttr: 'Export as PDF' },
        { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class=\"fa fa-print\"></i>', titleAttr: 'Print Table' },
        { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class=\"fa fa-columns\"></i>', titleAttr: 'Column Visibility' }
      ],
      language: {
        search: '',
        searchPlaceholder: 'Search...'
      },
      lengthChange: true,
      paging: true,
      info: true,
      ordering: true,
      scrollX: true,
      autoWidth: true,
      responsive: true
    });
    $(document).find('.dataTables_filter input').addClass('form-control').css({'width':'300px','display':'inline-block'});
    $('[data-bs-toggle="tooltip"]').tooltip();
    // Flatpickr init for OPD Patient form
    flatpickr('#opd-appointment-date', { enableTime: true, dateFormat: 'd-m-Y H:i' });
    flatpickr('#opd-dob', { dateFormat: 'd-m-Y' });
  });
</script>
@endpush 