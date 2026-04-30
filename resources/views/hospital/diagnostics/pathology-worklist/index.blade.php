@extends('layouts.hospital.app')
@section('title','Pathology')
@section('page_header_icon', '🧪')
@section('page_subtitle', 'Manage Pathology')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select id="filter-status" class="form-control select2">
                                <option value="">All</option>
                                <option value="ordered">Ordered</option>
                                <option value="sample_collected">Sample Collected</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="text" id="filter-date-from" class="form-control diagnosis-date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="text" id="filter-date-to" class="form-control diagnosis-date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label invisible">Action</label>
                            <button type="button" id="clear-filters" class="btn btn-light w-100 mt-auto">Clear Filters</button>
                        </div>
                    </div>
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Order No</th>
                                    <th>Ordered At</th>
                                    <th>Patient</th>
                                    <th>Visit No</th>
                                    <th>Test</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
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
