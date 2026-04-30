<!-- <a href="{{ route('admin.hospitals.show', $row->id) }}" class="btn btn-sm btn-primary">View</a>
<a href="{{ route('admin.hospitals.edit', $row->id) }}" class="btn btn-sm btn-warning">Edit</a>
<button data-id="{{ $row->id }}" class="btn btn-sm btn-danger delete-btn">Delete</button> -->

<ul class="action"> 
    <div class="btn-toolbar" role="toolbar" aria-label="Actions">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.hospitals.show', $row->id) }}" data-bs-toggle="tooltip" title="View" class="btn btn-outline-primary btn-xs p-2"><i class="fa-regular fa-eye"></i></a>
            <a href="{{ route('admin.hospitals.edit', $row->id) }}" data-bs-toggle="tooltip" title="Edit" class="btn btn-outline-primary btn-xs p-2"><i class="fa-regular fa-pen-to-square"></i></a>
            @if($row->status == 'Approved')
                <a href="{{ route('admin.hospitals.permission', base64_encode($row->id)) }}"  data-bs-toggle="tooltip" title="Assign Permission" class="btn btn-outline-primary btn-xs p-2"><i class="fa fa-tag"></i></a>
                <a href="javascript:;" data-id="{{base64_encode($row->user_id)}}" data-bs-toggle="tooltip" title="Auto Login" class="btn btn-outline-primary btn-xs p-2 autoin"><i class="fa fa-sign-in"></i></a>
            @endif
        </div>
    </div>
</ul>