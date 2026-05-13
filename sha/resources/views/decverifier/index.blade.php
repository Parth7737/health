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
      <div class="row">
         <div class="card mb-6 border border-primary">
               <div class="card-header">New verification</div>
               <div class="card-body">               
                     <div class="row g-5">                    
                        <!-- <div class="col-sm-3">
                           <label class="mb-3">Facility/Registry Id</label>
                           <input type="text" class="form-control aerrormesage filter" id="facility_id" name="facility_id" >
                        </div> -->
                        <div class="col-sm-3">
                           <label class="mb-3">Facility Name</label>
                           <input type="text" class="form-control aerrormesage filter" id="facility_name" name="facility_name" >
                        </div>
                        <div class="col-sm-3">
                           <label class="mb-3">Due Date</label>
                           <input type="text" class="form-control datepicker aerrormesage filter" id="due_date" placeholder="YYYY-MM-DD"  name="due_date" >
                        </div>
                        <div class="col-sm-3">
                           <label class="mb-3">Status</label>
                           <select name="status" id="status" class="select2 form-select filter form-select-lg aerrormesage" data-allow-clear="true" required >
                              <option value="">Select</option>
                              <option value="Physical Verification Pending">Physical Verification Pending</option>
                              <option value="Physical Verification Completed">Physical Verification Completed</option>
                           </select>
                        </div>
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
                     <th>Facility/Reference ID</th>
                     <th>Facility Name</th>
                     <th>Due Date</th>
                     <!-- <th>Specialities</th> -->
                     <th>Status Updated Date</th>
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
  

   // Event handler for filter card clicks
   $('.filter').on('change', function () {
      table.ajax.reload(); // Reload the DataTable with the new filter
   });
 
   $('.datepicker').daterangepicker({
      singleDatePicker: true,
      autoApply: false,
      autoUpdateInput: false,
      // maxDate: moment(), // Restrict to past dates
      locale: {
         format: 'YYYY-MM-DD'
      },
      opens: 'right'
   });

   $('.datepicker').val('');

   $('.datepicker').on('apply.daterangepicker', function (ev, picker) {
         $(this).val(picker.startDate.format('YYYY-MM-DD'));
         table.ajax.reload();
   });

   let table = $('#hospitalTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
         url: "{{ route('decverifier.getData') }}",
         type: "GET",
         data: function (d) {
            let status = $("#statusFilter").val();
            d.status = status || null; // Pass the selected status as a parameter
            d.due_date = $("#due_date").val() || null;
            d.facility_id = $("#facility_id").val() || null;
            d.facility_name = $("#facility_name").val() || null;
            d.status = $("#status").val() || null;
         }
      },
      columns: [
         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
         { data: 'hospital_id', name: 'hospital_id' },
         { data: 'facility_name', name: 'facility_name' },
         { data: 'due_date_of_physical_verification', name: 'due_date_of_physical_verification' },
         // { data: 'specialities', name: 'specialities', orderable: false, searchable: false },
         { data: 'updated_at', name: 'updated_at' },
         { data: 'status', name: 'status' },
         { data: 'action', name: 'action', orderable: false, searchable: false },
      ]
   });
});

</script>
@endpush