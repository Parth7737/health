<ul class="action">
    <li class="me-2">
        <a
            href="{{ route('hospital.ipd-patient.profile', ['allocation' => $row->id]) }}"
            data-bs-toggle="tooltip"
            title="Open IPD Profile"
        >
            <i class="fa-solid fa-file-medical"></i>
        </a>
    </li>

    @if(!$row->discharge_date)
        @can('edit-ipd-patient')
            <li class="me-2">
                <a
                    href="javascript:;"
                    class="transfer-ipd-btn"
                    data-id="{{ $row->id }}"
                    data-url="{{ route('hospital.ipd-patient.transfer.showform', ['allocation' => $row->id]) }}"
                    data-bs-toggle="tooltip"
                    title="Transfer Bed"
                >
                    <i class="fa-solid fa-right-left"></i>
                </a>
            </li>
            <li class="me-2">
                <a
                    href="javascript:;"
                    class="discharge-ipd-btn"
                    data-id="{{ $row->id }}"
                    data-url="{{ route('hospital.ipd-patient.discharge.showform', ['allocation' => $row->id]) }}"
                    data-bs-toggle="tooltip"
                    title="Discharge Patient"
                >
                    <i class="fa-solid fa-person-walking-arrow-right"></i>
                </a>
            </li>
        @endcan
    @endif
</ul>