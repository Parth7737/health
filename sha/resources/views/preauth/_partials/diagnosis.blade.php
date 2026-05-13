@if(@$preauth_diagnosis)
@foreach(@$preauth_diagnosis as $preauth_diagnos)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ @$preauth_diagnos->diagnosis->code }}</td>
    <td>{{ @$preauth_diagnos->diagnosis->name != 'Other'?@$preauth_diagnos->diagnosis->name:@$preauth_diagnos->other_diagnosis }}</td>
    <td>{{ @$preauth_diagnos->diagnosis_type }}</td>
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
                        onClick="deleteDiagnosis('{{ $preauth_diagnos->id }}')"><i
                            class="ri-delete-bin-7-line me-1"></i>
                        Delete</a>
                </div>
            </div>
        </td>
    @endif
</tr>
@endforeach
@endif