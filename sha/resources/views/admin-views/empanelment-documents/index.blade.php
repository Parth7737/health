@extends('layouts.admin.app',['main_li'=>'Empanelment','sub_li'=>'Documents'])
@section('title','Empanelment Documents')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Empanelment Documents</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addDocumentBtn" data-bs-toggle="modal" data-bs-target="#addDocumentModal"><i class="fa fa-plus"></i> Add Document</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Required</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->is_required ? 'Yes' : 'No' }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $document->id }}')" title="Edit Document"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('document-{{$document->id}}','Want to delete this Document?')" title="Delete Document"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.empanelment-documents.destroy', [$document->id]) }}" method="post" id="document-{{$document->id}}">
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
<div class="modal fade" id="addDocumentModal" tabindex="-1" role="dialog" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Add/Edit Document</h5>
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
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Document Name">
                    </div>
                    <div class="form-group">
                        <label for="is_required">Is Required?<span class="text-danger">*</span></label>
                        <select class="form-control" name="is_required" id="is_required">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
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

    $("#addDocumentBtn").on("click",function(){
        $("#name").val("");
        $("#is_required").val("0");
        $("#edit_id").val("");
    })
    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.empanelment-documents.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.empanelment-documents.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $('#addDocumentModal').modal('hide');
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
        const url = `{{ route('admin.empanelment-documents.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#name").val(data.data.name);
            $("#is_required").val(data.data.is_required);
            $("#edit_id").val(data.data.id);
            $("#addDocumentModal").modal('show');
        });
    }
</script>
@endpush
