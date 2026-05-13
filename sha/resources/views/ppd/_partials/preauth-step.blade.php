@php 
    $step1 = '';
    $step2 = '';
    $step3 = '';
    if($preauth_register->preauth_approved_date != ''){
        $step1 = 'crossed';
        $step2 = 'active';
    }
    if($preauth_register->claim_approved_date != ''){
        $step2 = 'crossed';
        $step3 = '';
    }
    if($preauth_register->claim_paid_date != ''){
        $step3 = 'crossed';
    }
@endphp
<div class="bs-stepper-header">
    <div class="step completed disabled crossed" data-target="#account-details">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">01</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Preauth Requested</span>
                    <span class="bs-stepper-subtitle">({{ date('d/m/Y',strtotime($preauth_register->preauth_submission_date))." | ".date('h:i A',strtotime($preauth_register->preauth_submission_date)) }})</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step1 }}" data-target="#personal-info">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">02</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">{{ $step1?'Preauth Approved':'Pending For Approval' }}</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step2 }}" data-target="#social-links">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">03</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Claim Approved</span>
                </span>
            </span>
        </button>
    </div>
    <div class="line"></div>
    <div class="step {{ $step3}}" data-target="#claim-pending">
        <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
            <span class="bs-stepper-label">
                <!-- <span class="bs-stepper-number">03</span> -->
                <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Claim Paid</span>
                </span>
            </span>
        </button>
    </div>
</div>