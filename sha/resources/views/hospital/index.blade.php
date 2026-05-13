@extends('layouts.hospital.app')
@section('title','Dashboard | Hospital Engagement Module')
@section('content')
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('hospital.dashboard')}}" class="menu-link bottom-menu-icons">
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
            @include('hospital.nav')
            <div class="col-sm-12 col-lg-3">
               <p class="mb-1">Hello, <span class="theme-color">{{auth()->user()->name}}</span></p>
               <div class="d-flex ">
                  <h6 class="mb-0 mb-md-0">Your Applications!</h6>
               </div>
            </div>
            <div class="col-sm-12 col-lg-9">
               @if(empty(auth()->user()->hospital_id))
               <div class="  d-flex justify-content-end align-items-center ms-5">
                  <a href="{{route('hospital.empanelmentRegistration.create')}}" class="btn btn-primary waves-effect waves-light text-white">New Empanelment</a>
               </div>
               @endif
            </div>
         </div>
         <div class="row g-6">
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-primary h-100 filter-card" data-status="Draft">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-draft-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Draft')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Draft</h6>

                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-info h-100 filter-card" data-status="Submitted">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-info"><i class="ri-save-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Submitted')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Submitted</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-warning h-100 filter-card" data-status="Queried">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Queried')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Queried</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-info h-100 filter-card" data-status="Query Replied">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-info"><i class="ri-reply-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Query Replied')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Query Replied</h6>
                     </div>
               </div>
            </div>
           
           
           
            <!--/ Card Border Shadow -->
         </div>
         <div class="row mt-3 g-6 toggle-div" style="display: none;">
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-success h-100 filter-card" data-status="Empanelment Recommended by DEC">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-emphasis ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Empanelment Recommended by DEC')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Empanelment Recommended by DEC</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-success h-100 filter-card" data-status="Approved Upgradation Request">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-check-double-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Approved Upgradation Request')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Approved Upgradation Request</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-success h-100 filter-card" data-status="Empanelled">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-emphasis ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Empanelled')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Empanelled</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-danger h-100 filter-card" data-status="Rejected">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-file-close-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Rejected')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Rejected</h6>

                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-primary h-100 filter-card" data-status="Upgradation Request">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-refresh-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Upgradation Request')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Upgradation Request</h6>

                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-info h-100 filter-card" data-status="Withdrawn">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-info"><i class="ri-file-close-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Withdrawn')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Withdrawn</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-warning h-100 filter-card" data-status="In-Active">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-stop-circle-fill ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'In-Active')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">In-Active</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-success h-100 filter-card" data-status="Re-Empanelled">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-emphasis ri-24px"></i></span></div>

                           <h4 class="mb-0">{{\App\CentralLogics\Helpers::getCount(auth()->user()->id, 'Re-Empanelled')}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Re-Empanelled</h6>
                     </div>
               </div>
            </div>
            <!--/ Card Border Shadow -->
         </div>
         <div class="row justify-content-end">
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
                     <th>Hospital ID</th>
                     <th>Facility Name</th>
                     <th>Facility Type</th>
                     <!-- <th>Specialities</th> -->
                     <th>Scheme Type</th>
                     <th>Status</th>
                     <th>Remarks</th>
                     <th>Submitted Date</th>
                     <th class="border-1">Action</th>
                  </tr>
               </thead>
               <tbody class="table-border-bottom-0">
                 
               </tbody>
            </table>
         </div>
      </div>
   </div>


   <div class="bg-white rounded-3 box-shadow p-5 mt-5">
      <div class="card">
         <div class="card-header"><h4>Notification <i class="ri-notification-2-line ri-22px"></i><h4></div>
         <div class="card-body">
            <div class="table-responsive text-nowrap">
               <table class="table" id="notificationTable">
                  <thead>                      
                     <tr>
                        <th>SR.No</th>
                        <th>Hospital Name</th>
                        <th>Remarks</th>
                        <th>Expiry Date</th>
                        <th class="border-1">Action</th>
                     </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                     @foreach($ExpiredDocument as $key => $value)
                     <tr id="exdocid{{$value->id}}">
                        <td>{{$loop->iteration}}</td>
                        <td>{{@$value->hospital->facility_name}}</td>
                        <td>{{@$value->notifications->message}}</td>
                        <td>{{@$value->expiry_date}}</td>
                        <td><a href="javascript:;" onclick="opendocumentdialog('{{$value->id}}');" class="btn btn-primary btn-sm">></a></td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title mb-4 text-white" id="documentModal">Update Expire Document</h4>
                <button type="button" class="btn-close mb-4 text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0" id="documentmodalbody">
                
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
         url: "{{ route('hospital.getData') }}",
         type: "POST",
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         data: function (d) {
            let status = $("#statusFilter").val();
            d.status = status || 'Draft'; // Pass the selected status as a parameter
         }
      },
      columns: [
         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
         { data: 'hospital_id', name: 'hospital_id' },
         { data: 'facility_name', name: 'facility_name' },
         { data: 'facility_type', name: 'facility_type' },
         // { data: 'specialities', name: 'specialities', orderable: false, searchable: false },
         { data: 'scheme', name: 'scheme' },
         { data: 'status', name: 'status' },
         { data: 'remark', name: 'remark' },
         { data: 'created_at', name: 'created_at' },
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

function opendocumentdialog(id) {
   ldrshow();
   $.ajax({
      url: '{{route("hospital.singleDocument")}}', 
      type: 'POST',
      data: {
         '_token': '{{csrf_token()}}',
         'id' : id
      },
      success: function (data) {
         // Hide loader (if ldrhide() is implemented)
         ldrhide();
        
         // Populate the content of the step
         $("#documentmodalbody").html(data.html || data);
         $('#documentModal').modal('show');
         loadSelect2();
         
      },
      error: function (xhr, status, error) {
         ldrhide(); // Hide loader on error
         console.error("Error loading step:", error);
         alert("Failed to load the step. Please try again.");
      }
   });
}

</script>
@endpush