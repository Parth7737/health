@extends('layouts.admin.app', ['main_li' => 'Facility Ownership', 'sub_li' => 'Sub Types'])
@section('title', 'Facility Ownership Sub Types')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Facility Ownership Sub Types</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addSubTypeBtn" data-bs-toggle="modal" data-bs-target="#addSubTypeModal"><i class="fa fa-plus"></i> Add Sub Type</button>
                    <button class="btn btn-primary btn-round ms-2" id="addSubType2Btn" data-bs-toggle="modal" data-bs-target="#addSubTypeModal2"><i class="fa fa-plus"></i> Add Sub Type 2</button>
                    <button class="btn btn-primary btn-round ms-2" id="addSubType3Btn" data-bs-toggle="modal" data-bs-target="#addSubTypeModal3"><i class="fa fa-plus"></i> Add Sub Type 3</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Facility Ownership Type</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subTypes as $subType)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subType->ownershipType->name ?? 'N/A' }}</td>
                                <td>{{ $subType->name }}</td>
                                <td>
                                    @if($subType->type == 0)
                                        <span>SubType</span>
                                    @elseif($subType->type == 1)
                                        <span>SubType 2</span>
                                    @elseif($subType->type == 2)
                                        <span>SubType 3</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $subType->id }}', '{{ $subType->type }}')" title="Edit Sub Type"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('subType-{{$subType->id}}','Want to delete this Sub Type?')" title="Delete Sub Type"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.facility_ownership_sub_types.destroy', [$subType->id]) }}" method="post" id="subType-{{$subType->id}}">
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
<div class="modal fade" id="addSubTypeModal" tabindex="-1" role="dialog" aria-labelledby="addSubTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubTypeModalLabel">Add/Edit Facility Ownership Sub Type</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                <div class="form-group">
                    <label for="facility_ownership_type_id">Facility Ownership Type<span class="text-danger">*</span></label>
                    <select class="form-control" name="facility_ownership_type_id" id="facility_ownership_type_id" required>
                        <option value="" disabled selected>Select Ownership Type</option>
                        @foreach($ownershipTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Sub Type Name">
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

<div class="modal fade" id="addSubTypeModal2" tabindex="-1" role="dialog" aria-labelledby="addSubTypeModal2Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubTypeModal2Label">Add/Edit Facility Ownership Sub Type</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form2">
                @csrf
                <input type="hidden" name="edit_id2" id="edit_id2">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="facility_ownership_type_id">Facility Ownership Type<span class="text-danger">*</span></label>
                        <select class="form-control" onchange="getSubType();" name="facility_ownership_type_id" id="facility_ownership_type_id2" required>
                            <option value="" disabled selected>Select Ownership Type</option>
                            @foreach($ownershipTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_id">Facility Ownership Sub Type<span class="text-danger">*</span></label>
                        <select class="form-control" name="type_id" id="type_id">
                            <option value="" >Select</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name2" placeholder="Enter Sub Type Name">
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

<div class="modal fade" id="addSubTypeModal3" tabindex="-1" role="dialog" aria-labelledby="addSubTypeModal3Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubTypeModal3Label">Add/Edit Facility Ownership Sub Type</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form3">
                @csrf
                <input type="hidden" name="edit_id3" id="edit_id3">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="facility_ownership_type_id3">Facility Ownership Type<span class="text-danger">*</span></label>
                        <select class="form-control" onchange="getSubType2();" name="facility_ownership_type_id" id="facility_ownership_type_id3" required>
                            <option value="" disabled selected>Select Ownership Type</option>
                            @foreach($ownershipTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_id3">Facility Ownership Sub Type<span class="text-danger">*</span></label>
                        <select class="form-control" name="type_id" onchange="getSubSubType2();" id="type_id3">
                            <option value="" >Select</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type2_id3">Facility Ownership Sub Type2<span class="text-danger">*</span></label>
                        <select class="form-control" name="type2_id" id="type2_id3">
                            <option value="" >Select</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name3" placeholder="Enter Sub Type Name">
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

    $("#addSubTypeBtn").on("click", function () {
        $("#facility_ownership_type_id").val("").change();
        $("#name").val("");
        $("#edit_id").val("");
    });

    $("#addSubType2Btn").on("click", function () {
        $("#facility_ownership_type_id2").val("").change();
        $("#type_id").val("");
        $("#name2").val("");
        $("#edit_id2").val("");
    });

    $("#addSubType2Btn").on("click", function () {
        $("#facility_ownership_type_id3").val("").change();
        $("#type_id2").val("");
        $("#name3").val("");
        $("#edit_id3").val("");
    });
    
    $('#add_edit_form').on('submit', function (e) {     
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.facility_ownership_sub_types.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.facility_ownership_sub_types.update', ':id') }}`.replace(':id', $("#edit_id").val());
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
                $('#addSubTypeModal').modal('hide');
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

    $('#add_edit_form2').on('submit', function (e) {     
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id2").val() === '') {
            url = "{{ route('admin.subtype2.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.subtype2edit', ':id') }}`.replace(':id', $("#edit_id2").val());
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
                $('#addSubTypeModal2').modal('hide');
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

    $('#add_edit_form3').on('submit', function (e) {     
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id3").val() === '') {
            url = "{{ route('admin.subtype3.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.subtype3edit', ':id') }}`.replace(':id', $("#edit_id3").val());
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
                $('#addSubTypeModal2').modal('hide');
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

    function getSubType(typeid = '') {
        var ownershiptype = $('#facility_ownership_type_id2').val();
        if(ownershiptype) {
            const url = `{{ route('admin.getsubtypes', ':id') }}`.replace(':id', ownershiptype);

            $.get(url, function (data) {
                $('#type_id').empty();
            
                // Add a default "Select" option
                $('#type_id').append('<option value="">Select</option>');
                
                // Loop through the data and add each item as an option
                console.log(typeid);    
                $.each(data.data, function (index, item) {
                    if(typeid == item.id) {
                        $('#type_id').append(`<option value="${item.id}" selected>${item.name}</option>`);
                    } else {
                        $('#type_id').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                });
            });
        } else {
            errorMessage('Please select an ownership type.');
        }
    }

    function getSubType2(typeid = '') {
        var ownershiptype = $('#facility_ownership_type_id3').val();
        if(ownershiptype) {
            const url = `{{ route('admin.getsubtypes', ':id') }}`.replace(':id', ownershiptype);

            $.get(url, function (data) {
                $('#type_id3').empty();
            
                // Add a default "Select" option
                $('#type_id3').append('<option value="">Select</option>');
                
                // Loop through the data and add each item as an option
                console.log(typeid);    
                $.each(data.data, function (index, item) {
                    if(typeid == item.id) {
                        $('#type_id3').append(`<option value="${item.id}" selected>${item.name}</option>`);
                    } else {
                        $('#type_id3').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                });
            });
        } else {
            errorMessage('Please select an ownership type.');
        }
    }
    function getSubSubType2(typeid = '') {
        var ownershiptype = $('#type_id3').val();
        if(ownershiptype) {
            const url = `{{ route('admin.getsubtypes2', ':id') }}`.replace(':id', ownershiptype);

            $.get(url, function (data) {
                $('#type2_id3').empty();
            
                // Add a default "Select" option
                $('#type2_id3').append('<option value="">Select</option>');
                
                // Loop through the data and add each item as an option
                console.log(typeid);    
                $.each(data.data, function (index, item) {
                    if(typeid == item.id) {
                        $('#type2_id3').append(`<option value="${item.id}" selected>${item.name}</option>`);
                    } else {
                        $('#type2_id3').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                });
            });
        } else {
            errorMessage('Please select an ownership type.');
        }
    }

    function editData(id, type = 0) {
        const url = `{{ route('admin.facility_ownership_sub_types.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            if(type == 0) {
                $("#name").val(data.data.name);

                $("#facility_ownership_type_id").val(data.data.facility_ownership_type_id);

                $("#edit_id").val(data.data.id);
                $("#addSubTypeModal").modal('show');
            } else if(type == 2) {
                $("#name3").val(data.data.name);

                $("#facility_ownership_type_id3").val(data.data.facility_ownership_type_id);
                $("#type_id3").val(data.data.type_id);
                $("#type2_id3").val(data.data.type2_id);

                getSubType2(data.data.type_id);
                getSubSubType2(data.data.type2_id);
                setTimeout(() => {
                    $("#edit_id3").val(data.data.id);
                    $("#addSubTypeModal3").modal('show');
                }, 1000);

            } else {
                $("#name2").val(data.data.name);

                $("#facility_ownership_type_id2").val(data.data.facility_ownership_type_id);

                getSubType(data.data.type_id);
                setTimeout(() => {
                    $("#edit_id2").val(data.data.id);
                    $("#addSubTypeModal2").modal('show');
                }, 1000);
               
            }
        });
    }
</script>
@endpush
