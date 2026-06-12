<ul class="action">
    <li><a href="javascript:;" data-id="{{ $row->id }}" class="view-purchase-btn" data-bs-toggle="tooltip" title="View"><i class="fa-regular fa-eye text-info"></i></a></li>
    @if($row->status === 'pending')
        @can('create-pharmacy-purchase')
            <li class="edit"><a href="javascript:;" data-id="{{ $row->id }}" class="editdata" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
        @endcan
        @can('approve-pharmacy-purchase')
            <li><a href="javascript:;" data-id="{{ $row->id }}" class="approve-btn" data-bs-toggle="tooltip" title="Approve"><i class="fa-solid fa-check text-success"></i></a></li>
        @endcan
        @can('reject-pharmacy-purchase')
            <li><a href="javascript:;" data-id="{{ $row->id }}" class="reject-btn" data-bs-toggle="tooltip" title="Reject"><i class="fa-solid fa-times text-danger"></i></a></li>
        @endcan
    @endif
    <li><a href="javascript:;" data-id="{{ $row->id }}" class="print-bill-btn" data-bs-toggle="tooltip" title="Print Invoice"><i class="fa-solid fa-print"></i></a></li>
</ul>
