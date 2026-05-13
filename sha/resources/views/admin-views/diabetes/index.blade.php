@extends('layouts.admin.app',['main_li'=>'Diabetes','sub_li'=>''])
@section('title','Diabetes')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Diabetes Records</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addDocumentBtn" data-bs-toggle="modal" data-bs-target="#addDocumentModal"><i class="fa fa-plus"></i>Add Diabetes Record</button>
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
                        @foreach($diabetes as $record)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $record->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $record->id }}')" title="Edit Diabetes Record"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('diabetes-{{$record->id}}','Want to delete this diabetes record?')" title="Delete Diabetes Record"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.diabetes.destroy', $record->id) }}" method="POST" id="diabetes-{{$record->id}}">
                                            @csrf
                                            @method('DELETE')
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
        <h5 class="modal-title" id="addDocumentModalLabel">Add/Edit Diabetes Record</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="javascript:" method="POST" id="add_edit_form">
          @csrf
          <input type="hidden" name="edit_id" id="edit_id">
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

    $("#addDocumentBtn").on("click",function(){
        $("#name").val("");
        $("#edit_id").val("");
    })
    // Form submission handling for Add/Edit
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
            url = "{{ route('admin.diabetes.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.diabetes.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $("#addDocumentModal").modal("hide");
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
        const url = `{{ route('admin.diabetes.show', ':id') }}`.replace(':id', id);
        $.get(url, function (data) {
            if (data.errors) {
                errorMessage(data.errors.join("<br> "));
            } else {
                $("#addDocumentModal").modal("show");
                $("#name").val(data.data.name);
                $("#edit_id").val(data.data.id);
            }
        });
    }
</script>
@endpush
