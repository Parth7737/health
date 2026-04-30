@extends('layouts.hospital.app')
@section('title', 'Hidden Zip Manager')
@section('page_header_icon', '📦')
@section('page_subtitle', 'Upload / Download / Delete ZIP')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Upload ZIP</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('hospital.hidden-zip-manager.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">ZIP File (max 50MB)</label>
                                <input type="file" name="zip_file" class="form-control" accept=".zip" required>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100" type="submit">Upload ZIP</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Available ZIP Files</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Updated At</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($files as $file)
                                    <tr>
                                        <td>{{ $file['name'] }}</td>
                                        <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                        <td>{{ $file['updated_at'] }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('hospital.hidden-zip-manager.download', $file['name']) }}" class="btn btn-sm btn-success">
                                                Download
                                            </a>
                                            <form action="{{ route('hospital.hidden-zip-manager.destroy', $file['name']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this ZIP file?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No ZIP files found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
