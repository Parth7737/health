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
                           <a href="{{route('sec.dashboard')}}" class="menu-link bottom-menu-icons">
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
               @include('sec.secnav')
            </div>
         </div>
      </div>
      <div class="row g-6">
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100 filter-card" data-status="1">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$done}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Done</h6>

                  </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100 filter-card" data-status="0">
                  <div class="card-body">
                     <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$pending}}</h4>
                     </div>
                     <h6 class="mb-0 fw-normal">Pending</h6>
                  </div>
            </div>
         </div>
      </div>
   </div>
   <input type="hidden" id="statusFilter">
   <div class="bg-white rounded-3 box-shadow p-5 mt-5">
        <div class="card">
            <div class="card-header">
                <div class="selectedstatus theme-color">Done</div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <label for="" class="control-label">Year</label>
                        <select name="year" id="year" class="select2 year">
                            @foreach($years as $key => $value)
                                <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table" id="hospitalTable">
                    <thead>                      
                        <tr>
                            <th>SR.No</th>
                            <th>Facility/Reference ID</th>
                            <th>Facility Name</th>
                            <th>Specialities Selected</th>
                            <th>Submission Date</th>
                            <th>Annual Declaration Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        
                    </tbody>
                    </table>
                </div>
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
         url: "{{ route('sec.getAnnualData') }}",
         type: "POST",
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         data: function (d) {
            let status = $("#statusFilter").val();
            let year = $("#year").val();
            d.status = status || 1; // Pass the selected status as a parameter
            d.year = year;
         }
      },
      columns: [
         { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
         { data: 'hospital_id', name: 'hospital_id' },
         { data: 'hospital.facility_name', name: 'hospital.facility_name' },
         { data: 'specialities', name: 'specialities', orderable: false, searchable: false },
         { data: 'hospital.status_update_date', name: 'hospital.status_update_date' },
         { data: 'submitted_date', name: 'submitted_date' },
         { data: 'hospital.status', name: 'hospital.status' },
      ]
   });

   // Event handler for filter card clicks
    $('.filter-card').on('click', function () {
        var status = $(this).data('status'); // Get the selected status
        if(status == 0) {
            $('.selectedstatus').text('Pending');
        } else {
            $('.selectedstatus').text('Done');
        }
        $("#statusFilter").val(status);
        table.ajax.reload(); // Reload the DataTable with the new filter
    });

    $('.year').on('change', function () {
        var status = $('.year').val();
        table.ajax.reload();
    });
});

</script>
@endpush