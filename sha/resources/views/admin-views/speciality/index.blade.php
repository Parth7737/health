@extends('layouts.admin.app',['main_li'=>'Speciality','sub_li'=>''])
@section('title','Speciality')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Speciality</h4>
                    <button class="btn btn-success btn-round ms-auto me-2" id="openImportModal" data-bs-toggle="modal" data-bs-target="#importSpecialityModal">
                        <i class="fa fa-upload"></i> Import Specialities
                    </button>
                    <a href="{{ asset('public/format/csv/master-specialities.csv') }}" download class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-download"></i> Download CSV Format
                    </a>
                    <button class="btn btn-primary btn-round ms-auto" id="addSpecialityBtn" data-bs-toggle="modal" data-bs-target="#addSpecialityModal"><i class="fa fa-plus"></i>Add Speciality</button>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Scheme Type</th>
                            <th>Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($specialities as $speciality)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $speciality->name }}</td>
                                <td>{{ @$speciality->schemeType->name }}</td>
                                <td>{{ $speciality->code }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn-xs btn--primary btn-outline-primary" onclick="editData('{{ $speciality->id }}')" title="Edit Speciality"><i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn  action-btn btn-xs btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('speciality-{{$speciality->id}}','Want to delete this speciality ?')" title="Delete Speciality"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.speciality.destroy',[$speciality->id])}}"
                                                method="post" id="speciality-{{$speciality->id}}">
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
<div class="modal fade" id="addSpecialityModal" tabindex="-1" role="dialog" aria-labelledby="addSpecialityModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSpecialityModalLabel">Add/Edit Speciality</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form action="javascript:" method="post" id="add_edit_form"enctype="multipart/form-data">
        @csrf
        <input type="hidden" class="form-control" name="edit_id" id="edit_id">
        <div class="modal-body">
            <div class="form-group">
                <label for="">Scheme Type<span class="text-danger"> *</span></label>
                @php $scheme_types = App\Models\SchemeType::get() @endphp
                <select class="form-control select2" name="scheme_type_id" id="scheme_type_id" >
                    <option value="">Select Scheme Type</option>
                    @foreach($scheme_types as $scheme_type)
                        <option value="{{ $scheme_type->id }}">{{ $scheme_type->name }}</option>
                    @endforeach
                </select>
            </div>
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

<div class="modal fade" id="importSpecialityModal" tabindex="-1" role="dialog" aria-labelledby="importSpecialityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSpecialityModalLabel">Import Specialities</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.speciality.import') }}" method="POST" enctype="multipart/form-data" id="import_procedures_form">
                @csrf
                <div class="modal-body">
                    <!-- File Upload -->
                    <div class="form-group">
                        <label for="file">Upload CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" id="file" accept=".csv" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Upload</button>
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
    $("#addSpecialityBtn").on("click", function () {
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
            url = "{{ route('admin.speciality.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.speciality.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                    $("#addSpecialityModal").hide();
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
        const url = `{{ route('admin.speciality.show', ':id') }}`.replace(':id', id);
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
                    $("#addSpecialityModal").modal("show");
                    $("#name").val(data.data.name);
                    $("#scheme_type_id").val(data.data.scheme_type_id);
                    $("#edit_id").val(data.data.id);
                    $("#code").val(data.data.code);
                }
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            dropdownParent: $('#addSpecialityModal')
        });
    });
</script>
@endpush