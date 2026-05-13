@extends('layouts.admin.app',['main_li'=>'Hospital Districts','sub_li'=>''])
@section('title','Hospital Districts')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Hospital Districts</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addDistrictBtn" data-bs-toggle="modal" data-bs-target="#addDistrictModal"><i class="fa fa-plus"></i>Add District</button>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>State</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hospitalDistricts as $district)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $district->name }}</td>
                                <td>{{ $district->state->name ?? 'N/A' }}</td> 
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $district->id }}')" title="Edit District"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:"
                                            onclick="form_alert('district-{{$district->id}}','Want to delete this district?')" title="Delete District"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.hospitalDistrict.destroy', $district->id) }}" method="post" id="district-{{$district->id}}">
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="addDistrictModal" tabindex="-1" role="dialog" aria-labelledby="addDistrictModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDistrictModalLabel">Add/Edit Hospital District</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="javascript:" method="post" id="add_edit_form">
          @csrf
          <input type="hidden" class="form-control" name="edit_id" id="edit_id">
          <div class="modal-body">
              <div class="form-group">
                  <label for="name">Name<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name" id="name" placeholder="Enter District Name">
              </div>
              <div class="form-group">
                    <label for="state_id">State<span class="text-danger">*</span></label>
                    <select class="form-control" name="state_id" id="state_id" required>
                        <option value="" disabled selected>Select State</option>
                        @foreach($hospitalStates as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
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
        $("#basic-datatables").DataTable({});
    });

    $("#addDistrictBtn").on("click", function () {
        $("#name").val("");
        $("#state_id").val("").change();
        $("#edit_id").val("");
    });
    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        console.log("Form Data before submit:", Object.fromEntries(formData));
        
        let url;
        let type;

        if ($("#edit_id").val() == '') {
            url = "{{ route('admin.hospitalDistrict.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.hospitalDistrict.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                    $("#addDistrictModal").modal("hide");
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
        const url = `{{ route('admin.hospitalDistrict.show', ':id') }}`.replace(':id', id);
        $.post({
            url: url,
            type: "get",
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#loading').hide();
                if (data.errors) {
                    let errors = [];
                    for (var i = 0; i < data.errors.length; i++) {
                        errors.push(data.errors[i].message);
                    }
                    let message = errors.join("<br> ");
                    errorMessage(message);
                } else {
                    $("#addDistrictModal").modal("show");
                    $("#name").val(data.data.name);
                    $("#state_id").val(data.data.state_id);
                    $("#edit_id").val(data.data.id);
                }
            }
        });
    }
</script>
@endpush
