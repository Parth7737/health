@php
    $status = $row->status ?? 'waiting';
    $nextLabel = $status === 'waiting' ? 'Move In-Room' : ($status === 'in_room' ? 'Mark Complete' : 'Completed');
@endphp

<ul class="action">
    <li class="me-2">
        <a
            href="{{ route('hospital.opd-patient.visits', $row->patient_id) }}"
            data-bs-toggle="tooltip"
            title="Open Patient Visits"
        >
            <i class="fa-solid fa-file-medical"></i>
        </a>
    </li>

    <li class="me-2">
        <a
            href="{{ route('hospital.opd-patient.visit-summary.print', ['opdPatient' => $row->id]) }}"
            target="_blank"
            data-bs-toggle="tooltip"
            title="Print OPD Slip"
        >
            <i class="fa-solid fa-print"></i>
        </a>
    </li>

    <li class="me-2">
        <a
            href="{{ route('hospital.opd-patient.sticker', ['opdPatient' => $row->id, 'autoprint' => 1]) }}"
            target="_blank"
            data-bs-toggle="tooltip"
            title="Print Token Sticker"
        >
            <i class="fa-solid fa-tag"></i>
        </a>
    </li>

    <li class="me-2">
        <a
            href="{{ route('hospital.opd-patient.file-sticker', ['opdPatient' => $row->id, 'autoprint' => 1]) }}"
            target="_blank"
            data-bs-toggle="tooltip"
            title="Print File Sticker"
        >
            <i class="fa-solid fa-folder-open"></i>
        </a>
    </li>

    @can('edit-opd-patient')
        @if($status != 'completed')
            <li class="me-2">
                <a
                    href="javascript:;"
                    data-id="{{ $row->id }}"
                    class="change-status-btn {{ $status === 'completed' ? 'disabled' : '' }}"
                    data-bs-toggle="tooltip"
                    title="{{ $nextLabel }}"
                >
                    <i class="fa-solid fa-forward-step"></i>
                </a>
            </li>
        @endif
    @endcan

    @can('create-opd-patient')
        @if($status == 'completed')
            <li class="me-2">
                <a
                    href="javascript:;"
                    data-patient-id="{{ $row->patient_id }}"
                    class="revisit-btn"
                    data-bs-toggle="tooltip"
                    title="Revisit"
                >
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </li>
        @endif
    @endcan

    @can('create-ipd-patient')
        @if(!(bool) ($row->has_active_ipd ?? false))
            <li class="me-2">
                <a
                    href="javascript:;"
                    class="move-to-ipd-btn"
                    data-opd-patient-id="{{ $row->id }}"
                    data-url="{{ route('hospital.ipd-patient.showform') }}"
                    data-bs-toggle="tooltip"
                    title="Move To IPD"
                >
                    <i class="fa-solid fa-bed-pulse"></i>
                </a>
            </li>
        @endif
    @endcan

    <li class="me-2">
        <a
            href="{{ route('hospital.opd-patient.health-card', $row->patient_id) }}"
            class=""
            target="_blank"
            data-bs-toggle="tooltip"
            title="Print Health Card"
        >
            <i class="fa-solid fa-id-card"></i>
        </a>
    </li>
    @can('delete-opd-patient')
        <li class="delete">
            <a href="javascript:;" data-id="{{ $row->id }}" class="deletebtn" id="delete{{ $row->id }}" data-bs-toggle="tooltip" title="Delete">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </li>
    @endcan
</ul>