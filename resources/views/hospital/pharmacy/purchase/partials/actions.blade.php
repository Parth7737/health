<ul class="action">
    @can('create-pharmacy-purchase')
        <li class="edit"><a href="javascript:;" data-id="{{ $row->id }}" class="editdata" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
    @endcan
    <li><a href="javascript:;" data-id="{{ $row->id }}" class="print-bill-btn" data-bs-toggle="tooltip" title="Print Invoice"><i class="fa-solid fa-print"></i></a></li>
</ul>
