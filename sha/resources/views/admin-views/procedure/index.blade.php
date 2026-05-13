@extends('layouts.admin.app',['main_li'=>'Procedures','sub_li'=>''])
@section('title','Procedures')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Procedures</h4>
                    <button class="btn btn-success btn-round ms-auto me-2" id="openImportModal" data-bs-toggle="modal" data-bs-target="#importProceduresModal">
                        <i class="fa fa-upload"></i> Import Procedures
                    </button>
                    <button class="btn btn-success btn-round ms-auto me-2" id="mapProcedureBtn" data-bs-toggle="modal" data-bs-target="#mapProcedureModal">
                        <i class="fa fa-upload"></i> Map Investigation
                    </button>
                    <a href="{{ asset('public/format/csv/master-procedures.csv') }}" download class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-download"></i> Download CSV Format
                    </a>
                    <a href="{{ asset('public/format/csv/master-investigation-map-procedure.csv') }}" download class="btn btn-primary btn-round ms-auto" ><i class="fa fa-download"></i> Download Map Format</a>
                    <a href="{{route('admin.procedure.create')}}" class="btn btn-primary btn-round ms-auto" ><i class="fa fa-plus"></i>Add Procedure</a>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Scheme</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procedures as $procedure)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ @$procedure->scheme->name }}</td>
                                <td>{{ @$procedure->procedure_category->name }}</td>
                                <td>
                                    @php
                                        $fullText = @$procedure->procedure_name;
                                        $truncatedText = \Illuminate\Support\Str::words($fullText, 5, '...');
                                    @endphp

                                    <div class="short-text" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $truncatedText }}
                                    </div>
                                    <div class="full-text" style="max-width: 250px; word-wrap: break-word; display: none;">
                                        {{ $fullText }}
                                    </div>

                                    @if(\Illuminate\Support\Str::wordCount($fullText) > 5)
                                        <a href="javascript:;" class="toggle-text">Show More</a>
                                    @endif
                                </td>
                                <td>{{ $procedure->procedure_code_2 }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a href="{{route('admin.procedure.edit',[$procedure->id])}}" class="btn action-btn btn--primary btn-xs btn-outline-primary mt-1" title="Edit Procedure"><i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-xs btn-outline-danger mt-1" href="javascript:"
                                            onclick="form_alert('procedure-{{$procedure->id}}','Want to delete this procedure ?')" title="Delete Procedure"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.procedure.destroy',[$procedure->id])}}"
                                                method="post" id="procedure-{{$procedure->id}}">
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

<!-- Import Procedures Modal -->
<div class="modal fade" id="importProceduresModal" tabindex="-1" role="dialog" aria-labelledby="importProceduresModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProceduresModalLabel">Import Procedures</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.procedures.import') }}" method="POST" enctype="multipart/form-data" id="import_procedures_form">
                @csrf
                <div class="modal-body">
                    <!-- File Upload -->
                    <div class="form-group">
                        <label for="procedure_file">Upload CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="procedure_file" id="procedure_file" accept=".csv" required>
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

<!-- Import Modal -->
<div class="modal fade" id="mapProcedureModal" tabindex="-1" role="dialog" aria-labelledby="mapProcedureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapProcedureModalLabel">Map Investigations</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.investigation.map-procedure') }}" method="POST" enctype="multipart/form-data" id="import_form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="beneficiary_file">Upload CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" id="file" accept=".csv" required>
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
        $("#basic-datatables").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.procedures.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'scheme', name: 'scheme' },
                { data: 'category', name: 'category' },
                { data: 'procedure_name', name: 'procedure_name', orderable: false, searchable: true },
                { data: 'procedure_code_2', name: 'procedure_code_2' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });

        // Toggle Show More / Show Less
        // $(document).on('click', '.toggle-text', function () {
        //     let container = $(this).closest('td');
        //     container.find('.short-text').toggle();
        //     container.find('.full-text').toggle();
        //     $(this).text($(this).text() === 'Show More' ? 'Show Less' : 'Show More');
        // });
    });
</script>
<script>
    document.addEventListener('click', function (event) {
    // Check if the clicked element has the class "toggle-text"
    if (event.target.classList.contains('toggle-text')) {
        const parent = event.target.parentElement;
        const shortText = parent.querySelector('.short-text');
        const fullText = parent.querySelector('.full-text');

        if (fullText.style.display === 'none') {
            shortText.style.display = 'none';
            fullText.style.display = 'inline';
            event.target.textContent = 'Show Less';
        } else {
            fullText.style.display = 'none';
            shortText.style.display = 'inline';
            event.target.textContent = 'Show More';
        }
    }
});
</script>

<script>
    $(document).ready(function () {
        $('#openImportModal').on('click', function () {
            $('#importProceduresModal').modal('show');
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            dropdownParent: $('#importProceduresModal')
        });
    });
</script>


@endpush