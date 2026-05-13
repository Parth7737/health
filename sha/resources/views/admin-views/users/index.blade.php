@extends('layouts.admin.app',['main_li' => 'Users', 'sub_li' => ''])
@section('title', 'Users List')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Users List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="users-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>User ID</th>
                                <th>Mobile No</th>
                                <th>Role</th>
                                <th>Facility Name</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->userid }}</td>
                                    <td>{{ $user->mobile_no }}</td>
                                    <td>{{ $user->role ? $user->role->name : 'No Role' }}</td>
                                    <td>{{ $user->hospital->facility_name ?? 'N/A' }}</td>
                                    <td>{{ $user->created_at->format('d-m-Y') }}</td>
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
        $("#users-table").DataTable();
    });
</script>
@endpush
