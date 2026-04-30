@extends('layouts.admin.app',['main_li'=>'Register Requests','sub_li'=>''])
@section('title','Register Requests')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Register Requests</h4>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Mobile No</th>
                            <th>Age</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="card-list">
                                        <div class="item-list">
                                            <div class="avatar">
                                                <img src="{{ asset('public/storage/'.$user->avatar) }}" alt="{{ $user->name }}" class="avatar-img rounded-circle">
                                            </div>
                                            <div class="info-user ms-3">
                                                <div class="username">{{ $user->name }}</div>
                                                <div class="status">{{ $user->designation }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->gender }}</td>
                                <td>{{ @$user->role->name }}</td>
                                <td>{{ $user->mobile_no }}</td>
                                <td>{{ $user->age }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    <div class="btn--container d-flex justify-content-center gap-2">
                                        <a class="btn action-btn btn--info btn-outline-info" 
                                        href="{{ route('admin.users.view', $user->id) }}" 
                                        title="View Details">
                                            <i class="icon icon-eye"></i>
                                        </a>
                                        
                                        <a class="btn action-btn btn--success btn-outline-success" 
                                        href="javascript:" 
                                        onclick="form_alert('user-{{$user->id}}','Want to approve this user ?')" 
                                        title="Approve User">
                                            <i class="icon icon-check"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.users.approve', [$user->id]) }}" 
                                            method="post" id="user-{{$user->id}}">
                                            @csrf @method('post')
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

@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
    });
    function editData(id) {
        const url = `{{ route('admin.document.show', ':id') }}`.replace(':id', id);
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
                    $("#addDocumentModal").modal("show");
                    $("#name").val(data.data.name);
                    $("#edit_id").val(data.data.id);
                }
            }
        });
    }
</script>
@endpush