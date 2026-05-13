@extends('layouts.preauth.app')
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
                    <!-- <h6 class="mb-0 mb-md-0">Your Hospital Dashboard!</h6> -->
                    <!-- <div class="switches-stacked  d-flex align-items-center ms-5">
                        <span class="switch-label me-2">Self</span>
                        <label class="switch">
                            <input type="checkbox" class="switch-input" />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                            <span class="switch-label">Entity</span>
                        </label>
                    </div> -->
                </div>
            </div>
            <div class="col-sm-12 col-lg-9">
               <div class="  d-flex justify-content-end align-items-center ms-5">
                  <a href="{{ route('preauth.search-beneficiary') }}" class="btn btn-primary waves-effect waves-light">New Patient</a>
               </div>
            </div>
        </div>
        <div class="row g-6">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-dark h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_REGISTER }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-dark">
                                        <i class="ri-calendar-schedule-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $submited_total }}</h4>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauthorization be initiated</h6>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PREAUTH_PENDING }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-calendar-schedule-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $pending_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_PREAUTH_PENDING,$conditions,1,'preauth_initiated_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauthorization Pending</h6>
                        @php 
                            $conditions = array('hospital_id'=>auth()->user()->hospital_id);
                            $conditionString = collect($conditions)
                            ->map(fn($v, $k) => "{$k}:{$v}")
                            ->implode('|');
                        @endphp
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PREAUTH_PENDING, 1, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PREAUTH_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-article-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $approved_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id);
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|'); 
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_PREAUTH_APPROVED,$conditions,0,'preauth_amount_without_deduction') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Under Treatment</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PREAUTH_APPROVED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PREAUTH_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $preauth_queries_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|'); 
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_PREAUTH_QUERIED,$conditions,1,'preauth_initiated_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauth Under Queries</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PREAUTH_QUERIED, 1, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PREAUTH_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-pass-expired-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $preauth_rejected_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|'); 
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_PREAUTH_REJECTED,$conditions,1,'preauth_initiated_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauth Rejected</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PREAUTH_REJECTED, 1, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PREAUTH_CANCELLED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-hospital-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $cancelled_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|'); 
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_PREAUTH_CANCELLED,$conditions,0,'preauth_initiated_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Preauth Cancelled by Hospital</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PREAUTH_CANCELLED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_SUBMITTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-info"><i class="ri-pass-pending-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_submited_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id);
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|'); 
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_SUBMITTED,$conditions,0,'preauth_amount_without_deduction') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claim to be initiated</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_SUBMITTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-secondary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_PENDING }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-secondary"><i class="ri-pass-pending-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_initiate_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_PENDING,$conditions,0,'claim_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Submitted By Medco</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_PENDING, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <!--/ Card Border Shadow -->
        </div>
        <div class="row mt-3 g-6 toggle-div" style="display: none;">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CPD_CLAIM_PENDING }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-share-forward-2-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_forward_cex_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CPD_CLAIM_PENDING,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claim Forwarded by CEX</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CPD_CLAIM_PENDING, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-contract-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_approved_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_APPROVED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Approved by CPD</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_APPROVED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_query_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_QUERIED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Under Query</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_QUERIED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_CLAIM_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-file-close-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $cpd_claim_rejected_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id);
                                $conditionString = collect($conditions)
                                ->map(fn($v, $k) => "{$k}:{$v}")
                                ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_REJECTED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Rejected By CPD</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_REJECTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
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
                            <h4 class="mb-0">{{ $aco_claim_approved_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id);
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_ACO_CLAIM_APPROVED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Approved By ACO</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ACO_CLAIM_APPROVED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ACO_CLAIM_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i
                                        class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $aco_claim_query_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_ACO_CLAIM_QUERIED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Queried By ACO</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ACO_CLAIM_QUERIED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
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
                            <h4 class="mb-0">{{ $aco_claim_rejected_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_ACO_CLAIM_REJECTED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Rejected By ACO</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ACO_CLAIM_REJECTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PAYMENT_REINITIATE_BY_ACO }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-info"><i class="ri-money-rupee-circle-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $aco_claim_reinitiate_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_PAYMENT_REINITIATE_BY_ACO,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Payment Re-Initiated By ACO</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PAYMENT_REINITIATE_BY_ACO, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
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
                            <h4 class="mb-0">{{ $sha_claim_approved_total }}</h4>
                            @php 
                                $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_SHA_CLAIM_APPROVED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Approved By SHA</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_SHA_CLAIM_APPROVED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_SHA_CLAIM_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i
                                        class="ri-file-close-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $sha_claim_rejected_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_SHA_CLAIM_REJECTED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Rejected By SHA</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_SHA_CLAIM_REJECTED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
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
                            <h4 class="mb-0">{{ $claim_sent_bank_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_SENT_TO_BANK,$conditions) }}</small>
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
                            <h4 class="mb-0">{{ $claim_paid_bank_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_CLAIM_PAID_BY_BANK,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Claims Paid By Bank</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_CLAIM_PAID_BY_BANK, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-money-rupee-circle-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $claim_payment_rejected_bank_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalAmount(PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Payment Rejected By Bank</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-dark h-100">
                <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-dark"><i
                                        class="ri-calendar-schedule-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_claim_initiated_medco_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id);
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Initiated By Medco</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i
                                        class="ri-contract-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_claim_aprroved_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id);
                            $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED,$conditions,'erroneous_appoved_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Approved</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-warning"><i
                                        class="ri-file-history-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_claim_query_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                            $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED,$conditions) }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims under Query</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-danger h-100">
                <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-danger"><i
                                        class="ri-pass-expired-fill ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_claim_rejected_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id);
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
                <div class="card card-border-shadow-primary h-100">
                <div class="card-body" onclick="getStatusData('{{ PreauthRegister::STATUS_ERRONEOUS_CLAIM_PAID }}')">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-money-rupee-circle-line ri-24px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $erroneous_claim_paid_total }}</h4>
                            @php $conditions = array('hospital_id'=>auth()->user()->hospital_id); 
                                $conditionString = collect($conditions)
                                    ->map(fn($v, $k) => "{$k}:{$v}")
                                    ->implode('|');
                            @endphp
                            <small class="mb-0 pt-5 ms-3">{{ Helpers::getTotalErroneousAmount(PreauthRegister::STATUS_ERRONEOUS_CLAIM_PAID,$conditions,'erroneous_appoved_amount') }}</small>
                        </div>
                        <h6 class="mb-0 fw-normal">Erroneous Claims Paid</h6>
                        <a href="{{route('getDownloadReport', [PreauthRegister::STATUS_ERRONEOUS_CLAIM_PAID, 0, base64_encode($conditionString)])}}" class="float-end"><i class="ri-download-line"></i></a>
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
                </div>
            </div>
        </div>

        <div class="row gx-6 mt-0">
            <div class="col-md-4">
                <div class="card">
                    <div class="form-floating form-floating-outline">
                        <input type="text" id="bs-rangepicker-basic" class="form-control date" />
                        <label for="bs-rangepicker-basic">Register DateRange</label>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-6">
                    <div class="card-body p-0 demo-vertical-spacing demo-only-element">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select select2" id="status"
                                aria-label="Floating label select example">
                                <option value="{{PreauthRegister::STATUS_REGISTER}}" selected>Preauthorization to be initiated</option>
                                <option value="{{PreauthRegister::STATUS_PREAUTH_PENDING}}">Preauthorization Pending</option>
                                <option value="{{PreauthRegister::STATUS_PREAUTH_APPROVED}}">Under Treatment</option>
                                <option value="{{PreauthRegister::STATUS_PREAUTH_QUERIED}}">Preauth Under Queries</option>
                                <option value="{{PreauthRegister::STATUS_PREAUTH_REJECTED}}">Preauth Rejected</option>
                                <option value="{{PreauthRegister::STATUS_PREAUTH_CANCELLED}}">Preauth Cancelled by Hospital</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_SUBMITTED}}">Claim to be initiated</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_PENDING}}">Claims Submitted By Medco</option>
                                <option value="{{PreauthRegister::STATUS_CPD_CLAIM_PENDING}}">Claim Forwarded by CEX</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_APPROVED}}">Claims Approved by CPD</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_QUERIED}}">Claims Under Query</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_REJECTED}}">Claims Rejected By CPD</option>
                                <option value="{{PreauthRegister::STATUS_ACO_CLAIM_APPROVED}}">Claims Approved By ACO</option>
                                <option value="{{PreauthRegister::STATUS_ACO_CLAIM_QUERIED}}">Claims Queried By ACO</option>
                                <option value="{{PreauthRegister::STATUS_ACO_CLAIM_REJECTED}}">Claims Rejected By ACO</option>
                                <option value="{{PreauthRegister::STATUS_PAYMENT_REINITIATE_BY_ACO}}">Payment Re-Initiated By ACO</option>
                                <option value="{{PreauthRegister::STATUS_SHA_CLAIM_APPROVED}}">Claims Approved By SHA</option>
                                <option value="{{PreauthRegister::STATUS_SHA_CLAIM_REJECTED}}">Claims Rejected By SHA</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_SENT_TO_BANK}}">Claim Sent to Bank</option>
                                <option value="{{PreauthRegister::STATUS_CLAIM_PAID_BY_BANK}}">Claims Paid By Bank</option>
                                <option value="{{PreauthRegister::STATUS_PAYMENT_REJECTED_BY_BANK}}">Payment Rejected By Bank</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING}}">Erroneous Claims Initiated By Medco</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED}}">Erroneous Claims Approved</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED}}">Erroneous Claims under Query</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED}}">Erroneous Claims Rejected</option>
                                <option value="{{PreauthRegister::STATUS_ERRONEOUS_CLAIM_PAID}}">Erroneous Claims Paid</option>
                                <!-- <option value="{{PreauthRegister::STATUS_CANCELLED}}">Registration Cancelled</option> -->
                            </select>
                            <label for="floatingSelect">Patient Status</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="d-flex justify-content-between flex-wrap align-items-center">
                    <div class="card-body p-0 demo-vertical-spacing demo-only-element">
                        <div class=" d-flex justify-content-center align-items-center ">
                            <div class="input-group" style="max-width: 400px;">
                                <input type="text" class="form-control" id="search" placeholder="Search"
                                    aria-label="Search">
                                <button class="btn btn-outline-secondary" id="search-btn" type="button">
                                    <i class="ri-search-line ri-22px scaleX-n1-rtl"></i>
                                </button>
                            </div>
                            <!-- <button class="info-btn ms-2"><i
                                    class="ri-information-line"></i></button> -->
                        </div>
                    </div>
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

