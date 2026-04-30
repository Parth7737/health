<div class="d-flex align-items-center gap-2">
    @can('edit-pathology-test')
        <a class="btn btn-outline-primary btn-xs editdata"
            data-id="{{ $row->id }}"
            href="javascript:void(0)"
            data-bs-toggle="tooltip"
            title="Edit">
            <i class="fa fa-edit"></i>
        </a>
    @endcan

    @can('delete-pathology-test')
        <a class="btn btn-outline-danger btn-xs deletebtn"
            data-id="{{ $row->id }}"
            href="javascript:void(0)"
            data-bs-toggle="tooltip"
            title="Delete">
            <i class="fa fa-trash"></i>
        </a>
    @endcan
</div>