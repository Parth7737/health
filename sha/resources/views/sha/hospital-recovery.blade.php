@extends('layouts.sha.app')
@section('title','Hospital Recovery')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Hospital Recovery</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('sha.hospital-recovery-amount') }}" class="btn btn-outline-primary">Back</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title theme-color">Hospital Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label><strong>Hospital Code:</strong></label>
                            <p>{{ $hospital->hospital_id }}</p>
                        </div>
                        <div class="col-md-3">
                            <label><strong>Facility Name:</strong></label>
                            <p>{{ $hospital->facility_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label><strong>Ownership Type:</strong></label>
                            <p>{{ $hospital->facilityOwnershipType->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label><strong>Date of Establishment:</strong></label>
                            <p>{{ $hospital->date_of_establishment }}</p>
                        </div>
                        <div class="col-md-3">
                            <label><strong>City:</strong></label>
                            @php $city = App\Models\Village::where('id',@$hospital->hospitalAddress->village)->first(); @endphp
                            <p>{{ @$city->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label><strong>District:</strong></label>
                            @php $district = App\Models\HospitalDistrict::where('id',@$hospital->hospitalAddress->district)->first(); @endphp
                            <p>{{ @$district->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label><strong>State:</strong></label>
                            @php $state = App\Models\HospitalState::where('id',@$hospital->hospitalAddress->state)->first(); @endphp
                            <p>{{ @$state->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label><strong>Phone No:</strong></label>
                            <p>{{ @$hospital->hospitalAddress->mobile_no }}</p>
                        </div>
                    </div>
                    <div class="border-top border-3 border-primary p-1"></div>
                </div>
                <div class="card-datatable text-nowrap">
                  <table class="datatables-ajax table table-bordered">
                    <thead>
                      <tr>
                        <th>SR No.</th>
                        <th>Date</th>
                        <th>Recovery Initiate Amount</th>
                        <th>Recovered Amount</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Attachment</th>
                      </tr>
                    </thead>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $("#recovery-btn").on("click",function(){
        swal({
            title: "Are you sure?",
            text: "Recover this request.",
            type: "warning",
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
                var formData = new FormData($('#recoveryForm')[0]);
                
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("aco.recovery-request-submit")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            successMessage(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }else{
                            errorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        $(".loader-overlay").hide();
                        $('.error').remove();
                        
                        if (xhr.status === 422) { 
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                } else{
                                    $(`.${field}`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    });
    $(document).ready(function(){
        
        var dt_ajax_table = $('.datatables-ajax');
        var dt_ajax = dt_ajax_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sha.hospital-recovery-history',[$hospital->id]) }}",
                type: "GET",
            },
            columns: [
                { 
                    data: null, 
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'date', name: 'date' },
                { data: 'recovery_amount', name: 'recovery_amount' },
                { data: 'recovered_amount', name: 'recovered_amount' },
                { data: 'status', name: 'status' },
                { data: 'remarks', name: 'remarks' },
                { data: 'recovery_supporting_doc', name: 'recovery_supporting_doc' },
            ],
            columnDefs: [
                { 
                    targets: 1,
                    orderable: false, 
                    searchable: false, 
                    render: function(data, type, row) {
                        return data;
                    }
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>'
                }
            }
        });
    })
</script>
@endpush