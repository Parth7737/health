@if(@$preauth_teams)
@foreach(@$preauth_teams as $preauth_team)
@php
    $doctor = $preauth_team->staff;
    $legacyTeam = $preauth_team->hospital_team;
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>
        @if($doctor)
            {{ trim($doctor->first_name . ' ' . $doctor->last_name) }}
        @else
            {{ @$legacyTeam->name ?? '—' }}
        @endif
    </td>
    <td>
        @if($doctor)
            {{ $doctor->staff_id ?? '—' }}
        @else
            {{ @$legacyTeam->hfr_id ?? @$legacyTeam->registration_no ?? '—' }}
        @endif
    </td>
    <td>
        @if($doctor)
            {{ $doctor->specialist->name ?? $doctor->specialization ?? '—' }}
        @else
            {{ @$legacyTeam->designation ?? '—' }}
        @endif
    </td>
    <td>
        @if($doctor)
            {{ $doctor->phone ?? '—' }}
        @else
            {{ @$legacyTeam->mobile ?? '—' }}
        @endif
    </td>
    @if(!isset($is_action_hide))
        <td>
            <button type="button" class=" bg-transparent border-0 p-0"
                onClick="deleteTeam('{{ $preauth_team->id }}')" title="Remove doctor">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
            </button>
        </td>
    @endif
</tr>
@endforeach
@endif
