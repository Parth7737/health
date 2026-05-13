@extends('layouts.cpd.app')
@section('title','Dashboard | Hospital Engagement Module')
@section('content')

@php
    use \App\Models\PreauthRegister;
    use App\CentralLogics\Helpers;
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
                    <a href="{{ route('cpd.case-search') }}" class="btn btn-outline-primary me-2"><i class="ri-user-search-line"></i> Case Search</a>
                </div>
            </div>
        </div>
        <div class="row g-6">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CPD_CLAIM_PENDING }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i
                                        class="ri-calendar-schedule-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $pending_total }}</h4>
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CPD_CLAIM_PENDING,[],1,'preauth_approved_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Pending</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CPD_CLAIM_PENDING, 1])}}" class="float-end"><i class="ri-download-line"></i></a>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i
                                        class="ri-contract-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $approve_total }}</h4>
                            @php $conditions = array('claim_approve_reject_query_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_APPROVED,$conditions,1) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Approved</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_APPROVED, 1, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i
                                        class="ri-file-close-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $reject_total }}</h4>
                            @php $conditions = array('claim_approve_reject_query_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_REJECTED,$conditions,0,'preauth_approved_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Rejected</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_REJECTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-info"><i
                                        class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $query_total }}</h4>
                            @php $conditions = array('claim_approve_reject_query_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_QUERIED,$conditions,'preauth_approved_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Queried</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_QUERIED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            
            <!--/ Card Border Shadow -->
        </div>
        <div class="row mt-3 g-6 toggle-div" style="display: none;">
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-warning h-100 filter-card" data-status="In-Active">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING }}')">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4">
                                 <span class="avatar-initial rounded-3 bg-label-warning"><i
                                       class="ri-calendar-schedule-fill ri-24px"></i></span>
                           </div>
                            <h4 class="mb-0">{{ $erroneous_claim_pending_total }}</h4>
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING,[],1) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Pending</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING, 1])}}" class="float-end"><i class="ri-download-line"></i></a>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-primary h-100 filter-card" data-status="Re-Empanelled">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4">
                                 <span class="avatar-initial rounded-3 bg-label-primary"><i
                                       class="ri-contract-line ri-24px"></i></span>
                           </div>
                            <h4 class="mb-0">{{ $erroneous_claim_approved_total }}</h4>
                            @php 
                                $conditions = array('erroneous_claim_approve_reject_query_by'=>auth()->user()->id);
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED,$conditions,'erroneous_appoved_amount',1) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Approved</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED, 1, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-danger h-100 filter-card" data-status="Re-Empanelled">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4">
                                 <span class="avatar-initial rounded-3 bg-label-danger"><i
                                       class="ri-file-close-fill ri-24px"></i></span>
                           </div>
                            <h4 class="mb-0">{{ $erroneous_claim_rejected_total }}</h4>
                            @php 
                                $conditions = array('erroneous_claim_approve_reject_query_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Rejected</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                     </div>
               </div>
            </div>
            <div class="col-sm-6 col-lg-3">
               <div class="card card-border-shadow-info h-100 filter-card" data-status="Re-Empanelled">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                           <div class="avatar me-4">
                                 <span class="avatar-initial rounded-3 bg-label-info"><i
                                       class="ri-file-history-fill ri-24px"></i></span>
                           </div>
                            <h4 class="mb-0">{{ $erroneous_claim_query_total }}</h4>
                            @php 
                                $conditions = array('erroneous_claim_approve_reject_query_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Queried</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                     </div>
               </div>
            </div>
            <!--/ Card Border Shadow -->
         </div>
         <div class="row justify-content-end">
            <div class="col-sm-6 col-lg-3">
               <div class="d-flex justify-content-end">
                     <div class="btn-group">
                        <button type="button"
                           class="btn btn-outline-primary border-0 dropdown-toggle toggle-boxes waves-effect"
                           data-bs-toggle="dropdown" aria-expanded="false">
                           View More
                        </button>
                     </div>
               </div>
            </div>
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
                    <!-- <div class="ms-3 d-flex align-items-center">
                        <button class="navi-button bg-transparent d-inline-block border-0"><i class="ri-arrow-left-s-line"></i></button>
                        <span class="bg-transparent d-inline-block border-0">3</span>
                        <button class="navi-button bg-transparent d-inline-block border-0"><i class="ri-arrow-right-s-line"></i></button>
                    </div> -->
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
                                <option value="{{PreauthRegister::STATUS_CPD_CLAIM_PENDING}}" selected>Claims Pending</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_APPROVED}}">Claims Approved</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_REJECTED}}">Claims Rejected</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_QUERIED}}">Claims Queried</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING}}">Erroneous Claim Pending</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED}}">Erroneous Claims Approved</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED}}">Erroneous Claims Rejected</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED}}">Erroneous Claims Queried</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_QUERIED}}">SHA Revoked Cases</option>
                            </select>
                            <label for="floatingSelect">Preauth Status</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
            </div>
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
    function generate_dt(url = "{{ route('cpd.dashboard-users') }}") {
        var length = $("#rows_per_page").val();
        var status = $("#status").val();
        var list_view = $(".list-view-btn.active").length;
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: { length: length, status: status,list_view:list_view},
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