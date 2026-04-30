@extends('layouts.hospital.app')
@section('title','Visitors')
@section('page_header_icon', '👥')
@section('page_subtitle', 'Manage Visitors')
@section('page_header_actions')
@can('create-visitor')
    <a class="btn btn-warning" href="{{ route('hospital.front-office.visitors.index') }}">Back</a>
    <button class="btn btn-info adddata" data-id="">+ Add Visitor</button>
@endcan
@endsection
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                <table id="xin-table" class="display table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Purpose</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Visit Date</th>
                            <th>Number of Persons</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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