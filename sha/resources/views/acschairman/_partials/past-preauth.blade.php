@php
    $past_histories = App\CentralLogics\Helpers::getPastHistory($preauth_register->id);
@endphp
@if(@$past_histories)
@foreach(@$past_histories as $past_history)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ @$past_history->benificiary->name }}</td>
    <td>{{ @$past_history->register_id }}</td>
    <td>-</td>
    <td>{{ @$past_history->hospital->facility_name }}</td>
    <td>{{ date("d/m/Y",strtotime($past_history->preauth_submission_date)) }}</td>
    <td>₹{{ number_format($past_history->preauth_amount_without_deduction,2) }}</td>
    <td>{{ $past_history->status_label }}</td>
    <td>
        
        <a href="{{ route('acschairman.past-history',[$past_history->id]) }}" class="angle-right">
            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 320 512">
                <path
                    d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z" />
            </svg>
        </a>
    </td>
</tr>
@endforeach
@endif