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
                <i class="ri-delete-bin-7-line me-1"></i>
            </button>
        </td>
    @endif
</tr>
@endforeach
@endif
