@extends('layouts.hospital.app')
@section('title', 'Print Header Footer')
@section('page_header_icon', '📋')
@section('page_subtitle', 'Manage Print Header Footer')
@section('page_header_actions')
@canany(['create-header-footer', 'edit-header-footer'])
    <button class="btn btn-info adddata" data-id="" data-type="{{ $selectedType }}">Manage {{ $selectedTypeLabel }}</button>
@endcanany
@endsection
@section('content')
<div class="container-fluid" id="header-footer-page" data-selected-type="{{ $selectedType }}">

    <div class="row g-4">
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="nav flex-lg-column nav-pills nav-primary">
                        @foreach($types as $typeKey => $typeLabel)
                            <a
                                class="nav-link {{ $selectedType === $typeKey ? 'active' : '' }}"
                                href="{{ route('hospital.settings.header-footer.index', ['type' => $typeKey]) }}"
                            >
                                {{ $typeLabel }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-1">{{ $selectedTypeLabel }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Header</th>
                                    <th>Footer</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@include('layouts.partials.datatable-css')
@endpush

@push('scripts')
@include('layouts.partials.datatable-js')
@endpush
