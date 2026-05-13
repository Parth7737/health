@if(@$procedures)
@php $i=1; @endphp
@foreach(@$procedures as $procedure)
@php
    $pstatus = @$procedure->preauth_status;
@endphp
<tr>
    <td>{{ $i++ }}</td>
    <td>{{ @$procedure->procedure->procedure_code_2 }}</td>
    <td>{{ @$procedure->procedure->medical_or_surgical }}</td>
    <td>{{ (@$procedure->stratification_price !=0)?"₹".number_format(@$procedure->stratification_price, 2):'N/A' }}</td>
    <td>{{ @$procedure->no_of_days }}</td>
    <td>₹{{ number_format(@$procedure->original_price, 2) }}</td>
    <td>{{ @$procedure->adj_per?@$procedure->adj_per."%":'100%' }}</td>
    <td>{{ @$procedure->incentive?@$procedure->incentive_per."%":'N/A' }}</td>
    
    @if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1)
        @php $procedure->procedure_price = $procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0); @endphp
    @endif
    @php $sub_total = @$procedure->procedure_price+@$procedure->incentive+@$procedure->stratification_price-@$procedure->deducted_amount @endphp
    <td>₹{{ number_format(@$sub_total, 2) }}</td>
    <td> <span class="badge @if($pstatus == 'Rejected') text-danger @elseif($pstatus == 'Query') text-warning @else text-primary @endif">{{$pstatus}}</span></td>
</tr>
@if($procedure->implant_id)
@php
    $pimpstatus = $procedure->preauth_implant_status == "Rejected" || @$procedure->preauth_status == "Rejected" ? "Rejected" : $procedure->preauth_implant_status;
@endphp
<tr>
    <td>{{ $i++ }}</td>
    <td>{{ @$procedure->implant->code }}</td>
    <td>{{ 'Implant' }}</td>
    <td>{{ 'N/A' }}</td>
    <td>{{ @$procedure->implant_qty }}</td>
    <td>₹{{ number_format(@$procedure->implant_price, 2) }}</td>
    <td>{{ 'N/A' }}</td>
    <td>{{ 'N/A' }}</td>
    @php $sub_total = @$procedure->implant_qty*@$procedure->implant_price @endphp
    <td>₹{{ number_format(@$sub_total, 2) }}</td>
    <td><span class="badge @if($pimpstatus == 'Rejected') text-danger @elseif($pimpstatus == 'Query') text-warning @else text-primary @endif">{{$pimpstatus}}</span></td>
</tr>
@endif
@endforeach
@endif