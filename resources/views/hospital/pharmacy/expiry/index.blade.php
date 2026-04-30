@extends('layouts.hospital.app')
@section('title','Pharmacy Expiry Management')
@section('page_header_icon', '💊')
@section('page_subtitle', 'Manage Pharmacy Expiry Management')
@section('page_header_actions')
@can('edit-pharmacy-expiry')
    <button class="btn btn-info btn-sm process-expiry">Process Expired Stock</button>
@endcan
@endsection
@section('content')
<div class="container-fluid">
    @include('hospital.pharmacy.partials.nav')
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Medicine</th>
                                <th>Batch No</th>
                                <th>Expiry Date</th>
                                <th>Available</th>
                                <th>Expired Qty</th>
                                <th>Status</th>
                                <th>Expiry State</th>
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