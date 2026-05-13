@extends('layouts.admin.app',['main_li'=>'License','sub_li'=>'LicenseType'])
@section('title','License Types')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">License Types</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addLicenseType" data-bs-toggle="modal" data-bs-target="#addLicenseModal">
                        <i class="fa fa-plus"></i> Add License Type
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>License</th>
                                <th>Is Required</th>
                                <th>Document Required</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licensesTypes as $type)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->licenses->name ?? 'N/A' }}</td>
                                <td>{{ $type->is_required ? 'Yes' : 'No' }}</td>
                                <td>{{ $type->document_required ? 'Yes' : 'No' }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $type->id }}')" title="Edit License Type">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('licenseType-{{ $type->id }}','Want to delete this License Type?')" title="Delete License Type">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{ route('admin.licenses_type.destroy', [$type->id]) }}" method="post" id="licenseType-{{ $type->id }}">
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
<div class="modal fade" id="addLicenseModal" tabindex="-1" role="dialog" aria-labelledby="addLicenseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLicenseModalLabel">Add/Edit License Type</h5>
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
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter License Type">
                    </div>
                    <div class="form-group">
                        <label for="license_id">License</label>
                        <select class="form-control" name="license_id" id="license_id">
                            <option value="">Select License</option>
                            @foreach($licenses as $license)
                            <option value="{{ $license->id }}">{{ $license->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_required">Is Required</label>
                        <input type="checkbox" name="is_required" id="is_required" value="1">
                    </div>
                    <div class="form-group">
                        <label for="document_required">Document Required</label>
                        <input type="checkbox" name="document_required" id="document_required" value="1">
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

    $("#addLicenseType").on("click", function () {
        $("#name").val("");
        $("#license_id").val("");
        $("#is_required").prop('checked', false);
        $("#document_required").prop('checked', false);
        $("#edit_id").val("");
    });

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.licenses_type.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.licenses_type.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $('#addLicenseModal').modal('hide');
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
        const url = `{{ route('admin.licenses_type.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#name").val(data.data.name);
            $("#license_id").val(data.data.license_id);
            $("#is_required").prop('checked', data.data.is_required);
            $("#document_required").prop('checked', data.data.document_required);
            $("#edit_id").val(data.data.id);
            $("#addLicenseModal").modal('show');
        });
    }
</script>
@endpush
