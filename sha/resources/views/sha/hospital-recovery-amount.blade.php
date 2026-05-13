@extends('layouts.sha.app')
@section('title','Hospital Recovery Amount')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Hospital Recovery Amount</li>
        </ol>
    </nav>
    <div class="row">
        <div class="bs-stepper-content">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('sha.dashboard') }}" class="btn btn-outline-primary">Back</a>
            </div>
            <div class="card mb-6 ps-0 border border-primary">
                <div class="card-body">
                    <div class="row row-cols-5">
                        <div class="col-md-6 col-lg-3">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="text" id="hospital_code" name="hospital_code" oninput="sanitize(this, 'm','20');" class="form-control" placeholder="" />
                                <label for="hospital_code">Hospital Code</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                <select class="form-select select2" id="hospital_id" name="hospital_id">
                                    <option value=""></option>
                                    @foreach($hospitals as $hospital)
                                        <option value="{{ $hospital->id }}">{{ $hospital->facility_name }}</option>
                                    @endforeach
                                </select>
                                <label for="diabetes">Hospitals</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                @php $facility_ownership_types = App\CentralLogics\Helpers::getCommanData('FacilityOwnershipType'); @endphp
                                <select class="form-select select2" id="facility_ownership_type" name="facility_ownership_type">
                                    <option value=""></option>
                                    @foreach($facility_ownership_types as $facility_ownership_type)
                                        <option value="{{ $facility_ownership_type->id }}">{{ $facility_ownership_type->name }}</option>
                                    @endforeach
                                </select>
                                <label for="diabetes">Type</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                @php $districts = App\CentralLogics\Helpers::getCommanData('HospitalDistrict'); @endphp
                                <select class="form-select select2" id="district_id" name="district_id">
                                    <option value=""></option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                <label for="diabetes">District</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                @php $scheme_types = App\CentralLogics\Helpers::getCommanData('SchemeType'); @endphp
                                <select class="form-select select2" id="scheme_type_id" name="scheme_type_id">
                                    <option value=""></option>
                                    @foreach($scheme_types as $scheme_type)
                                        <option value="{{ $scheme_type->id }}">{{ $scheme_type->name }}</option>
                                    @endforeach
                                </select>
                                <label for="diabetes">Scheme Type</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                <select class="form-select select2" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="0">Requested</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Rejected</option>
                                    <option value="3">Completed</option>
                                </select>
                                <label for="diabetes">Status</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-center mb-3">
                                <button class="btn btn-outline-primary me-2" id="search"><i class="ri-search-line"></i> Search</button>
                                <button class="btn btn-outline-info"><i class="ri-loop-right-line"></i> Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-datatable text-nowrap">
                  <table class="datatables-ajax table table-bordered">
                    <thead>
                      <tr>
                        <th>SR No.</th>
                        <th>Hospital Code</th>
                        <th>Name</th>
                        <th>Requested Date</th>
                        <th>Recovery Amount</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Attachment</th>
                        <th>Stop Payment</th>
                        <th>Action</th>
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
    function generate_dt(){
        var hospital_code = $("#hospital_code").val();
        var hospital_id = $("#hospital_id").val();
        var facility_ownership_type = $("#facility_ownership_type").val();
        var district_id = $("#district_id").val();
        var scheme_type_id = $("#scheme_type_id").val();
        var status = $("#status").val();

        var dt_ajax_table = $('.datatables-ajax');

        // Ensure table exists
        if (!dt_ajax_table.length) {
            console.error("Table element not found!");
            return;
        }

        // Check if DataTable is already initialized before destroying
        if ($.fn.DataTable.isDataTable(dt_ajax_table)) {
            dt_ajax_table.DataTable().destroy();
        }

        var dt_ajax = dt_ajax_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sha.search-recovery-hospitals') }}",
                type: "GET",
                data: function(d) {
                    d.hospital_code = hospital_code;
                    d.hospital_id = hospital_id;
                    d.facility_ownership_type = facility_ownership_type;
                    d.district_id = district_id;
                    d.scheme_type_id = scheme_type_id;
                    d.status = status;
                }
            },
            columns: [
                { 
                    data: null, 
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'hospital_code', name: 'hospital_code' },
                { data: 'facility_name', name: 'facility_name', orderable: false, searchable: false },
                { data: 'requested_date', name: 'requested_date' },
                { data: 'recovery_amount', name: 'recovery_amount' },
                { data: 'status', name: 'status' },
                { data: 'remarks', name: 'remarks' },
                { data: 'recovery_supporting_doc', name: 'recovery_supporting_doc' },
                { data: 'is_payment_stop', name: 'is_payment_stop' },
                { data: 'action', name: 'action' },
            ],
            columnDefs: [
                { 
                    targets: 1, // Facility Name Column
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
    }

    // Bind search button click
    $("#search").on("click", function(){
        generate_dt();
    });
    function updateStatus(id,status){
        status_label = (status==1)?'Approve':'Reject';
        swal({
            title: "Are you sure?",
            text: status_label+" this request.",
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
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("sha.update-recovery-status")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: {
                        id:id,status:status
                    },
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
                            let errorMessages = [];
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }else{
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                                errorMessages.push(errors[field][0]);
                            }
                            if (errorMessages.length > 0) {
                                errorMessage(errorMessages.join('<br>'));
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    }
</script>
@endpush