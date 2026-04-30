<ul class="action">
    @can('edit-diagnosis')
        <li class="edit me-2">
            <a href="javascript:;" class="edit-diagnosis-btn" data-id="{{ $row->id }}" data-bs-toggle="tooltip" title="Edit">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
        </li>
    @endcan

    @can('delete-diagnosis')
        <li class="delete">
            <a href="javascript:;" class="delete-diagnosis-btn" data-id="{{ $row->id }}" data-bs-toggle="tooltip" title="Delete">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </li>
    @endcan
</ul>
