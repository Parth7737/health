@extends('layouts.admin.app',['main_li'=>'Sub Service','sub_li'=>''])
@section('title','Sub Service')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Sub Services</h4>
                    <a href="{{route('admin.sub-service.create')}}" class="btn btn-primary btn-round ms-auto" ><i class="fa fa-plus"></i>Add Subservice</a>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service Name</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subservice as $implant)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                   {{@$implant->service->name}}
                                </td>
                                <td>{{ $implant->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a href="{{route('admin.sub-service.edit',[$implant])}}" class="btn action-btn btn--primary btn-xs btn-outline-primary mt-1" title="Edit sub-service"><i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-xs btn-outline-danger mt-1" href="javascript:"
                                            onclick="form_alert('implant-{{$implant->id}}','Want to delete this sub-service ?')" title="Delete sub-service"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.sub-service.destroy',[$implant->id])}}"
                                                method="post" id="implant-{{$implant->id}}">
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

@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
    });
</script>
@endpush