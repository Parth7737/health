@extends('layouts.hospital.app')
@section('title','Dashboard | Updrade Application')
@section('content')

@include('hospital.base.topheader')
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
        @include('hospital.base.topbarmenu')
        
        <div class="row g-6">
            @include('hospital.base.basicdetails')
            <div class="bg-white rounded-3 box-shadow p-5 mt-5">
                <div class="card">
                    <div class="card-header"><h4>WorkFlow History<h4></div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                        <table class="table" id="workFlowList">
                            <thead>                      
                                <tr>
                                    <th>SR.No</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                    <th>Attachment</th>
                                    <th>Remarks</th>
                                    <th>Date & Time</th>
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
   </div>
</div>
@endsection

@push('scripts')
    <script>
         $(document).ready(function () {
            let table = $('#workFlowList').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('hospital.getWorkFlowData', [base64_encode($hospital->id), base64_encode($hospital->uuid)]) }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: function (d) {
                        let status = null;
                        d.status = status || null; // Pass the selected status as a parameter
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
                    { data: 'facility_name', name: 'facility_name' },
                    { data: 'action', name: 'action' },
                    { data: 'attachment', name: 'attachment' },
                    { data: 'remark', name: 'remark' },
                    { data: 'created_at', name: 'created_at' },
                ]
            });
        });
    </script>
@endpush
