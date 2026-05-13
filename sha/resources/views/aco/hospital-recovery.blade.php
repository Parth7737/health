@extends('layouts.aco.app')
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
                <a href="{{ route('aco.hospital-recovery-amount') }}" class="btn btn-outline-primary">Back</a>
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
                    <h6 class="card-title theme-color">
                        Raise a Recovery Request
                    </h6>
                    <form onSubmit="return false" id="recoveryForm">
                        <div class="row">
                            <div class="col-md-3 mb-6 mt-7">
                                <div
                                    class="form-floating form-floating-outline">
                                    <input type="number" value="" id="recovery_amount" name="recovery_amount" class="form-control" />
                                    <label for="recovery_amount">Recovery Amount<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="formFile" class="form-label">Supporting Document</label>
                                <div class="file-upload-section">
                                    <div class="file-upload-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            height="24px"
                                            viewBox="0 -960 960 960"
                                            width="24px" fill="#6200ea">
                                            <path
                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                        </svg>
                                        <p>
                                            <strong>Browse</strong></p>
                                    </div>
                                    <input type="file" name="recovery_supporting_doc"
                                        class="file-input d-none" />
                                    <div
                                        class="uploaded-file file-upload-display d-none">
                                        <span
                                            class="file-name">Sample.pdf</span>
                                        <i class="fas fa-trash "></i>
                                        <button
                                            class="remove-file-btn bg-transparent border-0 p-0">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                height="24px"
                                                viewBox="0 -960 960 960"
                                                width="24px"
                                                fill="undefined">
                                                <path
                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-floating form-floating-outline mb-6">
                                <textarea class="form-control h-px-100" id="remarks" name="remarks" placeholder="Write remarks here..."></textarea>
                                <label for="remarks">Remarks<span class="text-danger">*</span></label>
                            </div>
                            <input type="hidden" name="hospital_id" value="{{ $hospital->id }}">
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-start">
                                    <button id="recovery-btn"
                                        class="btn btn-primary">Submit Request</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
                url: "{{ route('aco.hospital-recovery-history',[$hospital->id]) }}",
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