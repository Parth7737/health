@extends('layouts.front.app')
@section('title','Visitor | Paracare+')
@section('content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-4">
          <h3 class="mb-3 mb-md-0">Visitor List</h3>
        </div>
        <div class="col-sm-8 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
          <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addVisitorModal">+ Add Visitor</button>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
          <table id="visitor-table" class="display table-striped w-100">
            <thead class="table-light">
              <tr>
                <th>Purpose</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Date</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Diagnostic Tests</td>
                <td>Sagar</td>
                <td>1223456252</td>
                <td>06-08-2024</td>
                <td>11:06 AM</td>
                <td>3:06 AM</td>
                <td>
                  <ul class="action mb-0">
                    <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                    <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                  </ul>
                </td>
              </tr>
              <tr>
                <td>Treatment Procedures</td>
                <td>Parth</td>
                <td>1234567893</td>
                <td>06-08-2024</td>
                <td>11:06 AM</td>
                <td>12:15 AM</td>
                <td>
                  <ul class="action mb-0">
                    <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Parth')"><i class="fa-regular fa-eye"></i></a></li>
                    <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  
   <!-- Add Visitor Modal -->
   <div class="modal fade" id="addVisitorModal" tabindex="-1" aria-labelledby="addVisitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary ">
          <h5 class="modal-title text-white" id="addVisitorModalLabel">Add Visitor</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Purpose <span class="text-danger">*</span></label>
              <select class="form-select" required>
                <option>Select</option>
                <option>Diagnostic Tests</option>
                <option>Treatment Procedures</option>
                <option>Consultation</option>
                <option>Other</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">ID Card</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Number Of Person</label>
              <input type="number" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">In Time</label>
              <input type="time" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Out Time</label>
              <input type="time" class="form-control">
            </div>
            <div class="col-md-12">
              <label class="form-label">Note</label>
              <textarea class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label">Attach Document</label>
              <input type="file" class="form-control">
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
  <!-- Visitor Details Modal -->
  <div class="modal fade" id="visitorDetailsModal" tabindex="-1" aria-labelledby="visitorDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white" id="visitorDetailsModalLabel">Visitor Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-6"><strong>Purpose:</strong> <span id="detailPurpose"></span></div>
            <div class="col-6"><strong>Name:</strong> <span id="detailName"></span></div>
            <div class="col-6"><strong>Phone:</strong> <span id="detailPhone"></span></div>
            <div class="col-6"><strong>Number Of Person:</strong> <span id="detailNumPerson"></span></div>
            <div class="col-6"><strong>Date:</strong> <span id="detailDate"></span></div>
            <div class="col-6"><strong>In Time:</strong> <span id="detailInTime"></span></div>
            <div class="col-6"><strong>Out Time:</strong> <span id="detailOutTime"></span></div>
            <div class="col-12"><strong>Note:</strong> <span id="detailNote"></span></div>
          </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    var table = $('#visitor-table').DataTable({
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
        searchPlaceholder: 'Search visitors...'
      },
      lengthChange: true,
      paging: true,
      info: true,
      ordering: true,
      scrollX: true,
      autoWidth: true,
      responsive: true
    });
    $(table.table().container()).find('.dataTables_filter input').addClass('form-control').css({'width':'300px','display':'inline-block'});
    $('[data-bs-toggle="tooltip"]').tooltip();
    // Flatpickr init for date fields
    flatpickr('input[type="date"]', { dateFormat: 'd-m-Y' });
    flatpickr('input[type="datetime-local"]', { enableTime: true, dateFormat: 'd-m-Y H:i' });
  });
  function showVisitorDetails(name) {
    // Demo data for modal, replace with actual data as needed
    var data = {
      'Sagar': {
        purpose: 'Diagnostic Tests',
        name: 'Sagar',
        phone: '1223456252',
        numPerson: '3',
        date: '06-08-2024',
        inTime: '11:06 AM',
        outTime: '3:06 AM',
        note: 'N/A'
      },
      'Parth': {
        purpose: 'Treatment Procedures',
        name: 'Parth',
        phone: '1234567893',
        numPerson: '2',
        date: '06-08-2024',
        inTime: '11:06 AM',
        outTime: '12:15 AM',
        note: 'N/A'
      }
    };
    var d = data[name];
    if (d) {
      $('#detailPurpose').text(d.purpose);
      $('#detailName').text(d.name);
      $('#detailPhone').text(d.phone);
      $('#detailNumPerson').text(d.numPerson);
      $('#detailDate').text(d.date);
      $('#detailInTime').text(d.inTime);
      $('#detailOutTime').text(d.outTime);
      $('#detailNote').text(d.note);
      $('#visitorDetailsModal').modal('show');
    }
  }
  function deleteVisitor(e) {
    e.preventDefault();
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
        Swal.fire('Deleted!', 'Visitor has been deleted.', 'success');
      }
    });
  }
</script>
@endpush