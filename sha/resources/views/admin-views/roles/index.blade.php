@extends('layouts.admin.app', ['main_li' => 'Roles', 'sub_li' => ''])
@section('title', 'Roles')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <!-- <div class="d-flex align-items-center">
                    <h4 class="card-title">Roles</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addRoleModal"><i class="fa fa-plus"></i> Add Role</button>
                </div> -->
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Entity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->entity }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $role->id }}')" title="Edit Role"><i class="fa fa-edit"></i></a>
                                        <!-- <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('role-{{$role->id}}', 'Want to delete this Role?')" title="Delete Role"><i class="fa fa-trash"></i></a> -->
                                        <form action="{{ route('admin.roles.destroy', [$role->id]) }}" method="post" id="role-{{$role->id}}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoleModalLabel">Add/Edit Role</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Role Name">
                    </div>
                    <div class="form-group">
                        <label for="entity">Entity<span class="text-danger">*</span></label>
                        <select class="form-control" name="entity" id="entity">
                            <option value="">Select Entity</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable();
    });

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.roles.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.roles.update', ':id') }}`.replace(':id', $("#edit_id").val());
            type = "POST";
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: type,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $('#addRoleModal').modal('hide');
                successMessage(data.msg);
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                errorMessage('An error occurred.');
            }
        });
    });

    function editData(id) {
        const url = `{{ route('admin.roles.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#name").val(data.data.name);
            const entityId = $("#entity option").filter(function () {
            return $(this).text() === data.data.entity;
            }).val();
            $("#entity").val(entityId);
            $("#edit_id").val(data.data.id);
            $("#addRoleModal").modal('show');
        });
    }
</script>
@endpush
