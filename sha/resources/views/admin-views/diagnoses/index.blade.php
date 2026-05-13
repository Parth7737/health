@extends('layouts.admin.app',['main_li'=>'Diagnoses','sub_li'=>''])
@section('title','Diagnoses')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Diagnoses</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addDiagnosisBtn" data-bs-toggle="modal" data-bs-target="#addDiagnosisModal"><i class="fa fa-plus"></i> Add Diagnosis</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diagnoses as $diagnosis)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $diagnosis->name }}</td>
                                <td>{{ $diagnosis->code }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $diagnosis->id }}')" title="Edit Diagnosis"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('diagnosis-{{$diagnosis->id}}','Want to delete this Diagnosis?')" title="Delete Diagnosis"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.diagnoses.destroy', [$diagnosis->id]) }}" method="post" id="diagnosis-{{$diagnosis->id}}">
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
<div class="modal fade" id="addDiagnosisModal" tabindex="-1" role="dialog" aria-labelledby="addDiagnosisModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDiagnosisModalLabel">Add/Edit Diagnosis</h5>
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
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Diagnosis Name">
                    </div>
                    <div class="form-group">
                        <label for="code">Code</label>
                        <input type="text" class="form-control" name="code" id="code" placeholder="Enter Diagnosis Code (optional)">
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

    $("#addDiagnosisBtn").on("click",function(){
        $("#name").val("");
        $("#code").val("");
        $("#edit_id").val("");
    })
    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.diagnoses.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.diagnoses.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $('#addDiagnosisModal').modal('hide');
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
        const url = `{{ route('admin.diagnoses.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#name").val(data.data.name);
            $("#code").val(data.data.code);
            $("#edit_id").val(data.data.id);
            $("#addDiagnosisModal").modal('show');
        });
    }
</script>
@endpush
