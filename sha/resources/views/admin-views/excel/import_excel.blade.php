@extends('layouts.admin.app', ['main_li' => 'Import CSV Data', 'sub_li' => 'Import'])

@section('title', 'Import CSV Data')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Import CSV Data</h4>
                    <a href="{{ asset('public/format/csv/master-generate.csv') }}" download class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-download"></i> Download CSV Format
                    </a>
                </div>
            </div>
            <div class="card-body">

                {{-- Import for Tables with Only "Name" Column --}}
                <h5>Import Data (Only Name Column)</h5>
                <form action="{{ route('admin.excel.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="table_name_name" class="form-label">Select Table:</label>
                        <select name="table_name" id="table_name_name" class="form-control select2" required>
                            <option value="">-- Select Table --</option>
                            @foreach($nameOnlyTables as $table)
                                <option value="{{ $table }}">{{ ucfirst(str_replace('_', ' ', $table)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">Choose CSV File:</label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Import</button>
                </form>

                <hr>

                {{-- Import for Tables with "Name" and "Code" Columns --}}
                <h5>Import Data (Name & Code Columns)</h5>
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Import CSV Data</h4>
                    <a href="{{ asset('public/format/csv/master-generate_name_code.csv') }}" download class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-download"></i> Download CSV Format
                    </a>
                </div>
                <form action="{{ route('admin.excel.importWithCode') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="table_name_code" class="form-label">Select Table:</label>
                        <select name="table_name" id="table_name_code" class="form-control select2" required>
                            <option value="">-- Select Table --</option>
                            @foreach($nameAndCodeTables as $table)
                                <option value="{{ $table }}">{{ ucfirst(str_replace('_', ' ', $table)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">Choose CSV File:</label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Import</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
