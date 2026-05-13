
@php $discharge_date=''; @endphp
@if($preauth_register->discharge_type == 'Normal' || $preauth_register->discharge_type == 'DAMA')
    @php $discharge_date = date('d/m/Y',strtotime($preauth_register->discharge_date)); @endphp
@elseif($preauth_register->discharge_type == 'LAMA')
    @php $discharge_date = date('d/m/Y',strtotime($preauth_register->lama_date)); @endphp
@elseif($preauth_register->discharge_type == 'Death')
    @php $discharge_date = date('d/m/Y',strtotime($preauth_register->death_date)); @endphp
@endif
@php
    use App\Models\PreauthRegister;
    $step1 = $step2 = $step3 = $step4 = $step5 = $step6 = $step7 = $step8 = $step9 = '';
    if($preauth_register->created_at){
        $step1 = 'crossed';
        $step2 = 'active';
    }
    if($preauth_register->preauth_submission_date){
        $step2 = 'crossed';
        $step3 = 'active';
    }
    if($preauth_register->preauth_approved_date){
        $step3 = 'crossed';
        $step4 = 'active';
    }
    if($discharge_date){
        $step4 = 'crossed';
        $step5 = 'active';
    }
    if($preauth_register->bill_date){
        $step5 = 'crossed';
        $step6 = 'active';
    }
    if($preauth_register->claim_approved_date){
        $step6 = 'crossed';
        $step7 = 'active';
    }
    if($preauth_register->claim_aco_approved_date){
        $step7 = 'crossed';
        $step8 = 'active';
    }
    if($preauth_register->claim_paid_date){
        $step8 = 'crossed';
        $step9 = 'crossed';
        
    }
@endphp
<div class="bs-stepper-header overflow-auto">
    <div class="step {{ $step1 }}" data-target="#account-details">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">01</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Registred</span>
                    <span class="bs-stepper-subtitle">({{ date('d/m/Y',strtotime($preauth_register->created_at))." | ".date('h:i A',strtotime($preauth_register->created_at)) }})</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step2 }}" data-target="#personal-info">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">02</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Preauth Submission</span>
                    <span class="bs-stepper-subtitle">{{ ($preauth_register->preauth_submission_date) ? "(".date('d/m/Y',strtotime($preauth_register->preauth_submission_date))." | ".date('h:i A',strtotime($preauth_register->preauth_submission_date)).")" : '' }}</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step3 }}" data-target="#ppd-info">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">02</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    @if($preauth_register->status == PreauthRegister::STATUS_REGISTER || $preauth_register->status == PreauthRegister::STATUS_PREAUTH_PENDING)
                        <span class="bs-stepper-title">PPD Pending</span>
                    @elseif($preauth_register->status == PreauthRegister::STATUS_PREAUTH_APPROVED)
                        <span class="bs-stepper-title">PPD Approved</span>
                    @elseif($preauth_register->status == PreauthRegister::STATUS_PREAUTH_REJECTED)
                        <span class="bs-stepper-title">PPD Rejected</span>
                    @elseif($preauth_register->status == PreauthRegister::STATUS_PREAUTH_QUERIED)
                        <span class="bs-stepper-title">PPD Queried</span>
                    @else
                        @if($preauth_register->preauth_approved_date)
                            <span class="bs-stepper-title">PPD Approved</span>
                        @else
                            <span class="bs-stepper-title">PPD Pending</span>
                        @endif
                    @endif
                    <span class="bs-stepper-subtitle">{{ ($preauth_register->preauth_approved_date) ? "(".date('d/m/Y',strtotime($preauth_register->preauth_approved_date))." | ".date('h:i A',strtotime($preauth_register->preauth_approved_date)).")" : '' }}</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step4 }}" data-target="#discharge-info">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">03</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    @if($discharge_date)
                        <span class="bs-stepper-title">Discharge</span>
                        <span class="bs-stepper-subtitle">({{ $discharge_date }})</span>
                    @else
                        <span class="bs-stepper-title">Discharge Pending</span>
                    @endif
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step5 }}" data-target="#claim-submission">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Claim Submission</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step6 }}" data-target="#claim-pending">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <span class="d-flex flex-column gap-1 ms-2">
                    @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_APPROVED)
                        <span class="bs-stepper-title">CPD Approved</span>
                    @elseif($preauth_register->status == PreauthRegister::STATUS_CLAIM_QUERIED)
                        <span class="bs-stepper-title">CPD Rejected</span>
                    @elseif($preauth_register->status == PreauthRegister::STATUS_CLAIM_QUERIED)
                        <span class="bs-stepper-title">CPD Quired</span>
                    @else
                        @if($preauth_register->claim_approved_date)
                            <span class="bs-stepper-title">CPD Approved</span>
                        @else
                            <span class="bs-stepper-title">CPD Pending</span>
                        @endif
                    @endif
                    <span class="bs-stepper-subtitle">{{ ($preauth_register->claim_approved_date) ? "(".date('d/m/Y',strtotime($preauth_register->claim_approved_date))." | ".date('h:i A',strtotime($preauth_register->claim_approved_date)).")" : '' }}</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step7 }}" data-target="#aco-info">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">At ACO</span>
                    <span class="bs-stepper-subtitle">{{ ($preauth_register->claim_aco_approved_date) ? "(".date('d/m/Y',strtotime($preauth_register->claim_aco_approved_date))." | ".date('h:i A',strtotime($preauth_register->claim_aco_approved_date)).")" : '' }}</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step8 }}" data-target="#sha-info">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">At SHA</span>
                    <span class="bs-stepper-subtitle">{{ ($preauth_register->claim_paid_date) ? "(".date('d/m/Y',strtotime($preauth_register->claim_paid_date))." | ".date('h:i A',strtotime($preauth_register->claim_paid_date)).")" : '' }}</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step9 }}" data-target="#bank-info">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Paid By Bank</span>
                    <span class="bs-stepper-subtitle">{{ ($preauth_register->claim_paid_date) ? "(".date('d/m/Y',strtotime($preauth_register->claim_paid_date))." | ".date('h:i A',strtotime($preauth_register->claim_paid_date)).")" : '' }}</span>
                </span>
            </span>
        </button>
    </div>
</div>