@extends('layouts.admin.app',['main_li'=>'Hospital State','sub_li'=>''])

@section('title','Hospital States')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Hospital States</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addDocumentBtn" data-bs-toggle="modal" data-bs-target="#addDocumentModal"><i class="fa fa-plus"></i>Add Hospital State</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <!-- <th>Country ID</th> -->
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hospitalStates as $hospitalState)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <!-- <td>{{ $hospitalState->country_id }}</td> -->
                                    <td>{{ $hospitalState->name }}</td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $hospitalState->id }}')" title="Edit Hospital State"><i class="fa fa-edit"></i></a>
                                            <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:"
                                                onclick="form_alert('hospitalState-{{$hospitalState->id}}','Want to delete this hospital state?')" title="Delete Hospital State"><i class="fa fa-trash"></i></a>
                                            <form action="{{ route('admin.hospital-states.destroy', $hospitalState->id) }}" method="post" id="hospitalState-{{$hospitalState->id}}">
                                                @csrf
                                                @method('delete')
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
                <h5 class="modal-title" id="addDocumentModalLabel">Add/Edit Hospital State</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" class="form-control" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <!-- <div class="form-group">
                        <label for="country_id">Country ID <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="country_id" id="country_id" placeholder="Enter Country ID">
                    </div> -->
                    <input type="hidden" name="country_id" value="101">
                    <div class="form-group">
                        <label for="name">State Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter State Name">
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

    $("#addDocumentBtn").on("click", function () {
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
            url = "{{ route('admin.hospital-states.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.hospital-states.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $("#addDocumentModal").hide();
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
        const url = `{{ route('admin.hospital-states.show', ':id') }}`.replace(':id', id);
        $.get(url, function (data) {
            $("#addDocumentModal").modal("show");
            $("#name").val(data.data.name);
            $("#country_id").val(data.data.country_id);
            $("#edit_id").val(data.data.id);
        });
    }
</script>
@endpush
