<ul class="action">
    @can('edit-appointments')
        <li class="edit">
            <a href="javascript:;" data-id="{{ $row->id }}" class="editdata" data-bs-toggle="tooltip" title="Edit">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
        </li>
    @endcan

    @can('edit-appointments')
        @if($status === 'Pending')
            <li class="view">
                <a href="javascript:;" class="change-status-btn" data-status="Approved" data-id="{{ $row->id }}" data-bs-toggle="tooltip" title="Approve">
                    <i class="fa-solid fa-check"></i>
                </a>
            </li>
            <li class="view">
                <a href="javascript:;" class="change-status-btn" data-status="Cancelled" data-id="{{ $row->id }}" data-bs-toggle="tooltip" title="Cancel">
                    <i class="fa-solid fa-ban"></i>
                </a>
            </li>
        @elseif($status === 'Approved')
            <li class="view">
                <a href="javascript:;" class="change-status-btn" data-status="Cancelled" data-id="{{ $row->id }}" data-bs-toggle="tooltip" title="Cancel">
                    <i class="fa-solid fa-ban"></i>
                </a>
            </li>

            @if(empty($row->opd_patient_id))
                <li class="view">
                    <a href="javascript:;" class="move-to-opd-btn" data-id="{{ $row->id }}" data-bs-toggle="tooltip" title="Move To OPD">
                        <i class="fa-solid fa-right-left"></i>
                    </a>
                </li>
            @endif
        @endif
    @endcan

    @can('delete-appointments')
        <li class="delete">
            <a href="javascript:;" data-id="{{ $row->id }}" class="deletebtn" data-bs-toggle="tooltip" title="Delete">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </li>
    @endcan
</ul>
