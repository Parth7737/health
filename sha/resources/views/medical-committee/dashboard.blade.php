@extends('layouts.medical-committee.app')
@section('title','Dashboard | Hospital Engagement Module')
@section('content')

@php
    use \App\Models\PreauthRegister;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="bg-white rounded-3 box-shadow p-5">
        <div class="row g-6 mb-5">
            <div class="col-sm-12 col-lg-3">
                <p class="mb-1">Hello, <span class="theme-color">{{auth()->user()->name}}</span></p>
                <div class="d-flex ">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('medical-committee.case-search') }}" class="btn btn-outline-primary me-2"><i class="ri-user-search-line"></i> Case Search</a>
                </div>
            </div>
        </div>
        <div class="row g-6">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i
                                        class="ri-calendar-schedule-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $pending_total }}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauthorization Pending</h6>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i
                                        class="ri-contract-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $approved_total }}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauthorization Approved</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i
                                        class="ri-file-close-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $rejected_total }}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauthorization Rejected</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-info"><i
                                        class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $query_total }}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauthorization Queried</h6>
                    </div>
                </div>
            </div>
            <!--/ Card Border Shadow -->
        </div>
    </div>
    <div class="bg-white rounded-3 box-shadow p-5 mt-5">
        <div class="row justify-content-end mb-3 mt-0">
            <div class="col-md-3">
                <div class="d-flex flex-row align-items-center">
                    <p class="flex-shrink-0 mb-0">Rows Per page</p> 
                    <select class="form-control ms-3" id="rows_per_page">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row gx-6 mt-0">
            <div class="col-md-3">
                <div class="card mb-6">
                    <div class="card-body p-0 demo-vertical-spacing demo-only-element">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select select2" id="status"
                                aria-label="Floating label select example">
                                <option value="{{PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING}}" selected>Preauthorization Pending</option>
                                <option value="{{PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED}}">Preauthorization Approved</option>
                                <option value="{{PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED}}">Preauthorization Rejected</option>
                                <option value="{{PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED}}">Preauthorization Queried</option>
                            </select>
                            <label for="floatingSelect">Cases Status</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7"></div>
            <div class="col-md-2">
                <div class="d-flex justify-content-end flex-wrap align-items-center">
                    <div class="grid-toggle ms-4">
                        <button class="l-toggle list-view-btn active" ><i
                                class="ri-list-check-2"></i></button>
                        <button class="l-toggle  grid-view-btn" ><i
                                class="ri-layout-grid-fill"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="users"></div>
        <div class="pagination-controls mt-2"></div>
    </div>

</div>
@endsection
@push('scripts')
<script>
    function generate_dt(url = "{{ route('medical-committee.dashboard-users') }}") {
        var length = $("#rows_per_page").val();
        var status = $("#status").val();
        var list_view = $(".list-view-btn.active").length;
        var date = $(".date").val();
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: { length: length, status: status,list_view:list_view,date:date },
            success: function (res) {
                if (res.html) {
                    $(".users").html(res.html);
                    $(".pagination-controls").html(res.pagination);

                    // Attach click event to new pagination links
                    $(".pagination-controls a").on("click", function (e) {
                        e.preventDefault();
                        var newUrl = $(this).attr("href");
                        generate_dt(newUrl);
                    });
                } else {
                    $(".users").html("<div class='text-center'>No data available.</div>");
                }
            },
            error: function (xhr, status, error) {
                errorMessage("Failed to fetch data. Please try again later.");
            }
        });
    }

    $(document).ready(function () {
        generate_dt();
        $("#rows_per_page, #status,.date").on('change', function () {
            generate_dt();
        });
    });
    function getStatusData(status){
        $("#status").val(status).change();
    }

</script>

@endpush