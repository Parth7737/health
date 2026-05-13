@extends('layouts.admin.app',['main_li'=>'Packages','sub_li'=>''])
@section('title','Packages')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Packages</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addPackageBtn" data-bs-toggle="modal" data-bs-target="#addPackageModal"><i class="fa fa-plus"></i>Add Package</button>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $package->name }}</td>
                                <td>{{ $package->code }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn-xs btn--primary btn-outline-primary" onclick="editData('{{ $package->id }}')" title="Edit Package"><i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn  action-btn btn-xs btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('package-{{$package->id}}','Want to delete this package ?')" title="Delete Package"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.package.destroy',[$package->id])}}"
                                                method="post" id="package-{{$package->id}}">
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
<div class="modal fade" id="addPackageModal" tabindex="-1" role="dialog" aria-labelledby="addPackageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPackageModalLabel">Add/Edit Package</h5>
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
            <div class="form-group">
                <label for="email2">Code<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="code" id="code" placeholder="Enter Code">
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
    $("#addPackageBtn").on("click", function () {
        $("#name").val("");
        $("#code").val("");
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
            url = "{{ route('admin.package.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.package.update', ':id') }}`.replace(':id', $("#edit_id").val());
            type = "POST"; // Use POST, as browsers and jQuery do not natively support PUT
            formData.append('_method', 'PUT'); // Simulate PUT method with `_method` field
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
                    $("#addPackageModal").hide();
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
        const url = `{{ route('admin.package.show', ':id') }}`.replace(':id', id);
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
                    $("#addPackageModal").modal("show");
                    $("#name").val(data.data.name);
                    $("#edit_id").val(data.data.id);
                    $("#code").val(data.data.code);
                }
            }
        });
    }
</script>
@endpush