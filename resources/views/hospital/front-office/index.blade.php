@extends('layouts.hospital.app')
@section('title', 'Appointment Details')
@section('page_header_icon', 'AP')
@section('page_subtitle', 'Manage and track hospital front-office appointments')
@section('page_header_actions')
  @can('create-appointments')
    <button class="btn btn-primary btn-sm adddata" data-id="">+ Add Appointment</button>
  @endcan
  @can('view-visitor')
    <a href="{{ route('hospital.front-office.visitors.index') }}" class="btn btn-info btn-sm">Visitor Book</a>
  @endcan
@endsection
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <div class="row mb-3 align-items-end">
        <div class="col-md-3 mb-2 mb-md-0">
          <label class="form-label">From Date</label>
          <input type="date" class="form-control" id="from_date" name="from_date">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
          <label class="form-label">To Date</label>
          <input type="date" class="form-control" id="to_date" name="to_date">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="button" class="btn btn-primary btn-sm mt-md-4 mt-2" id="filterBtn">Filter</button>
          <button type="button" class="btn btn-secondary btn-sm mt-md-4 mt-2" id="resetBtn">Reset</button>
        </div>
      </div>

      <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
        <table id="xin-table" class="display table-striped">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Patient Name</th>
              <th>Appointment No</th>
              <th>Date & Slot</th>
              <th>Phone</th>
              <th>Gender</th>
              <th>Doctor</th>
              <th>Source</th>
              <th>Priority</th>
              <th>Live Consultant</th>
              <th>Status</th>
              <th>Actions</th>
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
