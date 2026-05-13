@extends('layouts.admin.app',['main_li'=>'NonAddon Related Procedures','sub_li'=>''])
@section('title','NonAddon Related Procedures')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">NonAddon Related Procedures</h4>
                    <button class="btn btn-success btn-round ms-auto me-2" id="importNonAddOnBtn" data-bs-toggle="modal" data-bs-target="#importNonAddOnModal">
                        <i class="fa fa-upload"></i> Import NonAddOn
                    </button>
                    <a href="{{ asset('public/format/csv/master-non-addon.csv') }}" download class="btn btn-primary btn-round ms-auto" ><i class="fa fa-download"></i> Download Format</a>
                    <button class="btn btn-primary btn-round ms-auto" id="addNonAddonRelatedProcoedure" data-bs-toggle="modal" data-bs-target="#addNonAddonRelatedModal"><i class="fa fa-plus"></i>Add NonAddon Related Procedure</button>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Primary Procedure</th>
                            <th>Non Addon Related Procedure</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procedures as $non_addon_procedure)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ @$non_addon_procedure->procedure->procedure_code_2 }}</td>
                                <td>{{ @$non_addon_procedure->nonaddon_procedure->procedure_code_2 }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn-xs btn--primary btn-outline-primary" onclick="editData('{{ $non_addon_procedure->id }}')" title="Edit NonAddon Related Procedure"><i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn  action-btn btn-xs btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('nonaddon-{{$non_addon_procedure->id}}','Want to delete this NonAddon Related Procedure ?')" title="Delete NonAddon Related Procedure"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.nonaddon.destroy',[$non_addon_procedure->id])}}"
                                                method="post" id="nonaddon-{{$non_addon_procedure->id}}">
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
<div class="modal fade" id="addNonAddonRelatedModal" tabindex="-1" role="dialog" aria-labelledby="addNonAddonRelatedModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addNonAddonRelatedModalLabel">Add/Edit NonAddon Related Procedure</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <form action="javascript:" method="post" id="add_edit_form"enctype="multipart/form-data">
        @csrf
        <input type="hidden" class="form-control" name="edit_id" id="edit_id">
        <div class="modal-body">
            <div class="form-group">
                <label for="email2">Primary Addon Procedure<span class="text-danger">*</span></label>
                <select class="form-control select2" name="procedure_id" id="procedure_id">
                    <option value="">Select Procedure</option>
                    @foreach($addon_procedures as $pro)
                        <option value="{{ $pro->id }}">{{ $pro->procedure_code_2 }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="email2">NonAddon Related Procedure<span class="text-danger">*</span></label>
                <select class="form-control select2" name="non_add_on_id" id="non_add_on_id">
                    <option value="">Select NonAddon Related</option>
                    @foreach($nonaddon_procedures as $nonaddon_procedure)
                        <option value="{{ $nonaddon_procedure->id }}">{{ $nonaddon_procedure->procedure_code_2 }}</option>
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

<!-- Import Modal -->
<div class="modal fade" id="importNonAddOnModal" tabindex="-1" role="dialog" aria-labelledby="importNonAddOnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importNonAddOnModalLabel">Import NonAddOn</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.non-addon.import') }}" method="POST" enctype="multipart/form-data" id="import_form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="beneficiary_file">Upload CSV File <span class="text-danger">*</span></label>
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
    $('#addNonAddonRelatedModal').on('shown.bs.modal', function() {
        $('.select2').select2({
            dropdownParent: $('#addNonAddonRelatedModal')
        });
    });
    $("#addNonAddonRelatedProcoedure").on("click",function(){
        $("#procedure_id").val("");
        $("#non_add_on_id").val("");
        $("#edit_id").val("");
    })
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
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
            url = "{{ route('admin.nonaddon.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.nonaddon.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                    $("#addNonAddonRelatedModal").hide();
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
        const url = `{{ route('admin.nonaddon.show', ':id') }}`.replace(':id', id);
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
                    $("#addNonAddonRelatedModal").modal("show");
                    $("#procedure_id").val(data.data.procedure_id);
                    $("#non_add_on_id").val(data.data.non_add_on_id);
                    $("#edit_id").val(data.data.id);
                    $(".select2").select2();
                }
            }
        });
    }
</script>
@endpush