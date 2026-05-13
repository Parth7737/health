@extends('layouts.aco.app')
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
                    <a href="{{ route('aco.case-search') }}" class="btn btn-outline-primary me-2"><i class="ri-user-search-line"></i> &nbsp;&nbsp;Case Search</a>
                    <a href="{{ route('aco.bank-account-details') }}" class="btn btn-outline-primary me-2"><i class="ri-bank-line"></i> &nbsp;&nbsp;Bank Details</a>
                    <a href="{{ route('aco.hospital-recovery-amount') }}" class="btn btn-outline-primary me-2"><i class="ri-device-recover-line"></i> &nbsp;&nbsp;Hospital Recovery Amount</a>
                    <a href="{{ route('aco.adjustment-upload') }}" class="btn btn-outline-primary"><i class="ri-wallet-3-line"></i> &nbsp;&nbsp;Adjustment Upload</a>
                </div>
            </div>
        </div>
        <div class="row g-6">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i
                                        class="ri-calendar-schedule-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $pending_total }}</h4>
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_APPROVED) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Pending</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_APPROVED, 0])}}" class="float-end"><i class="ri-download-line"></i></a>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ACO_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i
                                        class="ri-contract-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $approve_total }}</h4>
                            @php $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_ACO_CLAIM_APPROVED,$conditions,1) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Forwarded</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ACO_CLAIM_APPROVED, 1, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ACO_CLAIM_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i
                                        class="ri-file-close-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $reject_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_ACO_CLAIM_REJECTED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Rejected</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ACO_CLAIM_REJECTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ACO_CLAIM_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-info"><i
                                        class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $query_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_ACO_CLAIM_QUERIED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Queried To CPD</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ACO_CLAIM_QUERIED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i
                                        class="ri-calendar-schedule-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $pending_erroneous_total }}</h4>
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED,[],'erroneous_appoved_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Pending</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED, 0])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i
                                        class="ri-contract-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_approve_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED,$conditions,'erroneous_appoved_amount',1) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Forwarded</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED, 1, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i
                                        class="ri-file-close-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_rejected_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Rejected</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-info"><i
                                        class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_query_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id);
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Queried To CPD</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <!--/ Card Border Shadow -->
        </div>
        <div class="row mt-3 g-6 toggle-div" style="display: none;">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i
                                        class="ri-money-rupee-circle-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $payment_rejected_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Payment Rejected</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_SHA_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i
                                        class="ri-contract-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $sha_claim_approve_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_SHA_CLAIM_APPROVED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Approved By SHA</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_SHA_CLAIM_APPROVED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_SENT_TO_BANK }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-info"><i
                                        class="ri-money-rupee-circle-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_sent_to_bank_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_CLAIM_SENT_TO_BANK,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claim Sent to Bank</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_SENT_TO_BANK, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_PAID_BY_BANK }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i
                                        class="ri-money-rupee-circle-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_paid_by_bank_total }}</h4>
                            @php 
                                $conditions = array('aco_status_added_by'=>auth()->user()->id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_CLAIM_PAID_BY_BANK,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Paid By Bank</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_PAID_BY_BANK, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
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
                                <option value="{{PreauthRegister::STATUS_CLAIM_APPROVED}}" selected>Claims Pending</option>
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
                            <label for="floatingSelect">Cases Status</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end flex-wrap align-items-center">
                    <div class="grid-toggle ms-4">
                        <button type="button" onclick="selectcheckbox();" class="btn btn-primary d-none selectall me-2">Select All</button>
                        <button type="button" class="btn btn-primary approveall d-none me-2">Approve</button>
                        <button type="button" class="btn btn-outline-primary d-none cancelall me-2">Cancel</button>
                    </div>
                </div>
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

<div class="modal fade" id="approvemodal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h4 class="modal-title" id="previewModalLabel3">Process</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusform">       
                <div class="modal-body">                        
                    <div class="col-md-12 mt-2">
                        <select name="preauth_status" id="preauth_status" class="select2 form-select form-select-lg">
                            <option value="">Select</option>
                            <option value="Approve" selected>Approve</option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-floating form-floating-outline mb-6">
                            <textarea class="form-control h-px-100" id="remarks" name="remarks" placeholder="Write remarks here..."></textarea>
                            <label for="remarks">Approve Comments<span class="text-danger">*</span></label>
                        </div>
                    </div>               
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary previewsubmit" id="submitbutton">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        var requiredstatus = '{{PreauthRegister::STATUS_CLAIM_APPROVED}}';
        var selectedstatus = $('#status').val();
        if(selectedstatus == requiredstatus) {
            $('.selectall').removeClass('d-none');
           
        } else {
            $('.selectall').addClass('d-none');
        }

        $('#status').on('change', function() {
            var requiredstatus = '{{PreauthRegister::STATUS_CLAIM_APPROVED}}';
            var selectedstatus = $('#status').val();
            if(selectedstatus == requiredstatus) {
                $('.selectall').removeClass('d-none');
                $('.preauthid').removeClass('d-none');
            } else {
                $('.selectall').addClass('d-none');
                $('.preauthid').addClass('d-none');
            }
        });

        $('.cancelall').on('click', function() {
            $('.preauthid').prop('checked', function(i, val) {
                return !val;
            });
            $('.preauthid').addClass('d-none');
            $('.approveall, .cancelall').addClass('d-none');
            $('.selectall').removeClass('d-none');
        });

        $('.approveall').on('click', function() {
            $('#approvemodal').modal('show');
        });

        $('#submitbutton').on('click', function() {
            $(".loader-overlay").show();
            var status = $("#preauth_status").val();
            var remarks = $("#remarks").val();

            var preauthid = $('.preauthid:checked').map(function() {
                return $(this).val();
            }).get();

            $.ajax({
                url: '{{route("aco.bulkApprove")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: { preauth_status: status, remarks: remarks, preauthid: preauthid },
                success: function (res) {
                    $(".loader-overlay").hide();
                    if (res.success) {
                        successMessage(res.message);
                        location.reload();
                    } else {
                        errorMessage(res.message);
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
        });
    });

    function selectcheckbox() {
        $('.preauthid').prop('checked', true);
        $('.preauthid').removeClass('d-none');
        $('.approveall, .cancelall').removeClass('d-none');
        $('.selectall').addClass('d-none');
    }

    function generate_dt(url = "{{ route('aco.dashboard-users') }}") {
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
                    var requiredstatus = '{{PreauthRegister::STATUS_CLAIM_APPROVED}}';
                    // if(status == requiredstatus) {
                    //     $('.preauthid').removeClass('d-none');
                    // } else {
                    //     $('.preauthid').addClass('d-none');
                    // }
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
        if(status){
            $("#status").val(status).change();
        }
    }

</script>

@endpush