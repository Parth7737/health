@extends('layouts.admin.app',['main_li'=>'Banks Details','sub_li'=>''])
@section('title','Banks Details')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Banks Details</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" id="addvillagepProcedureBtn" data-bs-target="#addVillageModal">
                        <i class="fa fa-plus"></i> Add Bank
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>State</th>
                                <th>Bank Name</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>Ifsc Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banks as $bank)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bank->state->name ?? 'N/A' }}</td>
                                <td>{{ $bank->bank_name }}</td>
                                <td>{{ $bank->account_name }}</td>
                                <td>{{ $bank->account_number }}</td>
                                <td>{{ $bank->ifsc_code }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $bank->id }}')" title="Edit bank"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('bank-{{$bank->id}}','Want to delete this bank?')" title="Delete bank"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.bank-accounts.destroy', [$bank->id]) }}" method="post" id="bank-{{$bank->id}}">
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
<div class="modal fade" id="addVillageModal" tabindex="-1" role="dialog" aria-labelledby="addVillageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVillageModalLabel">Add/Edit Banks</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="state_id">State<span class="text-danger">*</span></label>
                        <select class="form-control" name="state_id" id="state_id" required>
                            <option value="" >Select a state</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Bank Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Enter Bank Name">
                    </div>
                    <div class="form-group">
                        <label for="name">Account Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Enter Account Name">
                    </div>
                    <div class="form-group">
                        <label for="name">Account Number<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_number" id="account_number" placeholder="Enter Account Number">
                    </div>
                    <div class="form-group">
                        <label for="name">Ifsc Code<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" placeholder="Enter Ifsc Code">
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

    $("#addvillagepProcedureBtn").on("click",function(){
        $("#state_id").val("").change();
        $("#name").val("");
        $("#edit_id").val("");
    })

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.bank-accounts.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.bank-accounts.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $('#addVillageModal').modal('hide');
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
        const url = `{{ route('admin.bank-accounts.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#state_id").val(data.data.state_id);
            $("#bank_name").val(data.data.bank_name);
            $("#ifsc_code").val(data.data.ifsc_code);
            $("#account_name").val(data.data.account_name);
            $("#account_number").val(data.data.account_number);
            $("#edit_id").val(data.data.id);
            $("#addVillageModal").modal('show');
        });
    }
</script>
@endpush
