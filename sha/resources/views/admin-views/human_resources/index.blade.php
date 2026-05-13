@extends('layouts.admin.app',['main_li'=>'HumanResources','sub_li'=>''])
@section('title','Human Resources')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Human Resources</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addHumanResource" data-bs-toggle="modal" data-bs-target="#addHumanResourceModal">
                        <i class="fa fa-plus"></i> Add Human Resource
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Type Slug</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($humanResources as $resource)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $resource->type }}</td>
                                <td>{{ $resource->type_slug }}</td>
                                <td>{{ $resource->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $resource->id }}')" title="Edit Resource">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('resource-{{$resource->id}}','Want to delete this Resource?')" title="Delete Resource">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{ route('admin.human_resources.destroy', [$resource->id]) }}" method="post" id="resource-{{$resource->id}}">
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
<div class="modal fade" id="addHumanResourceModal" tabindex="-1" role="dialog" aria-labelledby="addHumanResourceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHumanResourceModalLabel">Add/Edit Human Resource</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="type">Type<span class="text-danger">*</span></label>
                        <select class="form-control" name="type" id="type">
                            <option value="Medical Human Resource">Medical Human Resource</option>
                            <option value="Support Service Human Resource">Support Service Human Resource</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type_slug">Type Slug<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="type_slug" id="type_slug" readonly>
                    </div>
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name">
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

        // Reset form when opening modal
        $("#addHumanResource").on("click", function () {
            $("#name").val("");
            $("#type_slug").val("");
            $("#edit_id").val("");
        });

        // Populate slug based on type
        $("#type").on("change", function () {
            const type = $(this).val();
            const slug = type === "Medical Human Resource" ? "mhr" : "sshr";
            $("#type_slug").val(slug);
        });

        // Submit form
        $('#add_edit_form').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            let url, type;

            if ($("#edit_id").val() === '') {
                url = "{{ route('admin.human_resources.store') }}";
                type = "POST";
            } else {
                url = `{{ route('admin.human_resources.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                    $('#addHumanResourceModal').modal('hide');
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
    });

        function editData(id) {
            const url = `{{ route('admin.human_resources.show', ':id') }}`.replace(':id', id);

            $.get(url, function (data) {
                $("#type").val(data.data.type).change();
                $("#type_slug").val(data.data.type_slug);
                $("#name").val(data.data.name);
                $("#edit_id").val(data.data.id);
                $("#addHumanResourceModal").modal('show');
            });
        }
    
</script>
@endpush
