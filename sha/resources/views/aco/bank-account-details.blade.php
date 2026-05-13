@extends('layouts.aco.app')
@section('title','Hospital Bank Details')
@section('content')
<div class="p-4 flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Hospital Bank Details</li>
        </ol>
    </nav>
    <div class="row">
        <div class="bs-stepper-content">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('aco.dashboard') }}" class="btn btn-outline-primary">Back</a>
            </div>
            <div class="card mb-6 ps-0 border border-primary">
                <div class="card-body">
                    <div class="row row-cols-5">
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                @php $states = App\CentralLogics\Helpers::getCommanData('HospitalState'); @endphp
                                <select class="form-select select2" id="state" name="state">
                                    <option value=""></option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                <label for="state">Hospital State</label>
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
                                <label for="hospital_id">Hospitals</label>
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
                                <label for="scheme_type_id">Scheme</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex justify-content-center mb-3">
                                <button class="btn btn-outline-primary me-2" id="search"><i class="ri-search-line"></i> Search</button>
                                <button class="btn btn-outline-info" id="reset"><i class="ri-loop-right-line"></i> Reset</button>
                            </div>
                        </div>
                    </div>
                   
                </div>
                <div class="card-datatable table-responsive pt-0">
                  <table class="datatables-ajax table table-bordered">
                    <thead>
                      <tr>
                        <th>SR No.</th>
                        <th>Hospital</th>
                        <th>Hospital Id</th>
                        <th>Hospital Type</th>
                        <th>Account No.</th>
                        <th>Account Name</th>
                        <th>IFSC Code</th>
                        <th>Bank Name</th>
                        <th>Bank Branch</th>
                        <th>Pan No</th>
                        <th>TDS %</th>
                        <th>RF %</th>
                        <th>Hospital %</th>
                        <th>Scheme Id</th>
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
    generate_dt();
    function generate_dt(){
        var hospital_id = $("#hospital_id").val();
        var state = $("#state").val();
        var scheme_type_id = $("#scheme_type_id").val();

        var dt_ajax_table = $('.datatables-ajax');
        dt_ajax_table.DataTable().destroy(); // Destroy existing DataTable instance

        var dt_ajax = dt_ajax_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('aco.loadbankdetails') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function(d) {
                    d.hospital_id = hospital_id;
                    d.state = state;
                    d.scheme_type_id = scheme_type_id;
                }
            },
            columns: [
                { 
                    data: null, 
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }, orderable: false, searchable: false
                },
                { data: 'facility_name', name: 'facility_name'},
                { data: 'hospital_id', name: 'hospital_id'},
                { data: 'facility_ownership_type', name: 'facility_ownership_type', orderable: false, searchable: false },
                { data: 'financial_information.account_no', name: 'financial_information.account_no', orderable: false, searchable: false },
                { data: 'financial_information.account_holder', name: 'financial_information.account_holder', orderable: false, searchable: false },
                { data: 'financial_information.ifsc_code', name: 'financial_information.ifsc_code', orderable: false, searchable: false },
                { data: 'financial_information.bank_name', name: 'financial_information.bank_name', orderable: false, searchable: false },
                { data: 'financial_information.bank_branch_name', name: 'financial_information.bank_branch_name', orderable: false, searchable: false },
                { data: 'tax_details.pan_no', name: 'tax_details.pan_no', orderable: false, searchable: false },
                { data: 'tds', name: 'tds', orderable: false, searchable: false },
                { data: 'rf', name: 'rf', orderable: false, searchable: false },
                { data: 'hospital', name: 'hospital', orderable: false, searchable: false },
                { data: 'scheme_type.name', name: 'scheme_type.name', orderable: false, searchable: false },
            ],
            dom: '<"card-header flex-column flex-md-row border-bottom"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6 mt-5 mt-md-0"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
            {
                extend: 'collection',
                className: 'btn btn-label-primary dropdown-toggle me-4 waves-effect waves-light',
                text: '<i class="ri-external-link-line me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="ri-file-text-line me-1" ></i>Csv',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [1,2,3, 4, 5, 6, 7,8,9,10,11,12,13],
                            // prevent avatar to be display
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                    if (item.classList !== undefined && item.classList.contains('user-name')) {
                                        result = result + item.lastChild.firstChild.textContent;
                                    } else if (item.innerText === undefined) {
                                        result = result + item.textContent;
                                    } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="ri-file-excel-line me-1"></i>Excel',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [1,2,3, 4, 5, 6, 7,8,9,10,11,12,13],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="ri-file-pdf-line me-1"></i>Pdf',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [1,2,3, 4, 5, 6, 7,8,9,10,11,12,13],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        }
                    },
                ]
            },
            ],
            language: {
                paginate: {
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>'
                }
            }
        });
    }

    $("#search").on("click", function(){
        generate_dt();
    });

    $("#reset").on("click", function() {
        $("#state").val("").trigger('change'); 
        $("#hospital_id").val("").trigger('change'); 
        $("#scheme_type_id").val("").trigger('change'); 
        generate_dt();
    })
</script>
@endpush