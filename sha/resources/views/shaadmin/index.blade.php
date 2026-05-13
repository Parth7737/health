@extends('layouts.shaadmin.app')
@section('title','Dashboard | '.auth()->user()->role->name)
@section('content')
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('shaadmin.dashboard')}}" class="menu-link bottom-menu-icons">
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
            <div class="col-sm-12 col-lg-9">
               @if($isa == 0)
               <div class="  d-flex justify-content-end align-items-center ms-5">
                  <a href="@if(auth()->user()->role->name == 'ISA Admin') {{route('isaadmin.createisa')}} @else {{route('shaadmin.createisa')}} @endif" class="btn btn-primary waves-effect waves-light text-white">Create ISA USER</a>
               </div>
               @endif
            </div>
         </div>
         <div class="row g-6">
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-primary h-100 filter-card" data-status="Pending">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <!-- <div class="avatar me-4">
                                 <span class="avatar-initial rounded-3 bg-label-primary"><i
                                       class="ri-pencil-fill ri-24px"></i></span>
                           </div> -->
                           <h4 class="mb-0">{{$pending}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Pending</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-success h-100 filter-card" data-status="Approved">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <!-- <div class="avatar me-4">
                                 <span class="avatar-initial rounded-3 bg-label-warning"><i
                                       class="ri-pencil-fill ri-24px"></i></span>
                           </div> -->
                           <h4 class="mb-0">{{$approved}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Approved</h6>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-danger h-100 filter-card" data-status="Rejected">
                     <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                           <!-- <div class="avatar me-4">
                                 <span class="avatar-initial rounded-3 bg-label-warning"><i
                                       class="ri-pencil-fill ri-24px"></i></span>
                           </div> -->
                           <h4 class="mb-0">{{$reject}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Rejected</h6>
                     </div>
               </div>
            </div>
            @if(auth()->user()->role->name != "ISA Admin")
               <div class="col-sm-6 col-lg-3">
                  <div class="card card-border-shadow-promary h-100 filter-card" data-status="ISA">
                        <div class="card-body">
                           <div class="d-flex align-items-center mb-2">
                              <!-- <div class="avatar me-4">
                                    <span class="avatar-initial rounded-3 bg-label-warning"><i
                                          class="ri-pencil-fill ri-24px"></i></span>
                              </div> -->
                              <h4 class="mb-0">{{$isa}}</h4>
                           </div>
                           <h6 class="mb-0 fw-normal">ISA User</h6>
                        </div>
                  </div>
               </div>
            @endif
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
                     <th>Name</th>
                     <th>Gender</th>
                     <th>Role</th>
                     <th>Mobile No</th>
                     <th>Age</th>
                     <th>Created Date</th>
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
let table;

$(document).ready(function () {
   table = $('#hospitalTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
         url: @if(auth()->user()->role->name == "ISA Admin") "{{ route('isaadmin.getData') }}" @else "{{ route('shaadmin.getData') }}" @endif,
         type: "POST",
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         data: function (d) {
            let status = $("#statusFilter").val();
            d.status = status || 'Pending'; // Pass the selected status as a parameter
         }
      },
      columns: [
         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
         { data: 'name', name: 'name' },
         { data: 'gender', name: 'gender' },
         { data: 'role.name', name: 'role.name' },
         { data: 'mobile_no', name: 'mobile_no' },
         { data: 'age', name: 'age' },
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

function changeStatus(id, status) {
  
   swal({
      title: status + " User?",
      text: 'Are you sure you want to proceed?',
      icon: "warning",
      buttons: {
         cancel: {
            visible: true,
            text: "No, cancel!",
            className: "btn btn-danger",
         },
         confirm: {
            text: "Yes!",
            className: "btn btn-success",
         },
      },
   }).then((willDelete) => {
      if (willDelete) {  
         ldrshow();
         $.ajax({
            url: @if(auth()->user()->role->name == "ISA Admin") "{{ route('isaadmin.changeStatus') }}" @else "{{ route('shaadmin.changeStatus') }}" @endif,
            headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: { id: id, status: status },
            success: function (res) {
               ldrhide();
               if (res.success) {
                  successMessage(res.message);
                  table.ajax.reload();
               } else {
                  errorMessage(res.message);
               }
            },
            error: function (xhr, status, error) {
               ldrhide();
               errorMessage("Failed to fetch data. Please try again later.");
            }
         });
      }
   });
}


</script>
@endpush