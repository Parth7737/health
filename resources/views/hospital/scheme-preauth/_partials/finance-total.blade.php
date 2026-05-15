@php 
use \App\Models\PreauthRegister;
$total=0;$total_incentive=0;$total_deduction=0;$deduction_discharge_amount=0;$deduction_discharge_text='';$recovery_amount=0;$adjustment_amount=0; @endphp
@if(@$procedures)
@foreach(@$procedures as $procedure)
@php 
    $i=0;
    if($i==0){
        $deduction_discharge_text =  @$procedure->preauth_register->deduction_discharge_text;
        $deduction_discharge_amount = @$procedure->preauth_register->deduction_discharge_amount;
        $recovery_amount = @$procedure->preauth_register->recovery_amount;
        $adjustment_amount = @$procedure->preauth_register->adjustment->adjustment_amount;
    }
    $i++;
    $is_apply=0;
    if(!isset($is_resubmission)){
        $status_arr = array(PreauthRegister::STATUS_REGISTER,PreauthRegister::STATUS_PREAUTH_PENDING,PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_PREAUTH_QUERIED);
        if((($procedure->preauth_status == 'Approved' || $procedure->preauth_status == 'Forwarded To Medical Committee' || $procedure->preauth_implant_status == 'Approved') && ($procedure->preauth_claim_status == '' && $procedure->preauth_claim_implant_status == '')) || in_array($procedure->preauth_register->status,$status_arr)){
            $is_apply=1;
        }elseif($procedure->preauth_claim_status == 'Approved' || $procedure->preauth_claim_implant_status == 'Approved'){
            $is_apply=1;
        }
    }else{
        $is_apply=1;
    }
    if($is_apply){
        $total +=@$procedure->procedure_price;
        $total +=@$procedure->stratification_price;
        if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1){
            $total +=$procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0);;
        }
        if(($procedure->implant_id && !isset($is_resubmission) && (($procedure->preauth_implant_status == 'Approved' && $procedure->preauth_claim_implant_status == '') || in_array($procedure->preauth_register->status,$status_arr))) || ($procedure->implant_id && !isset($is_resubmission) && $procedure->preauth_claim_implant_status == 'Approved') || ($procedure->implant_id && isset($is_resubmission) && $procedure->is_implant_enhance_or_resubmission == 0)){
            $total +=@$procedure->implant_price*$procedure->implant_qty;
        }
        $total -=@$procedure->deducted_amount;
        $total_deduction +=@$procedure->deducted_amount;
        $total_incentive +=@$procedure->incentive;
    }
@endphp
@endforeach
@endif
<li><label class="">Total package amount
        (Without incentive)</label>
    <span>₹{{ number_format($total, 2) }}</span>
</li>
<li><label class="">Total deduction amount</label>
    <span>₹{{ number_format($total_deduction, 2) }}</span>
</li>
<li><label class="">Recovery amount adjusted</label>
    <span>₹{{ number_format($recovery_amount, 2) }}</span>
</li>
@if($adjustment_amount)
    <li><label class="">Adjusted Amount By ACO</label>
        <span>₹{{ number_format($adjustment_amount, 2) }}</span>
    </li>
@endif
@if($deduction_discharge_amount)
<li><label class="">{{ $deduction_discharge_text }}</label>
    <span>₹{{ number_format($deduction_discharge_amount, 2) }}</span>
</li>
@endif
<li><label class="">Total adjusted
        package
        amount (as per guidence)</label>
    <span>₹{{ number_format($total, 2) }}</span>
</li>
<li><label class="">Total payable amount
        (after incentives)</label>
    <span>₹{{ number_format($total+$total_incentive, 2) }}</span>
</li>