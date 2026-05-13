@extends('layouts.admin.app', ['main_li' => 'Hospitals', 'sub_li' => ''])
@section('title', 'Hospitals')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Hospitals</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hospital-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Facility Name</th>
                                <th>Facility Type</th>
                                <th>Ownership Type</th>
                                <th>Speciality Type</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hospitals as $hospital)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $hospital->facility_name }}</td>
                                    <td>{{ @$hospital->facilityType->name }}</td>
                                    <td>{{ @$hospital->facilityOwnershipType->name }}</td>
                                    <td>{{ @$hospital->facilitySpecialityType->name }}</td>
                                    <td>{{ date("Y-m-d H:i",strtotime($hospital->created_at)) }}</td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary btn-xs" href="{{ route('admin.hospitals.show', $hospital->id) }}" title="Edit Diagnosis"><i class="fa fa-eye"></i></a>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $("#hospital-datatables").DataTable({});
    });
</script>
@endpush
