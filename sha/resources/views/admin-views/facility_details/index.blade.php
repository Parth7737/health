@extends('layouts.admin.app', ['main_li'=>'Facility', 'sub_li'=>''])
@section('title','Facilities')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Facilities</h4>
                    <button class="btn btn-success btn-round ms-2" data-bs-toggle="modal" data-bs-target="#importFacilityModal">
                        <i class="fa fa-upload"></i> Import Facility
                    </button>
                    <a href="{{ asset('public/storage/csv/facility_sample.csv') }}" download class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-download"></i> Download CSV Format
                    </a>
                    <button class="btn btn-primary btn-round ms-auto" id="addFacilityBtn" data-bs-toggle="modal" data-bs-target="#addFacilityModal"><i class="fa fa-plus"></i> Add Facility</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Facility ID</th>
                                <th>Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>Sub-District</th>
                                <th>Ownership</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facilities as $facility)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $facility->facility_id }}</td>
                                <td>{{ $facility->facility_name }}</td>
                                <td>{{ $facility->state }}</td>
                                <td>{{ $facility->district }}</td>
                                <td>{{ $facility->sub_district }}</td>
                                <td>{{ $facility->facility_ownership }}</td>
                                <td>
                                    <div class="btn--container justify-content-center d-flex gap-1">
                                        <a class="btn action-btn btn--primary btn-xs btn-outline-primary mt-1" onclick="editData('{{ $facility->id }}')" title="Edit"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-xs btn-outline-danger mt-1" href="javascript:" onclick="form_alert('facility-{{$facility->id}}','Want to delete this facility?')" title="Delete"><i class="fa fa-trash"></i></a>
                                        <form action="#" method="post" id="facility-{{$facility->id}}">
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
<div class="modal fade" id="addFacilityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Facility</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form id="add_edit_form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Facility ID</label>
                        <input type="text" class="form-control" name="facility_id" id="facility_id" required>
                    </div>
                    <div class="form-group">
                        <label>Facility Name</label>
                        <input type="text" class="form-control" name="facility_name" id="facility_name" required>
                    </div>
                    <!-- Add remaining fields here -->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="importFacilityModal" tabindex="-1" role="dialog" aria-labelledby="importFacilityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importFacilityModalLabel">Import Facilities</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.facilities.import') }}" method="POST" enctype="multipart/form-data" id="import_facility_form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="facility_file">Upload CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="facility_file" id="facility_file" accept=".csv, .xlsx, .xls" required>
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
        $("#basic-datatables").DataTable();
    });

    $("#addFacilityBtn").on("click", function () {
        $("#facility_id").val("");
        $("#facility_name").val("");
        $("#edit_id").val("");
    });
    function editData(id) {
        $.get(`/admin/facilities/${id}`, function (data) {
            $("#facility_id").val(data.data.facility_id);
            $("#facility_name").val(data.data.facility_name);
            $("#edit_id").val(data.data.id);
            $("#addFacilityModal").modal('show');
        });
    }
</script>
@endpush