<div class="modal fade" id="cancelRegistrationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form onSubmit="return false" id="cancelRegistrationForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-6 mt-2">
                            @php $reasons = App\CentralLogics\Helpers::getCommanData('RegistrationCancelReason'); @endphp
                            <div class="form-floating form-floating-outline">
                                <select id="reason_id" name="cancel_reason" class="select2 form-select form-select-lg" data-allow-clear="true">
                                    <option value="">Select Reason</option>
                                    @foreach($reasons as $reason)
                                    <option value="{{ $reason->name }}">{{ $reason->name }}</option>
                                    @endforeach
                                </select>
                                <label for="reason_id">Reason<span class="text-danger">*</span></label>
                            </div>
                            <input type="hidden" name="registration_id" id="registration_id">
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="remarks" name="remarks" class="form-control" placeholder="Type Remarks Here" />
                                <label for="remarks">Remarks</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="cancel-btn">Cancel Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="cancelPreauthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form onSubmit="return false" id="cancelPreauthForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-6 mt-2">
                            @php $reasons = App\CentralLogics\Helpers::getCommanData('PreauthCancelReason'); @endphp
                            <div class="form-floating form-floating-outline">
                                <select id="preauth_reason_id" name="cancel_reason" class="select2 form-select form-select-lg" data-allow-clear="true">
                                    <option value="">Select Reason</option>
                                    @foreach($reasons as $reason)
                                    <option value="{{ $reason->name }}">{{ $reason->name }}</option>
                                    @endforeach
                                </select>
                                <label for="reason_id">Reason<span class="text-danger">*</span></label>
                            </div>
                            <input type="hidden" name="registration_id" id="preauth_registration_id">
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="preauth_remarks" name="remarks" class="form-control" placeholder="Type Remarks Here" />
                                <label for="preauth_remarks">Remarks</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="cancel-preauth-btn">Cancel Preauth</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function generate_dt(url = "{{ route('preauth.dashboard-users') }}") {
        var length = $("#rows_per_page").val();
        var status = $("#status").val();
        var list_view = $(".list-view-btn.active").length;
        var date = $(".date").val();
        var search = $("#search").val();
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: { length: length, status: status,list_view:list_view,date:date,search:search },
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
    $("#search-btn").on("click", function () {
        generate_dt();
    });

    $(document).ready(function() {
        $('#cancelRegistrationModal').on('shown.bs.modal', function() {
            $('#reason_id').select2({
                dropdownParent: $('#cancelRegistrationModal')
            });
        });
        $('#cancelPreauthModal').on('shown.bs.modal', function() {
            $('#preauth_reason_id').select2({
                dropdownParent: $('#cancelPreauthModal')
            });
        });
    });
    function cancelRegistration(registration_id){
        $("#registration_id").val(registration_id);
        $("#reason_id").val("");
        $("#remarks").val("");
        $("#cancelRegistrationModal").modal("show");
    }
    $("#cancel-btn").on("click",function(){
        
        var formData = new FormData($('#cancelRegistrationForm')[0]);
        
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.cancel-registration")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#cancelRegistrationModal").modal("hide");
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
    })
    function cancelPreauth(preauth_registration_id){
        $("#preauth_registration_id").val(preauth_registration_id);
        $("#preauth_reason_id").val("");
        $("#preauth_remarks").val("");
        $("#cancelPreauthModal").modal("show");
    }
    
    $("#cancel-preauth-btn").on("click",function(){
        
        var formData = new FormData($('#cancelPreauthForm')[0]);
        
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.cancel-preauth")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#cancelPreauthModal").modal("hide");
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
    })
    function getStatusData(status){
        $("#status").val(status).change();
    }
</script>

@endpush