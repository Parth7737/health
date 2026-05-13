@extends('layouts.dec.app')
@section('title','Dashboard | Initiate EDC')
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
   </div>
   <input type="hidden" id="statusFilter">
   <div class="bg-white rounded-3 box-shadow p-5 mt-5">
        <div class="card">
            <div class="card-header">
                <div class="selectedstatus theme-color">Initiate Action</div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <label for="" class="control-label">District</label>
                        <select name="district" id="district" class="select2 district filter">
                            <option value="">Select</option>
                            @foreach($districts as $key => $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="" class="control-label">Ownership Type</label>
                        <select name="ownership_type " id="ownership_type" class="select2 ownership_type filter">
                           <option value="">Select</option>
                           @foreach($ownershiptype as $key => $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-primary resetfilter mt-6" type="button">Reset</button>
                    </div>
                </div>                
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table" id="hospitalTable">
                    <thead>                      
                        <tr>
                            <th>SR.No</th>
                            <th>Facility Id</th>
                            <th>Facility Name</th>
                            <th>Ownership Type</th>
                            <th>District</th>
                            <th>Facility Contact</th>
                            <th>Specialities</th>
                            <th>Empanelment Status</th>
                            <!-- <th>Last Action Date</th> -->
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
            url: "{{ route('sec.initiate.hospitallist') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: function (d) {
                let ownership_type = $("#ownership_type").val();
                let district = $("#district").val();
                d.district = district; // Pass the selected status as a parameter
                d.ownership_type = ownership_type;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
            { data: 'hospital_id', name: 'hospital_id' },
            { data: 'facility_name', name: 'facility_name' },
            { data: 'ownership_type', name: 'ownership_type', orderable: false, searchable: false },
            { data: 'district_name', name: 'district_name', orderable: false, searchable: false },
            { data: 'mobile_no', name: 'mobile_no', orderable: false, searchable: false },
            { data: 'specialities', name: 'specialities', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
        ]
   });

    $('.district').on('change', function () {
        var district = $('.district').val();
        table.ajax.reload();
    });

    $('.ownership_type').on('change', function () {
        var ownership_type = $('.ownership_type').val();
        table.ajax.reload();
    });

    $('.resetfilter').on('click', function() {
        $('.filter').val('').trigger('change'); 
        $('#hospitalTable').DataTable().ajax.reload();
    });
});

</script>
@endpush