@extends('layouts.hospital.app')

@section('title', 'OPD Queue - View All')
@section('sidebar_variant', 'auto')

@section('page_header_icon', '🩺')
@section('page_subtitle', 'Manage OPD Queue - View All')
@section('page_header_actions')
<a href="{{ route('hospital.doctor-dashboard') }}" class="btn btn-info">
    <i class="fa-solid fa-house-medical me-1"></i> Back to Dashboard
</a>
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
                            <th>Patient</th>
                            <th>Age/Sex</th>
                            <th>Complaint</th>
                            <th>Wait</th>
                            <th>Priority</th>
                            <th>Doctor / Dept</th>
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
@endpush

@push('scripts')
@include('layouts.partials.datatable-js')
@endpush