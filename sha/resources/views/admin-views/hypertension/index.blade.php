@extends('layouts.admin.app',['main_li'=>'Hypertension','sub_li'=>''])
@section('title','Hypertension')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Hypertension</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addHypertensionBtn" data-bs-toggle="modal" data-bs-target="#addHypertensionModal"><i class="fa fa-plus"></i>Add Hypertension</button>
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
                        @foreach($hypertensions as $hypertension)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $hypertension->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $hypertension->id }}')" title="Edit Hypertension"><i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn  action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('hypertension-{{$hypertension->id}}','Want to delete this Hypertension ?')" title="Delete Hypertension"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.hypertension.destroy',[$hypertension->id])}}"
                                                method="post" id="hypertension-{{$hypertension->id}}">
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
<div class="modal fade" id="addHypertensionModal" tabindex="-1" role="dialog" aria-labelledby="addHypertensionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addHypertensionModalLabel">Add/Edit Hypertension</h5>
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
    $("#addHypertensionBtn").on("click", function () {
        $("#name").val("");
        $("#edit_id").val("");
    });
    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            }
        });

        let url;
        let type;

        if ($("#edit_id").val() == '') {
            url = "{{ route('admin.hypertension.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.hypertension.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                if (data.errors) {
                    let message = data.errors.join("<br> ");
                    errorMessage(message);
                } else {
                    $("#addHypertensionModal").modal("hide");
                    successMessage(data.msg);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                }
            },
            error: function () {
                errorMessage('An error occurred.');
            }
        });
    });

    function editData(id) {
        const url = `{{ route('admin.hypertension.edit', ':id') }}`.replace(':id', id);
        $.get(url, function(data) {
            $("#name").val(data.data.name);
            $("#edit_id").val(data.data.id);
            $("#addHypertensionModal").modal("show");
        });
    }
</script>
@endpush
