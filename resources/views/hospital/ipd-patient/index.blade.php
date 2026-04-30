@extends('layouts.hospital.app')
@section('title','IPD - In Patient')
@section('page_header_icon', '🏥')
@section('page_subtitle', 'Manage IPD - In Patient')
@section('page_header_actions')
<a href="{{ route('hospital.settings.beds.bed-dashboard') }}" class="btn btn-info">Bed Dashboard</a>
@can('create-ipd-patient')
    <button class="btn btn-info adddata" type="button">+ Admit</button>
@endcan
@endsection
@section('content')
<div class="container-fluid">

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Active Admissions</p>
                    <h3 class="mb-0">{{ (int) ($stats['active_admissions'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Today Admissions</p>
                    <h3 class="mb-0">{{ (int) ($stats['today_admissions'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Available Beds</p>
                    <h3 class="mb-0 text-success">{{ (int) ($stats['available_beds'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Occupied Beds</p>
                    <h3 class="mb-0 text-danger">{{ (int) ($stats['occupied_beds'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info d-flex align-items-center justify-content-between">
            <strong>Admission Workboard</strong>
            <!-- <small class="text-muted">Admit, transfer, collect payment and discharge from one place.</small> -->
        </div>
        <div class="card-body">
            <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                <table id="xin-table" class="display table-striped w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Admission No</th>
                            <th>Patient</th>
                            <th>UHID</th>
                            <th>Age / Gender</th>
                            <th>Consultant</th>
                            <th>Bed</th>
                            <th>LOS</th>
                            <th>Payer</th>
                            <th>Outstanding</th>
                            <th>Status</th>
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