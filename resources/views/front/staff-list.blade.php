@extends('layouts.front.app')
@section('title', 'Staff Directory')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-4">
        <h3 class="mb-3 mb-md-0">Staff Directory</h3>
      </div>
      <div class="col-sm-8 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">+ Add Staff</button>
        <a href="#" class="btn btn-info">Import</a>
        <a href="#" class="btn btn-info">Staff Attendance</a>
        <a href="#" class="btn btn-info">Leaves</a>
        <a href="#" class="btn btn-info">Track</a>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid py-3">
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body p-3">
            <form class="row g-2 align-items-center flex-wrap flex-md-nowrap" onsubmit="return false;">
                <div class="col-12 col-md-4">
                    <select class="form-select" id="roleFilter">
                        <option value="">All Roles</option>
                        <option>Super Admin</option>
                        <option>Attendance Admin</option>
                        <option>Pharmacist</option>
                        <option>Doctor</option>
                        <option>Surgeons</option>
                        <option>Pathologist</option>
                        <option>Ambulance Driver</option>
                        <option>Physician Assistants</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <input type="text" class="form-control" id="keywordSearch" placeholder="Search by name, role, etc...">
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-danger w-100" id="searchBtn" type="button"><i class="fa fa-search me-1"></i>Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body p-3">
            <ul class="nav nav-tabs mb-3" id="staffTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="card-tab" data-bs-toggle="tab" data-bs-target="#cardView" type="button" role="tab">Card View</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#listView" type="button" role="tab">List View</button>
                </li>
            </ul>
            <div class="tab-content" id="staffTabContent">
                <div class="tab-pane fade show active" id="cardView" role="tabpanel">
                    <div class="row g-3">
                        <!-- Static Card 1 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle mb-2 staff-avatar" alt="Dr Saurabh Sakhuja" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Dr Saurabh Sakhuja</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Super Admin</span></div>
                                    <div class="mb-2">
                                        <span class="d-block small"><i class="fa fa-phone me-1"></i>938657083</span>
                                        <span class="d-block small"><i class="fa fa-phone me-1"></i>7070707070</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 2 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle mb-2 staff-avatar" alt="Dr Parth Akbari" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Dr Parth Akbari</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Attendance Admin</span></div>
                                    <div class="mb-2">
                                        <span class="d-block small"><i class="fa fa-phone me-1"></i>9425689523</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">OT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 3 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle mb-2 staff-avatar" alt="Dr yash Rank" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Dr yash Rank</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Pharmacist</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">OT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 4 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle mb-2 staff-avatar" alt="Dr Ishit Test" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Dr Ishit Test</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Pathologist</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">Cardiology</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 5 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle mb-2 staff-avatar" alt="Dr Ikshit" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Dr Ikshit</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Doctor</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">Cardiology</span>
                                        <span class="badge bg-primary">Surgeons</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 6 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle mb-2 staff-avatar" alt="Dr Mohan" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Dr Mohan</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Pharmacist</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">OT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 7 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle mb-2 staff-avatar" alt="Dr Mamta Rani" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Dr Mamta Rani</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Doctor</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">OT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 8 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle mb-2 staff-avatar" alt="Rohan" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Rohan</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Ambulance Driver</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">OT</span>
                                        <span class="badge bg-primary">Physician Assistants</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 9 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle mb-2 staff-avatar" alt="Ramesh" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Ramesh</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Doctor</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">Emergency Department (ED)</span>
                                        <span class="badge bg-primary">Surgeons</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Static Card 10 -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100 staff-card">
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    <img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle mb-2 staff-avatar" alt="Manish" width="80" height="80">
                                    <h5 class="fw-bold mb-1">Manish</h5>
                                    <div class="text-muted small mb-1">Sakhuja Hospital</div>
                                    <div class="mb-1"><span class="badge bg-info">Pharmacist</span></div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge bg-secondary">Psychiatry</span>
                                        <span class="badge bg-primary">Surgeons</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="listView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="staff-list-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
<style>
.staff-card { transition: box-shadow .2s; }
.staff-card:hover { box-shadow: 0 0 0 4px #e3f2fd; }
.staff-avatar { object-fit: cover; border: 2px solid #e3e3e3; }
</style>
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
<script>
var liveconsultationTable = $('#staff-list-table').DataTable({
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
        searchPlaceholder: 'Search Leaves...'
      },
      lengthChange: true,
      paging: true,
      info: true,
      ordering: true,
      scrollX: true,
      autoWidth: true,
      responsive: true
    });
    $(document).ready(function() {
        // Trigger adjustment after tab is shown
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
            setTimeout(() => {
                $('#staff-list-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        });

        // Also adjust on initial page load
        setTimeout(() => {
            $('#staff-list-table').DataTable().columns.adjust().responsive.recalc();
        }, 200);
    });
</script>
    @endpush
