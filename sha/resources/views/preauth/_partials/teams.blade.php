@if(@$preauth_teams)
@foreach(@$preauth_teams as $preauth_team)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ @$preauth_team->hospital_team->name }}</td>
    <td>{{ @$preauth_team->hospital_team->hfr_id }}</td>
    <td>{{ @$preauth_team->hospital_team->designation }}</td>
    <td>{{ @$preauth_team->hospital_team->mobile }}</td>
    @if(!isset($is_action_hide))
        <td>
            <div class="dropdown">
                <button
                    type="button"
                    class="btn p-0 dropdown-toggle hide-arrow"
                    data-bs-toggle="dropdown">
                    <i
                        class="ri-more-2-line"></i>
                </button>
                <div
                    class="dropdown-menu">
                    <a class="dropdown-item"
                        onClick="deleteTeam('{{ $preauth_team->id }}')"><i
                            class="ri-delete-bin-7-line me-1"></i>
                        Delete</a>
                </div>
            </div>
        </td>
    @endif
</tr>
@endforeach
@endif