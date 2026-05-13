@extends('layouts.dec.app')
@section('title','Dashboard | DEC Verifier Approver')
@section('content')
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('decverifier.dashboard')}}" class="menu-link bottom-menu-icons">
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
                           <a href="{{route('decverifier.dashboard')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'decverifier.dashboard' ? 'bg-primary text-white' : '' }} ">
                              Dashboard                                                  
                           </a>
                     </li>  
                     <li class="nav-item">
                           <a href="{{route('decverifier.worklist')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'decverifier.worklist' ? 'bg-primary text-white' : '' }}">
                           Worklist                                                  
                           </a>
                     </li>  
                     <li class="nav-item">
                           <a href="#" class="nav-link btn-outline-primary">
                           EDC                                                  
                           </a>
                     </li> 
                     <li class="nav-item">
                           <a href="#" class="nav-link btn-outline-primary">
                           Annual Declartion                                                  
                           </a>
                     </li>                                               
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <div class="row g-6">
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100 filter-card" data-status="Empanelled">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
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
                        <h4 class="mb-0">{{$submitted}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Pending DEC</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-danger h-100 filter-card" data-status="Queried">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$queried}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Queried</h6>
                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-info h-100 filter-card" data-status="Upgradation Request">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$upgradation}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Upgradation Request</h6>
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
         url: "{{ route('decverifier.getData') }}",
         type: "POST",
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         data: function (d) {
            let status = $("#statusFilter").val();
            d.status = status || null; // Pass the selected status as a parameter
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