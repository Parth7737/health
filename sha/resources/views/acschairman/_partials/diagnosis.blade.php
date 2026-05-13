@if(@$preauth_diagnosis)
@foreach(@$preauth_diagnosis as $preauth_diagnos)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ @$preauth_diagnos->diagnosis->code }}</td>
    <td>{{ @$preauth_diagnos->diagnosis->name != 'Other'?@$preauth_diagnos->diagnosis->name:@$preauth_diagnos->other_diagnosis }}</td>
    <td>{{ @$preauth_diagnos->diagnosis_type }}</td>
</tr>
@endforeach
@endif