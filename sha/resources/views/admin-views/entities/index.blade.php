@extends('layouts.admin.app', ['main_li' => 'Entity', 'sub_li' => ''])
@section('title', 'Entity Types')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Entities</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addEntity" data-bs-toggle="modal" data-bs-target="#addEntityModal"><i class="fa fa-plus"></i> Add Entity</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entity->name }}</td>
                                <td>{{ $entity->type }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $entity->id }}')" title="Edit Entity"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('entity-{{$entity->id}}','Want to delete this entity?')" title="Delete Entity"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.entities.destroy', [$entity->id]) }}" method="post" id="entity-{{$entity->id}}">
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
<div class="modal fade" id="addEntityModal" tabindex="-1" role="dialog" aria-labelledby="addEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEntityModalLabel">Add/Edit Entity</h5>
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
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Entity Name">
                    </div>
                    <div class="form-group">
                        <label for="type">Type<span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control">
                            @foreach($entityTypes as $entityType)
                                <option value="{{ $entityType->name }}">{{ $entityType->name }}</option> <!-- Use name as value -->
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

    $("#addEntity").on("click",function(){
        $("#name").val("");
        $("#type").val("");
        $("#edit_id").val("");
    })

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.entities.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.entities.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $('#addEntityModal').modal('hide');
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
        const url = `{{ route('admin.entities.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#name").val(data.data.name);
            $("#type").val(data.data.type);
            $("#edit_id").val(data.data.id);
            $("#addEntityModal").modal('show');
        });
    }
</script>
@endpush
