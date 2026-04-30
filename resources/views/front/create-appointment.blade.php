@extends('layouts.front.app')
@section('title','Front Office | Paracare+')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-4">
        <h3 class="mb-3 mb-md-0">Appointment Details</h3>
      </div>
      <div class="col-sm-8 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">+ Add Appointment</button>
        <a href="Visitor-Book.html" class="btn btn-info">Visitor Book</a>
        <a href="Phone-Call-Log.html" class="btn btn-info">Phone Call Log</a>
        <div class="btn-group">
          <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Postal</button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="Postal-Receive.html">Receive</a></li>
            <li><a class="dropdown-item" href="Postal-Dispatch.html">Dispatch</a></li>
          </ul>
        </div>
        <a href="Complain.html" class="btn btn-info">Complain</a>
      </div>
    </div>
  </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <div class="row mb-3 align-items-end">
        <div class="col-md-2 mb-3 mb-md-0">
          <label class="form-label">From Date</label>
          <input type="date" class="form-control" placeholder="dd-mm-yyyy">
        </div>
        <div class="col-md-2">
          <label class="form-label">To Date</label>
          <input type="date" class="form-control" placeholder="dd-mm-yyyy">
        </div>
        
      </div>
      <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
          <table id="appointment-table" class="display table-striped">
          <thead class="table-light">
            <tr>
              <th>Patient Name</th>
              <th>Appointment No</th>
              <th>Date</th>
              <th>Phone</th>
              <th>Gender</th>
              <th>Doctor</th>
              <th>Source</th>
              <th>Priority</th>
              <th>Live Consultant</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><a href="#" class="text-primary">hiren</a></td>
              <td>APPNO21</td>
              <td>24-09-2024 04:20 PM</td>
              <td>9998568574</td>
              <td>Male</td>
              <td>Ramesh</td>
              <td>Offline</td>
              <td><span class="badge bg-danger">Emergency</span></td>
              <td>No</td>
              <td><span class="badge bg-success">Approved</span></td>
              <td>
                  <ul class="action"> 
                      <li class="view"> <a href="#" data-bs-toggle="tooltip" title="View"><i class="fa-regular fa-eye"></i></a></li>
                      <li class="edit"> <a href="#"  data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                      <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash-can"></i></a></li>
                    </ul>
                  </td>
            </tr>
            <tr>
              <td><a href="#" class="text-primary">parth patel</a></td>
              <td>Pending</td>
              <td>23-09-2024 08:07 PM</td>
              <td>7458695874</td>
              <td>Male</td>
              <td>Dr Ikshit</td>
              <td>Offline</td>
              <td><span class="badge bg-warning text-dark">Urgent</span></td>
              <td>No</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td>
                  <ul class="action"> 
                      <li class="view"> <a href="#" data-bs-toggle="tooltip" title="View"><i class="fa-regular fa-eye"></i></a></li>
                      <li class="edit"> <a href="#"  data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                      <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash-can"></i></a></li>
                    </ul>
                  </td>
            </tr>
            <tr>
              <td><a href="#" class="text-primary">parth patel</a></td>
              <td>Pending</td>
              <td>23-09-2024 08:07 PM</td>
              <td>7458695874</td>
              <td>Male</td>
              <td>Dr Ikshit</td>
              <td>Offline</td>
              <td><span class="badge bg-warning text-dark">Urgent</span></td>
              <td>No</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td>
                  <ul class="action"> 
                      <li class="view"> <a href="#" data-bs-toggle="tooltip" title="View"><i class="fa-regular fa-eye"></i></a></li>
                      <li class="edit"> <a href="#"  data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                      <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash-can"></i></a></li>
                    </ul>
                    </td>
            </tr>
            <tr>
              <td><a href="#" class="text-primary">for logy test</a></td>
              <td>APPNO13</td>
              <td>01-08-2024 05:19 PM</td>
              <td>9998568573</td>
              <td>Male</td>
              <td>Dr Ikshit</td>
              <td>Offline</td>
              <td><span class="badge bg-danger">Emergency</span></td>
              <td>No</td>
              <td><span class="badge bg-success">Approved</span></td>
              <td>
                  <ul class="action"> 
                      <li class="view"> <a href="#" data-bs-toggle="tooltip" title="View"><i class="fa-regular fa-eye"></i></a></li>
                      <li class="edit"> <a href="#"  data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                      <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash-can"></i></a></li>
                    </ul>
                  </td>
            </tr>
            <tr>
              <td><a href="#" class="text-primary">Ultimate Test</a></td>
              <td>APPNO12</td>
              <td>01-08-2024 01:58 PM</td>
              <td>7894563211</td>
              <td>Male</td>
              <td>Dr Ikshit</td>
              <td>Offline</td>
              <td><span class="badge bg-danger">Emergency</span></td>
              <td>No</td>
              <td><span class="badge bg-success">Approved</span></td>
              <td>
                  <ul class="action"> 
                      <li class="view"> <a href="#" data-bs-toggle="tooltip" title="View"><i class="fa-regular fa-eye"></i></a></li>
                      <li class="edit"> <a href="#"  data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                      <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash-can"></i></a></li>
                    </ul>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Add Appointment Modal -->
<div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary ">
        <h5 class="modal-title text-white" id="addAppointmentModalLabel">Add Appointment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Patient Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select class="form-select">
              <option>Select</option>
              <option>Male</option>
              <option>Female</option>
              <option>Other</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Doctor <span class="text-danger">*</span></label>
            <select class="form-select select2" required>
              <option>Select</option>
              <option>Dr Ikshit</option>
              <option>Dr Nitesh Kumar</option>
              <option>Dr Ahutosh Sayana</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label ">Appointment Priority</label>
            <select class="form-select select2">
              <option>Emergency</option>
              <option>Urgent</option>
              <option>Time-Sensitive</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Note <span class="text-danger">*</span></label>
            <textarea class="form-control" rows="1" required></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select">
              <option>Pending</option>
              <option>Approved</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Live Consultant (On Video Conference) <span class="text-danger">*</span></label>
            <select class="form-select" required>
              <option>No</option>
              <option>Yes</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Slot <span class="text-danger">*</span></label>
            <select class="form-select" required>
              <option>Select Slot</option>
              <option>Morning</option>
              <option>Afternoon</option>
              <option>Evening</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
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
    $('#appointment-table').DataTable({
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
    // Flatpickr init for date fields
    flatpickr('input[type="date"]', { dateFormat: 'd-m-Y' });
    flatpickr('input[type="datetime-local"]', { enableTime: true, dateFormat: 'd-m-Y H:i' });
  });
</script>
@endpush