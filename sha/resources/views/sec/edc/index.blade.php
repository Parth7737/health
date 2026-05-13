@extends('layouts.dec.app')
@section('title','Dashboard | EDC')
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
                <div class="d-flex justify-content-end mt-3">
                    <a class="btn btn-info rounded-0" href="{{route('sec.initiate.actionlist')}}">INITIATE ACTION</a>
                </div>
            </div>
         </div>
      </div>
        <div class="row g-6">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100 filter-card" data-status="Initiate General Communication">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h4 class="mb-0">{{$generalcommunication}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">General Communication</h6>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100 filter-card" data-status="Initiate Show Cause Notice">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h4 class="mb-0">{{$shaucause}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Show Caused</h6>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="Initiate Blacklist">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$blacklist}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">BlackList</h6>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="FIR">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$fir}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">FIR</h6>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="Stop Preauth">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$stopPreauth}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Stop Preauth</h6>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="Stop Payment">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$stopPayment}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Stop Payment</h6>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="De-Empanelled">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$deempanelled}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">De-Empanelled</h6>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="Suspended Facility">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$suspendfacility}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Suspended Facility</h6>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="Revoked">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$revoked}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Revoke Request</h6>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100 filter-card" data-status="Initiate Penalty">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0">{{$penalty}}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Penalised</h6>
                    </div>
                </div>
            </div>
        </div>
   </div>
   <input type="hidden" id="statusFilter">
   <div class="bg-white rounded-3 box-shadow p-5 mt-5">
        <div class="card">
            <div class="card-header">
                <div class="selectedstatus theme-color">General Communication</div>
                <hr>
                
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table" id="hospitalTable">
                        <thead>                      
                            <tr>
                                <th>SR.No</th>
                                <th>Order ID</th>
                                <th>Facility ID</th>
                                <th>Ownership Type</th>
                                <th>Facility Name</th>
                                <th>District</th>
                                <th>Status</th>
                                <th>Date of Issuance</th>
                                <!-- <th>Due Date</th> -->
                                <th>Submission Date</th>
                                <th>Details</th>
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
            url: '{{route("sec.edcactiondata")}}',
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: function (d) {
                let status = $("#statusFilter").val();
                d.status = status || 'Initiate General Communication';
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
            { data: 'order_id', name: 'order_id' },
            { data: 'hospital.hospital_id', name: 'hospital.hospital_id' },
            { data: 'facility_ownership_type', name: 'facility_ownership_type' },
            { data: 'hospital.facility_name', name: 'hospital.facility_name' },
            { data: 'district', name: 'district' },
            { data: 'status', name: 'status' },
            { data: 'date_of_issuance', name: 'date_of_issuance' },
            { data: 'submission_date', name: 'submission_date' },
            { data: 'action', name: 'action' },
        ]
    });

   // Event handler for filter card clicks
    $('.filter-card').on('click', function () {
        var status = $(this).data('status'); // Get the selected status
        if(status == "Initiate General Communication") {
            $('.selectedstatus').text("General Communication");
        } else if(status == "Initiate Show Cause Notice") {
            $('.selectedstatus').text("Show Cause");
        } else if(status == "Initiate Blacklist") { 
            $('.selectedstatus').text("Blacklist");
        } else if(status == "Initiate Penalty") { 
            $('.selectedstatus').text("Penalty");
        } else {
            $(".selectedstatus").text(status);
        }
        $("#statusFilter").val(status);
        table.ajax.reload(); // Reload the DataTable with the new filter
    });

});

</script>
@endpush