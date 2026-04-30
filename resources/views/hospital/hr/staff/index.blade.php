@extends('layouts.hospital.app')
@section('title','Staff')
@section('page_header_icon', '👥')
@section('page_subtitle', 'Manage Staff Management')
@section('page_header_actions')
@can('create-staff')
    <button class="btn btn-info adddata" data-id="">
        <i class="fa fa-plus me-1"></i>Add Staff
    </button>
@endcan
<a href="#" class="btn btn-info">
    <i class="fa fa-download me-1"></i>Import
</a>
<a href="#" class="btn btn-info">
    <i class="fa fa-calendar me-1"></i>Staff Attendance
</a>
<a href="#" class="btn btn-info">
    <i class="fa fa-clock me-1"></i>Leaves
</a>
@endsection
@section('content')
<!-- Container-fluid starts-->

<div class="container-fluid py-3">
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body p-3">
            <form class="row g-2 align-items-center flex-wrap" id="staffFilterForm" onsubmit="return false;">
                <div class="col-12 col-md-3">
                    <select class="form-select" id="roleFilter" name="role_filter">
                        <option value="">All</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ @$data->role_id === $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <input type="text" class="form-control" id="keywordSearch" name="keyword" placeholder="Search by name, email, phone, or staff ID...">
                </div>
                <div class="col-12 col-md-3 d-grid gap-2">
                    <button class="btn btn-info w-100" id="searchBtn" type="button">
                        <i class="fa fa-search me-1"></i>Search & Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body p-3">
            <ul class="nav nav-tabs mb-4" id="staffTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="card-tab" data-bs-toggle="tab" data-bs-target="#cardView" type="button" role="tab">
                        <i class="fa fa-th-large me-2"></i>Grid View
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="list-tab" data-bs-toggle="tab" data-bs-target="#listView" type="button" role="tab">
                        <i class="fa fa-list me-2"></i>List View
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="staffTabContent">
                <!-- Grid View Tab -->
                <div class="tab-pane fade show active" id="cardView" role="tabpanel">
                    <div class="row g-3" id="staffGridContainer">
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading staff data...</p>
                        </div>
                    </div>
                    <!-- load more button row -->
                    <div class="row">
                        <div class="col-12 text-center mt-3">
                            <button id="loadMoreGrid" class="btn btn-secondary d-none">Load More</button>
                        </div>
                    </div>
                </div>

                <!-- List View Tab -->
                <div class="tab-pane fade" id="listView" role="tabpanel">
                    <div class="table-responsive">
                        <table id="xin-table" class="display table table-striped table-hover w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
<style>
    .staff-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid #e9ecef;
    }

    .staff-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        border-color: #0d6efd;
    }

    .staff-avatar {
        border: 3px solid #e9ecef;
        object-fit: cover;
    }

    .staff-card:hover .staff-avatar {
        border-color: #0d6efd;
    }

    .section-title {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }

    .dt-ext {
        width: 100%;
    }

    /* Work Timings Styles */
    .work-timings-container {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        background-color: #f8f9fa;
    }

    .timing-item {
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }

    .timing-item:hover {
        border-left-color: #0b5ed7;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .timings-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .timings-list:empty::before {
        content: "No work timings added yet. Use the form above to add timings.";
        display: block;
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 20px;
    }
</style>
@endpush

@push('scripts')
@include('layouts.partials.datatable-js')
@include('layouts.partials.flatpickr-js')
@endpush