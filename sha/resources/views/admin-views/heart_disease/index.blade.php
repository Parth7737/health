@extends('layouts.admin.app', ['main_li' => 'Heart Disease', 'sub_li' => ''])
@section('title', 'Heart Disease')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Heart Disease</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addDocumentBtn" data-bs-toggle="modal" data-bs-target="#addDocumentModal"><i class="fa fa-plus"></i>Add Heart Disease</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($heartDiseases as $heartDisease)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $heartDisease->name }}</td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $heartDisease->id }}')" title="Edit Heart Disease"><i class="fa fa-edit"></i></a>
                                            <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:"
                                               onclick="form_alert('heartDisease-{{$heartDisease->id}}','Want to delete this Heart Disease?')" title="Delete Heart Disease"><i class="fa fa-trash"></i></a>
                                            <form action="{{ route('admin.heart_diseases.destroy', [$heartDisease->id]) }}" method="post" id="heartDisease-{{$heartDisease->id}}">
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
                <h5 class="modal-title" id="addDocumentModalLabel">Add/Edit Heart Disease</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" class="form-control" name="edit_id" id="edit_id">
                <div class="modal-body">
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
        $("#basic-datatables").DataTable({});
    });

    $("#addDocumentBtn").on("click", function () {
        $("#name").val("");
        $("#edit_id").val("");
    });

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
            }
        });
        let url;
        let type;

        if ($("#edit_id").val() == '') {
            url = "{{ route('admin.heart_diseases.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.heart_diseases.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $("#addDocumentModal").hide();
                successMessage(data.msg);
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('An error occurred.');
            }
        });
    });

    function editData(id) {
        const url = `{{ route('admin.heart_diseases.show', ':id') }}`.replace(':id', id);
        $.post({
            url: url,
            type: "get",
            success: function (data) {
                if (data.errors) {
                    alert(data.errors.join("<br> "));
                } else {
                    $("#addDocumentModal").modal("show");
                    $("#name").val(data.data.name);
                    $("#edit_id").val(data.data.id);
                }
            }
        });
    }
</script>
@endpush
