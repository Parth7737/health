<ul class="action">
    @can('edit-building')
        <li class="edit"><a href="javascript:;" data-id="{{ $row->id }}" class="editdata" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
    @endcan
    @can('delete-building')
        <li class="delete"><a href="javascript:;" data-id="{{ $row->id }}" class="deletebtn" data-bs-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash-can"></i></a></li>
    @endcan
</ul>
