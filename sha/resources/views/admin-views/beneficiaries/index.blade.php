@extends('layouts.admin.app',['main_li'=>'Beneficiary','sub_li'=>''])
@section('title','Beneficiaries')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Beneficiaries</h4>
                    <!-- <button class="btn btn-primary btn-round ms-auto" id="addBeneficiaryBtn" data-bs-toggle="modal" data-bs-target="#addBeneficiaryModal"><i class="fa fa-plus"></i> Add Beneficiary</button> -->
                    <button class="btn btn-success btn-round ms-auto me-2" id="importBeneficiaryBtn" data-bs-toggle="modal" data-bs-target="#importBeneficiaryModal">
                        <i class="fa fa-upload"></i> Import Beneficiaries
                    </button>
                    <a href="{{ asset('public/format/csv/beneficiaries-master.csv') }}" download class="btn btn-primary btn-round ms-auto" ><i class="fa fa-download"></i> Download Format</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="beneficiary-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Father's Name</th>
                                <th>Card No</th>
                                <th>Abha ID</th>
                                <th>Source</th>
                                <th>Family ID</th>
                                <th>Member ID</th>
                                <th>Mobile No</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addBeneficiaryModal" tabindex="-1" role="dialog" aria-labelledby="addBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBeneficiaryModalLabel">Add/Edit Beneficiary</h5>
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
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="father_name">Father's Name</label>
                        <input type="text" class="form-control" name="father_name" id="father_name" placeholder="Enter Father's Name">
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
<div class="modal fade" id="importBeneficiaryModal" tabindex="-1" role="dialog" aria-labelledby="importBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importBeneficiaryModalLabel">Import Beneficiaries</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.beneficiaries.import') }}" method="POST" enctype="multipart/form-data" id="import_form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="beneficiary_file">Upload CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="beneficiary_file" id="beneficiary_file" accept=".csv" required>
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
        $('#beneficiary-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.beneficiaries.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'father_name', name: 'father_name' },
                { data: 'card_id', name: 'card_id' },
                { data: 'aabha_id', name: 'aabha_id' },
                { data: 'source_type', name: 'source_type' },
                { data: 'family_id', name: 'family_id' },
                { data: 'member_id', name: 'member_id' },
                { data: 'mobile_no', name: 'mobile_no' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: [8], className: "text-center" }
            ]
        });
    });

    $("#addBeneficiaryBtn").on("click", function () {
        $("#name, #father_name").val("");
        $("#edit_id").val("");
    });

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.beneficiaries.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.beneficiaries.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $('#addBeneficiaryModal').modal('hide');
                location.reload();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    });

    function editData(id) {
        $.get(`{{ route('admin.beneficiaries.show', ':id') }}`.replace(':id', id), function (data) {
            $("#name").val(data.data.name);
            $("#father_name").val(data.data.father_name);
            $("#edit_id").val(data.data.id);
            $("#addBeneficiaryModal").modal('show');
        });
    }

    // $('#import_form').on('submit', function (e) {
    //     e.preventDefault();
    //     let formData = new FormData(this);

    //     $.ajax({
    //         url: "{{ route('admin.beneficiaries.import') }}",
    //         type: "POST",
    //         data: formData,
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         beforeSend: function() {
    //             $('.modal-footer button').prop('disabled', true);
    //         },
    //         success: function(response) {
    //             $('#importBeneficiaryModal').modal('hide');
    //             location.reload();
    //         },
    //         error: function(xhr) {
    //             console.error(xhr.responseText);
    //         },
    //         complete: function() {
    //             $('.modal-footer button').prop('disabled', false);
    //         }
    //     });
    // });

</script>
@endpush
