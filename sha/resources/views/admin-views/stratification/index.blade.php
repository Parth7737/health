@extends('layouts.admin.app',['main_li'=>'Stratifications','sub_li'=>''])
@section('title','Stratifications')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Stratifications</h4>
                    <button class="btn btn-success btn-round ms-auto me-2" id="importStratificationBtn" data-bs-toggle="modal" data-bs-target="#importStratificationModal">
                        <i class="fa fa-upload"></i> Import Stratification
                    </button>
                    <button class="btn btn-success btn-round ms-auto me-2" id="mapProcedureBtn" data-bs-toggle="modal" data-bs-target="#mapProcedureModal">
                        <i class="fa fa-upload"></i> Map Procedures
                    </button>
                    <a href="{{ asset('public/format/csv/master-stratification.csv') }}" download class="btn btn-primary btn-round ms-auto" ><i class="fa fa-download"></i> Download Format</a>
                    <a href="{{ asset('public/format/csv/master-stratification-map-procedure.csv') }}" download class="btn btn-primary btn-round ms-auto" ><i class="fa fa-download"></i> Download Map Format</a>
                    <a href="{{route('admin.stratification.create')}}" class="btn btn-primary btn-round ms-auto" ><i class="fa fa-plus"></i>Add Stratification</a>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Code2</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stratifications as $stratification)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @php
                                        $fullText = @$stratification->name;
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
                                <td>{{ $stratification->code }}</td>
                                <td>{{ $stratification->code2 }}</td>
                                <td>{{ $stratification->price }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a href="{{route('admin.stratification.edit',[$stratification->id])}}" class="btn action-btn btn--primary btn-xs btn-outline-primary mt-1" title="Edit Stratification"><i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-xs btn-outline-danger mt-1" href="javascript:"
                                            onclick="form_alert('stratification-{{$stratification->id}}','Want to delete this stratification ?')" title="Delete Stratification"><i class="fa fa-trash"></i>
                                        </a>
                                        <form action="{{route('admin.stratification.destroy',[$stratification->id])}}"
                                                method="post" id="stratification-{{$stratification->id}}">
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

<!-- Import Modal -->
<div class="modal fade" id="importStratificationModal" tabindex="-1" role="dialog" aria-labelledby="importStratificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStratificationModalLabel">Import Stratification</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.stratification.import') }}" method="POST" enctype="multipart/form-data" id="import_form">
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
<!-- Import Modal -->
<div class="modal fade" id="mapProcedureModal" tabindex="-1" role="dialog" aria-labelledby="mapProcedureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapProcedureModalLabel">Map Procedures</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.stratification.map-procedure') }}" method="POST" enctype="multipart/form-data" id="import_form">
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
        $("#basic-datatables").DataTable({});
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
@endpush