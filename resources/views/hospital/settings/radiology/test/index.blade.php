@extends('layouts.hospital.app')
@section('title','Radiology Tests')
@section('page_header_icon', '🔬')
@section('page_subtitle', 'Manage Radiology Tests')
@section('page_header_actions')
@can('create-radiology-test')
    <button class="btn btn-info adddata" data-id="">+ Add Test</button>
@endcan
@endsection
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3">
            @include('hospital.settings.radiology.submenu')
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Test Name</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Parameters</th>
                                    <th>Charge</th>
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
@include('layouts.partials.flatpickr-css')
@endpush
@push('scripts')
@include('layouts.partials.datatable-js')
@include('layouts.partials.flatpickr-js')
@endpush
