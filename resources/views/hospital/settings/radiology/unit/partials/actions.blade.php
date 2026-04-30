<ul class="action">
    @can('edit-radiology-unit')
        <li class="edit"> <a href="javascript:;" data-id="{{ $row->id }}" class="editdata" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
    @endcan
    @can('delete-radiology-unit')
        <li class="delete"><a href="javascript:;" data-id="{{ $row->id }}" class="deletebtn" id="delete{{$row->id}}"data-bs-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash-can"></i></a></li>
    @endcan
</ul>