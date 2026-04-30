@extends('layouts.admin.app')
@section('title','Front Office | Paracare')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-4">
        <h3 class="mb-3 mb-md-0">Hospital List</h3>
      </div>
      <div class="col-sm-8 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <a href="{{ route('admin.hospitals.create-wizard') }}" class="btn btn-info">+ Create Hospital</a>
      </div>
    </div>
  </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
          <table id="xin-table" class="display table-striped">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Hospital Type</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody> </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Add Appointment Modal -->
<div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary ">
        <h5 class="modal-title text-white" id="addAppointmentModalLabel">Add Appointment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Patient Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select class="form-select">
              <option>Select</option>
              <option>Male</option>
              <option>Female</option>
              <option>Other</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Doctor <span class="text-danger">*</span></label>
            <select class="form-select select2" required>
              <option>Select</option>
              <option>Dr Ikshit</option>
              <option>Dr Nitesh Kumar</option>
              <option>Dr Ahutosh Sayana</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label ">Appointment Priority</label>
            <select class="form-select select2">
              <option>Emergency</option>
              <option>Urgent</option>
              <option>Time-Sensitive</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Note <span class="text-danger">*</span></label>
            <textarea class="form-control" rows="1" required></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select">
              <option>Pending</option>
              <option>Approved</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Live Consultant (On Video Conference) <span class="text-danger">*</span></label>
            <select class="form-select" required>
              <option>No</option>
              <option>Yes</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Slot <span class="text-danger">*</span></label>
            <select class="form-select" required>
              <option>Select Slot</option>
              <option>Morning</option>
              <option>Afternoon</option>
              <option>Evening</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
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