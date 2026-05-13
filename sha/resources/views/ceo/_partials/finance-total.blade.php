@php $total=0;$total_incentive=0;$total_deduction=0;$deduction_discharge_amount=0;$deduction_discharge_text='';$recovery_amount=0;$adjustment_amount=0; @endphp
@php $committee_approved_amount=0; @endphp
@if(@$procedures)
@foreach(@$procedures as $procedure)
@php 
    $i=0;
    if($i==0){
        $deduction_discharge_text =  @$procedure->preauth_register->deduction_discharge_text;
        $deduction_discharge_amount = @$procedure->preauth_register->deduction_discharge_amount;
        $recovery_amount = @$procedure->preauth_register->recovery_amount;
        $adjustment_amount = @$procedure->preauth_register->adjustment->adjustment_amount;
        if($procedure->preauth_register->preauth_approved_date == null && $procedure->preauth_register->ceo_approved_date == null){
            $committee_approved_amount = @$procedure->preauth_register->committee_approved_amount;
        }elseif($procedure->preauth_register->preauth_approved_date == null && $procedure->preauth_register->ceo_approved_date){
            $committee_approved_amount = @$procedure->preauth_register->ceo_approved_amount;
        }
    }
    $i++;
    $total +=@$procedure->procedure_price;
    $total +=@$procedure->stratification_price;
    if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1){
        $total +=$procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0);;
    }
    $total +=@$procedure->implant_price*$procedure->implant_qty;
    $total -=@$procedure->deducted_amount;
    $total_deduction +=@$procedure->deducted_amount;

    $total_incentive +=@$procedure->incentive;
@endphp
@endforeach
@php 
    if($committee_approved_amount){
        $deduction = ($total-$committee_approved_amount);
        $total_deduction += $deduction;
        $total -= $deduction;
    }
@endphp
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