
@php $discharge_date=''; @endphp
@if($preauthRegister->discharge_type == 'Normal' || $preauthRegister->discharge_type == 'DAMA')
    @php $discharge_date = date('d/m/Y',strtotime($preauthRegister->discharge_date)); @endphp
@elseif($preauthRegister->discharge_type == 'LAMA')
    @php $discharge_date = date('d/m/Y',strtotime($preauthRegister->lama_date)); @endphp
@elseif($preauthRegister->discharge_type == 'Death')
    @php $discharge_date = date('d/m/Y',strtotime($preauthRegister->death_date)); @endphp
@endif
@php
    use App\Models\PreauthRegister;
    $step1 = $step2 = $step3 = $step4 = $step5 = $step6 = $step7 = $step8 = $step9 = '';
    if($preauthRegister->created_at){
        $step1 = 'crossed';
        $step2 = 'active';
    }
    if($preauthRegister->preauth_submission_date){
        $step2 = 'crossed';
        $step3 = 'active';
    }
    if($preauthRegister->preauth_approved_date){
        $step3 = 'crossed';
        $step4 = 'active';
    }
    if($discharge_date){
        $step4 = 'crossed';
        $step5 = 'active';
    }
    if($preauthRegister->bill_date){
        $step5 = 'crossed';
        $step6 = 'active';
    }
    if($preauthRegister->claim_approved_date){
        $step6 = 'crossed';
        $step7 = 'active';
    }
    if($preauthRegister->claim_aco_approved_date){
        $step7 = 'crossed';
        $step8 = 'active';
    }
    if($preauthRegister->claim_paid_date){
        $step8 = 'crossed';
        $step9 = 'crossed';
    }

    $ppdTitle = 'PPD Pending';
    if($preauthRegister->status == PreauthRegister::STATUS_PREAUTH_APPROVED){
        $ppdTitle = 'PPD Approved';
    } elseif($preauthRegister->status == PreauthRegister::STATUS_PREAUTH_REJECTED){
        $ppdTitle = 'PPD Rejected';
    } elseif($preauthRegister->status == PreauthRegister::STATUS_PREAUTH_QUERIED){
        $ppdTitle = 'PPD Queried';
    } elseif($preauthRegister->preauth_approved_date){
        $ppdTitle = 'PPD Approved';
    }

    $cpdTitle = 'CPD Pending';
    if($preauthRegister->status == PreauthRegister::STATUS_CLAIM_APPROVED || $preauthRegister->claim_approved_date){
        $cpdTitle = 'CPD Approved';
    } elseif($preauthRegister->status == PreauthRegister::STATUS_CLAIM_REJECTED){
        $cpdTitle = 'CPD Rejected';
    } elseif($preauthRegister->status == PreauthRegister::STATUS_CLAIM_QUERIED){
        $cpdTitle = 'CPD Queried';
    }

    $statusSteps = [
        [
            'state' => $step1,
            'title' => 'Registered',
            'subtitle' => $preauthRegister->created_at
                ? date('d/m/Y', strtotime($preauthRegister->created_at)) . ' · ' . date('h:i A', strtotime($preauthRegister->created_at))
                : '',
        ],
        [
            'state' => $step2,
            'title' => 'Preauth submission',
            'subtitle' => $preauthRegister->preauth_submission_date
                ? date('d/m/Y', strtotime($preauthRegister->preauth_submission_date)) . ' · ' . date('h:i A', strtotime($preauthRegister->preauth_submission_date))
                : 'Pending',
        ],
        [
            'state' => $step3,
            'title' => $ppdTitle,
            'subtitle' => $preauthRegister->preauth_approved_date
                ? date('d/m/Y', strtotime($preauthRegister->preauth_approved_date)) . ' · ' . date('h:i A', strtotime($preauthRegister->preauth_approved_date))
                : 'Pending',
        ],
        [
            'state' => $step4,
            'title' => $discharge_date ? 'Discharge' : 'Discharge pending',
            'subtitle' => $discharge_date ?: 'Pending',
        ],
        [
            'state' => $step5,
            'title' => 'Claim submission',
            'subtitle' => $preauthRegister->claim_submited_date
                ? date('d/m/Y', strtotime($preauthRegister->claim_submited_date))
                : 'Pending',
        ],
        [
            'state' => $step6,
            'title' => $cpdTitle,
            'subtitle' => $preauthRegister->claim_approved_date
                ? date('d/m/Y', strtotime($preauthRegister->claim_approved_date)) . ' · ' . date('h:i A', strtotime($preauthRegister->claim_approved_date))
                : 'Pending',
        ],
        [
            'state' => $step7,
            'title' => 'At ACO',
            'subtitle' => $preauthRegister->claim_aco_approved_date
                ? date('d/m/Y', strtotime($preauthRegister->claim_aco_approved_date))
                : 'Pending',
        ],
        [
            'state' => $step8,
            'title' => 'At SHA',
            'subtitle' => $preauthRegister->claim_paid_date
                ? date('d/m/Y', strtotime($preauthRegister->claim_paid_date))
                : 'Pending',
        ],
        [
            'state' => $step9,
            'title' => 'Paid by bank',
            'subtitle' => $preauthRegister->claim_paid_date
                ? date('d/m/Y', strtotime($preauthRegister->claim_paid_date)) . ' · ' . date('h:i A', strtotime($preauthRegister->claim_paid_date))
                : 'Pending',
        ],
    ];
@endphp

<div class="scheme-preauth-status-track" aria-label="Preauth case status">
    @foreach($statusSteps as $index => $statusStep)
        <div class="scheme-preauth-status-step step {{ $statusStep['state'] }}" data-step="{{ $index + 1 }}">
            <span class="scheme-preauth-status-step__icon bs-stepper-circle" aria-hidden="true">
                <i class="ri-check-line"></i>
            </span>
            <span class="scheme-preauth-status-step__body">
                <span class="scheme-preauth-status-step__title bs-stepper-title">{{ $statusStep['title'] }}</span>
                @if(filled($statusStep['subtitle']))
                    <span class="scheme-preauth-status-step__meta bs-stepper-subtitle">{{ $statusStep['subtitle'] }}</span>
                @endif
            </span>
        </div>
    @endforeach
</div>
