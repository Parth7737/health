@extends('layouts.dec.app')
@section('title','Dashboard | DEC Approver')
@section('content')
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('dec.dashboard')}}" class="menu-link bottom-menu-icons">
                                 <i class="ri-home-4-line"></i>
                           </a>
                        </li>
                        <li class="menu-item">
                           <a href="javascript:void(0)" onclick="location.reload();" class="menu-link bottom-menu-icons">
                                 <i class="ri-restart-line"></i>
                           </a>
                        </li>
                     </ul>
               </div>
            </div>
            <div class="col-md-7">
            </div>
         </div>
   </div>
</aside>
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
      <div class="row g-6 mb-5">
         <div class="col-sm-12 col-lg-3">
            <p class="mb-1">Hello, <span class="theme-color">{{auth()->user()->name}}</span></p>
            <div class="d-flex ">
               <h6 class="mb-0 mb-md-0">Your Applications!</h6>
            </div>
         </div>
         <div class="card shadow-none border-0 p-0 mb-6">
            <div class="card-header p-0">
               <div class="nav-align-top">
                  <ul class="nav nav-tabs ct-tabs" role="tablist">
                     <li class="nav-item active">
                           <a href="{{route('dec.dashboard')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'dec.dashboard' ? 'bg-primary text-white' : '' }} ">
                              Dashboard                                                  
                           </a>
                     </li>  
                     <li class="nav-item">
                           <a href="{{route('dec.worklist')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'dec.worklist' ? 'bg-primary text-white' : '' }}">
                           Worklist                                                  
                           </a>
                     </li>  
                     <!-- <li class="nav-item">
                           <a href="#" class="nav-link btn-outline-primary">
                           EDC                                                  
                           </a>
                     </li> 
                     <li class="nav-item">
                           <a href="#" class="nav-link btn-outline-primary">
                           Annual Declartion                                                  
                           </a>
                     </li>                                                -->
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <div class="row g-6">
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-success h-100 filter-card" data-status="Empanelled">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-emphasis ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$empanelled}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Empanelled</h6>

                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100 filter-card" data-status="Submitted">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-calendar-schedule-fill ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$submitted}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Pending DEC</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100 filter-card" data-status="Upgradation Request">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-refresh-fill ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$upgradation}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Upgradation Request</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100 filter-card" data-status="Queried">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$queried}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Queried</h6>
                  </div>
            </div>
         </div>
      </div>
      <div class="row mt-3 g-6 toggle-div" style="display: none;">
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-info h-100 filter-card" data-status="Query Replied">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-info"><i class="ri-reply-fill ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$qryreplied}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Query Replied</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-success h-100 filter-card" data-status="Empanelment Recommended by DEC">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-emphasis ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$recommendedbydec}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Empanelment Recommended by DEC</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-danger h-100 filter-card" data-status="Empanelment Not Recommended by DEC">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-file-close-fill ri-24px"></i></span></div>
                        <h4 class="mb-0">{{$rejected}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Empanelment Not Recommended by DEC</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-success h-100 filter-card" data-status="Approved Upgradation Request">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-check-double-fill ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$approveupgradationrequest}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Approved Upgradation Request</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100 filter-card" data-status="Query On Upgradation Request From Facility">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$queryupgradationrequest}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Query On Upgradation Request From Facility</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100 filter-card" data-status="Query Raised by SEC">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                       <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span></div>

                        <h4 class="mb-0">{{$queriedbysec}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Query Raised by SEC</h6>
                  </div>
            </div>
         </div>
      </div>

      <div class="row justify-content-end mt-2">
         <div class="col-sm-6 col-lg-3">
               <div class="d-flex justify-content-end">
                  <div class="btn-group">
                     <button type="button"
                           class="btn btn-outline-primary border-0 dropdown-toggle toggle-boxes waves-effect"
                           data-bs-toggle="dropdown" aria-expanded="false">
                           View More
                     </button>
                  </div>
               </div>
         </div>
      </div>
   </div>
   <input type="hidden" id="statusFilter">
   <div class="bg-white rounded-3 box-shadow p-5 mt-5">
      <div class="card">
         <div class="table-responsive text-nowrap">
            <table class="table" id="hospitalTable">
               <thead>                      
                  <tr>
                     <th>SR.No</th>
                     <th>Facility/Reference</th>
                     <th>Facility Name</th>
                     <th>Facility Type</th>
                     <!-- <th>Specialities</th> -->
                     <th>Submission Date</th>
                     <th>Status</th>
                     <th class="border-1">Action</th>
                  </tr>
               </thead>
               <tbody class="table-border-bottom-0">
                 
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
   let table = $('#hospitalTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
         url: "{{ route('dec.getData') }}",
         type: "POST",
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         data: function (d) {
            let status = $("#statusFilter").val();
            d.status = status || 'Empanelled'; // Pass the selected status as a parameter
         }
      },
      columns: [
         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
         { data: 'hospital_id', name: 'hospital_id' },
         { data: 'facility_name', name: 'facility_name' },
         { data: 'facility_type', name: 'facility_type' },
         // { data: 'specialities', name: 'specialities', orderable: false, searchable: false },
         { data: 'updated_at', name: 'updated_at' },
         { data: 'status', name: 'status' },
         { data: 'action', name: 'action', orderable: false, searchable: false },
      ]
   });

   // Event handler for filter card clicks
   $('.filter-card').on('click', function () {
      var status = $(this).data('status'); // Get the selected status
      $("#statusFilter").val(status);
      table.ajax.reload(); // Reload the DataTable with the new filter
   });
});

</script>
@endpush