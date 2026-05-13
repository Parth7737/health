@if(@$procedures)
@php $i=1; @endphp
@foreach(@$procedures as $procedure)
<tr>
    <td>{{ $i++ }}</td>
    <td>{{ @$procedure->procedure->procedure_code_2 }}</td>
    <td>{{ @$procedure->procedure->medical_or_surgical }}</td>
    <td>{{ (@$procedure->stratification_price !=0)?"₹".number_format(@$procedure->stratification_price, 2):'N/A' }}</td>
    <td>{{ @$procedure->no_of_days }}</td>
    <td>₹{{ number_format(@$procedure->procedure_price, 2) }}</td>
    <td>{{ @$procedure->adj_per?@$procedure->adj_per."%":'100%' }}</td>
    <td>{{ @$procedure->incentive?@$procedure->incentive_per."%":'N/A' }}</td>
    
    @if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1)
        @php $procedure->procedure_price = $procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0); @endphp
    @endif
    @php $sub_total = @$procedure->procedure_price+@$procedure->incentive+@$procedure->stratification_price-@$procedure->deducted_amount @endphp
    <td>₹{{ number_format(@$sub_total, 2) }}</td>
</tr>
@if($procedure->implant_id)
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
</tr>
@endif
@endforeach
@endif