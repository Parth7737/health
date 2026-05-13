@extends('layouts.admin.app',['main_li'=>'SchemeType','sub_li'=>''])
@section('title','SchemeType')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">SchemeType</h4>
                    <!-- <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addDocumentModal"><i class="fa fa-plus"></i>Add SchemeType</button> -->
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schemeTypes as $schemeType)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $schemeType->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <!-- <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $schemeType->id }}')" title="Edit SchemeTypes"><i class="fa fa-edit"></i>
                                        </a> -->
                                        <!-- <a class="btn  action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('schemeType-{{$schemeType->id}}','Want to delete this schemeType ?')" title="Delete schemeType"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.SchemeType.destroy',[$schemeType->id])}}"
                                                method="post" id="schemeType-{{$schemeType->id}}">
                                            @csrf @method('delete')
                                        </form> -->
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
        <h5 class="modal-title" id="addDocumentModalLabel">Add/Edit SchemeType</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form action="javascript:" method="post" id="add_edit_form"enctype="multipart/form-data">
        @csrf
        <input type="hidden" class="form-control" name="edit_id" id="edit_id">
        <div class="modal-body">
            <div class="form-group">
                <label for="email2">Name<span class="text-danger">*</span></label>
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
    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        console.log("Form Data before submit:", Object.fromEntries(formData));
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            }
        });

        let url;
        let type;

        if ($("#edit_id").val() == '') {
            url = "{{ route('admin.SchemeType.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.SchemeType.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#loading').hide();
                if (data.errors) {
                    let errors = [];
                    for (let i = 0; i < data.errors.length; i++) {
                        errors.push(data.errors[i].message);
                    }
                    let message = errors.join("<br> ");
                    errorMessage(message);
                } else {
                    $("#addDocumentModal").hide();
                    successMessage(data.msg);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                }
            },
            error: function (xhr) {
                $('#loading').hide();
                console.error(xhr.responseText);
                errorMessage('An error occurred.');
            }
        });
    });

    function editData(id) {
        const url = `{{ route('admin.SchemeType.show', ':id') }}`.replace(':id', id);
        $.post({
            url: url,
            type: "get",
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#loading').hide();
                if (data.errors) {
                    errors=[];
                    for (var i = 0; i < data.errors.length; i++) {
                        errors.push(data.errors[i].message);
                    }
                    message =errors.join("<br> ");
                    errorMessage(message);
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