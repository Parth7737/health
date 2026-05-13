@if(@$preauth_teams)
@foreach(@$preauth_teams as $preauth_team)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ @$preauth_team->hospital_team->name }}</td>
    <td>{{ @$preauth_team->hospital_team->hfr_id }}</td>
    <td>{{ @$preauth_team->hospital_team->designation }}</td>
    <td>{{ @$preauth_team->hospital_team->mobile }}</td>
</tr>
@endforeach
@endif