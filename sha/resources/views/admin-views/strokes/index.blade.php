@extends('layouts.admin.app',['main_li'=>'Stroke','sub_li'=>''])
@section('title','Stroke')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Strokes</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addStrokeBtn" data-bs-toggle="modal" data-bs-target="#addStrokeModal">
                        <i class="fa fa-plus"></i>Add Stroke
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($strokes as $stroke)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $stroke->name }}</td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $stroke->id }}')" title="Edit Stroke"><i class="fa fa-edit"></i></a>
                                            <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('stroke-{{$stroke->id}}','Want to delete this stroke?')" title="Delete Stroke"><i class="fa fa-trash"></i></a>
                                            <form action="{{route('admin.strokes.destroy',[$stroke->id])}}" method="post" id="stroke-{{$stroke->id}}">
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
<div class="modal fade" id="addStrokeModal" tabindex="-1" role="dialog" aria-labelledby="addStrokeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStrokeModalLabel">Add/Edit Stroke</h5>
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

    $("#addStrokeBtn").on("click", function () {
        $("#name").val("");
        $("#edit_id").val("");
    });
    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() == '') {
            url = "{{ route('admin.strokes.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.strokes.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $("#addStrokeModal").modal("hide");
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
        const url = `{{ route('admin.strokes.show', ':id') }}`.replace(':id', id);
        $.get(url, function (data) {
            $("#name").val(data.data.name);
            $("#edit_id").val(data.data.id);
            $("#addStrokeModal").modal("show");
        });
    }
</script>
@endpush
