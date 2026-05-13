@extends('layouts.aco.app')
@section('title','Case Search')
@section('content')
@php
    use App\Models\PreauthRegister;
@endphp
<div class="p-4 flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Case Search</li>
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
                            <div
                                class="form-floating form-floating-outline mb-3">
                                <select class="form-select select2" id="status" name="status">
                                    <option value=""></option>
                                    <option value="{{PreauthRegister::STATUS_CLAIM_APPROVED}}">Claims Pending</option>
                                    <option value="{{ PreauthRegister::STATUS_ACO_CLAIM_APPROVED }}">Claims Forwarded</option>
                                    <option value="{{PreauthRegister::STATUS_ACO_CLAIM_REJECTED}}">Claims Rejected</option>
                                    <option value="{{ PreauthRegister::STATUS_ACO_CLAIM_QUERIED }}">Claims Queried To CPD</option>
                                    <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED}}">Erroneous Claims Pending</option>
                                    <option value="{{PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED}}">Erroneous Claims Forwarded</option>
                                    <option value="{{PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED}}">Erroneous Claims Rejected</option>
                                    <option value="{{PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED}}">Erroneous Claims Queried To CPD</option>
                                    <option value="{{PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK}}">Payment Rejected</option>
                                    <option value="{{PreauthRegister::STATUS_SHA_CLAIM_APPROVED}}">Claims Approved By SHA</option>
                                    <option value="{{PreauthRegister::STATUS_CLAIM_SENT_TO_BANK}}">Claim Sent to Bank</option>
                                    <option value="{{PreauthRegister::STATUS_CLAIM_PAID_BY_BANK}}">Claims Paid By Bank</option>
                                </select>
                                <label for="case_status">Case Status</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="bs-rangepicker-basic" name="date" class="form-control date" />
                                    <label for="bs-rangepicker-basic">Date Range</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="case_id" name="case_id" class="form-control" />
                                    <label for="case_id">Registration ID / Program ID / Case ID</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex justify-content-first mb-3">
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
                        <th>Patient Name</th>
                        <th>Case ID</th>
                        <th>Hospital Name</th>
                        <th>Submission Date</th>
                        <th>Status</th>
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
    // generate_dt();
    let dt_ajax;
    function generate_dt(){
        var scheme_type_id = $("#scheme_type_id").val();
        var date = $("#bs-rangepicker-basic").val();
        var status = $("#status").val();
        var case_id = $("#case_id").val();

        if (!scheme_type_id && !date && !status && !case_id) {
            errorMessage("Please select at least one search filter (Scheme, Case Status, Date Range, or Case ID).");
            return;
        }

        if ($.fn.DataTable.isDataTable('.datatables-ajax')) {
            $('.datatables-ajax').DataTable().destroy();
        }

        dt_ajax = $('.datatables-ajax').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('aco.loadcasesearch') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function(d) {
                    d.case_id = case_id;
                    d.date = date;
                    d.status = status;
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
                { data: 'patient_name', name: 'patient_name', orderable: false, searchable: false },
                { data: 'register_id', name: 'register_id'},
                { data: 'hospital.facility_name', name: 'hospital.facility_name', orderable: false, searchable: false },
                { data: 'submission_date', name: 'submission_date', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                
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
                            columns: [1,2,3,4,5],
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
                            columns: [1,2,3,4,5],
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
                            columns: [1,2,3,4,5],
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
        $("#status").val("").trigger('change'); 
        $("#bs-rangepicker-basic").val(""); 
        $("#case_id").val(""); 
        $("#scheme_type_id").val("").trigger('change'); 
        if ($.fn.DataTable.isDataTable('.datatables-ajax')) {
            $('.datatables-ajax tbody').empty();
            $('.dataTables_info').remove();
            $('.dataTables_paginate').remove();
            $('.dataTables_length').remove();
            $('.dataTables_filter').remove();
            $('.dt-action-buttons').remove();
        }
    })
</script>
@endpush