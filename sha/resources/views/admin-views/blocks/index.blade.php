@extends('layouts.admin.app',['main_li'=>'Blocks','sub_li'=>''])
@section('title','Blocks')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Blocks</h4>
                    <a href="{{ asset('public/format/csv/block.csv') }}" download class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-download"></i> Download CSV Format
                    </a>
                    <button class="btn btn-primary btn-round ms-auto" id="addVillageBtn" data-bs-toggle="modal" id="importBlock" data-bs-target="#importBlockModal">
                        <i class="fa fa-plus"></i> Import Block
                    </button>
                    <button class="btn btn-primary btn-round ms-auto" id="addVillageBtn" data-bs-toggle="modal" id="addvillagepProcoedure" data-bs-target="#addVillageModal">
                        <i class="fa fa-plus"></i> Add Block
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>District</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blocks as $block)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $block->district->name ?? 'N/A' }}</td>
                                <td>{{ $block->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $block->id }}')" title="Edit Block"><i class="fa fa-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $blocks->links() }}
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
            <form action="{{route('admin.block.import')}}" id="importform" method="POST" enctype="multipart/form-data">
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
                        <select class="form-control" name="districti_id" id="districti_id" required>
                            <option value="" disabled selected>Select a District</option>
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
                        <select class="form-control" name="district_id" id="district_id" required>
                            <option value="" disabled selected>Select a State</option>
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
        // $("#state_id").val("").change();
        // $("#district_id").val("").change();
        $("#name").val("");
        $("#edit_id").val("");
    })

    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.blocks.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.blocks.update', ':id') }}`.replace(':id', $("#edit_id").val());
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

    function editData(id) {
        const url = `{{ route('admin.blocks.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {            
            $("#name").val(data.data.name);
            $("#state_id").val(data.data.state_id);
            fetchDistrict(data.data.district_id);
            $("#edit_id").val(data.data.id);
            $("#addVillageModal").modal('show');
        });
    }
</script>
@endpush
