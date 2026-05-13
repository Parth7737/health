@extends('layouts.admin.app',['main_li'=>'Village','sub_li'=>''])
@section('title','Villages')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Villages</h4>
                    <a href="{{ asset('public/format/csv/village.csv') }}" download class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-download"></i> Download CSV Format
                    </a>
                    <button class="btn btn-primary btn-round ms-auto" id="addVillageBtn" data-bs-toggle="modal" id="importBlock" data-bs-target="#importBlockModal">
                        <i class="fa fa-plus"></i> Import Villages
                    </button>
                    <button class="btn btn-primary btn-round ms-auto" id="addVillageBtn" data-bs-toggle="modal" id="addvillagepProcoedure" data-bs-target="#addVillageModal">
                        <i class="fa fa-plus"></i> Add Village
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
                                <th>District</th>
                                <th>Block</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($villages as $village)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $village->state->name ?? 'N/A' }}</td>
                                <td>{{ $village->district->name ?? 'N/A' }}</td>
                                <td>{{ $village->block->name ?? 'N/A' }}</td>
                                <td>{{ $village->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $village->id }}')" title="Edit Village"><i class="fa fa-edit"></i></a>
                                        <!-- <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('village-{{$village->id}}','Want to delete this Village?')" title="Delete Village"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.villages.destroy', [$village->id]) }}" method="post" id="village-{{$village->id}}">
                                            @csrf @method('delete')
                                        </form> -->
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $villages->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importBlockModal" tabindex="-1" role="dialog" aria-labelledby="importBlockLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importBlockLabel">Add/Edit Village</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('admin.village.import')}}" id="importform" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="statei_id">State<span class="text-danger">*</span></label>
                        <select class="form-control" name="statei_id" id="statei_id" onchange="fetchiDistrict();" required>
                            <option value="" disabled selected>Select a State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="districti_id">District<span class="text-danger">*</span></label>
                        <select class="form-control" name="districti_id"  onchange="fetchIBlock();" id="districti_id" required>
                            <option value="" disabled selected>Select a District</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="blocki_id">Block<span class="text-danger">*</span></label>
                        <select class="form-control" name="blocki_id" id="blocki_id" required>
                            <option value="" disabled selected>Select a Block</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="blockfile">Select File<span class="text-danger">*</span></label>
                        <input type="file" name="blockfile" class="form-control" accept=".csv" required>
                        <small>Need to import csv file. please download sample before import and that formate add file.</small>
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

<!-- Modal -->
<div class="modal fade" id="addVillageModal" tabindex="-1" role="dialog" aria-labelledby="addVillageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVillageModalLabel">Add/Edit Village</h5>
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
                        <select class="form-control" name="state_id" id="state_id" onchange="fetchDistrict();" required>
                            <option value="" disabled selected>Select a State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="district_id">District<span class="text-danger">*</span></label>
                        <select class="form-control" name="district_id" onchange="fetchBlock();" id="district_id" required>
                            <option value="" disabled selected>Select a State</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="block_id">Block<span class="text-danger">*</span></label>
                        <select class="form-control" name="block_id" id="block_id" required>
                            <option value="" disabled selected>Select a Block</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Village Name">
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

    @if(Session::get('error'))
        errorMessage("{{ Session::get('error') }}");
    @endif

    $(document).ready(function () {
        // $("#basic-datatables").DataTable();
    });

    $("#addVillageBtn").on("click",function(){
        $("#district_id").val("").change();
        $("#name").val("");
        $("#edit_id").val("");
    })

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.villages.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.villages.update', ':id') }}`.replace(':id', $("#edit_id").val());
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

    
    function fetchiDistrict() {
        var state_id = $("#statei_id").val();
        if(state_id) {
            const url = `{{ route('admin.getDistrict', ':id') }}`.replace(':id', state_id);

            $.get(url, function (data) {
                $('#districti_id').empty();
            
                $('#districti_id').append('<option value="">Select</option>');
                
                $.each(data.data, function (index, item) {
                    $('#districti_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            });
        } else {
            errorMessage('Please select an State.');
        }
    }

    function fetchDistrict(district_id = '') {
        var state_id = $("#state_id").val();
        if(state_id) {
            const url = `{{ route('admin.getDistrict', ':id') }}`.replace(':id', state_id);

            $.get(url, function (data) {
                $('#district_id').empty();
            
                $('#district_id').append('<option value="">Select</option>');
                
                $.each(data.data, function (index, item) {
                    if(district_id == item.id) {
                        $('#district_id').append(`<option value="${item.id}" selected>${item.name}</option>`);
                    } else {
                        $('#district_id').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                });
            });
        } else {
            errorMessage('Please select an State.');
        }
    }

    function fetchIBlock(blockid = '') {
        var district_id = $("#districti_id").val();
        if(district_id) {
            const url = `{{ route('admin.getblocks', ':id') }}`.replace(':id', district_id);

            $.get(url, function (data) {
                $('#blocki_id').empty();
            
                $('#blocki_id').append('<option value="">Select</option>');
                
                $.each(data.data, function (index, item) {
                    if(blockid == item.id) {
                        $('#blocki_id').append(`<option value="${item.id}" selected>${item.name}</option>`);
                    } else {
                        $('#blocki_id').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                });
            });
        } else {
            errorMessage('Please select an Block.');
        }
    }

    function fetchBlock(blockid = '') {
        var district_id = $("#district_id").val();
        if(district_id) {
            const url = `{{ route('admin.getblocks', ':id') }}`.replace(':id', district_id);

            $.get(url, function (data) {
                $('#block_id').empty();
            
                $('#block_id').append('<option value="">Select</option>');
                
                $.each(data.data, function (index, item) {
                    if(blockid == item.id) {
                        $('#block_id').append(`<option value="${item.id}" selected>${item.name}</option>`);
                    } else {
                        $('#block_id').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                });
            });
        } else {
            errorMessage('Please select an Block.');
        }
    }

    function editData(id) {
        const url = `{{ route('admin.villages.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#district_id").val(data.data.district_id);
            $("#state_id").val(data.data.state_id);
            $("#block_id").val(data.data.block_id);
            fetchDistrict(data.data.district_id);
            setTimeout(() => {
                fetchBlock(data.data.block_id);
            }, 1000);
            $("#name").val(data.data.name);
            $("#edit_id").val(data.data.id);
            $("#addVillageModal").modal('show');
        });
    }
</script>
@endpush
