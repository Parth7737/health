<ul class="action">
    @if(!$row->related_type)
        <li class="edit">
            <a href="javascript:;" data-id="{{ $row->id }}" class="editdata" data-bs-toggle="tooltip" title="Edit">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
        </li>
        <li class="delete">
            <a href="javascript:;" data-id="{{ $row->id }}" class="deletebtn" id="delete{{ $row->id }}" data-bs-toggle="tooltip" title="Delete">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </li>
    @else
        <li>
            <span data-bs-toggle="tooltip" title="Auto-managed — delete from source module">
                <i class="fa-solid fa-lock text-muted"></i>
            </span>
        </li>
    @endif
</ul>
