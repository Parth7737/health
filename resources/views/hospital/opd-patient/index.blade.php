@extends('layouts.hospital.app')
@section('title','OPD - Out Patient')
@section('page_header_icon', '🩺')
@section('page_subtitle', 'Manage OPD - Out Patient')
@section('page_header_actions')
@can('view-opd-patient')
    <a href="{{ auth()->user()->hasRole('Doctor') ? route('hospital.doctor-dashboard') : route('hospital.opd-patient.doctor-queue') }}" class="btn btn-sm btn-info">
        <i class="fa-solid fa-list-ol me-1"></i> Queue Desk
    </a>
    <a href="{{ route('hospital.opd-patient.token-display') }}" target="_blank" class="btn btn-sm btn-info">
        <i class="fa-solid fa-display me-1"></i> Token Display
    </a>
@endcan
@can('create-opd-patient')
    <button class="btn btn-sm btn-primary adddata" data-id="">+ Add OPD Patient</button>
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
                            <th>Token</th>
                            <th>Name</th>
                            <th>Patient ID</th>
                            <th>Phone</th>
                            <th>Consultant</th>
                            <th>Last Visit</th>
                            <th>Status</th>
                            <th>No. of Visits</th>
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